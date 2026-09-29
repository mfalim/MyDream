<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;


$frontend = FrontendController::class;

Route::get('/', [$frontend, 'home'])->name('home');

Route::get('/paket', [$frontend, 'catalog'])->name('catalog.index');
Route::get('/paket/{slug}', [$frontend, 'package'])->name('catalog.show');

Route::get('/vendor', [$frontend, 'vendors'])->name('vendor.index');
Route::get('/vendor/{slug}', [$frontend, 'vendor'])->name('vendor.show');

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

Route::get('/event', [$frontend, 'event'])->name('event.index');

Route::view('/welcome', 'welcome')->name('welcome');
