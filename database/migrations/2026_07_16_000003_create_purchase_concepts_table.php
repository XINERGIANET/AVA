<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePurchaseConceptsTable extends Migration
{
    /**
     * Catálogo abierto de "Concepto de la compra" (Combustible, Otros Gastos, y lo
     * que se agregue después) para no dejar el formulario de Compras centrado
     * únicamente en combustible.
     */
    public function up()
    {
        Schema::create('purchase_concepts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // is_fuel controla si el formulario muestra temperatura y tanques,
            // o el flujo genérico de productos + glosa.
            $table->boolean('is_fuel')->default(false);
            $table->unsignedTinyInteger('deleted')->default(0);
            $table->timestamps();
        });

        DB::table('purchase_concepts')->insert([
            ['name' => 'Combustible', 'is_fuel' => 1, 'deleted' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Otros Gastos', 'is_fuel' => 0, 'deleted' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('purchase_concepts');
    }
}
