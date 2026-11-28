<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class AuthOtpController extends Controller
{
    public function __construct(
        private readonly OtpService $otpService,
    ) {}

    public function showVerify(Request $request)
    {
        [, $purpose, $meta] = $this->resolveActiveSession($request, [
            OtpService::PURPOSE_REGISTER,
            OtpService::PURPOSE_ADMIN_LOGIN,
        ]);

        if (! $meta) {
            return redirect()->route('login')
                ->with('error', 'Sesi verifikasi tidak ditemukan atau telah kedaluwarsa. Silakan ulangi kembali.');
        }

        return view('login.login', ['mode' => $purpose, 'meta' => $meta]);
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => ['required', 'string', 'min:6', 'max:10']], [
            'code.required' => 'Kode OTP 6 digit wajib diisi.',
        ]);

        $challengeId = (string) $request->session()->get('otp_flow.challenge_id', '');
        $purpose = (string) $request->session()->get('otp_flow.purpose', '');

        if (! in_array($purpose, [OtpService::PURPOSE_REGISTER, OtpService::PURPOSE_ADMIN_LOGIN], true) || $challengeId === '') {
            return redirect()->route('login')->with('error', 'Sesi verifikasi telah berakhir.');
        }

        try {
            if ($purpose === OtpService::PURPOSE_REGISTER) {
                $user = $this->otpService->completeRegistration($challengeId, (string) $request->input('code'))['user'];
            } else {
                $entry = $this->otpService->verify($challengeId, (string) $request->input('code'), $purpose);
                $user = User::findOrFail($entry['payload']['user_id'] ?? 0);
            }
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $request->session()->forget('otp_flow');
        Auth::login($user);
        $request->session()->regenerate();

        return app(GoogleAuthController::class)->redirectAfterLogin($user);
    }

    public function showForgotForm()
    {
        return view('login.login', ['mode' => 'forgot_request', 'meta' => null]);
    }

    public function sendResetOtp(Request $request)
    {
        $request->validate(['email' => ['required', 'email', 'max:255']], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
        ]);

        try {
            $meta = $this->otpService->issuePasswordReset((string) $request->input('email'));
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $request->session()->put('otp_flow', [
            'challenge_id' => $meta['challenge_id'],
            'purpose' => OtpService::PURPOSE_PASSWORD_RESET,
        ]);

        return redirect()->route('password.otp.form')->with('success', 'Kode OTP telah dikirim ke ' . $meta['masked_email']);
    }

    public function showResetForm(Request $request)
    {
        [, , $meta] = $this->resolveActiveSession($request, [OtpService::PURPOSE_PASSWORD_RESET]);
        if (! $meta) {
            return redirect()->route('password.request')->with('error', 'Kode OTP telah kedaluwarsa. Silakan minta kode baru.');
        }

        return view('login.login', ['mode' => OtpService::PURPOSE_PASSWORD_RESET, 'meta' => $meta]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'min:6', 'max:10'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'code.required' => 'Kode OTP 6 digit wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $challengeId = (string) $request->session()->get('otp_flow.challenge_id', '');
        $purpose = (string) $request->session()->get('otp_flow.purpose', '');

        if ($purpose !== OtpService::PURPOSE_PASSWORD_RESET || $challengeId === '') {
            return redirect()->route('password.request')->with('error', 'Sesi atur ulang password telah berakhir.');
        }

        try {
            $this->otpService->completePasswordReset(
                $challengeId,
                (string) $request->input('code'),
                (string) $request->input('password')
            );
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $request->session()->forget('otp_flow');

        return redirect()->route('login')->with('success', 'Password berhasil diperbarui! Silakan masuk dengan password baru Anda.');
    }

    public function resend(Request $request)
    {
        $challengeId = (string) $request->session()->get('otp_flow.challenge_id', '');
        $purpose = (string) $request->session()->get('otp_flow.purpose', '');

        if ($challengeId === '' || $purpose === '') {
            return redirect()->route('login');
        }

        try {
            $this->otpService->resend($challengeId, $purpose);

            return back()->with('success', 'Kode OTP baru telah dikirim ke email Anda.');
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function resolveActiveSession(Request $request, array $allowedPurposes): array
    {
        $challengeId = (string) $request->session()->get('otp_flow.challenge_id', '');
        $purpose = (string) $request->session()->get('otp_flow.purpose', '');

        if ($challengeId === '' || ! in_array($purpose, $allowedPurposes, true)) {
            return ['', '', null];
        }

        $meta = $this->otpService->getMeta($challengeId, $purpose);
        if (! $meta) {
            $request->session()->forget('otp_flow');
        }

        return [$challengeId, $purpose, $meta];
    }
}
