<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailsToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Temporalmente deshabilitar el modo estricto para evitar error "Invalid default value for 'date'"
        \Illuminate\Support\Facades\DB::statement("SET SESSION sql_mode = ''");

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('category')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('observation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Temporalmente deshabilitar el modo estricto para evitar error "Invalid default value for 'date'"
        \Illuminate\Support\Facades\DB::statement("SET SESSION sql_mode = ''");

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['category', 'payment_method', 'observation']);
        });
    }
}
