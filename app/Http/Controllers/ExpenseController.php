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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
