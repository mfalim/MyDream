<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendor\LayananController;

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

    Route::get('/katalog', [LayananController::class, 'index'])
        ->name('katalog');

    Route::get('/tambahlayanan', function () {
        return view('vendor.tambahlayanan');
    })->name('tambahlayanan');

    Route::post('/layanan/store', [LayananController::class, 'store'])
        ->name('layanan.store');

    Route::get('/layanan/{id}/edit', [LayananController::class, 'edit'])
        ->name('layanan.edit');

    Route::put('/layanan/{id}', [LayananController::class, 'update'])
        ->name('layanan.update');

    Route::delete('/layanan/{id}', [LayananController::class, 'destroy'])
        ->name('layanan.destroy');

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