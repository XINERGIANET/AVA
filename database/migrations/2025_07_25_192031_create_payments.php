<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->unsignedBigInteger('sale_id');
            $table->string('voucher_type', 20)->nullable();
            $table->string('voucher_id', 30)->nullable();
            $table->string('voucher_file')->nullable();
            $table->string('number', 20)->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('client', 50)->nullable();
            $table->unsignedDecimal('amount', 10, 2)->default(0.00);
            $table->unsignedBigInteger('payment_method_id');
            $table->unsignedTinyInteger('deleted')->default(0);
            $table->date('date')->nullable();
            $table->timestamps();
            
            $table->foreign('payment_method_id', 'fk_payment_method_payment')->references('id')->on('payment_methods')->onDelete('restrict');
            $table->foreign('sale_id', 'fk_sale_payment')->references('id')->on('sales')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
