<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\GoogleAuthController as ApiGoogleAuthController;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function showLogin(Request $request)
    {
        $this->storeIntendedRedirect($request);

        return view('login.login');
    }

    /**
     * Login menggunakan Username / Email + Password.
     */
    public function loginWithCredentials(Request $request)
    {
        $this->storeIntendedRedirect($request);

        $validator = Validator::make($request->all(), [
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Username atau Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only('login') + ['auth_tab' => 'login']);
        }

        $login = Str::lower(trim((string) $request->input('login')));
        $password = (string) $request->input('password');

        $user = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? User::where('email', $login)->first()
            : User::where('username', $login)->orWhere('email', $login)->first();

        // Pendekatan 1 (Strict Auth Separation): Akun yang dibuat via Google OAuth
        // tanpa password tidak bisa login melalui form password.
        if ($user && ! empty($user->google_id) && empty($user->password)) {
            return back()
                ->withInput(['login' => $login, 'auth_tab' => 'login'])
                ->with('error', 'Akun ini terdaftar melalui Google. Silakan gunakan tombol Lanjutkan dengan Google.');
        }

        if (! $user || empty($user->password) || ! Hash::check($password, $user->password)) {
            return back()
                ->withInput(['login' => $login, 'auth_tab' => 'login'])
                ->with('error', 'Username/Email atau password yang Anda masukkan salah.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectAfterLogin($user);
    }

    /**
     * Registrasi akun Klien baru menggunakan Username (+ Email opsional) & Password.
     */
    public function registerWithCredentials(Request $request)
    {
        $this->storeIntendedRedirect($request);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_).',
            'username.unique' => 'Username tersebut sudah digunakan, silakan pilih username lain.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email tersebut sudah terdaftar pada akun lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only(['name', 'username', 'email']) + ['auth_tab' => 'register']);
        }

        $data = $validator->validated();
        $email = ! empty($data['email']) ? strtolower(trim($data['email'])) : null;

        $user = User::create([
            'name' => trim($data['name']),
            'username' => strtolower(trim($data['username'])),
            'email' => $email,
            'password' => $data['password'],
            'role' => 'client',
            'status' => 'approved',
            'google_id' => null,
            'avatar' => null,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectAfterLogin($user);
    }

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

        $this->storeIntendedRedirect($request);

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
    {
        $state = (string) $request->query('state', '');
        if (str_starts_with($state, 'mobile:')) {
            return $this->handleMobileCallback(substr($state, 7));
        }

        if ($request->filled('error') || ! $request->filled('code')) {
            return redirect()->route('login')->with(
                'error',
                'Proses masuk menggunakan Google dibatalkan.'
            );
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            try {
                $googleUser = Socialite::driver('google')->stateless()->user();
            } catch (\Throwable $fallbackException) {
                return redirect()->route('login')->with(
                    'error',
                    'Sesi autentikasi Google tidak valid atau telah berakhir. Silakan coba lagi.'
                );
            }
        }

        // Pendekatan 1 (Strict Auth Separation):
        // Jika email sudah didaftarkan sebagai akun Klien menggunakan Password (bukan Google SSO),
        // tolak login via Google agar metode autentikasi tetap terpisah dan aman.
        if (! empty($googleUser->email)) {
            $existingUser = User::where('email', $googleUser->email)->first();
            if ($existingUser && $existingUser->role === 'client' && ! empty($existingUser->password) && empty($existingUser->google_id)) {
                return redirect('/login')->with(
                    'error',
                    'Email ini sudah terdaftar menggunakan Password. Silakan masuk menggunakan Username/Email & Password Anda.'
                );
            }
        }

        $user = User::createOrLogin($googleUser);

        Auth::login($user);

        return $this->redirectAfterLogin($user);
    }

    /**
     * Alur pengecekan status dan redirect pasca-login (digunakan bersama oleh Google OAuth & Password Login).
     */
    protected function redirectAfterLogin(User $user)
    {
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
            if (! $member || empty($member->call_sign)) {
                return redirect('/member/profile/complete');
            }

            return redirect('/member/dashboard')->with('login_curtain', true);
        }

        return match ($user->role) {
            'admin' => redirect('/admin/dashboard')->with('login_curtain', true),
            'vendor' => redirect('/vendor/dashboard')->with('login_curtain', true),
            'client' => redirect()->intended('/user/dashboard')->with('login_curtain', true),
            default => redirect('/user/dashboard')->with('login_curtain', true),
        };
    }

    private function storeIntendedRedirect(Request $request): void
    {
        $redirect = $request->input('redirect');
        if (! is_string($redirect)) {
            return;
        }

        $parts = parse_url($redirect);
        if (
            ! is_array($parts)
            || (isset($parts['host']) && strcasecmp($parts['host'], $request->getHost()) !== 0)
            || (isset($parts['scheme']) && ! in_array(strtolower($parts['scheme']), ['http', 'https'], true))
            || ! isset($parts['path'])
            || ! is_string($parts['path'])
            || ! str_starts_with($parts['path'], '/')
            || str_starts_with($parts['path'], '//')
            || $parts['path'] === '/login'
        ) {
            return;
        }

        $intendedUrl = $parts['path'];
        if (isset($parts['query'])) {
            $intendedUrl .= '?' . $parts['query'];
        }
        if (isset($parts['fragment'])) {
            $intendedUrl .= '#' . $parts['fragment'];
        }

        $request->session()->put('url.intended', $intendedUrl);
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
