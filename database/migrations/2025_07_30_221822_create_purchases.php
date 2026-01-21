<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('voucher_type')->nullable()->comment("1: Factura , 2:Boleta , 3:Nota de Venta, 4: Otro");
            $table->string('invoice_number')->nullable();
            $table->unsignedBigInteger('payment_method_id');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->boolean('deleted')->default(0);
            $table->date('date');
            $table->timestamps();
            
            $table->foreign('payment_method_id', 'purchases_payment_method_id_foreign')->references('id')->on('payment_methods')->onDelete('restrict');
            $table->foreign('supplier_id', 'purchases_supplier_id_foreign')->references('id')->on('suppliers')->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases');
    }
}
