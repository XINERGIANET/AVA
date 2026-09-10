<?php

namespace App\Http\Controllers;

use App\Models\CashClose;
use App\Models\Isle;
use App\Models\Location;
use App\Models\LocationPrice;
use App\Models\Loan;
use App\Models\Measurement;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Pump;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CashCloseController extends Controller
{
    public function operations()
    {
        $user = Auth::user();
        $locationId = $user->location_id;
        $isles = Isle::where('deleted', 0)
            ->where('location_id', $locationId)
            ->when($user->role->nombre === 'worker' && $user->isle_id, fn ($query) => $query->where('id', $user->isle_id))
            ->orderBy('name')
            ->get();

        $openCashCloses = CashClose::with('user')
            ->whereIn('isle_id', $isles->pluck('id'))
            ->whereNull('real_cash_amount')
            ->orderByDesc('id')
            ->get()
            ->unique('isle_id')
            ->keyBy('isle_id');

        $openGeneralCash = CashClose::with('user', 'location')
            ->where('location_id', $locationId)
            ->where('cash_type', 'general')
            ->whereNull('real_cash_amount')
            ->latest('id')
            ->first();
        $cashMode = $openGeneralCash ? 'general' : ($openCashCloses->isNotEmpty() ? 'isle' : null);
        $generalLocation = Location::find($locationId);

        $isMaster = $user->role->nombre === 'master';
        $vaultLocations = $isMaster
            ? Location::where('deleted', 0)->orderBy('name')->get()
            : Location::where('deleted', 0)->where('id', $user->location_id)->get();

        return view('pettyCash.index', compact('isles', 'openCashCloses', 'openGeneralCash', 'cashMode', 'generalLocation', 'vaultLocations', 'isMaster'));
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = CashClose::with(['user', 'location', 'isle'])
            ->orderBy('date', 'desc');

        if (!in_array((int) $user->role_id, [1], true)) {
            $query->where('location_id', $user->location_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date . ' 00:00:00');
        }

        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date . ' 23:59:59');
        }

        $cashCloses = $query
            ->paginate(15)
            ->appends($request->query());

        $locations = in_array((int) $user->role_id, [1], true)
            ? Location::where('deleted', 0)->orderBy('name')->get()
            : Location::where('id', $user->location_id)->get();

        return view('cashClose.index', compact('cashCloses', 'locations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        if (Auth::user()->role->nombre === 'worker') {
            return response()->json(['status' => false, 'message' => 'No tiene permiso para abrir cajas.'], 403);
        }

        $request->validate([
            'initial_cash_amount' => 'required|numeric|min:0',
            'cash_type' => 'nullable|in:general,isle',
            'isle_id' => 'required_if:cash_type,isle|nullable|exists:isles,id',
        ]);

        $initialCashAmount = $request->input('initial_cash_amount');
        $cashType = $request->input('cash_type', 'isle');
        $isleId = $request->input('isle_id');
        $date = $request->input('date') ?: now()->format('Y-m-d');
        $user = Auth::user();

        DB::beginTransaction();

        try {
            if ($cashType === 'general') {
                $location = Location::lockForUpdate()->find($user->location_id);

                if (!$location) {
                    DB::rollBack();
                    return response()->json(['status' => false, 'message' => 'Sede no encontrada'], 404);
                }

                if ($this->hasOpenIsleCash((int) $location->id)) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'No se puede abrir caja general: primero cierre todas las cajas por isla abiertas.'
                    ], 422);
                }

                if ($this->hasOpenGeneralCash((int) $location->id)) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'No se puede abrir caja general: ya existe una caja general abierta.'
                    ], 422);
                }

                $cash_close = CashClose::create([
                    'initial_cash_amount' => $initialCashAmount,
                    'date' => $date,
                    'user_id' => $user->id,
                    'location_id' => $location->id,
                    'isle_id' => null,
                    'cash_type' => 'general',
                ]);

                $location->cash_amount = $initialCashAmount;
                $location->save();

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Caja general abierta correctamente.',
                    'orders' => $cash_close,
                    'new_balance' => $location->cash_amount
                ]);
            }

            $isle = Isle::lockForUpdate()->find($isleId);

            if (!$isle) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Isla no encontrada'
                ], 404);
            }

            if (!$this->canAccessIsle($isle)) {
                DB::rollBack();
                return response()->json(['status' => false, 'message' => 'No tiene acceso a esta isla.'], 403);
            }

            if ($this->hasOpenGeneralCash((int) $isle->location_id)) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'No se puede abrir caja por isla: primero cierre la caja general abierta.'
                ], 422);
            }

            $lastCashClose = CashClose::where('isle_id', $isleId)
                                ->where('cash_type', 'isle')
                                ->latest('id')
                                ->first();

            if ($lastCashClose && is_null($lastCashClose->real_cash_amount)) {
                DB::rollBack(); 
                
                return response()->json([
                    'status' => false,
                    'message' => 'No se puede abrir caja: Ya existe una sesión abierta en esta isla. Debe realizar el cierre primero.'
                ], 422); 
            }

            $cash_close = CashClose::create([
                'initial_cash_amount' => $initialCashAmount,
                'date' => $date,
                'user_id' => $user->id,
                'location_id' => $isle->location_id,
                'isle_id' => $isleId,
                'cash_type' => 'isle',
            ]);

            $isle->cash_amount = $initialCashAmount;
            $isle->save(); 

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Apertura de Caja registrada correctamente.',
                'orders' => $cash_close,
                'new_balance' => $isle->cash_amount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("Error en apertura de caja: " . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        try {
            if ($id === 'general') {
                return $this->showGeneralCash();
            }

            // 1. Validar Isla
            $isle = Isle::find($id);
            if (!$isle) {
                return response()->json(['status' => false, 'message' => 'Isla no encontrada'], 404);
            }
            if (!$this->canAccessIsle($isle)) {
                return response()->json(['status' => false, 'message' => 'No tiene acceso a esta isla.'], 403);
            }

            $cashClose = CashClose::where('isle_id', $id)
                ->where('cash_type', 'isle')
                ->whereNull('real_cash_amount') 
                ->latest('id') 
                ->first();

            if (!$cashClose) {
                return response()->json([
                    'status' => false,
                    'message' => 'No hay caja abierta para esta isla. Debe abrir caja primero.',
                    'calculated_cash_amount' => 0,
                    'initial_cash_amount' => 0,
                    'cash_sales' => 0,
                    'cash_expenses' => 0,
                    'total_adicional' => 0,
                    'cash_loans_granted' => 0,
                    'cash_loans_recovered' => 0,
                    'cash_close' => null
                ]); 
            }

            $startDate = $cashClose->created_at;
            $expensesToday = Transaction::where('isle_id', $id)
                ->where('created_at', '>=', $startDate) 
                ->where('type', 'scc') 
                ->sum('amount');

            // 4. CALCULAR VENTAS EN EFECTIVO DESDE LA APERTURA
            $cashSalesToday = Payment::where('payment_method_id', 1) // Efectivo
                ->where('deleted', 0)
                ->whereHas('sale', function ($querySale) use ($id, $startDate) {
                    $querySale->where('created_at', '>=', $startDate) // <--- CAMBIO CLAVE: Ya no es whereDate
                            ->where('deleted', 0)
                            ->whereHas('saleDetails.pump', function ($queryPump) use ($id) {
                                $queryPump->where('isle_id', $id);
                            });
                })
                ->sum('amount');

            // 5. CALCULAR ADICIONAL/VUELTO DESDE LA APERTURA
            $adicionalToday = Sale::where('created_at', '>=', $startDate) // <--- CAMBIO CLAVE
                ->where('deleted', 0)
                ->whereHas('saleDetails.pump', function ($queryPump) use ($id) {
                    $queryPump->where('isle_id', $id);
                })
                ->sum('adicional');

            $cashLoansGranted = Loan::where('isle_id', $id)
                ->where('deleted', false)
                ->where('created_at', '>=', $startDate)
                ->where(function ($queryLoan) {
                    $queryLoan->whereNull('send_method')
                        ->orWhere('send_method', 'like', '%Efectivo%');
                })
                ->sum('loan_amount');

            $cashLoansRecovered = Loan::where('isle_id', $id)
                ->where('deleted', false)
                ->where('created_at', '>=', $startDate)
                ->where('collection_method', 'like', '%Efectivo%')
                ->sum('recovered_amount');

            // 6. Saldo Actual de la Isla (Billetera acumulada en BD)
            $saldoActualIsla = floatval($isle->cash_amount);

            // 7. Reconciliacion de turno (contometro vs. registros en soles) - solo preview, no se persiste aqui
            $reconciliation = $this->computeShiftReconciliation($isle->location_id, [$isle->id], $cashClose, false);

            return response()->json([
                'status' => true,

                // Datos Totales
                'calculated_cash_amount' => $saldoActualIsla,
                'initial_cash_amount'    => floatval($cashClose->initial_cash_amount),

                // Datos Desglosados de ESTA sesión
                'cash_sales'      => floatval($cashSalesToday),
                'cash_expenses'   => floatval($expensesToday),
                'total_adicional' => floatval($adicionalToday),
                'cash_loans_granted' => floatval($cashLoansGranted),
                'cash_loans_recovered' => floatval($cashLoansRecovered),

                // Reconciliacion de turno (contometro vs. registros)
                'theoretical_sale_amount' => $reconciliation['theoretical_sale_amount'],
                'credits_amount'          => $reconciliation['credits_amount'],
                'transfers_amount'        => $reconciliation['transfers_amount'],
                'discounts_amount'        => $reconciliation['discounts_amount'],
                'meter_breakdown'         => $reconciliation['meter_breakdown'],

                // Objeto de cierre
                'cash_close' => $cashClose,
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        if (Auth::user()->role->nombre === 'worker') {
            return response()->json(['status' => false, 'message' => 'No tiene permiso para cerrar cajas.'], 403);
        }

        $request->validate([
            'real_cash_amount' => 'required|numeric',
            'final_cash_amount' => 'required|numeric'
        ]);

        try {
            $cashClose = CashClose::find($id);
            if (!$cashClose) {
                return response()->json(['status' => false, 'message' => 'Registro de cierre no encontrado'], 404);
            }
            if ($cashClose->cash_type === 'general') {
                if (!$this->canAccessLocation((int) $cashClose->location_id)) {
                    return response()->json(['status' => false, 'message' => 'No tiene acceso a esta caja.'], 403);
                }
            } elseif (!$cashClose->isle || !$this->canAccessIsle($cashClose->isle)) {
                return response()->json(['status' => false, 'message' => 'No tiene acceso a esta caja.'], 403);
            }

            $cashClose->final_cash_amount = $request->input('final_cash_amount');
            $cashClose->real_cash_amount = $request->input('real_cash_amount');
            if ($request->filled('date')) {
                $cashClose->date = $request->input('date');
            }

            if ($cashClose->cash_type === 'isle' && $cashClose->isle) {
                // Recalculado en el servidor: nunca se confia en montos que mande el cliente para la diferencia.
                $reconciliation = $this->computeShiftReconciliation($cashClose->isle->location_id, [$cashClose->isle_id], $cashClose, false);

                $cashClose->theoretical_sale_amount = $reconciliation['theoretical_sale_amount'];
                $cashClose->credits_amount          = $reconciliation['credits_amount'];
                $cashClose->transfers_amount        = $reconciliation['transfers_amount'];
                $cashClose->expenses_amount         = $reconciliation['expenses_amount'];
                $cashClose->discounts_amount        = $reconciliation['discounts_amount'];
                $cashClose->meter_breakdown         = $reconciliation['meter_breakdown'];

                $registeredSum = $reconciliation['credits_amount']
                    + $reconciliation['transfers_amount']
                    + $reconciliation['expenses_amount']
                    + $reconciliation['discounts_amount']
                    + (float) $cashClose->final_cash_amount;

                $cashClose->sale_variance_amount = round($registeredSum - $reconciliation['theoretical_sale_amount'], 2);
            } elseif ($cashClose->cash_type === 'general') {
                $isleIds = Isle::where('location_id', $cashClose->location_id)
                    ->where('deleted', 0)
                    ->pluck('id')
                    ->toArray();

                $reconciliation = $this->computeShiftReconciliation($cashClose->location_id, $isleIds, $cashClose, true);

                $cashClose->theoretical_sale_amount = $reconciliation['theoretical_sale_amount'];
                $cashClose->credits_amount          = $reconciliation['credits_amount'];
                $cashClose->transfers_amount        = $reconciliation['transfers_amount'];
                $cashClose->expenses_amount         = $reconciliation['expenses_amount'];
                $cashClose->discounts_amount        = $reconciliation['discounts_amount'];
                $cashClose->meter_breakdown         = $reconciliation['meter_breakdown'];

                $registeredSum = $reconciliation['credits_amount']
                    + $reconciliation['transfers_amount']
                    + $reconciliation['expenses_amount']
                    + $reconciliation['discounts_amount']
                    + (float) $cashClose->final_cash_amount;

                $cashClose->sale_variance_amount = round($registeredSum - $reconciliation['theoretical_sale_amount'], 2);
            }

            $cashClose->save();

            return response()->json(['status' => true, 'message' => 'Cierre de caja actualizado correctamente', 'cash_close' => $cashClose]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function checkStatus($isleId)
    {
        $isle = Isle::find($isleId);
        if (!$isle || !$this->canAccessIsle($isle)) {
            return response()->json(['status' => false, 'message' => 'No tiene acceso a esta isla.'], 403);
        }

        $generalCashOpen = CashClose::where('location_id', $isle->location_id)
            ->where('cash_type', 'general')
            ->whereNull('real_cash_amount')
            ->exists();

        if ($generalCashOpen) {
            return response()->json([
                'status' => true,
                'isOpen' => true,
                'cashMode' => 'general'
            ]);
        }

        $lastClose = CashClose::where('isle_id', $isleId)
                        ->where('cash_type', 'isle')
                        ->latest('id') // Equivalente a ORDER BY id DESC
                        ->first();

        $isOpen = $lastClose && is_null($lastClose->real_cash_amount);

        return response()->json([
            'status' => true,
            'isOpen' => $isOpen,
            'cashMode' => 'isle'
        ]);
    }

    private function canAccessIsle(Isle $isle): bool
    {
        $user = Auth::user();

        if ($user->role->nombre === 'master') {
            return true;
        }

        if ((int) $isle->location_id !== (int) $user->location_id) {
            return false;
        }

        return $user->role->nombre !== 'worker'
            || empty($user->isle_id)
            || (int) $user->isle_id === (int) $isle->id;
    }

    private function showGeneralCash()
    {
        $user = Auth::user();
        $location = Location::find($user->location_id);

        if (!$location || !$this->canAccessLocation((int) $location->id)) {
            return response()->json(['status' => false, 'message' => 'No tiene acceso a esta caja.'], 403);
        }

        $cashClose = CashClose::where('location_id', $location->id)
            ->where('cash_type', 'general')
            ->whereNull('real_cash_amount')
            ->latest('id')
            ->first();

        if (!$cashClose) {
            return response()->json([
                'status' => false,
                'message' => 'No hay caja general abierta. Debe abrir caja primero.',
                'calculated_cash_amount' => 0,
                'initial_cash_amount' => 0,
                'cash_sales' => 0,
                'cash_expenses' => 0,
                'total_adicional' => 0,
                'cash_loans_granted' => 0,
                'cash_loans_recovered' => 0,
                'cash_close' => null
            ]);
        }

        $startDate = $cashClose->created_at;
        $cashSales = Payment::where('payment_method_id', 1)
            ->where('deleted', 0)
            ->where('created_at', '>=', $startDate)
            ->whereHas('sale', function ($querySale) use ($location) {
                $querySale->where('location_id', $location->id)
                    ->where('deleted', 0);
            })
            ->sum('amount');

        $expenses = Transaction::where('location_id', $location->id)
            ->whereNull('isle_id')
            ->where('created_at', '>=', $startDate)
            ->where('type', 'scc')
            ->sum('amount');

        $isleIds = Isle::where('location_id', $location->id)->where('deleted', 0)->pluck('id')->toArray();
        $reconciliation = $this->computeShiftReconciliation($location->id, $isleIds, $cashClose, true);

        return response()->json([
            'status' => true,
            'calculated_cash_amount' => floatval($location->cash_amount ?? 0),
            'initial_cash_amount' => floatval($cashClose->initial_cash_amount),
            'cash_sales' => floatval($cashSales),
            'cash_expenses' => floatval($expenses),
            'total_adicional' => 0,
            'cash_loans_granted' => 0,
            'cash_loans_recovered' => 0,

            'theoretical_sale_amount' => $reconciliation['theoretical_sale_amount'],
            'credits_amount'          => $reconciliation['credits_amount'],
            'transfers_amount'        => $reconciliation['transfers_amount'],
            'discounts_amount'        => $reconciliation['discounts_amount'],
            'meter_breakdown'         => $reconciliation['meter_breakdown'],

            'cash_close' => $cashClose,
        ]);
    }

    private function hasOpenGeneralCash(int $locationId): bool
    {
        return CashClose::where('location_id', $locationId)
            ->where('cash_type', 'general')
            ->whereNull('real_cash_amount')
            ->exists();
    }

    private function hasOpenIsleCash(int $locationId): bool
    {
        return CashClose::where('location_id', $locationId)
            ->where('cash_type', 'isle')
            ->whereNull('real_cash_amount')
            ->exists();
    }

    private function canAccessLocation(int $locationId): bool
    {
        $user = Auth::user();

        return $user->role->nombre === 'master'
            || (int) $user->location_id === $locationId;
    }

    /**
     * Reconciliacion de cierre de turno acordada el 8-sep: venta teorica por
     * contometro (en soles) vs. suma de creditos + transferencias + gastos +
     * descuentos + efectivo entregado. La comparacion final es solo en soles,
     * nunca por producto/galonaje (ver plan de referencia).
     */
    /**
     * $isleIds: islas a incluir (una sola isla para cash_type='isle', todas las
     * islas de la sede para cash_type='general' -- ver $isGeneral).
     */
    private function computeShiftReconciliation(int $locationId, array $isleIds, CashClose $cashClose, bool $isGeneral): array
    {
        $startDate = $cashClose->created_at;
        $meterDate = $cashClose->date;

        $pumps = Pump::whereIn('isle_id', $isleIds)->where('deleted', 0)->get();
        $pumpsByProduct = $pumps->groupBy('product_id');

        $meterBreakdown = [];
        $theoreticalTotal = 0.0;

        foreach ($pumpsByProduct as $productId => $productPumps) {
            if (!$productId) {
                continue;
            }

            $gallons = (float) Measurement::whereIn('pump_id', $productPumps->pluck('id'))
                ->where('deleted', 0)
                ->whereDate('date', $meterDate)
                ->sum('amount_difference');

            $unitPrice = LocationPrice::where('location_id', $locationId)
                ->where('product_id', $productId)
                ->value('unit_price');

            $product = Product::find($productId);
            $unitPrice = $unitPrice ?? (float) optional($product)->unit_price;

            $subtotal = round($gallons * (float) $unitPrice, 2);
            $theoreticalTotal += $subtotal;

            $meterBreakdown[] = [
                'product_id'   => (int) $productId,
                'product_name' => optional($product)->name ?? 'Producto sin nombre',
                'gallons'      => round($gallons, 3),
                'unit_price'   => round((float) $unitPrice, 2),
                'subtotal'     => $subtotal,
            ];
        }

        // "Creditos otorgados" en produccion se registran como Sale.type_sale IN (1,2)
        // (Contrato/Credito, ver SaleController.php:441,795-808) -- el modelo Loan existe
        // pero no se usa (0 filas en datos reales), por eso no se usa aqui.
        // Ventas sin pump_id (camion/granel) quedan fuera a proposito: no pasaron por
        // ningun contometro, incluirlas generaria un "sobrante" falso.
        $creditsAmount = (float) Sale::whereIn('type_sale', [1, 2])
            ->where('deleted', 0)
            ->where('created_at', '>=', $startDate)
            ->whereHas('saleDetails.pump', function ($query) use ($isleIds) {
                $query->whereIn('isle_id', $isleIds);
            })
            ->sum('total');

        $transfersAmount = (float) Payment::where('payment_method_id', '!=', 1)
            ->where('deleted', 0)
            ->where('status', 'paid')
            ->whereHas('payment_method', function ($query) {
                $query->where('name', 'not like', '%Credito%')
                    ->where('name', 'not like', '%Crédito%');
            })
            ->whereHas('sale', function ($query) use ($isleIds, $startDate) {
                $query->where('created_at', '>=', $startDate)
                    ->where('deleted', 0)
                    ->whereHas('saleDetails.pump', function ($pumpQuery) use ($isleIds) {
                        $pumpQuery->whereIn('isle_id', $isleIds);
                    });
            })
            ->sum('amount');

        // Los gastos de Caja General se registran con isle_id NULL (ExpenseController.php:192);
        // los de caja por isla, con isle_id de esa isla (ExpenseController.php:233). Son dos
        // formas de registro distintas, no la misma consulta con un filtro mas amplio.
        if ($isGeneral) {
            $expensesAmount = (float) Transaction::where('location_id', $locationId)
                ->whereNull('isle_id')
                ->where('type', 'scc')
                ->where('created_at', '>=', $startDate)
                ->sum('amount');
        } else {
            $expensesAmount = (float) Transaction::whereIn('isle_id', $isleIds)
                ->where('type', 'scc')
                ->where('created_at', '>=', $startDate)
                ->sum('amount');
        }

        $discountDetails = SaleDetail::where('deleted', 0)
            ->whereHas('pump', function ($query) use ($isleIds) {
                $query->whereIn('isle_id', $isleIds);
            })
            ->whereHas('sale', function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate)->where('deleted', 0);
            })
            ->whereNotNull('discounted_price')
            ->get(['unit_price', 'discounted_price', 'quantity']);

        $discountsAmount = (float) $discountDetails->sum(function ($detail) {
            if ($detail->discounted_price === null || $detail->discounted_price === '') {
                return 0;
            }

            return max(0, ((float) $detail->unit_price - (float) $detail->discounted_price) * (float) $detail->quantity);
        });

        return [
            'theoretical_sale_amount' => round($theoreticalTotal, 2),
            'credits_amount'          => round($creditsAmount, 2),
            'transfers_amount'        => round($transfersAmount, 2),
            'expenses_amount'         => round($expensesAmount, 2),
            'discounts_amount'        => round($discountsAmount, 2),
            'meter_breakdown'         => $meterBreakdown,
        ];
    }
}
