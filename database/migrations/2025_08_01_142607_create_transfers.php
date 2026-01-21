<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransfers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_tank_id');
            $table->unsignedBigInteger('to_tank_id');
            $table->unsignedBigInteger('product_id');
            $table->dateTime('date');
            $table->timestamps();

            $table->foreign('from_tank_id', 'transfers_from_tank_id_fk')->references('id')->on('tanks')->onDelete('restrict');
            $table->foreign('to_tank_id', 'transfers_to_tank_id_fk')->references('id')->on('tanks')->onDelete('restrict');
            $table->foreign('product_id', 'transfers_product_id_fk')->references('id')->on('products')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfers');
    }
}
