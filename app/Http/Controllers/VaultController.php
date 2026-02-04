<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tank;
use App\Models\Location;
use App\Models\User;
use App\Models\Isle;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VaultController extends Controller
{
    public function index(Request $request)
    {
        // 1. Agregamos 'isle' al with() para optimizar la consulta
        $transactionsQuery = Transaction::with(['user', 'location', 'isle'])
            ->whereIn('type', ['eb', 'sb'])
            ->when(auth()->user()->role->nombre != 'master' && auth()->user()->location_id, function ($query) {
                return $query->where('location_id', auth()->user()->location_id);
            });

        $transactions = $transactionsQuery
            ->when($request->from_date, function ($q, $from) {
                return $q->whereDate('date', '>=', $from);
            })
            ->when($request->to_date, function ($q, $to) {
                return $q->whereDate('date', '<=', $to);
            })
            ->when($request->location_id, function ($q, $locationId) {
                return $q->where('location_id', $locationId);
            })
            // 2. NUEVO: Agregamos el filtro por ID de Isla
            ->when($request->isle_id, function ($q, $isleId) {
                return $q->where('isle_id', $isleId);
            })
            ->when($request->user_id, function ($q, $userId) {
                return $q->where('user_id', $userId);
            })
            ->when($request->status, function ($q, $status) {
                return $q->where('status', $status);
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        // Cargar sedes (Locations)
        $locations = Location::where('deleted', 0)
            ->when(auth()->user()->role->nombre != 'master' && auth()->user()->location_id, function ($query) {
                return $query->where('id', auth()->user()->location_id);
            })
            ->get();

        // 3. NUEVO: Cargar listado de Islas para el <select> del filtro
        $isles = Isle::where('deleted', 0)
            // Si el usuario tiene una sede asignada, solo mostrar islas de esa sede
            ->when(auth()->user()->location_id, function($q) {
                $q->where('location_id', auth()->user()->location_id);
            })
            // Opcional: Si es Master y ya filtró por una sede en el request, mostrar solo islas de esa sede
            ->when(!auth()->user()->location_id && $request->location_id, function($q) use ($request) {
                $q->where('location_id', $request->location_id);
            })
            ->get();

        $users = User::where('deleted', 0)->get();

        return view('vault.index', compact('transactions', 'locations', 'users', 'isles'));
    }

    public function create()
    {
        $user = auth()->user();
        $transactions = Transaction::with(['user', 'location'])
            ->whereIn('type', ['eb', 'sb'])
            ->when($user->role->nombre != 'master' || $user->location_id, function ($query) use ($user) {
                return $query->where('location_id', $user->location_id);
            })->orderByDesc('id')
            ->paginate(10);

        $location = Location::find($user->location_id);
        $isles = Isle::where('location_id', $user->location_id)
                    ->where('deleted', 0) 
                    ->get();

        return view('vault.create', compact('transactions', 'location', 'isles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'isle_id'     => 'required|integer|exists:isles,id',
            'type'        => 'required|string|in:sb,eb',
            'description' => 'nullable|string|max:500',
            'amount'      => 'required|numeric|min:0.01',
            'date'        => 'required|date',
        ]);

        $user = auth()->user();
        
        DB::beginTransaction();
        try {
            // 1. Buscamos y Bloqueamos la Isla
            $isle = Isle::lockForUpdate()->find($validated['isle_id']);
            
            $amount = floatval($validated['amount']);
            $currentVault = floatval($isle->vault ?? 0); // Saldo en Bóveda

            // Datos base de la transacción
            $transactionData = [
                'user_id'     => $user->id,
                'location_id' => $isle->location_id,
                'isle_id'     => $isle->id,
                'type'        => $validated['type'],
                'amount'      => $amount,
                'date'        => $validated['date'],
                'description' => $validated['description'],
            ];

            // 2. Lógica según el tipo
            // 'sb' = Salida de Bóveda (El dinero sale de la caja fuerte y va a la caja chica)
            if ($validated['type'] === 'sb') {
                
                // Validar que la BÓVEDA tenga dinero
                if ($currentVault < $amount) {
                    DB::rollBack();
                    return back()->withInput()->withErrors([
                        'amount' => 'Saldo insuficiente en la bóveda de la isla. Disponible: S/ ' . number_format($currentVault, 2)
                    ]);
                }

                // A. RESTAR de la Bóveda (Vault)
                $isle->decrement('vault', $amount);

                // B. SUMAR a la Caja Chica (Cash Amount) - ¡LO QUE PEDISTE!
                $isle->increment('cash_amount', $amount);
                
                // Descripción automática si no la puso
                if (empty($transactionData['description'])) {
                    $transactionData['description'] = 'Reposición de Caja Chica desde Bóveda';
                }

                $transactionData['status'] = 1; // Aprobado automáticamente (es movimiento interno)

            } else { 
                // 'eb' = Entrada a Bóveda (Dinero externo entra a la caja fuerte)
                // Nota: Si esto viene de un cierre de caja, usa la otra función storeVaultFromCashClose.
                // Esta opción 'eb' aquí suele ser para aportes de capital externos.
                
                $isWorkerMarkingDeposit = ($user->role->nombre === 'worker');

                if ($isWorkerMarkingDeposit) {
                    $transactionData['status'] = 0; // Pendiente
                } else {
                    $isle->increment('vault', $amount);
                    $transactionData['status'] = 1; // Aprobado
                }
            }

            // 3. Crear Transacción
            Transaction::create($transactionData);

            DB::commit();

            return redirect()->route('vault.create')->with('success', 'Movimiento registrado y saldos actualizados correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al registrar: ' . $e->getMessage()]);
        }
    }

    public function storeVaultFromCashClose(Request $request)
    {
        // 1. Agregar validación del isle_id
        $request->validate([
            'amount'  => 'required|numeric|min:0.01',
            'isle_id' => 'required|exists:isles,id',
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $amount = floatval($request->input('amount'));
            $isleId = $request->input('isle_id');

            // 2. Buscar la ISLA y bloquearla para transacciones seguras
            // Usamos la tabla 'isles' como fuente de la verdad
            $isle = Isle::lockForUpdate()->find($isleId);

            if (!$isle) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Isla no encontrada.'
                ], 404);
            }

            $currentCash = floatval($isle->cash_amount ?? 0);
            $isle->decrement('cash_amount', $amount);
            $location = Location::find($isle->location_id);

            if (auth()->user()->role->nombre === 'worker') {
                $expense = Transaction::create([
                    'user_id' => $user->id,
                    'location_id' => $location->id,
                    'isle_id' => $isle->id, // Guardamos de qué isla salió
                    'type' => 'eb', // Enviar a Bóveda
                    'description' => 'Transferencia a bóveda desde cierre de caja (Isla: ' . $isle->name . ')',
                    'amount' => $amount,
                    'date' => now(),
                    'status' => 0, // 0 = Pendiente
                ]);

                $message = 'Dinero descontado de la isla. Envío a bóveda pendiente de aprobación.';

            } else {
                // Si es Admin/Master, se aprueba automáticamente y entra a la Bóveda Central
                if ($location) {
                    $location->increment('vault', $amount);
                }

                $expense = Transaction::create([
                    'user_id' => $user->id,
                    'location_id' => $location->id,
                    'isle_id' => $isle->id,
                    'type' => 'eb',
                    'description' => 'Transferencia a bóveda desde cierre de caja (Isla: ' . $isle->name . ')',
                    'amount' => $amount,
                    'date' => now(),
                    'status' => 1, // 1 = Aprobado/Ingresado
                ]);

                $message = 'Dinero enviado a la bóveda correctamente.';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'expense' => $expense,
                'new_isle_balance' => $isle->cash_amount // Opcional: devolver saldo actualizado
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error creando movimiento a bóveda: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'amount' => $request->input('amount'),
                'isle_id' => $request->input('isle_id'),
                'exception' => $e
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear el movimiento: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approve($id)
    {
        $transaction = Transaction::findOrFail($id);
        $user = Auth::user();
        if ($transaction->status != 0) {
            return redirect()->back()->with('error', 'El movimiento no está pendiente de aprobación.');
        }

        if ($transaction->type != 'eb') {
            return redirect()->back()->with('error', 'El movimiento no es una entrada.');
        }

        if ($user->location_id != $transaction->location_id) {
            return redirect()->back()->with('error', 'Ubicación incorrecta.');
        }
        $rol = $user->role->nombre;
        if ($rol !== 'admin' && $rol !== 'master') {
            return redirect()->back()->with('error', 'Solo los administradores pueden aprobar retiros.');
        }

        DB::beginTransaction();
        try {
            $transaction->update(['status' => 1]);
            $location = $transaction->location;
            $location->increment('vault', $transaction->amount);
            DB::commit();

            return redirect()->route('vault.create')
                ->with('success', 'Retiro aprobado y bóveda actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al aprobar: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        // $tanque = Tank::findOrFail($id);
        // return response()->json($tanque);
    }

    public function edit($id)
    {
        // $tanque = Tank::findOrFail($id);
        // $sedes = Location::where('deleted', 0)->get();
        // $products = Product::where('deleted', 0)->get();

        // return view('tanques.edit', compact('tanque', 'sedes', 'products'));
    }

    public function update(Request $request, $id)
    {
        // $request->validate([
        //     'location_id' => 'required|integer|exists:locations,id',
        //     'name'        => 'required|string|max:255',
        //     'capacity'    => 'required|numeric|min:0',
        //     'product_id'  => 'required|integer|exists:products,id', 
        // ]);

        // $tanque = Tank::findOrFail($id);

        // $duplicado = Tank::where('id', '!=', $id)
        //     ->where('location_id', $request->location_id)
        //     ->where('name', $request->name)
        //     ->where('capacity', $request->capacity)
        //     ->where('product_id', $request->product_id) 
        //     ->where('deleted', 0)
        //     ->exists();

        // if ($duplicado) {
        //     return back()->withInput()->withErrors(['duplicate' => 'Ya existe otro tanque con los mismos datos.']);
        // }

        // $conflicto = Tank::where('id', '!=', $id)
        //     ->where('location_id', $request->location_id)
        //     ->where('name', $request->name)
        //     ->where('product_id', '!=', $request->product_id) 
        //     ->where('deleted', 0)
        //     ->exists();

        // if ($conflicto) {
        //     return back()->withInput()->withErrors(['producto_conflict' => 'Este nombre de tanque ya está asociado a otro producto.']);
        // }

        // $tanque->update($request->only(['location_id','name','capacity','product_id']));

        // return redirect()->route('tanques.index')->with('success', 'Tanque actualizado exitosamente.');
    }



    public function destroy($id)
    {
        // $tanque = Tank::findOrFail($id);
        // $tanque->update(['deleted' => 1]); // Cambiar estado a 1 (eliminado)
        // return redirect()->route('tanques.index')
        //     ->with('success', 'Tanque eliminado correctamente.');
    }
}
