<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreditFieldsToSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('voucher_code')->nullable()->after('adicional');
            $table->unsignedBigInteger('responsible_id')->nullable()->after('voucher_code');
            $table->text('detail')->nullable()->after('responsible_id');

            $table->foreign('responsible_id')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['responsible_id']);
            $table->dropColumn(['voucher_code', 'responsible_id', 'detail']);
        });
    }
}
