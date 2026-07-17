<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateVaultDestinationsTable extends Migration
{
    /**
     * Catálogo editable de destinos de egreso de Bóveda (Banco, Dueño, Otra
     * Bóveda, y lo que se agregue después) en vez de una lista fija en código.
     */
    public function up()
    {
        Schema::create('vault_destinations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedTinyInteger('deleted')->default(0);
            $table->timestamps();
        });

        DB::table('vault_destinations')->insert([
            ['name' => 'Banco', 'deleted' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dueño', 'deleted' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Otra Bóveda', 'deleted' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('vault_destinations');
    }
}
