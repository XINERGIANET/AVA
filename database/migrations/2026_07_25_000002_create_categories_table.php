<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('deleted')->default(0);
            $table->timestamps();
        });

        // Insertar categorías por defecto existentes en el sistema
        DB::table('categories')->insert([
            ['name' => 'Combustible', 'description' => 'Categoría principal para combustibles', 'deleted' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Inv. interno', 'description' => 'Categoría para inventarios internos', 'deleted' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
