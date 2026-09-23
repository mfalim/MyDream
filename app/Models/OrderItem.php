<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'package_id', 'vendor_id', 'title', 'price', 'quantity'];

    protected $casts = ['price' => 'decimal:2'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
