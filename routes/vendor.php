<?php

use App\Http\Controllers\Vendor\VendorPortalController;
use App\Http\Controllers\Vendor\VendorProfileController;
use App\Http\Middleware\EnsureUserRole;
use Illuminate\Support\Facades\Route;

Route::prefix('vendor')
    ->name('vendor.')
    ->middleware(['auth', EnsureUserRole::class . ':vendor'])
    ->group(function () {
        Route::get('/dashboard', [VendorPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/kalender', [VendorPortalController::class, 'kalender'])->name('kalender');
        Route::get('/tracking', [VendorPortalController::class, 'tracking'])->name('tracking');
        Route::get('/kas', [VendorPortalController::class, 'kas'])->name('kas');

        Route::get('/profil', [VendorProfileController::class, 'index'])->name('profil');
        Route::get('/profil/edit', [VendorProfileController::class, 'edit'])->name('profil.edit');
        Route::put('/profil', [VendorProfileController::class, 'update'])->name('profil.update');
    });
