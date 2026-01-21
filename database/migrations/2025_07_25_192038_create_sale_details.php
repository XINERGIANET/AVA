<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_details', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('sale_id');
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->unsignedTinyInteger('deleted')->default(0);
            
            $table->primary(['product_id', 'sale_id']);
            $table->foreign('product_id', 'sale_details_product_id_foreign')->references('id')->on('products')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('sale_id', 'sale_details_sale_id_foreign')->references('id')->on('sales')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_details');
    }
}
