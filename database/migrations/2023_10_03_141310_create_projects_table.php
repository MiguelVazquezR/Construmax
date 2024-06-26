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
            $table->string('service_type');
            $table->text('description');
            $table->string('currency');
            $table->text('address')->nullable();
            $table->boolean('is_strict')->default(0);
            $table->boolean('is_internal')->default(0);
            $table->json('budgets')->nullable();
            $table->date('start_date');
            $table->date('limit_date');
            $table->timestamp('finished_at')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('contact_id')->constrained();
            $table->unsignedBigInteger('opportunity_id')->nullable();
            $table->foreign('opportunity_id')->references('id')->on('opportunities')->cascadeOnDelete();
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
