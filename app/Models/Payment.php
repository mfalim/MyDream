<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
}
