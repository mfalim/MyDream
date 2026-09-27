<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\OrganizerEventController;
use App\Http\Controllers\Admin\TrackingController;

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

        // =========================================================
        // DASHBOARD
        // =========================================================

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');


        // =========================================================
        // CATEGORIES
        // =========================================================

        Route::resource('categories', CategoryController::class)->only([
            'index',
            'create',
            'store',
        ]);


        // =========================================================
        // PACKAGES
        // =========================================================

        Route::resource('packages', PackageController::class)->only([
            'index',
            'create',
            'store',
            'show',
            'edit',
            'update',
            'destroy',
        ]);


        // =========================================================
        // CALENDAR
        // =========================================================

        Route::get('/calendar', [CalendarController::class, 'index'])
            ->name('calendar');


        // =========================================================
        // EVENT API
        //
        // IMPORTANT:
        // These routes MUST be before /events/{event}
        // =========================================================

        Route::get('/events/by-month', [CalendarController::class, 'getEventsByMonth'])
            ->name('events.by-month');

        Route::get('/events/by-date', [CalendarController::class, 'getEventsByDate'])
            ->name('events.by-date');


        // =========================================================
        // CLIENT EVENTS
        // =========================================================

        Route::resource('events', BookingController::class)->only([
            'index',
            'create',
            'store',
            'show',
            'edit',
            'update',
        ]);


        // =========================================================
        // EVENTS BELONGING TO A BOOKING
        // =========================================================

        Route::get('/bookings/{booking}/events', [
            BookingController::class,
            'getEvents',
        ])->name('booking.events');

        // =========================================================
        // TRACKING
        // =========================================================

        Route::get('/tracking', [TrackingController::class, 'index'])
            ->name('tracking.index');

        Route::get('/tracking/{id}', [TrackingController::class, 'show'])
            ->name('tracking.show');

        Route::post('/tracking/schedule/{scheduleId}/status', [
            TrackingController::class,
            'updateScheduleStatus',
        ])->name('tracking.schedule.status');

        Route::post('/tracking/booking/{bookingId}/status', [
            TrackingController::class,
            'updateBookingStatus',
        ])->name('tracking.booking.status');


        // =========================================================
        // ORGANIZER EVENTS
        // =========================================================

        Route::resource('organizer-event', OrganizerEventController::class)->only([
            'index',
            'create',
            'store',
            'show',
            'edit',
            'update',
            'destroy',
        ]);


        // =========================================================
        // VENDORS
        // =========================================================

        Route::resource('vendors', VendorController::class)->only([
            'index',
            'create',
            'store',
            'show',
            'edit',
            'update',
            'destroy',
        ]);


        // =========================================================
        // MEMBERS
        // =========================================================

        Route::resource('members', MemberController::class)->only([
            'index',
            'create',
            'store',
            'show',
            'edit',
            'update',
            'destroy',
        ]);

        Route::post('/members/options/positions', [
            MemberController::class,
            'storePosition',
        ])->name('members.options.positions.store');

        Route::post('/members/options/specializations', [
            MemberController::class,
            'storeSpecialization',
        ])->name('members.options.specializations.store');
    });
