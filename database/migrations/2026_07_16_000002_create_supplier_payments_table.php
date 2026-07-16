<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierPaymentsTable extends Migration
{
    /**
     * Cuentas por Pagar: pagos (parciales o totales) que se van registrando
     * contra una compra específica, hasta saldar su deuda con el proveedor.
     */
    public function up()
    {
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->string('observation', 500)->nullable();
            $table->unsignedTinyInteger('deleted')->default(0);
            $table->timestamps();

            $table->foreign('purchase_id', 'supplier_payments_purchase_id_foreign')
                ->references('id')->on('purchases')->onDelete('restrict');
            $table->foreign('supplier_id', 'supplier_payments_supplier_id_foreign')
                ->references('id')->on('suppliers')->onDelete('restrict');
            $table->foreign('user_id', 'supplier_payments_user_id_foreign')
                ->references('id')->on('users')->onDelete('restrict');
            $table->foreign('payment_method_id', 'supplier_payments_payment_method_id_foreign')
                ->references('id')->on('payment_methods')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_payments');
    }
}
