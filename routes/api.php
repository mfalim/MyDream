<?php

use App\Http\Controllers\Api\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\EventController as AdminEventController;
use App\Http\Controllers\Api\Admin\CalendarController;
use App\Http\Controllers\Api\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\Api\Admin\MemberDivisionController;
use App\Http\Controllers\Api\Admin\MemberPositionController;
use App\Http\Controllers\Api\Admin\MemberSpecializationController;
use App\Http\Controllers\Api\Admin\OrganizerEventController;
use App\Http\Controllers\Api\Admin\PackageController as AdminPackageController;
use App\Http\Controllers\Api\Admin\PublicEventController as AdminPublicEventController;
use App\Http\Controllers\Api\Admin\TrackingController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Client\CartController;
use App\Http\Controllers\Api\Client\ClientController;
use App\Http\Controllers\Api\GoogleAuthController;
use App\Http\Controllers\Api\Member\MemberAccountController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\Site\AboutController;
use App\Http\Controllers\Api\Site\ArticleController;
use App\Http\Controllers\Api\Site\CatalogController;
use App\Http\Controllers\Api\Site\CategoryController;
use App\Http\Controllers\Api\Site\EventController;
use App\Http\Controllers\Api\Site\HomeController;
use App\Http\Controllers\Api\Site\InspirationController;
use App\Http\Controllers\Api\Site\PackageController;
use App\Http\Controllers\Api\Site\StoreController;
use App\Http\Controllers\Api\Site\VendorController as SiteVendorController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Middleware\EnsureUserRole;
use Illuminate\Support\Facades\Route;

Route::post('/auth/google', [GoogleAuthController::class, 'loginWithGoogle'])->name('api.auth.google');
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirectToGoogle'])->name('api.auth.google.redirect');
Route::get('/auth/google/poll', [GoogleAuthController::class, 'pollAuthResult'])->name('api.auth.google.poll');

Route::get('/home', [HomeController::class, 'index'])->name('api.home');
Route::get('/about', [AboutController::class, 'show'])->name('api.about');
Route::get('/catalog', [CatalogController::class, 'index'])->name('api.catalog');
Route::get('/catalog/categories', [CategoryController::class, 'index'])->name('api.catalog.categories');
Route::get('/catalog/packages', [PackageController::class, 'index'])->name('api.catalog.packages');
Route::get('/catalog/packages/{slug}', [PackageController::class, 'show'])->name('api.catalog.package');
Route::get('/store', [StoreController::class, 'index'])->name('api.store');
Route::get('/catalog/vendors/{slug}', [SiteVendorController::class, 'show'])->name('api.catalog.vendor');
Route::get('/content/articles', [ArticleController::class, 'index'])->name('api.content.articles');
Route::get('/content/articles/{slug}', [ArticleController::class, 'show'])->name('api.content.article');
Route::get('/content/events', [EventController::class, 'index'])->name('api.content.events');
Route::get('/content/events/{slug}', [EventController::class, 'show'])->name('api.content.event');
Route::get('/content/inspirations', [InspirationController::class, 'index'])->name('api.content.inspirations');
Route::get('/content/inspirations/{slug}', [InspirationController::class, 'show'])->name('api.content.inspiration');

Route::get('/members', [MemberController::class, 'index'])->name('api.members.index');
Route::get('/members/{member}', [MemberController::class, 'show'])->name('api.members.show');
Route::get('/vendors', [VendorController::class, 'index'])->name('api.vendors.index');
Route::get('/vendors/{vendor}', [VendorController::class, 'show'])->name('api.vendors.show');

Route::middleware(['auth:sanctum', EnsureUserRole::class . ':admin'])->group(function () {
    Route::post('/vendors', [VendorController::class, 'store'])->name('api.vendors.store');
    Route::put('/vendors/{vendor}', [VendorController::class, 'update'])->name('api.vendors.update');
    Route::patch('/vendors/{vendor}', [VendorController::class, 'update'])->name('api.vendors.patch');
    Route::delete('/vendors/{vendor}', [VendorController::class, 'destroy'])->name('api.vendors.destroy');
});

