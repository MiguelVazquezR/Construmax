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
            $table->foreignId('user_id')->constrained();
            $table->unsignedBigInteger('opportunity_id');
            $table->foreign('opportunity_id')->references('id')->on('opportunities');
            $table->unsignedBigInteger('project_group_id');
            $table->foreign('project_group_id')->references('id')->on('project_groups');
            $table->unsignedBigInteger('owner_id');
            $table->foreign('owner_id')->references('id')->on('users');
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
