<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDischargeDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discharge_details', function (Blueprint $table) {
            $table->unsignedBigInteger('discharge_id');
            $table->unsignedBigInteger('tank_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('truck_id');
            $table->decimal('quantity', 10, 2);
            $table->timestamps();

            $table->primary(['discharge_id', 'tank_id']);
            $table->foreign('discharge_id', 'discharge_details_discharge_id_foreign')->references('id')->on('discharges')->onDelete('restrict');
            $table->foreign('product_id', 'discharge_details_product_id_foreign')->references('id')->on('products')->onDelete('restrict');
            $table->foreign('tank_id', 'discharge_details_tank_id_foreign')->references('id')->on('tanks')->onDelete('restrict');
            $table->foreign('truck_id', 'discharge_details_truck_id_foreign')->references('id')->on('trucks')->onDelete('restrict');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discharge_details');
    }
}
