<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payments')) {
            return;
        }

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('type');                          // dp | full | remaining
            $table->decimal('amount', 15, 2);
            $table->string('method')->default('bank_transfer');
            $table->string('bank_info')->nullable();         // rekening yang ditampilkan saat user membayar
            $table->string('status')->default('pending');    // pending | verified | rejected
            $table->string('proof_path')->nullable();        // bukti transfer (opsional)
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
