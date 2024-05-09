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
        Schema::create('opportunity_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('limit_date');
            $table->string('time', 10);
            $table->timestamp('finished_at')->nullable();
            $table->text('description')->nullable();
            $table->string('priority');
            $table->string('reminder')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('opportunity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asigned_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunity_tasks');
    }
};
