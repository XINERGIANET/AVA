<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGeneralCashToCashClosesAndLocations extends Migration
{
    public function up()
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->decimal('cash_amount', 12, 2)->default(0)->after('deleted');
        });

        Schema::table('cash_closes', function (Blueprint $table) {
            $table->string('cash_type', 20)->default('isle')->after('isle_id');
        });
    }

    public function down()
    {
        Schema::table('cash_closes', function (Blueprint $table) {
            $table->dropColumn('cash_type');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn('cash_amount');
        });
    }
}
