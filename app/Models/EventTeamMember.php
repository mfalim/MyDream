<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTeamMember extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'role',
        'custom_role',
        'responsibilities',
        'status'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
