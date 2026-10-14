<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    protected $fillable = [
        'client_id',
        'package_id',
        'venue_name',
        'venue_address',
        'venue_city',
        'venue_province',
        'venue_maps',
        'guest_count',
        'total_price',
        'notes',
        'status',
        'invoice_number',
        'paid_amount',
        'payment_status',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'package_vendor', 'package_id', 'vendor_id', 'package_id', 'id')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /** Nomor invoice dibuat saat pembayaran pertama dilakukan. */
    public function ensureInvoiceNumber(): string
    {
        if (! $this->invoice_number) {
            do {
                $number = 'MD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
            } while (static::where('invoice_number', $number)->exists());

            $this->update(['invoice_number' => $number]);
        }

        return $this->invoice_number;
    }

    /**
     * Hitung ulang paid_amount dan payment_status dari tabel payments.
     * Panggil setiap kali sebuah payment dibuat, diverifikasi, atau ditolak.
     * Nilai: unpaid | awaiting_verification | dp_paid | paid
     */
    public function syncPaymentStatus(): string
    {
        $payments = $this->payments()->get();
        $paid = (float) $payments->where('status', 'verified')->sum('amount');
        $total = (float) $this->total_price;

        $status = match (true) {
            $payments->where('status', 'pending')->isNotEmpty() => 'awaiting_verification',
            $total > 0 && $paid >= $total => 'paid',
            $paid > 0 => 'dp_paid',
            default => 'unpaid',
        };

        if ($this->payment_status !== $status || (float) $this->paid_amount !== $paid) {
            $this->update(['payment_status' => $status, 'paid_amount' => $paid]);
        }

        return $status;
    }
}
