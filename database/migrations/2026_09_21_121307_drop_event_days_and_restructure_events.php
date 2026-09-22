<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['event_day_id']);
        });

        if (!Schema::hasColumn('events', 'booking_id')) {
            Schema::table('events', function (Blueprint $table) {
                $table->foreignId('booking_id')->after('id')->constrained('bookings')->cascadeOnDelete();
            });
        }

        if (!Schema::hasColumn('events', 'event_date')) {
            Schema::table('events', function (Blueprint $table) {
                $table->date('event_date')->after('event_type');
            });
        }

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('event_day_id');
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['event_day_id']);
            $table->dropColumn('event_day_id');
        });

        Schema::dropIfExists('event_days');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('event_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('name');
            $table->date('event_date');
            $table->timestamps();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
            $table->dropColumn(['booking_id', 'event_date']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('event_day_id')->after('id')->constrained('event_days')->cascadeOnDelete();
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->foreignId('event_day_id')->after('booking_vendor_id')->constrained('event_days')->cascadeOnDelete();
        });
    }
};
