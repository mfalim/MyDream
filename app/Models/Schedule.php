<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'event_id',
        'vendor_id',
        'price',
        'member_id',
        'activity',
        'location',
        'start_time',
        'end_time',
        'notes',
        'status',
        'agreed_fee',
        'paid_amount',
        'payment_status',
        'paid_at',
        'payment_notes',
        'payout_proof_path',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'agreed_fee' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function getPayoutProofUrlAttribute(): ?string
    {
        return $this->payout_proof_path ? asset('storage/' . ltrim($this->payout_proof_path, '/')) : null;
    }

    public function getEffectiveFeeAttribute(): float
    {
        if ($this->agreed_fee !== null && (float) $this->agreed_fee > 0) {
            return (float) $this->agreed_fee;
        }

        if ($this->price !== null && (float) $this->price > 0) {
            return (float) $this->price;
        }

        return (float) ($this->vendor?->price ?? 0);
    }

    public function getRemainingFeeAttribute(): float
    {
        return max(0, $this->effective_fee - (float) ($this->paid_amount ?? 0));
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
