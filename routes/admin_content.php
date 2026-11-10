<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\InspirationController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PublicEventController;
use App\Http\Middleware\EnsureUserRole;
use Illuminate\Support\Facades\Route;

// Konten & operasional yang dikelola admin. Rute di sini WAJIB login dengan role admin.
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', EnsureUserRole::class . ':admin'])
    ->group(function () {
        Route::resource('public-events', PublicEventController::class)
            ->parameters(['public-events' => 'event'])
            ->except(['show']);

        Route::resource('articles', ArticleController::class)->except(['show']);

        // Cerita dari client: ditinjau (setujui / minta revisi), tidak dibuat dari admin.
        Route::resource('inspirations', InspirationController::class)->only(['index', 'edit', 'update', 'destroy']);

        // Verifikasi pembayaran transfer.
        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
        Route::post('payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
    });
