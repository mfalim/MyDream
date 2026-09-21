<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'event_id',
        'activity',
        'location',
        'start_time',
        'end_time',
        'notes',
        'status'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
