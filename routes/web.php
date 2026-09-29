<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('vendor.dashboard');
});

Route::prefix('vendor')->name('vendor.')->group(function () {

    Route::get('/dashboard', function () {
        return view('vendor.dashboard');
    })->name('dashboard');

    Route::get('/kalender', function () {
        return view('vendor.kalender');
    })->name('kalender');

    Route::get('/tracking', function () {
        return view('vendor.tracking');
    })->name('tracking');

    Route::get('/konfirmasi', function () {
        return view('vendor.konfirmasi');
    })->name('konfirmasi');

    Route::get('/katalog', function () {
        return view('vendor.katalog');
    })->name('katalog');

    Route::get('/tambahlayanan', function () {
        return view('vendor.tambahlayanan');
    })->name('tambahlayanan');

    Route::get('/invoice', function () {
        return view('vendor.invoice');
    })->name('invoice');

    Route::get('/kolaborasi', function () {
        return view('vendor.kolaborasi');
    })->name('kolaborasi');

    Route::get('/overview', function () {
        return view('vendor.overview');
    })->name('overview');

});
