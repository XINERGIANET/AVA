<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasePlansTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Quien registra (Admin sede)
            $table->date('scheduled_date'); // Fecha planificada
            $table->decimal('available_money', 12, 2)->default(0); // Dinero en caja/bóveda reportado
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'partially_completed'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null'); // Quien revisa (Gerente/Master)
            $table->timestamp('reviewed_at')->nullable();
            $table->text('notes')->nullable(); // Observaciones del admin de sede
            $table->text('manager_notes')->nullable(); // Comentarios de gerencia
            $table->decimal('compliance_percentage', 5, 2)->nullable(); // % de eficacia de compra real vs solicitada
            $table->text('justification_notes')->nullable(); // Justificación de brecha (si cumplimiento < 100%)
            $table->tinyInteger('deleted')->default(0);
            $table->timestamps();
        });

        Schema::create('purchase_plan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_plan_id')->constrained('purchase_plans')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('tank_id')->nullable()->constrained('tanks')->onDelete('set null');
            $table->decimal('current_stock', 10, 2)->default(0); // Stock de tanque al solicitar (galones)
            $table->decimal('requested_quantity', 10, 2)->default(0); // Galones solicitados
            $table->decimal('approved_quantity', 10, 2)->nullable(); // Galones autorizados por gerencia
            $table->decimal('purchased_quantity', 10, 2)->default(0); // Galones efectivamente comprados
            $table->decimal('unit_price_estimate', 10, 2)->nullable();
            $table->decimal('estimated_total', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_plan_details');
        Schema::dropIfExists('purchase_plans');
    }
}
