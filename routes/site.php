<?php

use App\Http\Controllers\Site\AboutController;
use App\Http\Controllers\Site\ArticleController;
use App\Http\Controllers\Site\CatalogController;
use App\Http\Controllers\Site\EventController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\InspirationController;
use App\Http\Controllers\Site\PackageController;
use App\Http\Controllers\Site\PaymentController;
use App\Http\Controllers\Site\StoreController;
use App\Http\Controllers\Site\VendorController;
use Illuminate\Support\Facades\Route;

// Halaman publik baru (Tailwind). Nama route & URL sama dengan versi lama agar link lain tidak putus.
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/store', [StoreController::class, 'index'])->name('store');

Route::get('/paket', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/paket/{slug}', [PackageController::class, 'show'])->name('catalog.show');

Route::get('/vendor', [CatalogController::class, 'vendors'])->name('vendor.index');
Route::get('/vendor/{slug}', [VendorController::class, 'show'])->name('vendor.show');

Route::get('/inspirasi', [InspirationController::class, 'index'])->name('inspiration.index');
Route::get('/inspirasi/{slug}', [InspirationController::class, 'show'])->name('inspiration.show');

Route::get('/event', [EventController::class, 'index'])->name('event.index');
Route::get('/event/{slug}', [EventController::class, 'show'])->name('event.show');

Route::get('/blog', [ArticleController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [ArticleController::class, 'show'])->name('blog.show');

Route::get('/tentang-kami', [AboutController::class, 'index'])->name('about');

// Pembayaran (step 3). Keranjang & checkout tetap memakai controller tim (User\CartController, User\CheckoutController).
Route::middleware('auth')->group(function () {
    Route::get('/pembayaran/{bookingId}', [PaymentController::class, 'show'])->whereNumber('bookingId')->name('payment.index');
    Route::post('/pembayaran/{bookingId}', [PaymentController::class, 'pay'])->whereNumber('bookingId')->name('payment.pay');
    Route::get('/pembayaran/{bookingId}/sukses', [PaymentController::class, 'success'])->whereNumber('bookingId')->name('payment.success');
});
