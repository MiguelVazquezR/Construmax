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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('currency');
            $table->unsignedTinyInteger('invoice_type');
            $table->boolean('is_strict')->default(0);
            $table->boolean('is_internal')->default(0);
            $table->unsignedFloat('budget');
            $table->date('start_date');
            $table->date('limit_date');
            $table->timestamp('finished_at')->nullable();
            $table->foreignId('project_group_id')->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('opportunity_id')->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
