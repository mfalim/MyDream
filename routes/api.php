<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Client\CartController;
use App\Http\Controllers\Api\Client\ClientController;
use App\Http\Controllers\Api\GoogleAuthController;
use App\Http\Controllers\Api\Member\MemberAccountController;
use App\Http\Controllers\Api\NotificationController;
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
use App\Http\Controllers\Site\PaymentController as SitePaymentController;
use App\Http\Middleware\EnsureUserRole;
use Illuminate\Support\Facades\Route;

// Autentikasi Mobile (hanya Klien & Kru/Member) + OTP Pendaftaran/Reset Password + Webhook Midtrans
Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('api.auth.login');
Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:6,1')->name('api.auth.register');
Route::post('/auth/register/verify', [AuthController::class, 'verifyRegisterOtp'])->middleware('throttle:10,1')->name('api.auth.register.verify');
Route::post('/auth/password/otp', [AuthController::class, 'requestPasswordOtp'])->middleware('throttle:5,1')->name('api.auth.password.otp');
Route::post('/auth/password/reset', [AuthController::class, 'resetPasswordWithOtp'])->middleware('throttle:10,1')->name('api.auth.password.reset');
Route::post('/auth/otp/resend', [AuthController::class, 'resendOtp'])->middleware('throttle:4,1')->name('api.auth.otp.resend');
Route::post('/auth/google', [GoogleAuthController::class, 'loginWithGoogle'])->middleware('throttle:10,1')->name('api.auth.google');
Route::post('/midtrans/notification', [SitePaymentController::class, 'notification'])
    ->name('midtrans.notification');

// Katalog & Konten Publik Mobile (Read-Only)
Route::get('/home', [HomeController::class, 'index'])->name('api.home');
Route::get('/about', [AboutController::class, 'show'])->name('api.about');
Route::get('/catalog', [CatalogController::class, 'index'])->name('api.catalog');
Route::get('/catalog/categories', [CategoryController::class, 'index'])->name('api.catalog.categories');
Route::get('/catalog/packages', [PackageController::class, 'index'])->name('api.catalog.packages');
Route::get('/catalog/packages/{slug}', [PackageController::class, 'show'])->name('api.catalog.package');
Route::get('/store', [StoreController::class, 'index'])->name('api.store');
Route::get('/catalog/vendors/{slug}', [SiteVendorController::class, 'show'])->name('api.catalog.vendor');
Route::get('/vendors', [VendorController::class, 'index'])->name('api.vendors.index');
Route::get('/vendors/{vendor}', [VendorController::class, 'show'])->name('api.vendors.show');
Route::get('/content/articles', [ArticleController::class, 'index'])->name('api.content.articles');
Route::get('/content/articles/{slug}', [ArticleController::class, 'show'])->name('api.content.article');
Route::get('/content/events', [EventController::class, 'index'])->name('api.content.events');
Route::get('/content/events/{slug}', [EventController::class, 'show'])->name('api.content.event');
Route::get('/content/inspirations', [InspirationController::class, 'index'])->name('api.content.inspirations');
Route::get('/content/inspirations/{slug}', [InspirationController::class, 'show'])->name('api.content.inspiration');

// Portal Klien Mobile
Route::middleware(['auth:sanctum', EnsureUserRole::class . ':client'])->prefix('client')->name('api.client.')->group(function () {
    Route::get('/dashboard', [ClientController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [ClientController::class, 'showProfile'])->name('profile.show');
    Route::put('/profile', [ClientController::class, 'saveProfile'])->name('profile.update');
    Route::post('/profile', [ClientController::class, 'saveProfile'])->name('profile.store');
    Route::get('/bookings', [ClientController::class, 'bookings'])->name('bookings.index');
    Route::post('/bookings', [ClientController::class, 'createBooking'])->name('bookings.store');
    Route::get('/bookings/{bookingId}/payments', [ClientController::class, 'bookingPayments'])->name('bookings.payments.index');
    Route::post('/bookings/{bookingId}/payments', [ClientController::class, 'submitPayment'])->name('bookings.payments.store');
    Route::post('/bookings/{bookingId}/payments/midtrans', [SitePaymentController::class, 'pay'])
        ->whereNumber('bookingId')
        ->name('bookings.payments.midtrans');
    Route::get('/events', [ClientController::class, 'events'])->name('events.index');
    Route::post('/events', [ClientController::class, 'createEvent'])->name('events.store');
    Route::get('/reviews', [ClientController::class, 'reviews'])->name('reviews.index');
    Route::post('/reviews', [ClientController::class, 'storeReview'])->name('reviews.store');
});

// Portal Kru / Member Mobile
Route::middleware(['auth:sanctum', EnsureUserRole::class . ':member'])->prefix('member')->name('api.member.')->group(function () {
    Route::get('/dashboard', [MemberAccountController::class, 'dashboard'])->name('dashboard');
    Route::put('/profile', [MemberAccountController::class, 'updateProfile'])->name('profile.update');
    Route::patch('/schedules/{id}/status', [MemberAccountController::class, 'updateScheduleStatus'])->name('schedule.status');
    Route::post('/schedules/{id}/assign', [MemberAccountController::class, 'assignScheduleMember'])->name('schedule.assign');
    Route::post('/events/{eventId}/members/{memberId}/attendance', [MemberAccountController::class, 'updateTeamAttendance'])->name('team.attendance');
});

// Sesi, Notifikasi & Keranjang
Route::middleware(['auth:sanctum', EnsureUserRole::class . ':client,member'])->group(function () {
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('api.notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('api.notifications.read_all');
    Route::post('/notifications/device-token', [NotificationController::class, 'saveDeviceToken'])->name('api.notifications.device_token');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('api.notifications.read');
});

Route::middleware(['auth:sanctum', EnsureUserRole::class . ':client'])->prefix('cart')->name('api.cart.')->group(function () {
    Route::get('/', [CartController::class, 'show'])->name('show');
    Route::post('/packages/{packageId}', [CartController::class, 'addPackage'])->name('packages.add');
    Route::post('/vendors/{vendorId}', [CartController::class, 'addVendor'])->name('vendors.add');
    Route::delete('/vendors/{vendorId}', [CartController::class, 'removeVendor'])->name('vendors.remove');
    Route::delete('/', [CartController::class, 'clear'])->name('clear');
});
