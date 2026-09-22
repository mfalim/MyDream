<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'booking_id',
        'name',
        'photo',
        'event_type',
        'event_date',
        'guest_count',
        'start_time',
        'end_time',
        'package_id',
        'notes',
        'status'
    ];

    protected $casts = [
        'event_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function eventMembers()
    {
        return $this->hasMany(EventMember::class);
    }

    public function members()
    {
        return $this->belongsToMany(Member::class, 'event_members')
            ->withPivot('role', 'notes', 'status')
            ->withTimestamps();
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
