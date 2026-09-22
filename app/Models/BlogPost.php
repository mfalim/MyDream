<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = ['slug', 'title', 'category', 'excerpt', 'content', 'image', 'published_at'];

    protected $casts = ['published_at' => 'datetime'];
}
