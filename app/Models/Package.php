<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'availability_date',
        'guest_capacity',
    ];

    protected $casts = [
        'guest_capacity' => 'integer',
        'availability_date' => 'date',
    ];

    public function getIsActiveAttribute(): bool
    {
        $availabilityDate = $this->getRawOriginal('availability_date');

        return is_string($availabilityDate)
            && $availabilityDate >= now()->toDateString();
    }

    public function getPriceAttribute(): float
    {
        $vendors = $this->relationLoaded('vendors')
            ? $this->vendors
            : $this->vendors()->get();

        return (float) $vendors->sum('price');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'package_vendor')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function getCoverPhotoAttribute(): ?VendorPhoto
    {
        $vendors = $this->relationLoaded('vendors')
            ? $this->vendors
            : $this->vendors()->with('photos')->get();

        foreach ($vendors as $vendor) {
            $photo = $vendor->photos->firstWhere('is_cover', true) ?? $vendor->photos->first();

            if ($photo) {
                return $photo;
            }
        }

        return null;
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
