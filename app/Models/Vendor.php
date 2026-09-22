<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'name',
        'category_id',
        'price',
        'phone',
        'address',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function booking_vendors()
    {
        return $this->hasMany(BookingVendor::class);
    }

    public function photos()
    {
        return $this->hasMany(VendorPhoto::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_vendor')
            ->withPivot('status')
            ->withTimestamps();
    }
}
