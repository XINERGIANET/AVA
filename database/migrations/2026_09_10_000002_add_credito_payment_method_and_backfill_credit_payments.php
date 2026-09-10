<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddCreditoPaymentMethodAndBackfillCreditPayments extends Migration
{
    public function up()
    {
        $creditMethod = DB::table('payment_methods')
            ->whereRaw('LOWER(name) IN (?, ?, ?, ?)', ['credito', 'crédito', 'pendiente', 'pending'])
            ->where('deleted', 0)
            ->orderBy('id')
            ->first();

        $creditMethodId = $creditMethod
            ? $creditMethod->id
            : DB::table('payment_methods')->insertGetId([
                'name' => 'Crédito',
                'location_id' => null,
                'deleted' => 0,
            ]);

        // Backfill: pagos pendientes de ventas a credito/contrato (type_sale 1 o 2) que
        // quedaron mal etiquetados con el metodo de pago que caia por defecto (normalmente
        // Efectivo, id mas bajo) antes de que existiera un metodo "Credito" real -- ver
        // SaleController::resolveCreditPendingPaymentMethodId().
        DB::table('payments')
            ->join('sales', 'sales.id', '=', 'payments.sale_id')
            ->whereIn('sales.type_sale', [1, 2])
            ->where('payments.status', 'pending')
            ->where('payments.payment_method_id', '!=', $creditMethodId)
            ->update(['payments.payment_method_id' => $creditMethodId]);
    }

    public function down()
    {
        // No se revierte el backfill (no queda registro de cual era el metodo original
        // antes de esta migracion); solo se deja de usar la fila 'Credito' hacia adelante.
    }
}
