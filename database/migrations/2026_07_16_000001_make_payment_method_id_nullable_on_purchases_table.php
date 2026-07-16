<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakePaymentMethodIdNullableOnPurchasesTable extends Migration
{
    /**
     * Las compras ahora nacen como Cuenta por Pagar: el método de pago ya no se
     * elige al registrar la compra, se define después al registrar el pago al proveedor.
     *
     * Se usa SQL directo (en vez de Schema::table()->change()) porque este proyecto
     * tiene instalado doctrine/dbal 4.x, incompatible con el puente de Doctrine que
     * usa Laravel 8 para modificar columnas existentes.
     */
    public function up()
    {
        DB::statement('ALTER TABLE purchases MODIFY payment_method_id BIGINT UNSIGNED NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE purchases MODIFY payment_method_id BIGINT UNSIGNED NOT NULL');
    }
}
