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
        Schema::table('event_members', function (Blueprint $table) {
            $table->string('attendance_status', 30)->default('present')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_members', function (Blueprint $table) {
            $table->enum('attendance_status', ['full_day', 'half_day', 'briefing', 'late', 'absent'])
                ->nullable()
                ->change();
        });
    }
};