Route::middleware(['auth:sanctum', EnsureUserRole::class . ':admin'])
    ->prefix('admin')
    ->name('api.admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/calendar/events', [CalendarController::class, 'index'])->name('calendar.events');
        Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');
        Route::get('/tracking/events/{id}', [TrackingController::class, 'showEvent'])->name('tracking.show');
        Route::patch('/tracking/schedules/{scheduleId}/status', [TrackingController::class, 'updateScheduleStatus'])->name('tracking.schedule.status');
        Route::patch('/tracking/schedules/{scheduleId}/time', [TrackingController::class, 'updateScheduleTime'])->name('tracking.schedule.time');
        Route::patch('/tracking/bookings/{bookingId}/status', [TrackingController::class, 'updateBookingStatus'])->name('tracking.booking.status');

        Route::get('/events', [AdminEventController::class, 'index'])->name('events.index');
        Route::post('/events', [AdminEventController::class, 'store'])->name('events.store');
        Route::get('/events/{id}', [AdminEventController::class, 'show'])->whereNumber('id')->name('events.show');
        Route::put('/events/{id}', [AdminEventController::class, 'update'])->whereNumber('id')->name('events.update');
        Route::patch('/events/{id}', [AdminEventController::class, 'update'])->whereNumber('id')->name('events.patch');
        Route::delete('/events/{id}', [AdminEventController::class, 'destroy'])->whereNumber('id')->name('events.destroy');

        $resources = [
            'categories' => [AdminCategoryController::class, 'categories'],
            'packages' => [AdminPackageController::class, 'packages'],
            'articles' => [AdminArticleController::class, 'articles'],
            'public-events' => [AdminPublicEventController::class, 'public-events'],
            'organizer-events' => [OrganizerEventController::class, 'organizer-events'],
            'members' => [AdminMemberController::class, 'members'],
            'member-divisions' => [MemberDivisionController::class, 'member-divisions'],
            'member-positions' => [MemberPositionController::class, 'member-positions'],
            'member-specializations' => [MemberSpecializationController::class, 'member-specializations'],
        ];
        foreach ($resources as $path => [$controller, $name]) {
            Route::get('/' . $path, [$controller, 'index'])->name($name . '.index');
            Route::post('/' . $path, [$controller, 'store'])->name($name . '.store');
            Route::get('/' . $path . '/{id}', [$controller, 'show'])->whereNumber('id')->name($name . '.show');
            Route::put('/' . $path . '/{id}', [$controller, 'update'])->whereNumber('id')->name($name . '.update');
            Route::patch('/' . $path . '/{id}', [$controller, 'update'])->whereNumber('id')->name($name . '.patch');
            Route::delete('/' . $path . '/{id}', [$controller, 'destroy'])->whereNumber('id')->name($name . '.destroy');
        }
    });

Route::middleware(['auth:sanctum', EnsureUserRole::class . ':client'])->prefix('client')->name('api.client.')->group(function () {
    Route::get('/dashboard', [ClientController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [ClientController::class, 'showProfile'])->name('profile.show');
    Route::put('/profile', [ClientController::class, 'saveProfile'])->name('profile.update');
    Route::post('/profile', [ClientController::class, 'saveProfile'])->name('profile.store');
    Route::get('/bookings', [ClientController::class, 'bookings'])->name('bookings.index');
    Route::post('/bookings', [ClientController::class, 'createBooking'])->name('bookings.store');
    Route::get('/bookings/{bookingId}/payments', [ClientController::class, 'bookingPayments'])->name('bookings.payments.index');
    Route::post('/bookings/{bookingId}/payments', [ClientController::class, 'submitPayment'])->name('bookings.payments.store');
    Route::get('/events', [ClientController::class, 'events'])->name('events.index');
    Route::post('/events', [ClientController::class, 'createEvent'])->name('events.store');
});

Route::middleware(['auth:sanctum', EnsureUserRole::class . ':member'])->prefix('member')->name('api.member.')->group(function () {
    Route::get('/dashboard', [MemberAccountController::class, 'dashboard'])->name('dashboard');
    Route::put('/profile', [MemberAccountController::class, 'updateProfile'])->name('profile.update');
    Route::patch('/schedules/{id}/status', [MemberAccountController::class, 'updateScheduleStatus'])->name('schedule.status');
    Route::post('/schedules/{id}/assign', [MemberAccountController::class, 'assignScheduleMember'])->name('schedule.assign');
    Route::post('/events/{eventId}/members/{memberId}/attendance', [MemberAccountController::class, 'updateTeamAttendance'])->name('team.attendance');
});

Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me'])->name('api.me');
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout'])->name('api.logout');
Route::middleware('auth:sanctum')->prefix('cart')->name('api.cart.')->group(function () {
    Route::get('/', [CartController::class, 'show'])->name('show');
    Route::post('/packages/{packageId}', [CartController::class, 'addPackage'])->name('packages.add');
    Route::post('/vendors/{vendorId}', [CartController::class, 'addVendor'])->name('vendors.add');
    Route::delete('/vendors/{vendorId}', [CartController::class, 'removeVendor'])->name('vendors.remove');
    Route::delete('/', [CartController::class, 'clear'])->name('clear');
});
