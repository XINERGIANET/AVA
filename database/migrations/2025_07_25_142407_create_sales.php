<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('order_detail_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('client', 50)->nullable();
            $table->string('phone', 20)->nullable();
            $table->decimal('total', 10, 2);
            $table->dateTime('date');
            $table->boolean('deleted')->default(0);
            $table->timestamps();
            
            $table->foreign('user_id', 'sales_user_id_fk')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('client_id', 'sales_client_id_fk')->references('id')->on('clients')->onDelete('restrict');
            $table->foreign('location_id', 'sales_location_id_fk')->references('id')->on('locations')->onDelete('restrict');
            $table->foreign('order_detail_id', 'sales_order_detail_id_fk')->references('id')->on('order_details')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
