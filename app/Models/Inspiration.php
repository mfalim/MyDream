<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Inspiration extends Model
{
    public const THEMES = [
        'Adat Jawa Solo', 'Outdoor Modern', 'Ballroom Reception', 'Minimalist Garden',
        'Rustic Terracotta', 'Intimate Dinner', 'Lainnya',
    ];

    protected $fillable = [
        'booking_id', 'package_id', 'title', 'slug', 'theme',
        'couple_names', 'venue_name', 'city', 'event_date', 'guest_count', 'show_couple_names',
        'story', 'tips', 'testimonial', 'rating',
        'status', 'admin_note', 'consented_at', 'submitted_at', 'reviewed_at',
    ];

    protected $casts = [
        'event_date' => 'date',
        'show_couple_names' => 'boolean',
        'consented_at' => 'datetime',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        // Slug dibuat sekali dari judul saat pertama disimpan, dan dijamin unik.
        static::saving(function (Inspiration $item) {
            if (blank($item->slug)) {
                $base = Str::slug($item->title) ?: 'cerita';
                $slug = $base;
                $i = 2;

                while (static::where('slug', $slug)->where('id', '!=', $item->id ?? 0)->exists()) {
                    $slug = $base . '-' . $i++;
                }

                $item->slug = $slug;
            }
        });
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function photos()
    {
        return $this->hasMany(InspirationPhoto::class)->orderBy('sort_order')->orderBy('id');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function getCoverAttribute(): ?string
    {
        return $this->photos->first()?->photo;
    }

    /** Nama yang boleh tampil di publik: nama asli atau disamarkan sesuai pilihan client. */
    public function getDisplayCoupleAttribute(): string
    {
        return $this->show_couple_names ? $this->couple_names : 'Pasangan MyDream';
    }

    public function getStatusLabelAttribute(): string
    {
        return ['pending' => 'Menunggu Tinjauan', 'approved' => 'Tayang', 'rejected' => 'Perlu Revisi'][$this->status] ?? $this->status;
    }

    /** Vendor yang benar-benar bertugas di acara ini (dari jadwal acara pesanan). */
    public function usedVendors(): Collection
    {
        if (! $this->booking) {
            return collect();
        }

        return $this->booking->events
            ->flatMap(fn ($event) => $event->schedules)
            ->map(fn ($schedule) => $schedule->vendor)
            ->filter()
            ->unique('id')
            ->values();
    }
}
