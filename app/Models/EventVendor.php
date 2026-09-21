<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventVendor extends Model
{
    protected $fillable = [
        'event_id',
        'vendor_id',
        'start_time',
        'end_time',
        'status',
        'notes'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
