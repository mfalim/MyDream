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

        Route::post('public-events/{event}/toggle-publish', [PublicEventController::class, 'togglePublish'])
            ->name('public-events.toggle-publish');

        Route::post('public-events/quick-promote/{eventId}', [PublicEventController::class, 'quickPromote'])
            ->name('public-events.quick-promote');

        Route::resource('articles', ArticleController::class)->except(['show']);

        // Cerita dari client: ditinjau (setujui / minta revisi), tidak dibuat dari admin.
        Route::resource('inspirations', InspirationController::class)->only(['index', 'edit', 'update', 'destroy']);

        // Pusat Keuangan: Verifikasi pembayaran transfer klien, input manual, pencairan vendor, & cetak invoice.
        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('payments/manual', [PaymentController::class, 'storeManualPayment'])->name('payments.store-manual');
        Route::get('payments/bookings/{booking}/invoice', [PaymentController::class, 'invoice'])->name('payments.invoice');
        Route::post('payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
        Route::post('payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
        Route::post('payments/schedules/{schedule}/payout', [PaymentController::class, 'updateVendorPayout'])->name('payments.vendor-payout');
    });
