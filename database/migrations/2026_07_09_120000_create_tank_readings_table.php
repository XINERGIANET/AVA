<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTankReadingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tank_readings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tank_id');
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->decimal('physical_quantity', 10, 3);
            $table->decimal('previous_stock', 10, 3)->default(0.000);
            $table->decimal('theoretical_stock', 10, 3)->default(0.000);
            $table->decimal('difference', 10, 3)->default(0.000);
            $table->enum('status', ['falta', 'cuadra', 'sobra'])->default('cuadra');
            $table->text('notes')->nullable();
            $table->boolean('deleted')->default(0);
            $table->timestamps();

            $table->foreign('tank_id', 'tank_readings_tank_id_foreign')->references('id')->on('tanks')->onDelete('restrict');
            $table->foreign('user_id', 'tank_readings_user_id_foreign')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tank_readings');
    }
}
