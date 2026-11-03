<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InspirationPhoto extends Model
{
    protected $fillable = ['inspiration_id', 'photo', 'sort_order'];

    public function inspiration()
    {
        return $this->belongsTo(Inspiration::class);
    }
}
