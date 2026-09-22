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

    protected $casts = [
        'price' => 'decimal:2',
    ];

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
