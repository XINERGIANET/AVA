<?php

namespace App\Http\Controllers;

use App\Models\CashClose;
use App\Models\Isle;
use App\Models\Location;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Sale;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CashCloseController extends Controller
{

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

        $locations = in_array((int) $user->role_id, [1, 2], true)
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
        $request->validate([
            'initial_cash_amount' => 'required|numeric|min:0',
            'isle_id' => 'required|exists:isles,id',
        ]);

        $initialCashAmount = $request->input('initial_cash_amount');
        $isleId = $request->input('isle_id');
        $date = now()->format('Y-m-d');
        $user = Auth::user();

        // Iniciamos transacción
        DB::beginTransaction();

        try {
            // 1. Buscar y bloquear la isla
            $isle = Isle::lockForUpdate()->find($isleId);

            if (!$isle) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Isla no encontrada'
                ], 404);
            }

            // 2. Verificar duplicados
            $existingCashClose = CashClose::where('isle_id', $isleId)
                ->whereDate('date', $date)
                ->first();

            if ($existingCashClose) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Ya existe una apertura de caja para esta isla hoy'
                ], 400);
            }

            $cash_close = CashClose::create([
                'initial_cash_amount' => $initialCashAmount,
                'date' => $date,
                'user_id' => $user->id,
                'location_id' => $isle->location_id,
                'isle_id' => $isleId,
            ]);

            $isle->cash_amount = $initialCashAmount;
            $isle->save(); 

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Apertura de Caja registrada. Saldo de isla reiniciado al monto inicial.',
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
            $date = now()->format('Y-m-d');
            
            // 1. Validar Isla y obtener saldo real
            $isle = Isle::find($id);
            if (!$isle) {
                return response()->json(['status' => false, 'message' => 'Isla no encontrada'], 404);
            }

            // 2. Buscar Apertura del Día
            $cashClose = CashClose::where('isle_id', $id)
                ->whereDate('date', $date)
                ->first();

            // 3. CALCULAR EGRESOS DEL DÍA (Tabla transactions)
            // Esto se mantiene igual porque las transacciones se guardan directas con isle_id
            $expensesToday = Transaction::where('isle_id', $id)
                ->whereDate('date', $date)
                ->where('type', 'scc') 
                ->sum('amount');

            // 4. CALCULAR VENTAS EN EFECTIVO (Logica Relacional Surtidor -> Isla)
            // Buscamos pagos (Efectivo ID=1) donde la Venta asociada tenga detalles 
            // que pertenezcan a un Surtidor de esta Isla.
            $cashSalesToday = Payment::where('payment_method_id', 1) // ID 1 = Efectivo
                ->where('deleted', 0)
                ->whereHas('sale', function ($querySale) use ($id, $date) {
                    $querySale->whereDate('date', $date)
                              ->where('deleted', 0)
                              // Aquí está la magia: Filtramos si la venta tiene detalles en esta isla
                              ->whereHas('saleDetails.pump', function ($queryPump) use ($id) {
                                  $queryPump->where('isle_id', $id);
                              });
                })
                ->sum('amount');

            // 5. CALCULAR ADICIONAL/VUELTO (Logica Relacional Surtidor -> Isla)
            $adicionalToday = Sale::whereDate('date', $date)
                ->where('deleted', 0)
                ->whereHas('saleDetails.pump', function ($queryPump) use ($id) {
                    $queryPump->where('isle_id', $id);
                })
                ->sum('adicional');

            // 6. Saldo Final Real (Directo de la Billetera de la Isla en BD)
            $saldoActualIsla = floatval($isle->cash_amount);

            return response()->json([
                'status' => true,
                
                // Datos Totales
                'calculated_cash_amount' => $saldoActualIsla, 
                'initial_cash_amount'    => $cashClose ? floatval($cashClose->initial_cash_amount) : 0,
                
                // Datos Desglosados "Informativos"
                'cash_sales'      => floatval($cashSalesToday),
                'cash_expenses'   => floatval($expensesToday),
                'total_adicional' => floatval($adicionalToday),
                
                // Objeto de cierre (para el ID)
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
        $request->validate([
            'real_cash_amount' => 'required|numeric',
            'final_cash_amount' => 'required|numeric'
        ]);

        try {
            $cashClose = CashClose::find($id);
            if (!$cashClose) {
                return response()->json(['status' => false, 'message' => 'Registro de cierre no encontrado'], 404);
            }

            $cashClose->final_cash_amount = $request->input('final_cash_amount');
            $cashClose->real_cash_amount = $request->input('real_cash_amount');
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
}
