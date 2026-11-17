<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorPhoto extends Model
{
    protected $fillable = [
        'vendor_id',
        'photo',
        'is_cover',
        'sort_order',
    ];

    protected $casts = [
        'is_cover' => 'boolean',
    ];

    protected $appends = [
        'url',
    ];

    public function getUrlAttribute(): ?string
    {
        if (! $this->photo) {
            return null;
        }

        return str_starts_with($this->photo, 'http')
            ? $this->photo
            : asset('storage/' . ltrim($this->photo, '/'));
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
