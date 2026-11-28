<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\GoogleAuthController;
use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AuthController extends Controller
{
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

    public function register(Request $request, OtpService $otpService): JsonResponse
    {
        $validated = $request->validate(
            OtpService::REGISTER_RULES + ['device_name' => ['nullable', 'string', 'max:255']],
            OtpService::REGISTER_MESSAGES
        );

        try {
            $meta = $otpService->issueRegistration($validated, (string) ($validated['device_name'] ?? 'flutter-token'));
        } catch (RuntimeException $e) {
            return $this->fail($e->getMessage());
        }

        return $this->otpResponse($meta, 'Kode OTP 6 digit telah dikirim ke ' . $meta['masked_email']);
    }

    public function verifyRegisterOtp(Request $request, OtpService $otpService): JsonResponse
    {
        $request->validate([
            'challenge_id' => ['required', 'string'],
            'code' => ['required', 'string', 'min:6', 'max:10'],
        ], ['code.required' => 'Kode OTP 6 digit wajib diisi.']);

        try {
            ['user' => $user, 'payload' => $payload] = $otpService->completeRegistration(
                (string) $request->input('challenge_id'),
                (string) $request->input('code')
            );
        } catch (RuntimeException $e) {
            return $this->fail($e->getMessage());
        }

        return app(GoogleAuthController::class)->buildUserAuthResponse(
            $user,
            (string) ($payload['device_name'] ?? 'flutter-token')
        );
    }

    public function requestPasswordOtp(Request $request, OtpService $otpService): JsonResponse
    {
        $request->validate(['email' => ['required', 'email', 'max:255']], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
        ]);

        try {
            $meta = $otpService->issuePasswordReset((string) $request->input('email'));
        } catch (RuntimeException $e) {
            return $this->fail($e->getMessage());
        }

        return $this->otpResponse($meta, 'Kode OTP telah dikirim ke ' . $meta['masked_email']);
    }

    public function resetPasswordWithOtp(Request $request, OtpService $otpService): JsonResponse
    {
        $request->validate([
            'challenge_id' => ['required', 'string'],
            'code' => ['required', 'string', 'min:6', 'max:10'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'code.required' => 'Kode OTP 6 digit wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        try {
            $otpService->completePasswordReset(
                (string) $request->input('challenge_id'),
                (string) $request->input('code'),
                (string) $request->input('password')
            );
        } catch (RuntimeException $e) {
            return $this->fail($e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Kata sandi berhasil diperbarui. Silakan masuk dengan kata sandi baru Anda.',
        ]);
    }

    public function resendOtp(Request $request, OtpService $otpService): JsonResponse
    {
        $request->validate([
            'challenge_id' => ['required', 'string'],
            'purpose' => ['required', 'in:register,password_reset'],
        ]);

        try {
            $meta = $otpService->resend((string) $request->input('challenge_id'), (string) $request->input('purpose'));
        } catch (RuntimeException $e) {
            return $this->fail($e->getMessage());
        }

        return $this->otpResponse($meta, 'Kode OTP baru telah dikirim ke ' . $meta['masked_email']);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        if (! in_array($user->role, ['client', 'member'], true)) {
            $user->currentAccessToken()?->delete();

            return response()->json([
                'success' => false,
                'message' => 'Akun Admin dan Vendor hanya dapat diakses melalui Website.',
            ], 403);
        }

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
        $member = $user->role === 'member' ? Member::where('user_id', $user->id)->first() : null;

        $nextStep = match ($user->role) {
            'client' => is_null($user->client) ? 'complete_profile' : 'dashboard',
            'member' => ($member && ! empty($member->call_sign)) ? 'dashboard' : 'complete_profile',
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
        $user = $request->user();
        $user->update(['fcm_token' => null]);
        $user->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out.']);
    }

    private function otpResponse(array $meta, string $message): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'challenge_id' => $meta['challenge_id'],
                'masked_email' => $meta['masked_email'],
                'resend_wait' => $meta['resend_wait'],
                'expires_in' => $meta['expires_in'],
                'dev_code' => $meta['dev_code'],
            ],
        ]);
    }

    private function fail(string $message, int $status = 422): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message], $status);
    }
}
