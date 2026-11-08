<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['event_id', 'client_id', 'rating', 'comment'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
