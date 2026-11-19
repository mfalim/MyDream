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

        $midtransChanged = false;
        foreach ($payments as $payment) {
            if ($payment->method === 'midtrans' && in_array($payment->status, ['initiated', 'pending'], true)) {
                if ($payment->syncWithMidtrans()) {
                    $midtransChanged = true;
                }
            }
        }

        if ($midtransChanged) {
            $payments = $this->payments()->get();
        }

        $paid = (float) $payments->where('status', 'verified')->sum('amount');
        $total = (float) $this->total_price;
        $hasPending = $payments->where('status', 'pending')->isNotEmpty();

        $status = match (true) {
            $total > 0 && $paid >= $total => 'paid',
            $paid > 0 => 'dp_paid',
            $hasPending => 'awaiting_verification',
            default => 'unpaid',
        };

        $updates = [];
        if ($this->payment_status !== $status || (float) $this->paid_amount !== $paid) {
            $updates['payment_status'] = $status;
            $updates['paid_amount'] = $paid;
        }
        if ($paid > 0 && $this->status === 'pending') {
            $updates['status'] = 'approved';
        }
        if (! empty($updates)) {
            $this->update($updates);
        }

        return $status;
    }

    /**
     * Ringkasan status, nominal DP, sisa tagihan, dan tenggat waktu pembayaran.
     */
    public function paymentSummary(): array
    {
        $payments = $this->relationLoaded('payments') ? $this->payments : $this->payments()->get();
        $events = $this->relationLoaded('events') ? $this->events : $this->events()->get();

        $total = (float) $this->total_price;
        $paid = (float) $this->paid_amount;
        $remaining = max(0, $total - $paid);
        $dpPercent = max(1, min(100, (int) config('mydream.dp_percent', 10)));
        $dpAmount = $remaining > 0
            ? min((int) ceil($total * $dpPercent / 100), (int) round($remaining))
            : 0;
        $percentPaid = $total > 0 ? min(100, (int) round(($paid / $total) * 100)) : 0;

        $pending = (float) $payments
            ->filter(fn (Payment $p) => $p->status === 'pending'
                || ($p->status === 'initiated' && in_array($p->transaction_status, ['pending', 'challenge'], true)))
            ->sum('amount');

        $earliestEventDate = $events->pluck('event_date')->filter()->sort()->first();
        $deadlineDaysBefore = max(1, (int) config('mydream.payment_deadline_days', 14));
        $dpDeadlineHours = max(1, (int) config('mydream.dp_deadline_hours', 24));

        $settlementDeadline = $earliestEventDate
            ? \Carbon\Carbon::parse($earliestEventDate)->subDays($deadlineDaysBefore)->endOfDay()
            : null;

        $deadline = null;
        $deadlineType = null;

        if ($remaining > 0) {
            if ($paid > 0) {
                $deadline = $settlementDeadline ?? ($this->created_at ?? now())->copy()->addDays($deadlineDaysBefore)->endOfDay();
                $deadlineType = 'settlement';
            } else {
                $dpDeadline = ($this->created_at ?? now())->copy()->addHours($dpDeadlineHours);
                $deadline = ($settlementDeadline && $settlementDeadline->lt($dpDeadline)) ? $settlementDeadline : $dpDeadline;
                $deadlineType = 'dp';
            }
        }

        $daysToDeadline = $deadline
            ? (int) now()->startOfDay()->diffInDays($deadline->copy()->startOfDay(), false)
            : null;
        $isOverdue = $deadline ? $deadline->isPast() : false;
        $isUrgent = $deadline && ! $isOverdue && $daysToDeadline !== null && $daysToDeadline <= 7;

        return [
            'status' => $this->payment_status ?: 'unpaid',
            'total' => $total,
            'paid' => $paid,
            'pending' => $pending,
            'remaining' => $remaining,
            'dp_percent' => $dpPercent,
            'dp_amount' => $dpAmount,
            'percent_paid' => $percentPaid,
            'deadline' => $deadline,
            'deadline_type' => $deadlineType,
            'deadline_days_before' => $deadlineDaysBefore,
            'settlement_deadline' => $settlementDeadline,
            'days_to_deadline' => $daysToDeadline,
            'is_overdue' => $isOverdue,
            'is_urgent' => $isUrgent,
        ];
    }

    /**
     * Hitung total harga booking berdasarkan akumulasi seluruh paket pada event-event rangkaian acara
     */
    public function calculateTotalPrice(): float
    {
        $events = $this->events()->with('package')->get();
        if ($events->isNotEmpty() && $events->whereNotNull('package_id')->isNotEmpty()) {
            $totalFromPackages = (float) $events->sum(fn ($e) => (float) ($e->package?->price ?? 0));
            if ($totalFromPackages > 0) {
                return $totalFromPackages;
            }
        }

        if ($this->package) {
            return (float) $this->package->price;
        }

        $vendorTotal = (float) $this->vendors()->sum('price');
        if ($vendorTotal > 0) {
            return $vendorTotal;
        }

        return (float) $this->total_price;
    }

    /**
     * Sinkronkan total_price booking bila berbeda dengan kalkulasi paket rangkaian acara
     */
    public function syncTotalPrice(): float
    {
        $calculated = $this->calculateTotalPrice();
        if (abs((float) $this->total_price - $calculated) > 0.01 && $calculated > 0) {
            $this->update(['total_price' => $calculated]);
        }

        return $calculated;
    }

    /**
     * Kumpulan seluruh paket pernikahan yang dipilih dalam booking (dari booking & seluruh event-nya)
     */
    public function getAllPackagesAttribute()
    {
        $packages = collect();
        if ($this->package) {
            $packages->push($this->package);
        }
        foreach ($this->events as $event) {
            if ($event->package && ! $packages->contains('id', $event->package_id)) {
                $packages->push($event->package);
            }
        }

        return $packages;
    }

    /**
     * Alamat lengkap venue (Nama, Jalan, Kota, Provinsi)
     */
    public function getFullVenueAddressAttribute(): string
    {
        return collect([
            $this->venue_address,
            $this->venue_city,
            $this->venue_province,
        ])->filter(fn ($v) => filled($v))->join(', ');
    }
}
