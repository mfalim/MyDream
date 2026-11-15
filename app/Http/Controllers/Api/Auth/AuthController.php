<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\GoogleAuthController;
use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Login via Username / Email + Password untuk Mobile Flutter.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ], [
            'login.required' => 'Username atau Email wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $login = trim((string) $request->input('login'));
        $password = (string) $request->input('password');
        $deviceName = (string) $request->input('device_name', 'flutter-token');

        $user = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? User::where('email', $login)->first()
            : User::where('username', $login)->orWhere('email', $login)->first();

        // Pendekatan 1 (Strict Auth Separation):
        // Akun yang dibuat via Google OAuth tanpa password tidak bisa login melalui form password.
        if ($user && ! empty($user->google_id) && empty($user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Akun ini terdaftar melalui Google. Silakan gunakan tombol Lanjutkan dengan Google.',
            ], 403);
        }

        if (! $user || empty($user->password) || ! Hash::check($password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Username/Email atau kata sandi yang Anda masukkan salah.',
            ], 401);
        }

        return app(GoogleAuthController::class)->buildUserAuthResponse($user, $deviceName);
    }

    /**
     * Registrasi akun Klien baru via Username (+ Email opsional) & Password untuk Mobile Flutter.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_).',
            'username.unique' => 'Username tersebut sudah digunakan, silakan pilih username lain.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email tersebut sudah terdaftar pada akun lain.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $email = ! empty($validated['email']) ? strtolower(trim($validated['email'])) : null;
        $deviceName = (string) ($validated['device_name'] ?? 'flutter-token');

        $user = User::create([
            'name' => trim($validated['name']),
            'username' => strtolower(trim($validated['username'])),
            'email' => $email,
            'password' => $validated['password'],
            'role' => 'client',
            'status' => 'approved',
            'google_id' => null,
            'avatar' => null,
        ]);

        return app(GoogleAuthController::class)->buildUserAuthResponse($user, $deviceName);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        if (in_array($user->status, ['pending', 'rejected'], true)) {
            $user->currentAccessToken()?->delete();

            return response()->json([
                'success' => false,
                'message' => $user->status === 'pending'
                    ? 'Akun Anda masih menunggu verifikasi admin.'
                    : 'Akun Anda ditolak oleh admin.',
            ], 403);
        }

        $user->load('client');
        $member = null;
        if ($user->role === 'member') {
            $member = Member::where('user_id', $user->id)->first();
        }

        $nextStep = match ($user->role) {
            'client' => is_null($user->client) ? 'complete_profile' : 'dashboard',
            'member' => ($member && !empty($member->call_sign)) ? 'dashboard' : 'complete_profile',
            default => 'dashboard',
        };

        $data = $user->toArray();
        $data['has_password'] = ! empty($user->password);
        $data['next_step'] = $nextStep;
        $data['member'] = $member;

        return response()->json(['data' => $data]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out.']);
    }
}
