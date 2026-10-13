<?php

use App\Http\Controllers\Site\CatalogController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\PackageController;
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
