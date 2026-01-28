<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Isle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $location_id = $request->location_id;

        $currentUser = Auth::user();
        $isMaster = $currentUser->role->nombre === 'master';
        $isAdmin = $currentUser->role->nombre === 'admin';

        // Construir la consulta base
        $query = Transaction::with('location', 'isle')
            ->where('type', 'scc');

        // Aplicar filtros de permisos según el rol del usuario
        if ($isMaster || $isAdmin) {
            // Master y Admin ven todo
        } else {
            // Worker: solo ve de su sede y/o isla
            if ($currentUser->isle_id) {
                // Si tiene isla asignada, solo ve esa isla
                $query->where('isle_id', $currentUser->isle_id);
            } elseif ($currentUser->location_id) {
                // Si no tiene isla asignada pero tiene sede, ve toda su sede
                $query->where('location_id', $currentUser->location_id);
            }
        }

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
        if ($isMaster || $isAdmin) {
            // Master y Admin ven todo
        } else {
            if ($currentUser->isle_id) {
                $totalQuery->where('isle_id', $currentUser->isle_id);
            } elseif ($currentUser->location_id) {
                $totalQuery->where('location_id', $currentUser->location_id);
            }
        }

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
            ->when(!$isMaster && !$isAdmin && $currentUser->location_id, function ($q) use ($currentUser) {
                $q->where('id', $currentUser->location_id);
            })
            ->orderBy('name')
            ->get();

        // Cargar islas según permisos para el formulario de editar
        $isles = Isle::where('deleted', 0)
            ->when(!$isMaster && !$isAdmin, function ($q) use ($currentUser) {
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
            'isle_id' => 'required|exists:isles,id',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();

            $isle = Isle::lockForUpdate()->find($request->input('isle_id'));
            if ($isle->cash_amount < $request->input('amount')) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Saldo insuficiente en la isla. Disponible: ' . number_format($isle->cash_amount, 2)
                ], 400);
            }

            $expense = Transaction::create([
                'user_id' => $user->id,
                'location_id' => $isle->location_id,
                'isle_id' => $isle->id,
                'type' => 'scc',
                'description' => $request->input('description'),
                'amount' => $request->input('amount'),
                'date' => now(),
            ]);

            $isle->decrement('cash_amount', $request->input('amount'));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Egreso registrado exitosamente. Nuevo saldo: ' . ($isle->cash_amount - $request->input('amount')),
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
                    // Aumentó el monto, verificar saldo suficiente
                    if ($newIsle->cash_amount < $amountDifference) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Saldo insuficiente en la isla. Disponible: ' . number_format($newIsle->cash_amount, 2)
                        ], 400);
                    }
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
}
