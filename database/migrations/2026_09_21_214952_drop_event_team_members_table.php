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
        Schema::dropIfExists('event_team_members');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('event_team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('role', ['lead_director', 'co_director', 'coordinator', 'technical', 'documentation', 'other'])->default('other');
            $table->string('custom_role')->nullable();
            $table->text('responsibilities')->nullable();
            $table->enum('status', ['assigned', 'confirmed', 'on_duty', 'completed'])->default('assigned');
            $table->timestamps();
        });
    }
};
