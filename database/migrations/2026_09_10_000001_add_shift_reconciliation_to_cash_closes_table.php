<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShiftReconciliationToCashClosesTable extends Migration
{
    public function up()
    {
        Schema::table('cash_closes', function (Blueprint $table) {
            if (!Schema::hasColumn('cash_closes', 'theoretical_sale_amount')) {
                $table->decimal('theoretical_sale_amount', 12, 2)->nullable()->after('final_cash_amount');
            }
            if (!Schema::hasColumn('cash_closes', 'credits_amount')) {
                $table->decimal('credits_amount', 12, 2)->nullable()->after('theoretical_sale_amount');
            }
            if (!Schema::hasColumn('cash_closes', 'transfers_amount')) {
                $table->decimal('transfers_amount', 12, 2)->nullable()->after('credits_amount');
            }
            if (!Schema::hasColumn('cash_closes', 'expenses_amount')) {
                $table->decimal('expenses_amount', 12, 2)->nullable()->after('transfers_amount');
            }
            if (!Schema::hasColumn('cash_closes', 'discounts_amount')) {
                $table->decimal('discounts_amount', 12, 2)->nullable()->after('expenses_amount');
            }
            if (!Schema::hasColumn('cash_closes', 'sale_variance_amount')) {
                $table->decimal('sale_variance_amount', 12, 2)->nullable()->after('discounts_amount');
            }
            if (!Schema::hasColumn('cash_closes', 'meter_breakdown')) {
                $table->json('meter_breakdown')->nullable()->after('sale_variance_amount');
            }
        });
    }

    public function down()
    {
        Schema::table('cash_closes', function (Blueprint $table) {
            foreach ([
                'theoretical_sale_amount',
                'credits_amount',
                'transfers_amount',
                'expenses_amount',
                'discounts_amount',
                'sale_variance_amount',
                'meter_breakdown',
            ] as $column) {
                if (Schema::hasColumn('cash_closes', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
