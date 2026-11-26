<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Middleware\EnsureUserRole;

// Semua halaman publik (home, paket, vendor, inspirasi, event, blog, tentang, pembayaran)
// sudah pindah ke routes/site.php. Di sini tinggal keranjang & checkout, yang logikanya
// tetap di controller tim (hanya nama view-nya yang dialihkan ke tampilan baru).

Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');

Route::get('/checkout', [CheckoutController::class, 'index'])
    ->middleware(['auth', EnsureUserRole::class . ':client'])
    ->name('checkout.index');

Route::post('/checkout', [CheckoutController::class, 'store'])
    ->middleware(['auth', EnsureUserRole::class . ':client'])
    ->name('checkout.store');
