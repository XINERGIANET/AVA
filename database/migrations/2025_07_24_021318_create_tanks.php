<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTanks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tanks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id');
            $table->string('name');
            $table->integer('capacity');
            $table->string('deleted')->default('0');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->timestamps();

            $table->foreign('product_id', 'tanks_product_id_foreign')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('location_id', 'tanks_location_id_foreign')->references('id')->on('locations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tanques');
    }
}
