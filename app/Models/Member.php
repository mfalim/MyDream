<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'member_code',
        'name',
        'call_sign',
        'phone',
        'email',
        'domicile',
        'photo',
        'division',
        'position',
        'specialization',
        'certification',
        'ht_code',
        'emergency_name',
        'emergency_phone',
        'daily_fee',
        'status',
    ];

    protected $casts = [
        'daily_fee' => 'decimal:2',
    ];
}
