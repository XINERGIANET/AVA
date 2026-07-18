<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tank;
use App\Models\Location;
use App\Models\User;
use App\Models\Isle;
use App\Models\Product;
use App\Models\VaultDestination;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class VaultController extends Controller
{
    public function index(Request $request)
    {
        // 1. Agregamos 'isle' al with() para optimizar la consulta
        $transactionsQuery = Transaction::with(['user', 'location', 'isle', 'vault_destination'])
            ->whereIn('type', ['eb', 'sb', 'eg'])
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

        // Tarjetas de resumen: saldo acumulado en bóveda de la sede actualmente seleccionada
        // (la del selector superior, auth()->user()->location_id), sumando el vault de sus islas.
        $currentLocationId = auth()->user()->location_id;
        $currentLocation = $currentLocationId ? Location::find($currentLocationId) : null;
        $vaultAccumulated = $currentLocationId
            ? Isle::where('location_id', $currentLocationId)->where('deleted', 0)->sum('vault')
            : 0;

        // Destinos de egreso: catálogo editable + cuánto se ha enviado a cada uno
        // (solo egresos aprobados/efectivos). Es una suma en vivo sobre el
        // historial de movimientos, no un saldo/sub-libro que se mantenga aparte.
        $vaultDestinations = VaultDestination::where('deleted', 0)->orderBy('name')->get();
        $egresosPorDestino = Transaction::where('type', 'eg')
            ->where('status', 1)
            ->when($currentLocationId, function ($q) use ($currentLocationId) {
                $q->where('location_id', $currentLocationId);
            })
            ->selectRaw('vault_destination_id, SUM(amount) as total')
            ->groupBy('vault_destination_id')
            ->pluck('total', 'vault_destination_id');

        // Total de bóveda por sede (suma en vivo de sus islas), para "Generar
        // Movimiento": la bóveda es un solo pozo por sede, no por isla.
        $locationVaultTotals = Isle::where('deleted', 0)
            ->selectRaw('location_id, SUM(vault) as total')
            ->groupBy('location_id')
            ->pluck('total', 'location_id');

        return view('vault.index', compact(
            'transactions',
            'locations',
            'users',
            'isles',
            'currentLocation',
            'vaultAccumulated',
            'vaultDestinations',
            'egresosPorDestino',
            'locationVaultTotals'
        ));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        if ($user->role->nombre !== 'master') {
            abort(403, 'Solo el usuario master puede registrar movimientos desde esta pantalla.');
        }
        $transactionsBaseQuery = Transaction::with(['user', 'location', 'isle', 'vault_destination'])
            ->whereIn('type', ['eb', 'sb', 'eg'])
            ->when($user->role->nombre != 'master' || $user->location_id, function ($query) use ($user) {
                return $query->where('location_id', $user->location_id);
            });

        $latestTransactionDate = (clone $transactionsBaseQuery)
            ->orderByDesc('date')
            ->value('date');

        $defaultDate = $latestTransactionDate
            ? Carbon::parse($latestTransactionDate)->toDateString()
            : now()->toDateString();

        $fromDate = $request->input('from_date') ?: $defaultDate;
        $toDate = $request->input('to_date') ?: $defaultDate;

        $transactions = (clone $transactionsBaseQuery)
            ->whereDate('date', '>=', $fromDate)
            ->whereDate('date', '<=', $toDate)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(10);
        $transactions->appends($request->query());

        $filteredTotal = (clone $transactionsBaseQuery)
            ->whereDate('date', '>=', $fromDate)
            ->whereDate('date', '<=', $toDate)
            ->sum('amount');

        $location = Location::find($user->location_id);
        $isles = Isle::where('location_id', $user->location_id)
                    ->where('deleted', 0) 
                    ->get();

        return view('vault.create', compact(
            'transactions',
            'location',
            'isles',
            'filteredTotal',
            'fromDate',
            'toDate',
            'defaultDate'
        ));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user->role->nombre !== 'master') {
            abort(403, 'Solo el usuario master puede registrar movimientos desde esta pantalla.');
        }

        $validated = $request->validate([
            'isle_id'     => 'required|integer|exists:isles,id',
            'type'        => 'required|string|in:sb,eb',
            'description' => 'nullable|string|max:500',
            'amount'      => 'required|numeric|min:0.01',
            'date'        => 'required|date',
        ]);

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

    /**
     * "Generar Movimiento": Ingreso (aporte externo, ej. el dueño mete plata) o
     * Egreso (a un destino del catálogo: Banco, Dueño, Otra Bóveda, etc.).
     *
     * La bóveda es un solo pozo por SEDE, no por isla: el tope/saldo disponible
     * es la suma en vivo de Isle.vault de esa sede (igual que la tarjeta
     * "Acumulado en bóveda"), sin mantener un campo aparte que se pueda
     * desincronizar. Si una sola isla no alcanza para el egreso, se descuenta
     * de la que tenga más saldo primero y se continúa con la siguiente hasta
     * cubrir el monto, dejando una fila de transacción por cada isla tocada
     * para que el historial muestre transparentemente de dónde salió cada sol.
     *
     * Solo registra el movimiento, no un sub-libro acumulado por destino (ese
     * acumulado se calcula en vivo sumando estos movimientos).
     */
    public function storeMovement(Request $request)
    {
        $validated = $request->validate([
            'location_id'           => 'required|integer|exists:locations,id',
            'movement_type'         => 'required|string|in:ingreso,egreso',
            'vault_destination_id'  => 'required_if:movement_type,egreso|nullable|integer|exists:vault_destinations,id',
            'description'           => 'nullable|string|max:500',
            'amount'                => 'required|numeric|min:0.01',
            'date'                  => 'required|date',
        ]);

        $user = auth()->user();
        $isWorker = ($user->role->nombre === 'worker');
        $locationId = $validated['location_id'];
        $amount = floatval($validated['amount']);

        DB::beginTransaction();
        try {
            if ($validated['movement_type'] === 'egreso') {
                $islas = Isle::where('location_id', $locationId)
                    ->where('deleted', 0)
                    ->orderByDesc('vault')
                    ->lockForUpdate()
                    ->get();

                $totalVault = $islas->sum('vault');

                if ($totalVault < $amount) {
                    DB::rollBack();
                    return back()->withInput()->withErrors([
                        'amount' => 'Saldo insuficiente en la bóveda de la sede. Disponible: S/ ' . number_format($totalVault, 2)
                    ]);
                }

                $destination = VaultDestination::find($validated['vault_destination_id']);
                $status = $isWorker ? 0 : 1;
                $remaining = $amount;
                $userDescription = $validated['description'];

                foreach ($islas as $isla) {
                    if ($remaining <= 0) {
                        break;
                    }

                    $take = min(floatval($isla->vault), $remaining);
                    if ($take <= 0) {
                        continue;
                    }

                    if (!$isWorker) {
                        $isla->decrement('vault', $take);
                    }

                    $autoDescription = 'Egreso de bóveda hacia ' . $destination->name . ' (S/ ' . number_format($take, 2) . ' desde ' . $isla->name . ')';

                    Transaction::create([
                        'user_id'              => $user->id,
                        'location_id'          => $locationId,
                        'isle_id'              => $isla->id,
                        'type'                 => 'eg',
                        'vault_destination_id' => $destination->id,
                        'amount'               => $take,
                        'date'                 => $validated['date'],
                        'description'          => $userDescription ? $userDescription . ' — ' . $autoDescription : $autoDescription,
                        'status'               => $status,
                    ]);

                    $remaining -= $take;
                }
            } else {
                // Ingreso manual (aporte externo, ej. el dueño mete plata): se
                // acredita a la primera isla activa de la sede, igual criterio
                // que ya usa la transferencia de cierre de caja entre sedes.
                $isla = Isle::where('location_id', $locationId)
                    ->where('deleted', 0)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->first();

                if (!$isla) {
                    DB::rollBack();
                    return back()->withInput()->withErrors(['error' => 'La sede seleccionada no tiene islas activas.']);
                }

                $status = $isWorker ? 0 : 1;

                if (!$isWorker) {
                    $isla->increment('vault', $amount);
                }

                $autoDescription = 'Ingreso manual a bóveda (acreditado a ' . $isla->name . ')';

                Transaction::create([
                    'user_id'     => $user->id,
                    'location_id' => $locationId,
                    'isle_id'     => $isla->id,
                    'type'        => 'eb',
                    'amount'      => $amount,
                    'date'        => $validated['date'],
                    'description' => $validated['description'] ? $validated['description'] . ' — ' . $autoDescription : $autoDescription,
                    'status'      => $status,
                ]);
            }

            DB::commit();

            return redirect()->route('vault.index')->with('success', 'Movimiento registrado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al registrar el movimiento: ' . $e->getMessage()]);
        }
    }

    public function storeVaultFromCashClose(Request $request)
    {
        // 1. Agregar validación del isle_id
        $request->validate([
            'amount'  => 'required|numeric|min:0.01',
            'isle_id' => 'required|exists:isles,id',
            'location_id' => 'nullable|exists:locations,id',
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

            $isMaster = $user->role->nombre === 'master';
            if (!$isMaster && (int) $isle->location_id !== (int) $user->location_id) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permisos para operar la caja de esta isla.'
                ], 403);
            }

            $currentCash = floatval($isle->cash_amount ?? 0);
            if ($currentCash < $amount) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Saldo insuficiente en la caja chica. Disponible: S/ ' . number_format($currentCash, 2)
                ], 422);
            }

            // Un admin (no master) solo puede enviar a la bóveda de su propia sede,
            // sin importar qué location_id llegue en el request.
            $destinationLocationId = $isMaster
                ? ($request->input('location_id') ?: $isle->location_id)
                : $user->location_id;
            $destinationLocation = Location::lockForUpdate()->find($destinationLocationId);

            if (!$destinationLocation) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Boveda destino no encontrada.'
                ], 404);
            }

            $isle->decrement('cash_amount', $amount);
            $sourceLocation = Location::find($isle->location_id);
            $description = 'Transferencia a boveda desde cierre de caja (Isla: ' . $isle->name . ', Sede origen: ' . ($sourceLocation->name ?? 'N/A') . ') hacia boveda de ' . $destinationLocation->name;

            if (auth()->user()->role->nombre === 'worker') {
                $expense = Transaction::create([
                    'user_id' => $user->id,
                    'location_id' => $destinationLocation->id,
                    'isle_id' => $isle->id, // Guardamos de qué isla salió
                    'type' => 'eb', // Enviar a Bóveda
                    'description' => $description,
                    'amount' => $amount,
                    'date' => now(),
                    'status' => 0, // 0 = Pendiente
                ]);

                $message = 'Dinero descontado de la isla. Envío a bóveda pendiente de aprobación.';

            } else {
                // Si es Admin/Master, se aprueba automáticamente y entra a la Bóveda de la isla.
                // El saldo de bóveda se lleva por isla (Isle.vault), no por sede, para que
                // coincida con lo que ya muestra la pantalla de Registrar Bóveda.
                if ((int) $destinationLocation->id === (int) $isle->location_id) {
                    $isle->increment('vault', $amount);
                } else {
                    // Transferencia a otra sede: se acredita a la primera isla activa de esa sede.
                    $destinationIsle = Isle::where('location_id', $destinationLocation->id)
                        ->where('deleted', 0)
                        ->lockForUpdate()
                        ->first();

                    if ($destinationIsle) {
                        $destinationIsle->increment('vault', $amount);
                    }
                }

                $expense = Transaction::create([
                    'user_id' => $user->id,
                    'location_id' => $destinationLocation->id,
                    'isle_id' => $isle->id,
                    'type' => 'eb',
                    'description' => $description,
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

        if (!in_array($transaction->type, ['eb', 'eg'], true)) {
            return redirect()->back()->with('error', 'Este movimiento no requiere aprobación.');
        }

        if ($user->location_id != $transaction->location_id) {
            return redirect()->back()->with('error', 'Ubicación incorrecta.');
        }
        $rol = $user->role->nombre;
        if ($rol !== 'admin' && $rol !== 'master') {
            return redirect()->back()->with('error', 'Solo los administradores pueden aprobar movimientos.');
        }

        DB::beginTransaction();
        try {
            // El saldo de bóveda se lleva por isla (Isle.vault), no por sede.
            $isle = $transaction->isle_id ? Isle::lockForUpdate()->find($transaction->isle_id) : null;
            if (!$isle) {
                DB::rollBack();
                return redirect()->back()->with('error', 'No se encontró la isla asociada al movimiento.');
            }

            if ($transaction->type === 'eg') {
                $currentVault = floatval($isle->vault ?? 0);
                if ($currentVault < $transaction->amount) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Saldo insuficiente en la bóveda de la isla para aprobar este egreso.');
                }
                $isle->decrement('vault', $transaction->amount);
            } else {
                $isle->increment('vault', $transaction->amount);
            }

            $transaction->update(['status' => 1]);
            DB::commit();

            return redirect()->back()->with('success', 'Movimiento aprobado y bóveda actualizada exitosamente.');
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
        $user = Auth::user();
        $transaction = Transaction::findOrFail($id);
        $role = $user->role->nombre ?? '';

        if ($role !== 'admin' && $role !== 'master') {
            return redirect()->back()->with('error', 'Solo administradores o master pueden eliminar movimientos.');
        }

        if ($role !== 'master' && $user->location_id != $transaction->location_id) {
            return redirect()->back()->with('error', 'No puedes eliminar movimientos de otra sede.');
        }

        if (!in_array($transaction->type, ['eb', 'sb', 'eg'], true)) {
            return redirect()->back()->with('error', 'Solo se pueden eliminar movimientos de bóveda.');
        }

        DB::beginTransaction();
        try {
            $transaction = Transaction::lockForUpdate()->findOrFail($id);
            $isle = $transaction->isle_id ? Isle::lockForUpdate()->find($transaction->isle_id) : null;
            $location = $transaction->location_id ? Location::lockForUpdate()->find($transaction->location_id) : null;

            $amount = floatval($transaction->amount);
            $isApproved = intval($transaction->status) === 1;
            $description = (string) ($transaction->description ?? '');
            $isCashCloseTransfer = $transaction->type === 'eb'
                && stripos($description, 'desde cierre de caja') !== false;

            if ($transaction->type === 'sb') {
                if (!$isle) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'No se encontró la isla asociada al movimiento.');
                }

                $currentCash = floatval($isle->cash_amount ?? 0);
                if ($currentCash < $amount) {
                    DB::rollBack();
                    return redirect()->back()->with(
                        'error',
                        'No se puede eliminar: la caja chica de la isla no tiene saldo suficiente para revertir la salida.'
                    );
                }

                $isle->increment('vault', $amount);
                $isle->decrement('cash_amount', $amount);
            }

            if ($transaction->type === 'eb') {
                if ($isCashCloseTransfer) {
                    if (!$isle) {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'No se encontró la isla asociada al movimiento.');
                    }

                    if ($isApproved) {
                        if (!$location) {
                            DB::rollBack();
                            return redirect()->back()->with('error', 'No se encontró la sede asociada al movimiento.');
                        }

                        $currentLocationVault = floatval($location->vault ?? 0);
                        if ($currentLocationVault < $amount) {
                            DB::rollBack();
                            return redirect()->back()->with(
                                'error',
                                'No se puede eliminar: la bóveda de la sede no tiene saldo suficiente para revertir la transferencia.'
                            );
                        }

                        $location->decrement('vault', $amount);
                    }

                    $isle->increment('cash_amount', $amount);
                } else {
                    if ($isApproved) {
                        if (!$isle) {
                            DB::rollBack();
                            return redirect()->back()->with('error', 'No se encontró la isla asociada al movimiento.');
                        }

                        $currentIsleVault = floatval($isle->vault ?? 0);
                        if ($currentIsleVault < $amount) {
                            DB::rollBack();
                            return redirect()->back()->with(
                                'error',
                                'No se puede eliminar: la bóveda de la isla no tiene saldo suficiente para revertir la entrada.'
                            );
                        }

                        $isle->decrement('vault', $amount);
                    }
                }
            }

            if ($transaction->type === 'eg') {
                if ($isApproved) {
                    if (!$isle) {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'No se encontró la isla asociada al movimiento.');
                    }

                    // El egreso ya aprobado había restado de la bóveda de la isla:
                    // al eliminarlo, se le devuelve ese monto.
                    $isle->increment('vault', $amount);
                }
                // Si estaba pendiente, no se había descontado nada aún; no hay saldo que revertir.
            }

            $transaction->delete();

            DB::commit();

            return redirect()->route('vault.create')
                ->with('success', 'Movimiento eliminado y saldos revertidos correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al eliminar el movimiento: ' . $e->getMessage());
        }
    }
}
