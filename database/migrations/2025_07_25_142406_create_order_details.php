<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('area')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->decimal('quantity',10,2,true);
            $table->timestamps();

            $table->foreign('order_id', 'order_details_order_id_fk')->references('id')->on('orders')->onDelete('restrict');
            $table->foreign('product_id', 'order_details_product_id_fk')->references('id')->on('products')->onDelete('restrict');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_details');
    }
}
