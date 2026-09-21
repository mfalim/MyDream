<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\Client\ClientController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/login', function () {
    return view('login.login');
})->name('login');


Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');

// contoh middleware login
Route::middleware('auth')->group(function () {

    Route::get('/login/profile', [ClientController::class, 'profile'])
        ->name('client.profile');

    Route::post('/login/profile', [ClientController::class, 'storeProfile'])
        ->name('client.profile.store');
});



Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('categories', CategoryController::class)
            ->only([
                'index',
                'create',
                'store'
            ]);

        Route::resource('packages', PackageController::class)
            ->only([
                'index',
                'create',
                'store',
                'show',
                'edit',
                'update',
                'destroy'
            ]);

        Route::resource('vendors', VendorController::class)
            ->only([
                'index',
                'create',
                'store',
                'show',
                'edit',
                'update',
                'destroy'
            ]);
        Route::resource('members', MemberController::class)
            ->only([
                'index',
                'create',
                'store',
                'show',
                'edit',
                'update',
                'destroy',
            ]);
        Route::post('/members/options/positions', [MemberController::class, 'storePosition'])
            ->name('members.options.positions.store');
        Route::post('/members/options/specializations', [MemberController::class, 'storeSpecialization'])
            ->name('members.options.specializations.store');
    });
