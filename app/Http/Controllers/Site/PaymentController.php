<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Step 3/3: pembayaran booking melalui Midtrans Snap.
 */
class PaymentController extends Controller
{
    public function show(Request $request, string $bookingId): View
    {
        $booking = $this->ownedBooking($request, $bookingId);
        $booking->syncPaymentStatus();
        $booking->load('payments');

        $total = (float) $booking->total_price;
        $paid = (float) $booking->paid_amount;

        return view('site.payment.show', [
            'booking' => $booking,
            'event' => $booking->events->first(),
            'total' => $total,
            'paid' => $paid,
            'pending' => (float) $booking->payments
                ->whereIn('status', ['pending', 'initiated'])
                ->sum('amount'),
            'remaining' => max(0, $total - $paid),
            'options' => $this->options($booking),
            'dpPercent' => (int) config('mydream.dp_percent'),
            'payments' => $booking->payments->sortByDesc('id')->values(),
            'snapClientKey' => config('services.midtrans.client_key'),
            'snapScriptUrl' => config('services.midtrans.is_production')
                ? 'https://app.midtrans.com/snap/snap.js'
                : 'https://app.sandbox.midtrans.com/snap/snap.js',
        ]);
    }

    public function pay(Request $request, string $bookingId): JsonResponse
    {
        $booking = $this->ownedBooking($request, $bookingId);
        $validated = $request->validate([
            'scheme' => ['required', Rule::in(array_keys($this->options($booking)))],
        ], [
            'scheme.required' => 'Pilih skema pembayaran terlebih dahulu.',
            'scheme.in' => 'Skema pembayaran tidak valid.',
        ]);

        $serverKey = config('services.midtrans.server_key');
        if (! filled($serverKey) || ! filled(config('services.midtrans.client_key'))) {
            return response()->json([
                'message' => 'Konfigurasi Midtrans belum lengkap. Atur MIDTRANS_SERVER_KEY dan MIDTRANS_CLIENT_KEY di file .env.',
            ], 503);
        }

        $result = DB::transaction(function () use ($booking, $validated, $serverKey) {
            $lockedBooking = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();
            $lockedBooking->syncPaymentStatus();
            $options = $this->options($lockedBooking);

            if (! isset($options[$validated['scheme']])) {
                return ['error' => 'Tidak ada pembayaran yang perlu dilakukan saat ini.'];
            }

            $amount = $options[$validated['scheme']]['amount'];
            $payment = Payment::query()
                ->where('booking_id', $lockedBooking->id)
                ->where('type', $validated['scheme'])
                ->where('amount', $amount)
                ->where('status', 'initiated')
                ->whereIn('transaction_status', ['created', 'pending'])
                ->whereNotNull('snap_token')
                ->latest('id')
                ->first();

            if ($payment) {
                return ['token' => $payment->snap_token];
            }

            $invoice = $lockedBooking->ensureInvoiceNumber();
            $orderId = 'MID-' . $lockedBooking->id . '-' . Str::lower(Str::random(20));
            $payment = Payment::create([
                'booking_id' => $lockedBooking->id,
                'type' => $validated['scheme'],
                'amount' => $amount,
                'method' => 'midtrans',
                'status' => 'initiated',
                'midtrans_order_id' => $orderId,
                'transaction_status' => 'creating',
            ]);

            try {
                $response = Http::withBasicAuth($serverKey, '')
                    ->acceptJson()
                    ->timeout(20)
                    ->post(rtrim(config('services.midtrans.base_url'), '/') . '/snap/v1/transactions', [
                        'transaction_details' => [
                            'order_id' => $orderId,
                            'gross_amount' => $amount,
                        ],
                        'item_details' => [[
                            'id' => $validated['scheme'],
                            'price' => $amount,
                            'quantity' => 1,
                            'name' => mb_substr('Reservasi #' . $lockedBooking->id . ' - ' . $invoice, 0, 50),
                        ]],
                    ]);
            } catch (ConnectionException) {
                Log::warning('Midtrans Snap request could not connect.', [
                    'booking_id' => $lockedBooking->id,
                    'order_id' => $orderId,
                ]);
                $payment->update(['status' => 'rejected', 'transaction_status' => 'request_failed']);

                return ['error' => 'Tidak dapat terhubung ke Midtrans. Silakan coba lagi.'];
            }

            $token = $response->json('token');
            if (! $response->successful() || ! filled($token)) {
                Log::warning('Midtrans Snap rejected a transaction request.', [
                    'booking_id' => $lockedBooking->id,
                    'order_id' => $orderId,
                    'http_status' => $response->status(),
                ]);
                $payment->update(['status' => 'rejected', 'transaction_status' => 'request_failed']);

                return ['error' => 'Midtrans belum dapat membuat transaksi. Periksa konfigurasi key dan coba lagi.'];
            }

            $payment->update([
                'snap_token' => $token,
                'transaction_status' => 'created',
            ]);

            return ['token' => $token];
        });

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], 502);
        }

        return response()->json(['token' => $result['token']]);
    }

    public function notification(Request $request): JsonResponse
    {
        $data = $request->validate([
            'order_id' => ['required', 'string'],
            'status_code' => ['required', 'string'],
            'gross_amount' => ['required', 'numeric', 'gt:0'],
            'signature_key' => ['required', 'string'],
            'transaction_status' => ['required', 'string'],
            'fraud_status' => ['nullable', 'string'],
        ]);

        $serverKey = config('services.midtrans.server_key');
        if (! filled($serverKey)) {
            Log::error('Midtrans notification received without a configured server key.');

            return response()->json(['message' => 'Payment gateway is not configured.'], 503);
        }

        $expectedSignature = hash(
            'sha512',
            $data['order_id'] . $data['status_code'] . $request->input('gross_amount') . $serverKey
        );

        if (! hash_equals($expectedSignature, strtolower($data['signature_key']))) {
            return response()->json(['message' => 'Invalid signature.'], 403);
        }

        $payment = Payment::where('midtrans_order_id', $data['order_id'])->first();
        if (! $payment) {
            return response()->json(['message' => 'Payment not found.'], 404);
        }

        if (number_format((float) $payment->amount, 2, '.', '') !== number_format((float) $data['gross_amount'], 2, '.', '')) {
            Log::warning('Midtrans notification amount does not match the stored payment.', [
                'order_id' => $data['order_id'],
            ]);

            return response()->json(['message' => 'Transaction amount mismatch.'], 422);
        }

        DB::transaction(function () use ($payment, $data) {
            $lockedPayment = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
            $status = $data['transaction_status'];
            $fraudStatus = $data['fraud_status'] ?? null;

            if ($status === 'settlement' || ($status === 'capture' && $fraudStatus === 'accept')) {
                $lockedPayment->update([
                    'status' => 'verified',
                    'verified_at' => $lockedPayment->verified_at ?? now(),
                    'transaction_status' => $status,
                ]);
            } elseif (in_array($status, ['refund', 'partial_refund'], true)) {
                $lockedPayment->update(['status' => 'rejected', 'transaction_status' => $status]);
            } elseif (in_array($status, ['deny', 'cancel', 'expire'], true) || ($status === 'capture' && $fraudStatus === 'deny')) {
                if ($lockedPayment->status !== 'verified') {
                    $lockedPayment->update(['status' => 'rejected', 'transaction_status' => $status]);
                }
            } elseif (in_array($status, ['pending', 'capture', 'challenge'], true)) {
                if (! in_array($lockedPayment->status, ['verified', 'rejected'], true)) {
                    $lockedPayment->update([
                        'status' => 'initiated',
                        'transaction_status' => $status,
                    ]);
                }
            } else {
                $lockedPayment->update(['transaction_status' => $status]);
            }

            $lockedPayment->booking->syncPaymentStatus();
        });

        return response()->json(['message' => 'Notification processed.']);
    }

    /** Rute lama /pembayaran/{id}/sukses: status sekarang ada di halaman pembayaran. */
    public function success(string $bookingId): RedirectResponse
    {
        return redirect()->route('payment.index', $bookingId);
    }

    private function ownedBooking(Request $request, string $bookingId): Booking
    {
        $client = Client::where('users_id', $request->user()->getAuthIdentifier())->first();
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

}
