<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'booking_id',
        'name',
        'photo',
        'event_type',
        'event_date',
        'guest_count',
        'start_time',
        'end_time',
        'package_id',
        'notes',
        'status'
    ];

    protected $casts = [
        'event_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public static function normalizeStatus(?string $status): ?string
    {
        if ($status === null) {
            return null;
        }

        return match ($status) {
            'scheduled' => 'upcoming',
            'draft', 'upcoming', 'ongoing', 'completed', 'cancelled' => $status,
            default => null,
        };
    }

    public static function syncAutomaticStatus(): int
    {
        $updated = 0;
        $now = now();

        foreach (self::whereNotIn('status', ['cancelled'])->get() as $event) {
            $resolvedStatus = $event->resolveAutomaticStatus($now);

            if ($resolvedStatus !== null && $resolvedStatus !== $event->status) {
                $event->status = $resolvedStatus;
                $event->saveQuietly();
                $updated++;
            }
        }

        return $updated;
    }

    public function resolveAutomaticStatus(?Carbon $now = null): ?string
    {
        $now ??= now();

        $currentStatus = self::normalizeStatus($this->status) ?? $this->status;

        if ($currentStatus === 'cancelled') {
            return 'cancelled';
        }

        if ($currentStatus === 'draft') {
            return 'draft';
        }

        if (! $this->event_date) {
            return $currentStatus;
        }

        $startDateTime = Carbon::parse($this->event_date)->setTimeFromTimeString($this->start_time ? $this->start_time->format('H:i:s') : '00:00:00');
        $endDateTime = Carbon::parse($this->event_date)->setTimeFromTimeString($this->end_time ? $this->end_time->format('H:i:s') : '23:59:59');

        if ($currentStatus === 'upcoming') {
            return $now->greaterThanOrEqualTo($startDateTime) ? 'ongoing' : 'upcoming';
        }

        if ($currentStatus === 'ongoing') {
            return $now->greaterThan($endDateTime) ? 'completed' : 'ongoing';
        }

        if ($currentStatus === 'completed') {
            return 'completed';
        }

        return $currentStatus;
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function eventMembers()
    {
        return $this->hasMany(EventMember::class);
    }

    public function members()
    {
        return $this->belongsToMany(Member::class, 'event_members')
            ->withPivot('role', 'notes', 'status')
            ->withTimestamps();
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function getEffectiveStartTimeAttribute(): ?string
    {
        $stored = $this->attributes['start_time'] ?? null;

        if ($stored) {
            return $stored;
        }

        return $this->schedules()
            ->whereNotNull('start_time')
            ->orderBy('start_time')
            ->value('start_time');
    }

    public function getEffectiveEndTimeAttribute(): ?string
    {
        $stored = $this->attributes['end_time'] ?? null;

        if ($stored) {
            return $stored;
        }

        return $this->schedules()
            ->whereNotNull('end_time')
            ->orderByDesc('end_time')
            ->value('end_time');
    }

    public function getEffectiveTimeRangeAttribute(): ?string
    {
        $start = $this->effective_start_time;
        $end = $this->effective_end_time;

        if (! $start && ! $end) {
            return null;
        }

        if ($start && $end) {
            return date('H:i', strtotime($start)) . ' - ' . date('H:i', strtotime($end)) . ' WIB';
        }

        return $start ? date('H:i', strtotime($start)) . ' WIB' : date('H:i', strtotime($end)) . ' WIB';
    }
}
