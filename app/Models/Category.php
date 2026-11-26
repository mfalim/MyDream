<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'image',
    ];

    protected $appends = [
        'image_url',
    ];

    public function vendors()
    {
        return $this->hasMany(Vendor::class, 'category_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Media::url($this->image) : null;
    }

    public function isCatering(): bool
    {
        return Str::contains(Str::lower($this->name), ['catering', 'katering']);
    }
}
