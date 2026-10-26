<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function getProofUrlAttribute(): ?string
    {
        return $this->proof_path ? asset('storage/' . ltrim($this->proof_path, '/')) : null;
    }
}
