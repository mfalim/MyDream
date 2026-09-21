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
        Schema::create('event_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('name');
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->string('event_session')->nullable();
            $table->string('event_type')->nullable();
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->string('lead_director')->nullable();
            $table->string('director_role')->nullable();
            $table->integer('internal_team_count')->nullable();
            $table->string('team_status')->nullable();
            $table->text('team_members')->nullable();
            $table->string('current_rundown')->nullable();
            $table->integer('rundown_progress')->nullable();
            $table->integer('total_rundown_steps')->nullable();
            $table->integer('completed_rundown_steps')->nullable();
            $table->string('event_overall_status')->nullable();
            $table->string('event_banner')->nullable();
            $table->text('special_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_days');
    }
};
