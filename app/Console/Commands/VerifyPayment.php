<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;

/**
 * Pengganti sementara tombol verifikasi di dashboard admin.
 *   php artisan mydream:verify-payment 3           -> verifikasi payment #3
 *   php artisan mydream:verify-payment 3 --reject  -> tolak payment #3
 */
class VerifyPayment extends Command
{
    protected $signature = 'mydream:verify-payment {id : ID payment} {--reject : Tolak pembayaran}';

    protected $description = 'Verifikasi atau tolak pembayaran transfer user';

    public function handle(): int
    {
        $payment = Payment::with('booking')->find($this->argument('id'));

        if (! $payment) {
            $this->error('Payment tidak ditemukan.');

            return self::FAILURE;
        }

        $reject = (bool) $this->option('reject');

        $payment->update([
            'status' => $reject ? 'rejected' : 'verified',
            'verified_at' => now(),
        ]);

        $status = $payment->booking->syncPaymentStatus();

        $this->info(($reject ? 'Ditolak' : 'Terverifikasi') . ". Status booking #{$payment->booking_id}: {$status}");

        return self::SUCCESS;
    }
}
