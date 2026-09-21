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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_vendor_id')->constrained('booking_vendors')->cascadeOnDelete();
            $table->foreignId('event_day_id')->constrained('event_days')->cascadeOnDelete();
            $table->string('activity');
            $table->string('location')->nullable();
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['approved', 'pending', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
