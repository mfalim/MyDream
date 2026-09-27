<?php


use Illuminate\Support\Facades\Route;

Route::prefix('vendor')->group(function () {

    Route::view('/dashboard', 'vendor.dashboard')
        ->name('vendor.dashboard');

    Route::view('/kalender', 'vendor.kalender')
        ->name('vendor.kalender');

    Route::view('/tracking', 'vendor.tracking')
        ->name('vendor.tracking');

    Route::view('/konfirmasi', 'vendor.konfirmasi')
        ->name('vendor.konfirmasi');

    Route::view('/katalog', 'vendor.katalog')
        ->name('vendor.katalog');

    Route::view('/invoice', 'vendor.invoice')
        ->name('vendor.invoice');

    Route::view('/kolaborasi', 'vendor.kolaborasi')
        ->name('vendor.kolaborasi');

    Route::view('/overview', 'vendor.overview')
        ->name('vendor.overview');

    Route::view('/tambah-vendor', 'vendor.tambahvendor')
        ->name('vendor.tambahvendor');

});