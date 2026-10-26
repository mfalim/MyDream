<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Step 3/3: pembayaran lewat transfer bank manual.
 * Alur: user pilih DP/Lunas -> transfer -> klik "Saya sudah transfer" (+ bukti opsional)
 * -> status "Menunggu Verifikasi" -> admin verifikasi (sementara: php artisan mydream:verify-payment).
 */
class PaymentController extends Controller
{
    public function show(string $bookingId): View
    {
        $booking = $this->ownedBooking($bookingId);
        $booking->syncPaymentStatus();
        $booking->load('payments');

        $total = (float) $booking->total_price;
        $paid = (float) $booking->paid_amount;

        return view('site.payment.show', [
            'booking' => $booking,
            'event' => $booking->events->first(),
            'total' => $total,
            'paid' => $paid,
            'pending' => (float) $booking->payments->where('status', 'pending')->sum('amount'),
            'remaining' => max(0, $total - $paid),
            'options' => $this->options($booking),
            'bank' => $this->bank(),
            'dpPercent' => (int) config('mydream.dp_percent'),
            'payments' => $booking->payments->sortByDesc('id')->values(),
        ]);
    }

    public function pay(Request $request, string $bookingId): RedirectResponse
    {
        $booking = $this->ownedBooking($bookingId);
        $booking->syncPaymentStatus();

        $options = $this->options($booking);
        if (empty($options)) {
            return back()->with('error', 'Tidak ada pembayaran yang perlu dilakukan saat ini.');
        }

        $bank = $this->bank();
        if (! $bank) {
            return back()->with('error', 'Rekening transfer belum diatur. Silakan hubungi admin.');
        }

        $data = $request->validate([
            'scheme' => ['required', Rule::in(array_keys($options))],
            'proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'scheme.required' => 'Pilih skema pembayaran terlebih dahulu.',
            'scheme.in' => 'Skema pembayaran tidak valid.',
            'proof.mimes' => 'Bukti transfer harus berupa JPG, PNG, atau PDF.',
            'proof.max' => 'Ukuran bukti transfer maksimal 2 MB.',
        ]);

        $proofPath = $request->file('proof')?->store('payment-proofs', 'public');

        DB::transaction(function () use ($booking, $options, $data, $bank, $proofPath) {
            $booking->ensureInvoiceNumber();

            Payment::create([
                'booking_id' => $booking->id,
                'type' => $data['scheme'],
                'amount' => $options[$data['scheme']]['amount'],
                'method' => 'bank_transfer',
                'bank_info' => trim("{$bank['name']} {$bank['number']} " . ($bank['holder'] ? "a.n. {$bank['holder']}" : '')),
                'status' => 'pending',
                'proof_path' => $proofPath,
            ]);

            $booking->syncPaymentStatus();
        });

        return redirect()
            ->route('payment.index', $booking->id)
            ->with('success', 'Terima kasih. Pembayaran Anda sedang menunggu verifikasi admin.');
    }

    /** Rute lama /pembayaran/{id}/sukses: status sekarang ada di halaman pembayaran. */
    public function success(string $bookingId): RedirectResponse
    {
        return redirect()->route('payment.index', $bookingId);
    }

    private function ownedBooking(string $bookingId): Booking
    {
        $client = Client::where('users_id', Auth::id())->first();
        abort_if(! $client, 403, 'Lengkapi data mempelai terlebih dahulu.');

        return Booking::with(['client', 'package', 'events', 'payments'])
            ->where('client_id', $client->id)
            ->findOrFail($bookingId);
    }

    /** Pilihan pembayaran yang boleh dilakukan user saat ini. Kosong = tidak ada yang perlu dibayar. */
    private function options(Booking $booking): array
    {
        $total = (float) $booking->total_price;
        $percent = max(1, min(100, (int) config('mydream.dp_percent')));

        if ($total <= 0) {
            return [];
        }

        if ($booking->payment_status === 'unpaid') {
            return [
                'dp' => [
                    'label' => "DP {$percent}%",
                    'amount' => (int) ceil($total * $percent / 100),
                    'note' => 'Kunci tanggal sekarang, sisanya dilunasi sebelum hari acara.',
                ],
                'full' => [
                    'label' => 'Bayar Lunas',
                    'amount' => (int) round($total),
                    'note' => 'Selesaikan seluruh pembayaran sekaligus.',
                ],
            ];
        }

        if ($booking->payment_status === 'dp_paid') {
            return [
                'remaining' => [
                    'label' => 'Pelunasan',
                    'amount' => (int) round(max(0, $total - (float) $booking->paid_amount)),
                    'note' => 'Sisa pembayaran setelah DP.',
                ],
            ];
        }

        return [];
    }

    private function bank(): ?array
    {
        $bank = config('mydream.bank');

        if (! filled($bank['name'] ?? null) || ! filled($bank['number'] ?? null)) {
            return null;
        }

        return ['name' => $bank['name'], 'number' => $bank['number'], 'holder' => $bank['holder'] ?? null];
    }
}
