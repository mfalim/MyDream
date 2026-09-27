<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\User\RundownController;
use App\Http\Controllers\User\ExploreController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\VendorConfirmationController;
use App\Http\Controllers\User\EventController;

Route::prefix('user')
    ->name('user.')
    ->middleware('auth')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | 1. DASHBOARD PERNIKAHAN
        |--------------------------------------------------------------------------
        */
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | 2. KONFIRMASI VENDOR
        |--------------------------------------------------------------------------
        */
        Route::get('/konfirmasi-vendor', [VendorConfirmationController::class, 'index'])
            ->name('vendor-confirmation');

        /*
        |--------------------------------------------------------------------------
        | 3. EKSPLOR & TAMBAH VENDOR
        |--------------------------------------------------------------------------
        */
        Route::get('/explore-vendor', [ExploreController::class, 'index'])
            ->name('explore-vendor')
            ->withoutMiddleware('auth');

        Route::get('/explore-vendor/vendor/{id}', [ExploreController::class, 'show'])
            ->name('vendor-overview')
            ->withoutMiddleware('auth');

        Route::get('/explore-vendor/package/{id}', [ExploreController::class, 'showPackage'])
            ->name('package-overview')
            ->withoutMiddleware('auth');

        /*
        |--------------------------------------------------------------------------
        | 4. KERANJANG & CHECKOUT
        |--------------------------------------------------------------------------
        */
        Route::get('/cart', [CartController::class, 'index'])
            ->name('cart');

        Route::post('/cart/package/{packageId}', [CartController::class, 'addPackage'])
            ->name('cart.add-package');

        Route::post('/cart/vendor/{vendorId}', [CartController::class, 'addVendor'])
            ->name('cart.add-vendor');

        Route::delete('/cart/vendor/{vendorId}', [CartController::class, 'remove'])
            ->name('cart.remove');

        Route::delete('/cart/clear', [CartController::class, 'clear'])
            ->name('cart.clear');

        Route::get('/checkout', [CheckoutController::class, 'index'])
            ->name('checkout');

        Route::post('/checkout', [CheckoutController::class, 'store'])
            ->name('checkout.store');

        Route::get('/payment/{bookingId}', [CheckoutController::class, 'payment'])
            ->name('payment');

       Route::get('/payment/{bookingId}', [CheckoutController::class, 'payment'])
    ->name('payment');

        /*
        |--------------------------------------------------------------------------
        | 5. EVENT / STATUS ACARA
        |--------------------------------------------------------------------------
        */
        Route::get('/event', [EventController::class, 'index'])
            ->name('event');

        /*
        |--------------------------------------------------------------------------
        | 6. RUNDOWN & TIMELINE
        |--------------------------------------------------------------------------
        */
        Route::get('/rundown-timeline', [RundownController::class, 'index'])
            ->name('rundown');

        Route::get('/rundown-timeline/create', [RundownController::class, 'create'])
            ->name('rundown.create');

        Route::post('/rundown-timeline', [RundownController::class, 'store'])
            ->name('rundown.store');

        Route::get('/rundown-timeline/{date}', [RundownController::class, 'show'])
            ->name('rundown.detail');
    });
