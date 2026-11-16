<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendor\VendorPortalController;
use App\Http\Controllers\Vendor\VendorProfileController;

Route::get('/', function () {
    return redirect()->route('vendor.dashboard');
});

Route::prefix('vendor')->name('vendor.')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('vendor.dashboard');
    })->name('dashboard');

    // Kalender dan jadwal acara
    Route::get('/kalender', [VendorPortalController::class, 'kalender'])
        ->name('kalender');

    // Tracking tugas
    Route::get('/tracking', [VendorPortalController::class, 'tracking'])
        ->name('tracking');

    // Profil vendor
    Route::get('/profil', [VendorProfileController::class, 'index'])
        ->name('profil');

    Route::get('/profil/edit', [VendorProfileController::class, 'edit'])
        ->name('profil.edit');

    Route::put('/profil', [VendorProfileController::class, 'update'])
        ->name('profil.update');

});
