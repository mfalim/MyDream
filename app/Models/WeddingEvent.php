<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeddingEvent extends Model
{
    protected $fillable = ['couple', 'venue', 'event_date', 'status', 'image', 'notes'];

    protected $casts = ['event_date' => 'date'];
}
