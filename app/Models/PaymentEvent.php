<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'payment_id',
        'booking_id',
        'midtrans_order_id',
        'amount',
        'event_key',
        'source',
        'status_from',
        'status_to',
        'transaction_status_from',
        'transaction_status_to',
        'message',
        'created_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
