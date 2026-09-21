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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_day_id')->constrained('event_days')->cascadeOnDelete();
            $table->string('name'); // e.g., "Akad Nikah", "Resepsi", "Lamaran"
            $table->string('event_type')->nullable(); // e.g., "ceremony", "reception", "engagement"
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, ongoing, completed, cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
