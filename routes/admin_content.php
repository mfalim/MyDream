<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\PublicEventController;
use App\Http\Middleware\EnsureUserRole;
use Illuminate\Support\Facades\Route;

// Konten website yang dikelola admin. Rute di sini WAJIB login dengan role admin.
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', EnsureUserRole::class . ':admin'])
    ->group(function () {
        Route::resource('public-events', PublicEventController::class)
            ->parameters(['public-events' => 'event'])
            ->except(['show']);

        Route::resource('articles', ArticleController::class)->except(['show']);
    });
