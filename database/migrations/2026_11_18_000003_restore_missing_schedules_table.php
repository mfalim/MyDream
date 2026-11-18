<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('schedules')) {
            return;
        }

        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->nullable()->constrained('events')->cascadeOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->cascadeOnDelete();
            $table->decimal('price', 15, 2)->nullable();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('activity');
            $table->string('location')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['approved', 'pending', 'rejected'])->default('pending');
            $table->decimal('agreed_fee', 15, 2)->nullable();
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->string('payment_status', 30)->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->text('payment_notes')->nullable();
            $table->string('payout_proof_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Keep schedule data created after this recovery migration.
    }
};
