<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddConceptAndGlosaToPurchasesTable extends Migration
{
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_concept_id')->nullable()->after('id');
            $table->text('glosa')->nullable()->after('invoice_number');

            $table->foreign('purchase_concept_id', 'purchases_purchase_concept_id_foreign')
                ->references('id')->on('purchase_concepts')->onDelete('restrict');
        });

        // Las compras que ya existían son todas de combustible (era el único flujo
        // que existía antes de este cambio).
        $fuelConceptId = DB::table('purchase_concepts')->where('name', 'Combustible')->value('id');
        if ($fuelConceptId) {
            DB::table('purchases')->whereNull('purchase_concept_id')->update(['purchase_concept_id' => $fuelConceptId]);
        }
    }

    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign('purchases_purchase_concept_id_foreign');
            $table->dropColumn(['purchase_concept_id', 'glosa']);
        });
    }
}
