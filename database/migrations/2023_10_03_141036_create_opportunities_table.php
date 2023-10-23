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
            $table->string('status');
            $table->unsignedFloat('amount');
            $table->date('close_date');
            $table->foreignId('contact_id')->constrained();
            $table->foreignId('customer_id')->constrained();
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
