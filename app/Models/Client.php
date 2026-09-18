<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'users_id', 'full_name', 
        'address', 'phone'
    ];

    public static function findClientByIdUser($idUser) {
        $client = self::select('id')
             ->where('id', $idUser)
             ->first();

        return $client;
    }
}
