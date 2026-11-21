<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Member\ProfileController as MemberProfileController;
use App\Http\Middleware\EnsureUserRole;

Route::view('/login', 'login.login')->name('login');
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
Route::post('/login', [GoogleAuthController::class, 'loginWithCredentials'])->name('login.attempt');
Route::post('/register', [GoogleAuthController::class, 'registerWithCredentials'])->name('register.attempt');

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
