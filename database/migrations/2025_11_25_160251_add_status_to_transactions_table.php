<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddStatusToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET SESSION sql_mode = ""');
        
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('status')->default(0);
        });
        
        // Opcional: reactivar el modo estricto
        DB::statement("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION'");
   
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET SESSION sql_mode = ""');
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        DB::statement("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION'");

    }
}
