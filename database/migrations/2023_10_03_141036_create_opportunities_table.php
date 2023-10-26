<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('priority');
            $table->unsignedTinyInteger('probability')->nullable();
            $table->string('lost_oportunity_razon')->nullable();
            $table->string('service_type');
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('branch')->nullable();
            $table->string('status');
            $table->text('description')->nullable();
            $table->unsignedFloat('amount');
            $table->date('start_date');
            $table->date('close_date');
            $table->timestamp('finished_at')->nullable();
            $table->foreignId('contact_id')->constrained();
            $table->foreignId('customer_id')->nullable()->constrained();
            $table->foreignId('user_id')->constrained();
            $table->unsignedBigInteger('seller_id');
            $table->foreign('seller_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
