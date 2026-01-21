<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsleIdToCashClosesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cash_closes', function (Blueprint $table) {
            $table->unsignedBigInteger('isle_id')->nullable();
            $table->foreign('isle_id')->references('id')->on('isles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cash_closes', function (Blueprint $table) {
            $table->dropForeign(['isle_id']);
            $table->dropColumn('isle_id');
        });
    }
}
