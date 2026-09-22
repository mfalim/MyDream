<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inspiration extends Model
{
    protected $fillable = ['title', 'theme', 'image', 'description'];
}
