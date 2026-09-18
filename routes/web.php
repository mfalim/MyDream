<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\VendorController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('categories', CategoryController::class)
            ->only([
                'index',
                'create',
                'store'
            ]);

        Route::resource('packages', PackageController::class)
            ->only([
                'index',
                'create',
                'store'
            ]);

        Route::resource('vendors', VendorController::class)
            ->only([
                'index',
                'create',
                'store'
            ]);
    });
