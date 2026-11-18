<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function isCatering(): bool
    {
        return Str::contains(Str::lower($this->name), ['catering', 'katering']);
    }
}
