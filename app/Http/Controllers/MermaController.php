<?php

namespace App\Http\Controllers;

use App\Exports\MermaExport;
use App\Models\Location;
use App\Models\Product;
use App\Models\PurchaseDetail;
use App\Models\Tank;
use App\Models\TankReading;
use App\Models\Waste;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MermaController extends Controller
{
    // Tolerancia relativa sobre el stock teórico para considerar que "cuadra".
    const TOLERANCIA_PORCENTAJE = 0.02;

    // Umbrales de nivel de stock respecto a la capacidad del tanque.
    const STOCK_SIN = 0.05;
    const STOCK_BAJO = 0.25;
    const STOCK_MEDIO = 0.60;

    /**
     * Pantalla de toma de inventario: una fila por tanque de la sede seleccionada.
     */
    public function create(Request $request)
    {
        $currentUser = auth()->user();
        $isMaster = $currentUser->role->nombre === 'master';

        $locations = Location::where('deleted', 0)
            ->when(!$isMaster && $currentUser->location_id, function ($q) use ($currentUser) {
                $q->where('id', $currentUser->location_id);
            })
            ->orderBy('name')
            ->get();

        $currentLocationId = $request->input('location_id', $currentUser->location_id);
        if (!$isMaster && $currentUser->location_id) {
            $currentLocationId = $currentUser->location_id;
        }
        // Sin sede asignada ni elegida (típico de master en la primera carga): usar la
        // primera de la lista, para que el desplegable y los tanques mostrados coincidan.
        if (!$currentLocationId && $locations->isNotEmpty()) {
            $currentLocationId = $locations->first()->id;
        }

        $tanks = Tank::where('deleted', 0)
            ->where('is_reserve', 0)
            ->when($currentLocationId, function ($q) use ($currentLocationId) {
                $q->where('location_id', $currentLocationId);
            })
            ->with('product')
            ->orderBy('name')
            ->get();

        foreach ($tanks as $tank) {
            $lastReading = TankReading::where('tank_id', $tank->id)
                ->where('deleted', 0)
                ->orderByDesc('date')
                ->orderByDesc('id')
                ->first();

            $tank->last_reading = $lastReading;
            $tank->referencia = $lastReading ? $lastReading->physical_quantity : $tank->stored_quantity;
        }

        return view('merma.create', compact('locations', 'tanks', 'currentLocationId'));
    }

    /**
     * Guarda una toma de inventario por tanque, calcula el teórico, clasifica
     * la diferencia (falta/cuadra/sobra), registra la merma si corresponde y
     * recalibra el stock contable del tanque al valor físico ingresado.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_id'         => 'required|exists:locations,id',
            'date'                => 'required|date',
            'lecturas'            => 'required|array',
            'lecturas.*.fisico'   => 'nullable|numeric|min:0',
        ]);

        $currentUser = auth()->user();
        $userId = $currentUser->id;
        $isMaster = $currentUser->role->nombre === 'master';
        if (!$isMaster && $validated['location_id'] != $currentUser->location_id) {
            return redirect()->back()->with('error', 'No tiene permisos para registrar tomas de inventario en otra sede.');
        }

        $date = $validated['date'];
        $savedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($validated['lecturas'] as $tankId => $data) {
                if (!isset($data['fisico']) || $data['fisico'] === null || $data['fisico'] === '') {
                    continue;
                }

                $tank = Tank::find($tankId);
                if (!$tank) {
                    continue;
                }

                if (!$isMaster && $tank->location_id != $currentUser->location_id) {
                    continue;
                }

                $fisico = round(floatval($data['fisico']), 3);

                $previousReading = TankReading::where('tank_id', $tank->id)
                    ->where('deleted', 0)
                    ->orderByDesc('date')
                    ->orderByDesc('id')
                    ->first();

                if ($previousReading) {
                    // Hay una toma anterior: el teórico se arma sumando/restando
                    // solo lo que se movió DESDE esa foto física hasta hoy.
                    $stockInicial = $previousReading->physical_quantity;
                    $fromDate = $previousReading->date->format('Y-m-d');

                    // Ingresos al tanque en el período: compras + descargas de camión cisterna.
                    $ingresos = DB::table('purchase_details')
                        ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id')
                        ->where('purchase_details.tank_id', $tank->id)
                        ->where('purchases.date', '>', $fromDate)
                        ->where('purchases.date', '<=', $date)
                        ->sum('purchase_details.quantity');

                    $ingresos += DB::table('discharge_details')
                        ->join('discharges', 'discharge_details.discharge_id', '=', 'discharges.id')
                        ->where('discharge_details.tank_id', $tank->id)
                        ->where('discharges.date', '>', $fromDate)
                        ->where('discharges.date', '<=', $date)
                        ->sum('discharge_details.quantity');

                    // Ventas del período. sale_details no guarda tank_id, así que se asume
                    // un único tanque operativo (is_reserve = 0) por producto/sede: si una
                    // sede llega a tener más de uno, este cálculo se debe repartir entre ellos.
                    $ventas = DB::table('sale_details')
                        ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
                        ->where('sale_details.product_id', $tank->product_id)
                        ->where('sales.location_id', $tank->location_id)
                        ->where('sales.deleted', 0)
                        ->where('sales.date', '>', $fromDate)
                        ->where('sales.date', '<=', $date)
                        ->sum('sale_details.quantity');

                    $teorico = $stockInicial + $ingresos - $ventas;
                } else {
                    // Primera toma de este tanque: no hay foto física anterior que sirva de
                    // ancla. `stored_quantity` ya lleva aplicado todo el historial de compras/
                    // descargas/ventas hasta hoy (SaleController, PurchaseController, etc. lo
                    // actualizan en cada movimiento), así que el teórico de referencia es ese
                    // saldo contable actual — sumarle el histórico otra vez lo duplicaría.
                    $stockInicial = $tank->stored_quantity;
                    $teorico = $tank->stored_quantity;
                }
                $diferencia = $fisico - $teorico;

                $tolerancia = abs($teorico) * self::TOLERANCIA_PORCENTAJE;
                if (abs($diferencia) <= $tolerancia) {
                    $status = 'cuadra';
                } elseif ($diferencia < 0) {
                    $status = 'falta';
                } else {
                    $status = 'sobra';
                }

                TankReading::create([
                    'tank_id'            => $tank->id,
                    'user_id'            => $userId,
                    'date'               => $date,
                    'physical_quantity'  => $fisico,
                    'previous_stock'     => $stockInicial,
                    'theoretical_stock'  => $teorico,
                    'difference'         => $diferencia,
                    'status'             => $status,
                    'notes'              => $data['notas'] ?? null,
                    'deleted'            => 0,
                ]);

                if ($status !== 'cuadra') {
                    Waste::create([
                        'amount'      => abs($diferencia),
                        'type'        => 'serafin',
                        'product_id'  => $tank->product_id,
                        'location_id' => $tank->location_id,
                    ]);
                }

                // La lectura física se vuelve la nueva línea base del tanque.
                $tank->stored_quantity = $fisico;
                $tank->save();

                $savedCount++;
            }

            DB::commit();

            if ($savedCount > 0) {
                return redirect()->route('merma.create', ['location_id' => $validated['location_id']])
                    ->with('success', "Se registraron correctamente $savedCount tomas de inventario.");
            }

            return redirect()->back()->with('warning', 'No se ingresó ningún valor físico, no se guardaron datos.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    /**
     * Reporte "Control de Merma": historial de tomas de inventario con
     * clasificación falta/cuadra/sobra y nivel de stock actual del tanque.
     */
    public function index(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $location_id = $request->location_id;
        $product_id = $request->product_id;

        $currentUser = auth()->user();
        $isMaster = $currentUser->role->nombre === 'master';

        $query = TankReading::with(['tank.product', 'tank.location', 'user'])
            ->where('deleted', 0)
            ->when($start_date, fn($q) => $q->whereDate('date', '>=', $start_date))
            ->when($end_date, fn($q) => $q->whereDate('date', '<=', $end_date))
            ->when($location_id, function ($q) use ($location_id) {
                $q->whereHas('tank', fn($t) => $t->where('location_id', $location_id));
            })
            ->when($product_id, function ($q) use ($product_id) {
                $q->whereHas('tank', fn($t) => $t->where('product_id', $product_id));
            })
            ->when(!$isMaster && $currentUser->location_id, function ($q) use ($currentUser) {
                $q->whereHas('tank', fn($t) => $t->where('location_id', $currentUser->location_id));
            })
            ->orderBy('date', 'desc')
            ->orderByDesc('id');

        $readings = $query->paginate(20)->withQueryString();

        $lastSuppliers = [];
        foreach ($readings as $reading) {
            $tankId = $reading->tank_id;

            if (!array_key_exists($tankId, $lastSuppliers)) {
                $purchaseDetail = PurchaseDetail::where('tank_id', $tankId)
                    ->whereHas('purchase', fn($q) => $q->whereDate('date', '<=', $reading->date))
                    ->with('purchase.supplier')
                    ->latest('id')
                    ->first();

                $lastSuppliers[$tankId] = ($purchaseDetail && $purchaseDetail->purchase)
                    ? $purchaseDetail->purchase->supplier
                    : null;
            }

            $reading->last_supplier = $lastSuppliers[$tankId];
            $reading->stock_level = $reading->tank ? $this->nivelStock($reading->tank) : 'SIN DATO';
        }

        $locations = $isMaster
            ? Location::where('deleted', 0)->orderBy('name')->get()
            : Location::where('deleted', 0)->where('id', $currentUser->location_id)->orderBy('name')->get();
        $products = Product::where('deleted', 0)->orderBy('name')->get();

        return view('merma.index', compact('readings', 'locations', 'products', 'isMaster'));
    }

    /**
     * Exporta el reporte de merma a Excel respetando los mismos filtros del índice.
     */
    public function excel(Request $request)
    {
        $currentUser = auth()->user();
        $isMaster = $currentUser->role->nombre === 'master';
        $locationId = $isMaster ? $request->location_id : $currentUser->location_id;

        $filename = 'control_merma_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(
            new MermaExport(
                $request->start_date,
                $request->end_date,
                $locationId,
                $request->product_id
            ),
            $filename
        );
    }

    private function nivelStock(Tank $tank)
    {
        if (!$tank->capacity || $tank->capacity <= 0) {
            return 'SIN DATO';
        }

        $porcentaje = $tank->stored_quantity / $tank->capacity;

        if ($porcentaje <= self::STOCK_SIN) {
            return 'SIN STOCK';
        } elseif ($porcentaje <= self::STOCK_BAJO) {
            return 'STOCK BAJO';
        } elseif ($porcentaje <= self::STOCK_MEDIO) {
            return 'STOCK MEDIO';
        }

        return 'STOCK ALTO';
    }
}
