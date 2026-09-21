<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizerEvent extends Model
{
    protected $fillable = [
        'title',
        'description',
        'event_date',
        'start_time',
        'end_time',
        'event_type',
        'location',
        'priority',
        'attendees',
        'status',
        'notes'
    ];

    protected $casts = [
        'event_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];
}
