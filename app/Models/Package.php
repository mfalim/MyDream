<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'price',
        'photo',
    ];

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
}
