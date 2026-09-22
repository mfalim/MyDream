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
        Schema::table('schedules', function (Blueprint $table) {
            if (Schema::hasColumn('schedules', 'booking_vendor_id')) {
                $table->dropForeign(['booking_vendor_id']);
                $table->dropColumn('booking_vendor_id');
            }
            if (Schema::hasColumn('schedules', 'event_day_id')) {
                $table->dropForeign(['event_day_id']);
                $table->dropColumn('event_day_id');
            }
            
            $table->foreignId('member_id')->nullable()->after('vendor_id')->constrained('members')->onDelete('set null');
            $table->enum('status', ['approved', 'pending', 'rejected'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->dropColumn('member_id');
            $table->enum('status', ['approved', 'pending', 'rejected'])->default('pending')->change();
        });
    }
};
