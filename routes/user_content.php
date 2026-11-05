<?php

use App\Http\Controllers\User\BillingController;
use App\Http\Controllers\User\InspirationController;
use Illuminate\Support\Facades\Route;

// Dashboard client: tagihan & pembayaran, serta menulis cerita/inspirasi (setelah tanggal acara lewat).
Route::prefix('user')
    ->name('user.')
    ->middleware('auth')
    ->group(function () {
        Route::get('/tagihan', [BillingController::class, 'index'])->name('billing');

        Route::get('/inspirasi', [InspirationController::class, 'index'])->name('inspiration');
        Route::get('/inspirasi/{booking}', [InspirationController::class, 'form'])->whereNumber('booking')->name('inspiration.form');
        Route::post('/inspirasi/{booking}', [InspirationController::class, 'save'])->whereNumber('booking')->name('inspiration.save');
    });
