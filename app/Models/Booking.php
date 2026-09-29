<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'client_id',
        'package_id',
        'venue_name',
        'venue_address',
        'venue_city',
        'venue_province',
        'venue_maps',
        'guest_count',
        'total_price',
        'notes',
        'status'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'package_vendor', 'package_id', 'vendor_id', 'package_id', 'id')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
