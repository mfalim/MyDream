<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'category',
        'phone',
        'address',
        'description',
        'photo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_vendor')
            ->withPivot('status')
            ->withTimestamps();
    }
}
