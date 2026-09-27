<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\Client\ClientController;

Route::get('/login', function () {
    return view('login.login');
})->name('login');

Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.redirect');

Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name(
    'google.callback',
);

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/login/profile', [ClientController::class, 'profile'])->name('client.profile');

    Route::post('/login/profile', [ClientController::class, 'storeProfile'])->name(
        'client.profile.store',
    );
});

require __DIR__ . '/frontend.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/user.php';
