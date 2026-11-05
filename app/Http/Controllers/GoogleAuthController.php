<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\GoogleAuthController as ApiGoogleAuthController;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(Request $request)
    {
        if ($request->filled('mobile_session')) {
            return Socialite::driver('google')
                ->stateless()
                ->with([
                    'state' => 'mobile:' . $request->query('mobile_session'),
                    'prompt' => 'select_account',
                ])
                ->redirect();
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
    {
        $state = (string) $request->query('state', '');
        if (str_starts_with($state, 'mobile:')) {
            return $this->handleMobileCallback(substr($state, 7));
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            $googleUser = Socialite::driver('google')->stateless()->user();
        }

        $user = User::createOrLogin($googleUser);

        Auth::login($user);

        if ($user->status === 'pending') {
            Auth::logout();

            return redirect('/login')->with('error', 'Akun Anda masih menunggu verifikasi admin.');
        }

        if ($user->status === 'rejected') {
            Auth::logout();

            return redirect('/login')->with('error', 'Akun Anda ditolak oleh admin.');
        }

        if ($user->role == 'client' && is_null(Client::findClientByIdUser($user->id))) {
            return redirect('/login/profile');
        }

        if ($user->role == 'member') {
            $member = \App\Models\Member::where('user_id', $user->id)->first();
            if (! $member) {
                return redirect('/member/profile/complete');
            }

            return redirect('/member/dashboard');
        }

        return match ($user->role) {
            'admin' => redirect('/admin/dashboard'),
            'vendor' => redirect('/vendor/dashboard'),
            'client' => redirect('/user/dashboard'),
            default => redirect('/user/dashboard'),
        };
    }

    /**
     * Menangani callback Google OAuth ketika dipanggil dari alur browser Mobile Flutter.
     */
    protected function handleMobileCallback(string $sessionId)
    {
        try {
            $socialiteUser = Socialite::driver('google')->stateless()->user();
            $googleUser = (object) [
                'id' => $socialiteUser->getId(),
                'name' => $socialiteUser->getName() ?: explode('@', $socialiteUser->getEmail())[0],
                'email' => $socialiteUser->getEmail(),
                'avatar' => $socialiteUser->getAvatar(),
            ];

            $apiResponse = app(ApiGoogleAuthController::class)
                ->issueAuthResponse($googleUser, 'flutter-token');

            $payload = $apiResponse->getData(true);
            $payload['status_code'] = $apiResponse->getStatusCode();

            Cache::put('mobile_google_auth:' . $sessionId, $payload, now()->addMinutes(5));

            $message = $payload['message'] ?? 'Login berhasil.';
            $isSuccess = ! empty($payload['success']);
        } catch (\Throwable $e) {
            report($e);
            $isSuccess = false;
            $message = 'Login Google gagal: ' . $e->getMessage();
            Cache::put('mobile_google_auth:' . $sessionId, [
                'success' => false,
                'message' => $message,
                'status_code' => 401,
            ], now()->addMinutes(5));
        }

        $deepLink = 'mydream://auth/callback?session_id=' . urlencode($sessionId);
        $title = $isSuccess ? 'Login Berhasil' : 'Login Gagal';
        $color = $isSuccess ? '#486b69' : '#c0392b';

        return response(<<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title} - MyDream</title>
    <style>
        body { font-family: sans-serif; background: #f5f3ef; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; padding: 20px; text-align: center; }
        .card { background: #fff; padding: 32px 24px; border-radius: 16px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); max-width: 380px; width: 100%; }
        h2 { color: {$color}; margin-top: 0; }
        p { color: #555; line-height: 1.5; }
        .btn { display: inline-block; margin-top: 16px; padding: 12px 24px; background: {$color}; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600; }
    </style>
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.location.href = "{$deepLink}";
            }, 300);
        };
    </script>
</head>
<body>
    <div class="card">
        <h2>{$title}</h2>
        <p>{$message}</p>
        <p>Silakan kembali ke aplikasi <strong>MyDream</strong>.</p>
        <a class="btn" href="{$deepLink}">Kembali ke Aplikasi</a>
    </div>
</body>
</html>
HTML);
    }
}
