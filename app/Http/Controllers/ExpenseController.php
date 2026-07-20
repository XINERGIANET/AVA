<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\CashClose;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Isle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExpensesExport;

class ExpenseController extends Controller
{
    /**
     * Solo master ve/gestiona egresos de todas las sedes. Admin queda acotado
     * a su propia sede (antes se trataba igual que master, lo cual dejaba ver
     * y exportar egresos de cualquier sede).
     */
    private function scopeExpensesQuery($query, $currentUser, bool $isMaster)
    {
        if ($isMaster) {
            return $query;
        }

        if ($currentUser->isle_id) {
            $query->where('isle_id', $currentUser->isle_id);
        } elseif ($currentUser->location_id) {
            $query->where('location_id', $currentUser->location_id);
        }

        return $query;
    }

    private function userCanAccessExpense($currentUser, bool $isMaster, Transaction $expense): bool
    {
        if ($isMaster) {
            return true;
        }

        if ($currentUser->isle_id) {
            return (int) $expense->isle_id === (int) $currentUser->isle_id;
        }

        if ($currentUser->location_id) {
            return (int) $expense->location_id === (int) $currentUser->location_id;
        }

        return false;
    }

    public function index(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $location_id = $request->location_id;

        $currentUser = Auth::user();
        $isMaster = $currentUser->role->nombre === 'master';

        // Construir la consulta base
        $query = Transaction::with('location', 'isle')
            ->where('type', 'scc');

        // Aplicar filtros de permisos según el rol del usuario
        $this->scopeExpensesQuery($query, $currentUser, $isMaster);

        // Aplicar filtros de fecha
        if ($start_date) {
            $query->whereDate('date', '>=', $start_date);
        }

        if ($end_date) {
            $query->whereDate('date', '<=', $end_date);
        }

        // Aplicar filtro de sede (si se selecciona en el filtro)
        if ($location_id) {
            $query->where('location_id', $location_id);
        }

        // Ordenar y paginar
        $expenses = $query->orderBy('date', 'desc')->paginate(10)->withQueryString();

        // Calcular total con los mismos filtros aplicados
        $totalQuery = Transaction::where('type', 'scc');

        // Aplicar los mismos filtros de permisos para el total
        $this->scopeExpensesQuery($totalQuery, $currentUser, $isMaster);

        // Aplicar los mismos filtros de fecha para el total
        if ($start_date) {
            $totalQuery->whereDate('date', '>=', $start_date);
        }

        if ($end_date) {
            $totalQuery->whereDate('date', '<=', $end_date);
        }

        if ($location_id) {
            $totalQuery->where('location_id', $location_id);
        }

        $totalExpenses = $totalQuery->sum('amount');

        // Cargar locations según permisos
        $locations = Location::where('deleted', 0)
            ->when(!$isMaster && $currentUser->location_id, function ($q) use ($currentUser) {
                $q->where('id', $currentUser->location_id);
            })
            ->orderBy('name')
            ->get();

        // Cargar islas según permisos para el formulario de editar
        $isles = Isle::where('deleted', 0)
            ->when(!$isMaster, function ($q) use ($currentUser) {
                if ($currentUser->isle_id) {
                    $q->where('id', $currentUser->isle_id);
                } elseif ($currentUser->location_id) {
                    $q->where('location_id', $currentUser->location_id);
                }
            })
            ->orderBy('name')
            ->get();

        return view('expenses.index', compact('expenses', 'locations', 'totalExpenses', 'isles'));
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
        // 1. Validar entrada
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'payment_method' => 'nullable|string|max:255',
            'observation' => 'nullable|string',
            'cash_type' => 'nullable|in:general,isle',
            'isle_id' => 'required_if:cash_type,isle|nullable|exists:isles,id',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $isMaster = $user->role->nombre === 'master';
            $cashType = $request->input('cash_type', 'isle');

            if ($cashType === 'general') {
                $location = Location::lockForUpdate()->find($user->location_id);

                if (!$location) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Sede no encontrada.'], 404);
                }

                $generalCashOpen = CashClose::where('location_id', $location->id)
                    ->where('cash_type', 'general')
                    ->whereNull('real_cash_amount')
                    ->exists();

                if (!$generalCashOpen) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'No hay caja general abierta.'], 422);
                }

                $expense = Transaction::create([
                    'user_id' => $user->id,
                    'location_id' => $location->id,
                    'isle_id' => null,
                    'type' => 'scc',
                    'description' => $request->input('description'),
                    'category' => $request->input('category'),
                    'payment_method' => $request->input('payment_method') ?: 'Efectivo',
                    'observation' => $request->input('observation'),
                    'amount' => $request->input('amount'),
                    'date' => now(),
                ]);

                $location->decrement('cash_amount', $request->input('amount'));

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Egreso registrado exitosamente.',
                    'expense' => $expense
                ]);
            }

            $isle = Isle::lockForUpdate()->find($request->input('isle_id'));

            if (!$isMaster) {
                $allowed = $user->isle_id
                    ? (int) $isle->id === (int) $user->isle_id
                    : (int) $isle->location_id === (int) $user->location_id;
                if (!$allowed) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'No tienes permiso para registrar un egreso en esa isla.'
                    ], 403);
                }
            }

            $expense = Transaction::create([
                'user_id' => $user->id,
                'location_id' => $isle->location_id,
                'isle_id' => $isle->id,
                'type' => 'scc',
                'description' => $request->input('description'),
                'category' => $request->input('category'),
                'payment_method' => $request->input('payment_method') ?: 'Efectivo',
                'observation' => $request->input('observation'),
                'amount' => $request->input('amount'),
                'date' => now(),
            ]);

            $isle->decrement('cash_amount', $request->input('amount'));
            $isle->refresh();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Egreso registrado exitosamente. Nuevo saldo: ' . $isle->cash_amount,
                'expense' => $expense
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creando egreso: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'amount' => $request->input('amount'),
                'exception' => $e
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear el egreso: ' . $e->getMessage()
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $expense = Transaction::with('location', 'isle')->find($id);

        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => 'Egreso no encontrado'
            ], 404);
        }

        $currentUser = Auth::user();
        $isMaster = $currentUser->role->nombre === 'master';
        if (!$this->userCanAccessExpense($currentUser, $isMaster, $expense)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para ver este egreso.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'expense' => $expense
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'payment_method' => 'nullable|string|max:255',
            'observation' => 'nullable|string',
            'isle_id' => 'required|exists:isles,id',
            'date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $expense = Transaction::find($id);

            if (!$expense) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Egreso no encontrado'
                ], 404);
            }

            $currentUser = Auth::user();
            $isMaster = $currentUser->role->nombre === 'master';

            if (!$this->userCanAccessExpense($currentUser, $isMaster, $expense)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para editar este egreso.'
                ], 403);
            }

            $newIsleForCheck = Isle::find($request->isle_id);
            if (!$isMaster && $newIsleForCheck) {
                $allowed = $currentUser->isle_id
                    ? (int) $newIsleForCheck->id === (int) $currentUser->isle_id
                    : (int) $newIsleForCheck->location_id === (int) $currentUser->location_id;
                if (!$allowed) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'No tienes permiso para mover este egreso a esa isla.'
                    ], 403);
                }
            }

            $oldIsle = Isle::find($expense->isle_id);
            $newIsle = Isle::lockForUpdate()->find($request->isle_id);
            $amountDifference = $request->amount - $expense->amount;

            // Si cambió la isla o el monto, ajustar los saldos
            if ($expense->isle_id != $request->isle_id) {
                // Devolver el monto a la isla anterior
                $oldIsle->increment('cash_amount', $expense->amount);
                
                // Verificar que la nueva isla tenga suficiente saldo
                if ($newIsle->cash_amount < $request->amount) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Saldo insuficiente en la isla seleccionada. Disponible: ' . number_format($newIsle->cash_amount, 2)
                    ], 400);
                }
                
                // Restar el nuevo monto de la nueva isla
                $newIsle->decrement('cash_amount', $request->amount);
            } else {
                // Misma isla, solo ajustar la diferencia
                if ($amountDifference > 0) {
                    $newIsle->decrement('cash_amount', $amountDifference);
                } elseif ($amountDifference < 0) {
                    // Disminuyó el monto, devolver la diferencia
                    $newIsle->increment('cash_amount', abs($amountDifference));
                }
            }

            // Actualizar el egreso
            $expense->update([
                'isle_id' => $request->isle_id,
                'location_id' => $newIsle->location_id,
                'description' => $request->description,
                'category' => $request->category,
                'payment_method' => $request->payment_method ?: 'Efectivo',
                'observation' => $request->observation,
                'amount' => $request->amount,
                'date' => $request->date,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Egreso actualizado exitosamente',
                'expense' => $expense->load('location', 'isle')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error actualizando egreso: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'expense_id' => $id,
                'exception' => $e
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el egreso: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $expense = Transaction::find($id);
            
            if (!$expense) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Egreso no encontrado'
                ], 404);
            }

            $currentUser = Auth::user();
            $isMaster = $currentUser->role->nombre === 'master';
            if (!$this->userCanAccessExpense($currentUser, $isMaster, $expense)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para eliminar este egreso.'
                ], 403);
            }

            // Devolver el monto a la isla
            $isle = Isle::lockForUpdate()->find($expense->isle_id);
            if ($isle) {
                $isle->increment('cash_amount', $expense->amount);
            }

            // Eliminar el egreso
            $expense->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Egreso eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error eliminando egreso: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'expense_id' => $id,
                'exception' => $e
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el egreso: ' . $e->getMessage()
            ], 500);
        }
    }

    public function excel(Request $request)
    {
        $currentUser = Auth::user();
        $isMaster = $currentUser->role->nombre === 'master';
        $locationId = $isMaster ? $request->location_id : $currentUser->location_id;

        return Excel::download(new ExpensesExport($request->start_date, $request->end_date, $locationId), 'detalles_de_gastos.xlsx');
    }
}
