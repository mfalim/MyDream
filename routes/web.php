<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\OrganizerEventController;
use App\Http\Controllers\Admin\TrackingController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\Client\ClientController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/login', function () {
    return view('login.login');
})->name('login');


Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');

// contoh middleware login
Route::middleware('auth')->group(function () {

    Route::get('/login/profile', [ClientController::class, 'profile'])
        ->name('client.profile');

    Route::post('/login/profile', [ClientController::class, 'storeProfile'])
        ->name('client.profile.store');
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
                'store',
                'show',
                'edit',
                'update',
                'destroy'
            ]);

        Route::resource('event_day', BookingController::class)
            ->only([
                'index',
                'create',
                'store'
            ]);
        
        Route::get('/event/{id}', [BookingController::class, 'show'])->name('event.show');
        Route::get('/event/{id}/edit', [BookingController::class, 'edit'])->name('event.edit');
        Route::put('/event/{id}', [BookingController::class, 'update'])->name('event.update');
        
        Route::get('/booking/{booking}/event-days', [BookingController::class, 'getEventDays'])->name('booking.event-days');
        
        Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
        Route::get('/events/by-month', [CalendarController::class, 'getEventsByMonth'])->name('events.by-month');
        Route::get('/events/by-date', [CalendarController::class, 'getEventsByDate'])->name('events.by-date');
        
        Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');
        Route::get('/tracking/{id}', [TrackingController::class, 'show'])->name('tracking.show');
        Route::post('/tracking/schedule/{scheduleId}/status', [TrackingController::class, 'updateScheduleStatus'])->name('tracking.schedule.status');
        
        Route::resource('organizer-event', OrganizerEventController::class)
            ->only([
                'index',
                'create',
                'store',
                'show',
                'edit',
                'update',
                'destroy'
            ]);
        
        Route::resource('vendors', VendorController::class)
            ->only([
                'index',
                'create',
                'store',
                'show',
                'edit',
                'update',
                'destroy'
            ]);
        Route::resource('members', MemberController::class)
            ->only([
                'index',
                'create',
                'store',
                'show',
                'edit',
                'update',
                'destroy',
            ]);
        Route::post('/members/options/positions', [MemberController::class, 'storePosition'])
            ->name('members.options.positions.store');
        Route::post('/members/options/specializations', [MemberController::class, 'storeSpecialization'])
            ->name('members.options.specializations.store');
    });
