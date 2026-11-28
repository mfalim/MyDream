<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthOtpController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Member\ProfileController as MemberProfileController;
use App\Http\Middleware\EnsureUserRole;

Route::get('/login', [GoogleAuthController::class, 'showLogin'])->name('login');
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
Route::post('/login', [GoogleAuthController::class, 'loginWithCredentials'])->middleware('throttle:10,1')->name('login.attempt');
Route::post('/register', [GoogleAuthController::class, 'registerWithCredentials'])->middleware('throttle:6,1')->name('register.attempt');

// Verifikasi OTP Pendaftaran, Login Admin (opsional), & Lupa/Ubah Password
Route::get('/login/otp', [AuthOtpController::class, 'showVerify'])->name('auth.otp.show');
Route::post('/login/otp', [AuthOtpController::class, 'verify'])->middleware('throttle:10,1')->name('auth.otp.verify');
Route::post('/login/otp/resend', [AuthOtpController::class, 'resend'])->middleware('throttle:4,1')->name('auth.otp.resend');
Route::get('/forgot-password', [AuthOtpController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [AuthOtpController::class, 'sendResetOtp'])->middleware('throttle:5,1')->name('password.otp.send');
Route::get('/reset-password', [AuthOtpController::class, 'showResetForm'])->name('password.otp.form');
Route::post('/reset-password', [AuthOtpController::class, 'resetPassword'])->middleware('throttle:10,1')->name('password.otp.update');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', EnsureUserRole::class . ':client'])->group(function () {
    Route::get('/login/profile', [ClientController::class, 'profile'])->name('client.profile');
    Route::post('/login/profile', [ClientController::class, 'storeProfile'])->name('client.profile.store');
});

Route::prefix('member')->name('member.')->middleware(['auth', EnsureUserRole::class . ':member'])->group(function () {
    Route::get('/profile/complete', [MemberProfileController::class, 'showCompleteForm'])->name('profile.complete');
    Route::post('/profile/complete', [MemberProfileController::class, 'storeProfile'])->name('profile.store');
    Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/status', [MemberDashboardController::class, 'updateAvailabilityStatus'])->name('status.update');
    Route::post('/dashboard/schedule/{id}/status', [MemberDashboardController::class, 'updateScheduleStatus'])->name('schedule.status');
    Route::post('/dashboard/schedule/{id}/assign', [MemberDashboardController::class, 'assignScheduleMember'])->name('schedule.assign');
    Route::post('/dashboard/event/{eventId}/member/{memberId}/attendance', [MemberDashboardController::class, 'updateTeamAttendance'])->name('event.attendance');
});

require __DIR__ . '/vendor.php';
require __DIR__ . '/site.php';
require __DIR__ . '/frontend.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/user.php';
