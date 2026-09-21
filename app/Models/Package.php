<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'availability_date',
        'duration',
        'guest_capacity',
        'photo',
    ];

    protected $casts = [
        'duration' => 'decimal:2',
        'guest_capacity' => 'integer',
        'availability_date' => 'date',
    ];

    public function getIsActiveAttribute(): bool
    {
        $availabilityDate = $this->getRawOriginal('availability_date');

        return is_string($availabilityDate)
            && $availabilityDate >= now()->toDateString();
    }

    public function getDurationLabelAttribute(): string
    {
        if (!$this->duration) {
            return '-';
        }

        $duration = rtrim(rtrim(number_format((float) $this->duration, 2, '.', ''), '0'), '.');

        return $duration . ' jam';
    }

    public function getPriceAttribute(): float
    {
        $vendors = $this->relationLoaded('vendors')
            ? $this->vendors
            : $this->vendors()->get();

        return (float) $vendors->sum('price');
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'package_vendor')
            ->withPivot('status')
            ->withTimestamps();
    }
    //
}
