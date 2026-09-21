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
            $table->dropForeign(['booking_vendor_id']);
            $table->dropColumn('booking_vendor_id');
        });

        Schema::dropIfExists('booking_vendors');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('booking_vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['confirmed', 'on_site', 'loading', 'pending'])->default('pending');
            $table->timestamps();
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->foreignId('booking_vendor_id')->nullable()->after('id')->constrained('booking_vendors')->cascadeOnDelete();
        });
    }
};
