<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            // Snapshot identifiers keep audit rows traceable if a payment row is removed later.
            $table->unsignedBigInteger('booking_id');
            $table->string('midtrans_order_id')->nullable()->index();
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('event_key', 64)->unique();
            $table->string('source', 40);
            $table->string('status_from')->nullable();
            $table->string('status_to')->nullable();
            $table->string('transaction_status_from')->nullable();
            $table->string('transaction_status_to')->nullable();
            $table->string('message')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['booking_id', 'created_at']);
        });

        DB::table('payments')->orderBy('id')->chunkById(200, function ($payments): void {
            foreach ($payments as $payment) {
                DB::table('payment_events')->insert([
                    'payment_id' => $payment->id,
                    'booking_id' => $payment->booking_id,
                    'midtrans_order_id' => $payment->midtrans_order_id ?? null,
                    'amount' => $payment->amount,
                    'event_key' => hash('sha256', 'legacy-payment-snapshot|' . $payment->id),
                    'source' => 'legacy_snapshot',
                    'status_from' => null,
                    'status_to' => $payment->status,
                    'transaction_status_from' => null,
                    'transaction_status_to' => $payment->transaction_status ?? null,
                    'message' => 'Snapshot status transaksi saat pencatatan audit mulai digunakan.',
                    'created_at' => now(),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_events');
    }
};
