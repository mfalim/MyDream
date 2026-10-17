<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Event publik yang tampil di halaman /event. Dikelola admin.
 * Status (akan datang / berlangsung / selesai) dihitung otomatis dari tanggal.
 */
class PublicEvent extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'photo',
        'starts_on',
        'ends_on',
        'start_time',
        'end_time',
        'location',
        'is_published',
    ];

    protected $casts = [
        'starts_on' => 'date',
        'ends_on' => 'date',
        'is_published' => 'boolean',
    ];

    protected static function booted(): void
    {
        // Slug dibuat otomatis dari judul bila kosong, dan dijamin unik.
        static::saving(function (PublicEvent $event) {
            if (blank($event->slug)) {
                $base = Str::slug($event->title) ?: 'event';
                $slug = $base;
                $i = 2;

                while (static::where('slug', $slug)->where('id', '!=', $event->id ?? 0)->exists()) {
                    $slug = $base . '-' . $i++;
                }

                $event->slug = $slug;
            }
        });
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'public_event_vendor');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /** upcoming | ongoing | finished */
    public function getStatusAttribute(): string
    {
        $today = today();
        $end = $this->ends_on ?? $this->starts_on;

        return match (true) {
            $end->lt($today) => 'finished',
            $this->starts_on->gt($today) => 'upcoming',
            default => 'ongoing',
        };
    }

    /** "14 November 2026" atau "14 - 15 November 2026" */
    public function getDateLabelAttribute(): string
    {
        $start = $this->starts_on->copy()->locale('id');
        $end = ($this->ends_on ?? $this->starts_on)->copy()->locale('id');

        if ($start->isSameDay($end)) {
            return $start->translatedFormat('d F Y');
        }

        return $start->isSameMonth($end)
            ? $start->translatedFormat('d') . ' - ' . $end->translatedFormat('d F Y')
            : $start->translatedFormat('d F') . ' - ' . $end->translatedFormat('d F Y');
    }

    /** "10.00 - 20.00 WIB" atau null bila jam belum diisi */
    public function getTimeLabelAttribute(): ?string
    {
        if (! $this->start_time) {
            return null;
        }

        $format = fn ($time) => str_replace(':', '.', substr($time, 0, 5));

        return $format($this->start_time)
            . ($this->end_time ? ' - ' . $format($this->end_time) : '')
            . ' WIB';
    }
}
