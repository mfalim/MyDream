<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Payment extends Model
{
    protected $fillable = [
        'booking_id',
        'type',
        'amount',
        'method',
        'bank_info',
        'status',
        'proof_path',
        'admin_note',
        'verified_at',
        'midtrans_order_id',
        'snap_token',
        'transaction_status',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function events()
    {
        return $this->hasMany(PaymentEvent::class)->orderBy('created_at')->orderBy('id');
    }

    public function recordLifecycleEvent(
        string $source,
        ?string $statusFrom,
        ?string $transactionStatusFrom,
        ?string $message = null,
        ?string $eventKey = null,
    ): ?PaymentEvent {
        if ($statusFrom === $this->status && $transactionStatusFrom === $this->transaction_status) {
            return null;
        }

        $eventKey ??= (string) Str::uuid();

        return PaymentEvent::firstOrCreate(
            ['event_key' => $eventKey],
            [
                'payment_id' => $this->id,
                'booking_id' => $this->booking_id,
                'midtrans_order_id' => $this->midtrans_order_id,
                'amount' => $this->amount,
                'source' => $source,
                'status_from' => $statusFrom,
                'status_to' => $this->status,
                'transaction_status_from' => $transactionStatusFrom,
                'transaction_status_to' => $this->transaction_status,
                'message' => $message,
                'created_at' => now(),
            ],
        );
    }

    public function getProofUrlAttribute(): ?string
    {
        return $this->proof_path ? asset('storage/' . ltrim($this->proof_path, '/')) : null;
    }

    public function getSnapRedirectUrlAttribute(): ?string
    {
        if (! filled($this->snap_token)) {
            return null;
        }

        $baseUrl = rtrim(config('services.midtrans.base_url', 'https://app.sandbox.midtrans.com'), '/');

        return $baseUrl . '/snap/v2/vtweb/' . $this->snap_token;
    }

    /**
     * Sinkronkan status transaksi langsung ke REST API Midtrans (/v2/{order_id}/status).
     * Memastikan pembayaran tetap terdeteksi lunas tanpa memerlukan webhook/ngrok.
     */
    public function syncWithMidtrans(): bool
    {
        if ($this->method !== 'midtrans' || ! filled($this->midtrans_order_id)) {
            return false;
        }

        if (in_array($this->status, ['verified', 'rejected'], true)) {
            return false;
        }

        $serverKey = config('services.midtrans.server_key');
        if (! filled($serverKey)) {
            return false;
        }

        $apiUrl = rtrim(config('services.midtrans.api_url', 'https://api.sandbox.midtrans.com'), '/');

        try {
            $response = Http::withBasicAuth($serverKey, '')
                ->acceptJson()
                ->timeout(8)
                ->get($apiUrl . '/v2/' . urlencode($this->midtrans_order_id) . '/status');

            if (! $response->successful()) {
                return false;
            }

            $statusCode = (string) $response->json('status_code', '');
            $txStatus = $response->json('transaction_status');
            $fraudStatus = $response->json('fraud_status');

            if ($statusCode === '404' || ! is_string($txStatus) || $txStatus === '') {
                return false;
            }

            $previousStatus = $this->status;
            $previousTxStatus = $this->transaction_status;

            if ($txStatus === 'settlement' || ($txStatus === 'capture' && $fraudStatus === 'accept')) {
                $this->update([
                    'status' => 'verified',
                    'verified_at' => $this->verified_at ?? now(),
                    'transaction_status' => $txStatus,
                ]);
            } elseif (in_array($txStatus, ['refund', 'partial_refund', 'deny', 'cancel', 'expire'], true) || ($txStatus === 'capture' && $fraudStatus === 'deny')) {
                $this->update([
                    'status' => 'rejected',
                    'transaction_status' => $txStatus,
                ]);
            } elseif (in_array($txStatus, ['pending', 'capture', 'challenge'], true)) {
                $this->update([
                    'status' => filled($this->proof_path) ? 'pending' : 'initiated',
                    'transaction_status' => $txStatus,
                ]);
            }

            if ($previousStatus !== $this->status || $previousTxStatus !== $this->transaction_status) {
                $this->recordLifecycleEvent(
                    'midtrans_sync',
                    $previousStatus,
                    $previousTxStatus,
                    'Status transaksi disinkronkan dari Midtrans (' . $txStatus . ').'
                );

                return true;
            }
        } catch (\Throwable) {
            // Abaikan kegagalan jaringan sementara saat sinkronisasi pasif
        }

        return false;
    }
}
