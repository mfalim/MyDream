<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventMember extends Model
{
    protected $fillable = [
        'event_id',
        'member_id',
        'role',
        'notes',
        'status',
        'attendance_status',
        'actual_hours',
        'allowance',
        'total_fee',
        'payment_status',
    ];

    protected $casts = [
        'actual_hours' => 'decimal:2',
        'allowance' => 'decimal:2',
        'total_fee' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $eventMember) {
            $eventMember->calculateTotalFee();
        });
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function isCoordinator(): bool
    {
        $role = strtolower(trim((string) $this->role));

        return str_contains($role, 'coordinator')
            || str_contains($role, 'director')
            || str_contains($role, 'lead');
    }

    public function calculateTotalFee(): float
    {
        $dailyFee = (float) ($this->member?->daily_fee ?? 0);
        if ($dailyFee <= 0) {
            $dailyFee = $this->isCoordinator() ? 150000 : 100000;
        }

        $actualHours = (float) ($this->actual_hours ?? 0);
        $allowance = (float) ($this->allowance ?? 0);

        if ($this->attendance_status === 'absent') {
            $this->total_fee = 0;
        } else {
            $hourlyRate = $dailyFee > 0 ? ($dailyFee / 8) : 0;
            $workFee = $actualHours * $hourlyRate;

            $this->total_fee = round($workFee + $allowance, 2);
        }

        if (empty($this->payment_status)) {
            $this->payment_status = 'pending';
        }

        return (float) $this->total_fee;
    }
}
