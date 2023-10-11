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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('department');
            $table->string('priority');
            $table->string('status')->default('Por hacer');
            $table->boolean('is_paused')->default(0);
            $table->date('start_date');
            $table->date('limit_date');
            $table->time('start_time')->nullable();
            $table->time('limit_time')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('project_id')->nullable()->constrained();
            $table->foreignId('opportunity_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
