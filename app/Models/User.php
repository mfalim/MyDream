<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    protected $fillable = [
    'name',
    'email',
    'google_id',
    'avatar',
    'role',
    'status',
];
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function createOrLogin($googleUser) {
        return self::firstOrCreate(
            ['google_id' => $googleUser->id],
            [
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'avatar' => $googleUser->avatar,
                'role' => 'client',
                'status' => 'approved',
            ]
        );
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'users_id');
    }

    public function bookings()
    {
        return $this->hasManyThrough(Booking::class, Client::class, 'users_id', 'client_id');
    }
}
