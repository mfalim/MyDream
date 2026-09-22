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

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
