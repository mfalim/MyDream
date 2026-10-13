<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;

// Home, Paket, dan Vendor sudah pindah ke routes/site.php.
// Sisa halaman di bawah masih memakai tampilan lama dan akan dipindah bertahap.

$frontend = FrontendController::class;

Route::get('/blog', [$frontend, 'blog'])->name('blog.index');
Route::get('/blog/{slug}', [$frontend, 'article'])->name('blog.show');

Route::get('/inspirasi', [$frontend, 'inspiration'])->name('inspiration.index');
Route::get('/event', [$frontend, 'event'])->name('event.index');

Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');

Route::get('/checkout', [CheckoutController::class, 'index'])
    ->middleware('auth')
    ->name('checkout.index');

Route::post('/checkout', [CheckoutController::class, 'store'])
    ->middleware('auth')
    ->name('checkout.store');

Route::get('/pembayaran/{bookingId}', [CheckoutController::class, 'payment'])
    ->middleware('auth')
    ->name('payment.index');

Route::get('/pembayaran/{bookingId}/sukses', [CheckoutController::class, 'paymentSuccess'])
    ->middleware('auth')
    ->name('payment.success');

Route::view('/welcome', 'welcome')->name('welcome');
