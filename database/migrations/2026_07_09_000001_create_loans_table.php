<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('isle_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('loan_date');
            $table->decimal('loan_amount', 10, 2);
            $table->string('send_method', 50)->nullable();
            $table->date('due_date')->nullable();
            $table->decimal('recovered_amount', 10, 2)->default(0);
            $table->string('collection_method', 50)->nullable();
            $table->date('collection_date')->nullable();
            $table->string('status', 20)->default('pending');
            $table->boolean('deleted')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('restrict');
            $table->foreign('isle_id')->references('id')->on('isles')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('loans');
    }
}
