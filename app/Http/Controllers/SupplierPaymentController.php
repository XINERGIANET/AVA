<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SupplierPaymentController extends Controller
{
    /**
     * Cuentas por Pagar: compras registradas sin método de pago (es decir, nacidas
     * ya como cuenta por pagar) con su saldo pendiente frente al proveedor.
     */
    public function index(Request $request)
    {
        $suppliers = Supplier::where('deleted', 0)->get();
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $supplierId = $request->supplier_id;
        $onlyPending = $request->input('only_pending', '1');

        $query = Purchase::with(['purchase_details', 'supplier', 'payments'])
            ->whereNull('payment_method_id')
            ->when($startDate, fn($q) => $q->whereDate('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('date', '<=', $endDate))
            ->when($supplierId, fn($q) => $q->where('supplier_id', $supplierId))
            ->when(auth()->user()->role->nombre != 'master' && auth()->user()->location_id, function ($query) {
                $query->whereHas('purchase_details.tank', function ($q) {
                    $q->where('location_id', auth()->user()->location_id);
                });
            })
            ->orderBy('date', 'desc');

        $purchases = $query->get();

        if ($onlyPending === '1') {
            $purchases = $purchases->filter(fn($purchase) => $purchase->balance > 0.009)->values();
        }

        $totalDebt = $purchases->sum('balance');

        $purchases = new \Illuminate\Pagination\LengthAwarePaginator(
            $purchases->forPage($request->input('page', 1), 20),
            $purchases->count(),
            20,
            $request->input('page', 1),
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $paymentMethods = PaymentMethod::where('deleted', 0)->get();

        return view('supplier_payments.index', compact('purchases', 'suppliers', 'paymentMethods', 'totalDebt', 'onlyPending'));
    }

    /**
     * Registra un pago (parcial o total) contra una compra puntual.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purchase_id'       => 'required|exists:purchases,id',
            'amount'            => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'date'              => 'required|date',
            'observation'       => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error'  => $validator->errors()->first(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $purchase = Purchase::findOrFail($request->purchase_id);
            $balance = $purchase->balance;

            if ($request->amount > $balance + 0.009) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'error' => 'El monto excede el saldo pendiente de la compra. Saldo disponible: S/ ' . number_format($balance, 2),
                ], 422);
            }

            $payment = SupplierPayment::create([
                'purchase_id'       => $purchase->id,
                'supplier_id'       => $purchase->supplier_id,
                'user_id'           => auth()->id(),
                'payment_method_id' => $request->payment_method_id,
                'amount'            => $request->amount,
                'date'              => $request->date,
                'observation'       => $request->observation,
                'deleted'           => 0,
            ]);

            DB::commit();

            $purchase->refresh();

            return response()->json([
                'status' => true,
                'message' => 'Pago registrado correctamente.',
                'payment' => $payment,
                'new_balance' => $purchase->balance,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'error' => 'Error al registrar el pago: ' . $e->getMessage(),
            ], 500);
        }
    }
}
