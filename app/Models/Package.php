<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'availability_date',
        'guest_capacity',
        'duration',
        'photo',
    ];

    protected $casts = [
        'guest_capacity' => 'integer',
        'availability_date' => 'date',
        'duration' => 'decimal:2',
    ];

    public function getIsActiveAttribute(): bool
    {
        $availabilityDate = $this->getRawOriginal('availability_date');

        return is_string($availabilityDate) && $availabilityDate >= now()->toDateString();
    }

    public function getPriceAttribute(): float
    {
        $vendors = $this->relationLoaded('vendors') ? $this->vendors : $this->vendors()->get();

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

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
