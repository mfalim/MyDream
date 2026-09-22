<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'users_id', 
        'groom_name', 
        'bride_name',
        'groom_phone',
        'bride_phone',
        'email'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public static function findClientByIdUser($idUser) {
        $client = self::select('id')
             ->where('id', $idUser)
             ->first();

        return $client;
    }
}
