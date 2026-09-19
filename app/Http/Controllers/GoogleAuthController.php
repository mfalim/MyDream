<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
{
    $googleUser = Socialite::driver('google')->user();

    $user = User::createOrLogin($googleUser);

    Auth::login($user);

    if ($user->status === 'pending') {
        Auth::logout();

        return redirect('/login')
            ->with('error', 'Akun Anda masih menunggu verifikasi admin.');
    }

    if ($user->status === 'rejected') {
        Auth::logout();

        return redirect('/login')
            ->with('error', 'Akun Anda ditolak oleh admin.');
    }
    
    if ($user->role == 'client' && is_null(Client::findClientByIdUser($user->id))) {
        return redirect('/login/profile');
    }

    return match ($user->role) {
        'admin' => redirect('/admin/dashboard'),
        'vendor' => redirect('/vendor/dashboard'),
        'client' => redirect('/client/dashboard'),
    };
}
}
