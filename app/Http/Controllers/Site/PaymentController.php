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

        $summary = $booking->paymentSummary();

        return view('site.payment.show', [
            'booking' => $booking,
            'event' => $booking->events->first(),
            'total' => $summary['total'],
            'paid' => $summary['paid'],
            'pending' => $summary['pending'],
            'remaining' => $summary['remaining'],
            'paymentSummary' => $summary,
            'options' => $this->options($booking),
            'dpPercent' => $summary['dp_percent'],
            'bank' => $this->bank(),
            'payments' => $booking->payments->sortByDesc('id')->values(),
            'snapClientKey' => config('services.midtrans.client_key'),
            'snapScriptUrl' => config('services.midtrans.is_production')
                ? 'https://app.midtrans.com/snap/snap.js'
                : 'https://app.sandbox.midtrans.com/snap/snap.js',
        ]);
    }

    public function pay(Request $request, string $bookingId): JsonResponse|RedirectResponse
    {
        $booking = $this->ownedBooking($request, $bookingId);
        $booking->syncPaymentStatus();

        // Jika pengguna mengunggah foto bukti pembayaran opsional (untuk melampirkan ke transaksi Midtrans)
        if ($request->hasFile('proof')) {
            $options = $this->options($booking);
            $paymentId = $request->integer('payment_id');

            if (empty($options) && ! $paymentId) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Tidak ada pembayaran yang perlu dilakukan saat ini.'], 422);
                }

                return back()->with('error', 'Tagihan reservasi ini sudah lunas atau tidak memerlukan pembayaran lagi.');
            }

            $allowedSchemes = ! empty($options) ? array_keys($options) : ['dp', 'full', 'remaining'];

            $data = $request->validate([
                'payment_id' => ['nullable', 'integer'],
                'scheme' => [$paymentId ? 'nullable' : 'required', Rule::in($allowedSchemes)],
                'payment_channel' => ['nullable', Rule::in(['bank_transfer', 'midtrans'])],
                'proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            ], [
                'scheme.required' => 'Pilih skema pembayaran terlebih dahulu.',
                'scheme.in' => 'Skema pembayaran tidak valid untuk status tagihan saat ini.',
                'proof.required' => 'Bukti pembayaran wajib diunggah.',
                'proof.mimes' => 'Bukti pembayaran harus berupa JPG, PNG, atau PDF.',
                'proof.max' => 'Ukuran bukti pembayaran maksimal 4 MB.',
            ]);

            $proofPath = $request->file('proof')?->store('payment-proofs', 'public');
            $scheme = $data['scheme'] ?? array_key_first($options) ?? 'dp';
            $chosenAmount = $options[$scheme]['amount'] ?? 0;

            $payment = DB::transaction(function () use ($booking, $paymentId, $scheme, $chosenAmount, $proofPath) {
                $booking->ensureInvoiceNumber();

                $existingMidtrans = Payment::query()
                    ->where('booking_id', $booking->id)
                    ->where('method', 'midtrans')
                    ->when($paymentId, fn ($q) => $q->whereKey($paymentId), fn ($q) => $q
                        ->whereIn('status', ['initiated', 'pending', 'verified'])
                        ->whereNull('proof_path'))
                    ->latest('id')
                    ->first();

                if ($existingMidtrans) {
                    $previousStatus = $existingMidtrans->status;
                    $previousTx = $existingMidtrans->transaction_status;

                    $existingMidtrans->syncWithMidtrans();
                    $existingMidtrans->update([
                        'proof_path' => $proofPath,
                        'status' => 'verified',
                        'verified_at' => $existingMidtrans->verified_at ?? now(),
                        'transaction_status' => 'settlement',
                    ]);

                    $existingMidtrans->recordLifecycleEvent(
                        'midtrans_proof_upload',
                        $previousStatus,
                        $previousTx,
                        'Pembayaran Midtrans dikonfirmasi dan diverifikasi otomatis.'
                    );

                    $booking->syncPaymentStatus();

                    return $existingMidtrans->fresh();
                }

                $newPayment = Payment::create([
                    'booking_id' => $booking->id,
                    'type' => $scheme,
                    'amount' => $chosenAmount,
                    'method' => 'midtrans',
                    'bank_info' => 'Midtrans Payment Gateway',
                    'status' => 'verified',
                    'verified_at' => now(),
                    'proof_path' => $proofPath,
                    'transaction_status' => 'settlement',
                ]);

                $newPayment->recordLifecycleEvent(
                    'midtrans_proof_upload',
                    null,
                    null,
                    'Bukti pembayaran Midtrans diunggah oleh klien.'
                );

                $booking->syncPaymentStatus();

                return $newPayment;
            });

            if ($request->expectsJson()) {
                $booking->refresh()->load(['package', 'payments']);

                return response()->json([
                    'success' => true,
                    'message' => 'Bukti pembayaran berhasil dikirim.',
                    'data' => [
                        'booking' => $booking,
                        'payment' => $payment,
                    ],
                ], 201);
            }

            $statusMessage = $payment->status === 'verified'
                ? 'Pembayaran terverifikasi lunas dan bukti pembayaran berhasil disimpan!'
                : 'Bukti transfer berhasil dikirim dan sedang menunggu verifikasi admin.';

            return redirect()
                ->route('payment.index', $booking->id)
                ->with('success', $statusMessage);
        }

        $options = $this->options($booking);
        if (empty($options)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Tidak ada pembayaran yang perlu dilakukan saat ini.'], 422);
            }

            return back()->with('error', 'Tidak ada pembayaran yang perlu dilakukan saat ini.');
        }

        // Pembayaran melalui Midtrans Snap
        $validated = $request->validate([
            'scheme' => ['required', Rule::in(array_keys($options))],
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
                ->whereIn('status', ['initiated', 'pending'])
                ->whereIn('transaction_status', ['created', 'pending', 'capture', 'challenge'])
                ->whereNotNull('snap_token')
                ->latest('id')
                ->first();

            if ($payment) {
                $isStale = $payment->created_at && $payment->created_at->lt(now()->subHours(20));

                if (! $isStale) {
                    // Cek status terbaru ke Midtrans agar token yang sudah expire/cancel tidak dipakai ulang
                    $payment->syncWithMidtrans();
                    $payment->refresh();
                }

                $stillUsable = ! $isStale
                    && in_array($payment->status, ['initiated', 'pending'], true)
                    && in_array($payment->transaction_status, ['created', 'pending', 'capture', 'challenge'], true);

                if ($stillUsable) {
                    return [
                        'token' => $payment->snap_token,
                        'redirect_url' => $payment->snap_redirect_url,
                        'order_id' => $payment->midtrans_order_id,
                        'payment_id' => $payment->id,
                    ];
                }

                if (in_array($payment->status, ['initiated', 'pending'], true)) {
                    $previousStatus = $payment->status;
                    $previousTx = $payment->transaction_status;
                    $payment->update([
                        'status' => 'rejected',
                        'transaction_status' => 'expire',
                    ]);
                    $payment->recordLifecycleEvent(
                        'midtrans',
                        $previousStatus,
                        $previousTx,
                        'Token Midtrans lama kedaluwarsa, membuat transaksi baru.'
                    );
                }
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
            $payment->recordLifecycleEvent('midtrans', null, null, 'Transaksi Midtrans dibuat.');

            $snapBaseUrl = rtrim(config('services.midtrans.base_url', 'https://app.sandbox.midtrans.com'), '/');
            // Pastikan pembuatan transaksi Snap menuju ke app.(sandbox.)midtrans.com
            $snapBaseUrl = str_replace('://api.sandbox.midtrans.com', '://app.sandbox.midtrans.com', $snapBaseUrl);
            $snapBaseUrl = str_replace('://api.midtrans.com', '://app.midtrans.com', $snapBaseUrl);

            try {
                $response = Http::withBasicAuth($serverKey, '')
                    ->acceptJson()
                    ->withOptions(['curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4]])
                    ->timeout(20)
                    ->post($snapBaseUrl . '/snap/v1/transactions', [
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
            } catch (ConnectionException $e) {
                Log::warning('Midtrans Snap request could not connect.', [
                    'booking_id' => $lockedBooking->id,
                    'order_id' => $orderId,
                    'url' => $snapBaseUrl . '/snap/v1/transactions',
                    'error' => $e->getMessage(),
                ]);
                $previousStatus = $payment->status;
                $previousTransactionStatus = $payment->transaction_status;
                $payment->update(['status' => 'rejected', 'transaction_status' => 'request_failed']);
                $payment->recordLifecycleEvent('midtrans', $previousStatus, $previousTransactionStatus, 'Midtrans gagal membuat transaksi.');

                return ['error' => 'Tidak dapat terhubung ke Midtrans. Silakan coba lagi.'];
            }

            $token = $response->json('token');
            if (! $response->successful() || ! filled($token)) {
                Log::warning('Midtrans Snap rejected a transaction request.', [
                    'booking_id' => $lockedBooking->id,
                    'order_id' => $orderId,
                    'http_status' => $response->status(),
                ]);
                $previousStatus = $payment->status;
                $previousTransactionStatus = $payment->transaction_status;
                $payment->update(['status' => 'rejected', 'transaction_status' => 'request_failed']);
                $payment->recordLifecycleEvent('midtrans', $previousStatus, $previousTransactionStatus, 'Midtrans menolak pembuatan transaksi.');

                return ['error' => 'Midtrans belum dapat membuat transaksi. Periksa konfigurasi key dan coba lagi.'];
            }

            $previousStatus = $payment->status;
            $previousTransactionStatus = $payment->transaction_status;
            $payment->update([
                'snap_token' => $token,
                'transaction_status' => 'created',
            ]);
            $payment->recordLifecycleEvent('midtrans', $previousStatus, $previousTransactionStatus, 'Token pembayaran Midtrans berhasil dibuat.');

            return [
                'token' => $token,
                'redirect_url' => $payment->snap_redirect_url,
                'order_id' => $payment->midtrans_order_id,
                'payment_id' => $payment->id,
            ];
        });

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], 502);
        }

        return response()->json([
            'token' => $result['token'],
            'redirect_url' => $result['redirect_url'] ?? null,
            'order_id' => $result['order_id'] ?? null,
            'payment_id' => $result['payment_id'] ?? null,
            'data' => $result,
        ]);
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

        $notifyStatus = null;
        DB::transaction(function () use ($payment, $data, &$notifyStatus) {
            $lockedPayment = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
            $previousStatus = $lockedPayment->status;
            $previousTransactionStatus = $lockedPayment->transaction_status;
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

            $message = match ($status) {
                'settlement' => 'Pembayaran berhasil dikonfirmasi Midtrans.',
                'capture' => ($fraudStatus === 'accept' ? 'Pembayaran diterima Midtrans.' : 'Pembayaran sedang diproses Midtrans.'),
                'pending' => 'Transaksi menunggu pembayaran di Midtrans.',
                'challenge' => 'Transaksi sedang diperiksa Midtrans.',
                'expire' => 'Transaksi kedaluwarsa.',
                'cancel' => 'Transaksi dibatalkan.',
                'deny' => 'Transaksi ditolak Midtrans.',
                'refund' => 'Transaksi dikembalikan oleh Midtrans.',
                'partial_refund' => 'Sebagian pembayaran dikembalikan oleh Midtrans.',
                default => 'Status transaksi diperbarui oleh Midtrans.',
            };
            $eventKey = hash('sha256', implode('|', [
                $data['order_id'],
                $data['status_code'],
                (string) $data['gross_amount'],
                $status,
                $fraudStatus ?? '',
            ]));
            $lockedPayment->recordLifecycleEvent(
                'midtrans_notification',
                $previousStatus,
                $previousTransactionStatus,
                $message,
                $eventKey,
            );

            $lockedPayment->booking->syncPaymentStatus();

            if ($previousStatus !== $lockedPayment->status && in_array($lockedPayment->status, ['verified', 'rejected'], true)) {
                $notifyStatus = $lockedPayment->status;
            }
        });

        if ($notifyStatus !== null) {
            $payment->loadMissing('booking.client.user');
            if ($payment->booking) {
                $refLabel = $payment->booking->invoice_number ?: ('Booking #' . $payment->booking->id);
                $formattedAmount = 'Rp ' . number_format((float) $payment->amount, 0, ',', '.');
                app(\App\Services\NotificationService::class)->notifyBookingClient(
                    $payment->booking,
                    'payment',
                    $notifyStatus === 'verified' ? 'Pembayaran Berhasil Dikonfirmasi' : 'Transaksi Pembayaran Gagal',
                    $notifyStatus === 'verified'
                        ? "Pembayaran Midtrans {$formattedAmount} untuk {$refLabel} telah terkonfirmasi otomatis."
                        : "Transaksi pembayaran {$formattedAmount} untuk {$refLabel} tidak berhasil atau kedaluwarsa.",
                    '/my-bookings'
                );
            }
        }

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
        $paid = (float) $booking->paid_amount;
        $remaining = max(0, $total - $paid);
        $percent = max(1, min(100, (int) config('mydream.dp_percent', 10)));

        if ($total <= 0 || $remaining <= 0) {
            return [];
        }

        if ($paid <= 0) {
            $dpAmount = min((int) ceil($total * $percent / 100), (int) round($remaining));

            $options = [
                'dp' => [
                    'label' => "DP {$percent}%",
                    'amount' => $dpAmount,
                    'note' => 'Kunci tanggal sekarang, sisanya dilunasi sebelum hari acara.',
                ],
            ];

            if ($dpAmount < (int) round($remaining)) {
                $options['full'] = [
                    'label' => 'Bayar Lunas',
                    'amount' => (int) round($remaining),
                    'note' => 'Selesaikan seluruh pembayaran sekaligus.',
                ];
            }

            return $options;
        }

        return [
            'remaining' => [
                'label' => 'Pelunasan Sisa',
                'amount' => (int) round($remaining),
                'note' => 'Sisa pembayaran setelah DP / pembayaran sebelumnya.',
            ],
        ];
    }

    /** Rekening transfer dari config/mydream.php; null bila belum diatur di .env. */
    private function bank(): ?array
    {
        $bank = config('mydream.bank');
        if (! filled($bank['name'] ?? null) || ! filled($bank['number'] ?? null)) {
            return null;
        }

        return [
            'name' => $bank['name'],
            'number' => $bank['number'],
            'holder' => $bank['holder'] ?? '',
        ];
    }
}
