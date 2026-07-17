<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddLocationIdToPurchasesTable extends Migration
{
    /**
     * `purchases` nunca tuvo su propia sede: para compras de Combustible se
     * infería indirectamente por el tanque (purchase_details.tank_id ->
     * tanks.location_id), y para "Otros Gastos" (sin tanque) no había forma
     * de saber a qué sede pertenecía. Esto impedía que un admin de sede viera
     * solo sus propias compras.
     */
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->after('id')->constrained('locations');
        });

        // Backfill: para compras existentes con tanque, tomar la sede del
        // primer tanque usado en sus detalles.
        DB::statement('
            UPDATE purchases p
            JOIN (
                SELECT pd.purchase_id, MIN(t.location_id) AS location_id
                FROM purchase_details pd
                JOIN tanks t ON t.id = pd.tank_id
                GROUP BY pd.purchase_id
            ) src ON src.purchase_id = p.id
            SET p.location_id = src.location_id
            WHERE p.location_id IS NULL
        ');
    }

    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('location_id');
        });
    }
}
