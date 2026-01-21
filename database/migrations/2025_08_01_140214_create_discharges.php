<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDischarges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discharges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id')->nullable();
            $table->unsignedBigInteger('location_id');
            $table->dateTime('date');
            $table->decimal('total_quantity', 10, 2);
            $table->boolean('deleted')->default(0);
            $table->timestamps();

            $table->foreign('purchase_id', 'discharges_purchase_id_foreign')->references('id')->on('purchases')->onDelete('restrict');
            $table->foreign('location_id', 'discharges_location_id_foreign')->references('id')->on('locations')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discharges');
    }
}
