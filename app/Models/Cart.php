<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id', 'type', 'package_id'];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'cart_vendor');
    }
}
