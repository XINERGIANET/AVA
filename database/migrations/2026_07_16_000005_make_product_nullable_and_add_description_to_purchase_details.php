<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MakeProductNullableAndAddDescriptionToPurchaseDetails extends Migration
{
    /**
     * Las líneas de "Otros Gastos" no siempre corresponden a un producto del
     * catálogo (ej. "Reparación de bomba"): permitimos product_id nulo y
     * agregamos una descripción libre para esos casos.
     *
     * Se usa SQL directo (no Schema::table()->change()) por la incompatibilidad
     * de doctrine/dbal 4.x con el puente de Doctrine de Laravel 8.
     */
    public function up()
    {
        DB::statement('ALTER TABLE purchase_details MODIFY product_id BIGINT UNSIGNED NULL');

        // measurement_unit era ENUM('galones','litros') NOT NULL: no admitía unidades
        // libres como "servicio" o "unidad" que necesitan las líneas de Otros Gastos.
        DB::statement("ALTER TABLE purchase_details MODIFY measurement_unit VARCHAR(50) NULL");

        Schema::table('purchase_details', function (Blueprint $table) {
            $table->string('description', 255)->nullable()->after('product_id');
        });
    }

    public function down()
    {
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        DB::statement("ALTER TABLE purchase_details MODIFY measurement_unit ENUM('galones','litros') NOT NULL");
        DB::statement('ALTER TABLE purchase_details MODIFY product_id BIGINT UNSIGNED NOT NULL');
    }
}
