<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddIsleIdToTransactionsTable extends Migration
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
            $table->unsignedBigInteger('isle_id')->nullable()->after('location_id');
            $table->foreign('isle_id', 'transactions_isle_id_fk')->references('id')->on('isles')->onDelete('restrict');
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
            $table->dropForeign('transactions_isle_id_fk');
            $table->dropColumn('isle_id');
        });
        
        DB::statement("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION'");
    }
}