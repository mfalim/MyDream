<?php

use App\Http\Controllers\User\InspirationController;
use Illuminate\Support\Facades\Route;

// Dashboard client: menulis cerita/inspirasi pernikahan (hanya setelah tanggal acara lewat).
Route::prefix('user')
    ->name('user.')
    ->middleware('auth')
    ->group(function () {
        Route::get('/inspirasi', [InspirationController::class, 'index'])->name('inspiration');
        Route::get('/inspirasi/{booking}', [InspirationController::class, 'form'])->whereNumber('booking')->name('inspiration.form');
        Route::post('/inspirasi/{booking}', [InspirationController::class, 'save'])->whereNumber('booking')->name('inspiration.save');
    });
