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
            $table->enum('attendance_status', ['full_day', 'half_day', 'briefing', 'late', 'absent'])
                ->nullable()
                ->after('status');
            $table->decimal('actual_hours', 5, 2)->default(0)->after('attendance_status');
            $table->decimal('allowance', 15, 2)->default(0)->after('actual_hours');
            $table->decimal('total_fee', 15, 2)->default(0)->after('allowance');
            $table->enum('payment_status', ['pending', 'approved', 'paid'])->default('pending')->after('total_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_members', function (Blueprint $table) {
            $table->dropColumn([
                'attendance_status',
                'actual_hours',
                'allowance',
                'total_fee',
                'payment_status',
            ]);
        });
    }
};
