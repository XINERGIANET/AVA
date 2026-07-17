<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddEgresoToTransactionsTable extends Migration
{
    /**
     * "Generar Movimiento" agrega un tercer tipo de movimiento de bóveda: 'eg'
     * (Egreso a destino externo: Banco, Dueño, Otra Bóveda, etc.), distinto del
     * 'sb' existente (Salida de bóveda hacia caja chica, movimiento interno).
     *
     * Se usa SQL directo para el ENUM (no Schema::table()->change()) por la
     * incompatibilidad de doctrine/dbal 4.x con el puente de Doctrine de Laravel 8.
     */
    public function up()
    {
        // La columna 'date' de transactions tiene un default legacy
        // '0000-00-00 00:00:00' que el sql_mode actual (NO_ZERO_DATE) ya no
        // permite. MySQL revalida toda la fila al hacer cualquier ALTER, así
        // que se relaja el modo solo para este statement (no se toca el
        // default existente, que es ajeno a este cambio).
        $originalSqlMode = DB::selectOne('SELECT @@session.sql_mode as mode')->mode;
        DB::statement("SET SESSION sql_mode = REPLACE(@@sql_mode, 'NO_ZERO_DATE', '')");

        DB::statement("ALTER TABLE transactions MODIFY type ENUM('scc','sb','eb','eg') NOT NULL");

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('vault_destination_id')->nullable()->after('isle_id')
                ->constrained('vault_destinations');
        });

        DB::statement("SET SESSION sql_mode = '" . $originalSqlMode . "'");
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('vault_destination_id');
        });

        DB::statement("ALTER TABLE transactions MODIFY type ENUM('scc','sb','eb') NOT NULL");
    }
}
