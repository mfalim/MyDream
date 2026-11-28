<?php

namespace App\Services;

use App\Mail\OtpCodeMail;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Layanan OTP & pemulihan akun terpusat (Web + Mobile API) berbasis Cache.
 * - HMAC-SHA256 constant-time untuk kode 6 digit (~0.005ms, bebas beban Bcrypt).
 * - Pengiriman SMTP dieksekusi pasca-respons HTTP (afterResponse) agar klik terasa instan.
 */
class OtpService
{
    public const PURPOSE_REGISTER = 'register';
    public const PURPOSE_PASSWORD_RESET = 'password_reset';
    public const PURPOSE_ADMIN_LOGIN = 'admin_login';

    public const REGISTER_RULES = [
        'name' => ['required', 'string', 'max:120'],
        'username' => ['required', 'string', 'min:3', 'max:50', 'alpha_dash', 'unique:users,username'],
        'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'string', 'min:6', 'confirmed'],
    ];

    public const REGISTER_MESSAGES = [
        'name.required' => 'Nama lengkap wajib diisi.',
        'username.required' => 'Username wajib diisi.',
        'username.min' => 'Username minimal 3 karakter.',
        'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_).',
        'username.unique' => 'Username tersebut sudah digunakan, silakan pilih username lain.',
        'email.required' => 'Email wajib diisi untuk menerima kode OTP.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email tersebut sudah terdaftar pada akun lain.',
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal 6 karakter.',
        'password.confirmed' => 'Konfirmasi password tidak cocok.',
    ];

    private const TTL_SECONDS = 300;
    private const RESEND_COOLDOWN = 60;
    private const MAX_ATTEMPTS = 5;

    public function issueRegistration(array $validated, string $deviceName = 'web'): array
    {
        return $this->issue(self::PURPOSE_REGISTER, $validated['email'], [
            'name' => trim($validated['name']),
            'username' => strtolower(trim($validated['username'])),
            'password_hash' => Hash::make($validated['password']),
            'device_name' => $deviceName,
        ]);
    }

    /**
     * @return array{user: User, payload: array}
     */
    public function completeRegistration(string $challengeId, string $code): array
    {
        $entry = $this->verify($challengeId, $code, self::PURPOSE_REGISTER);
        $payload = $entry['payload'] ?? [];
        $username = (string) ($payload['username'] ?? '');

        $alreadyTaken = User::query()
            ->where('username', $username)
            ->orWhere('email', $entry['email'])
            ->exists();

        if ($alreadyTaken) {
            throw new RuntimeException('Username atau Email tersebut sudah terdaftar. Silakan langsung masuk.');
        }

        $user = new User([
            'name' => $payload['name'],
            'username' => $username,
            'email' => $entry['email'],
            'role' => 'client',
            'status' => 'approved',
            'google_id' => null,
            'avatar' => null,
        ]);
        $user->setRawAttributes(array_merge($user->getAttributes(), [
            'password' => $payload['password_hash'],
        ]));
        $user->save();

        return ['user' => $user, 'payload' => $payload];
    }

    public function issuePasswordReset(string $email): array
    {
        $normalized = strtolower(trim($email));
        $user = User::where('email', $normalized)->first();

        if (! $user) {
            throw new RuntimeException('Email tersebut belum terdaftar pada sistem kami.');
        }

        if (! empty($user->google_id) && empty($user->password)) {
            throw new RuntimeException('Akun ini menggunakan Google Sign-In dan tidak memiliki password manual.');
        }

        return $this->issue(self::PURPOSE_PASSWORD_RESET, $normalized, [
            'user_id' => $user->id,
        ]);
    }

    public function completePasswordReset(string $challengeId, string $code, string $newPassword): User
    {
        $entry = $this->verify($challengeId, $code, self::PURPOSE_PASSWORD_RESET);
        $user = User::findOrFail($entry['payload']['user_id'] ?? 0);
        $user->password = $newPassword;
        $user->save();
        $user->tokens()->delete();

        return $user;
    }

    public function issue(string $purpose, string $email, array $payload = []): array
    {
        $challengeId = (string) Str::uuid();
        $normalizedEmail = strtolower(trim($email));
        $code = (string) random_int(100000, 999999);
        $now = now()->timestamp;

        $entry = [
            'purpose' => $purpose,
            'email' => $normalizedEmail,
            'masked_email' => self::maskEmail($normalizedEmail),
            'code_mac' => $this->mac($code),
            'attempts' => 0,
            'expires_at' => $now + self::TTL_SECONDS,
            'resend_at' => $now + self::RESEND_COOLDOWN,
            'payload' => $payload,
            'dev_code' => $this->isLocalLogMailer() ? $code : null,
        ];

        Cache::put($this->cacheKey($challengeId), $entry, now()->addSeconds(self::TTL_SECONDS));
        $this->deliverMail($normalizedEmail, $code, $purpose);

        return $this->publicMeta($challengeId, $entry);
    }

    public function resend(string $challengeId, string $expectedPurpose): array
    {
        $entry = Cache::get($this->cacheKey($challengeId));
        if (! is_array($entry) || ($entry['purpose'] ?? null) !== $expectedPurpose) {
            throw new RuntimeException('Sesi OTP telah berakhir. Silakan ulangi proses dari awal.');
        }

        $waitSeconds = max(0, (int) ($entry['resend_at'] ?? 0) - now()->timestamp);
        if ($waitSeconds > 0) {
            throw new RuntimeException("Mohon tunggu {$waitSeconds} detik sebelum meminta kode baru.");
        }

        $code = (string) random_int(100000, 999999);
        $now = now()->timestamp;

        $entry['code_mac'] = $this->mac($code);
        $entry['attempts'] = 0;
        $entry['expires_at'] = $now + self::TTL_SECONDS;
        $entry['resend_at'] = $now + self::RESEND_COOLDOWN;
        $entry['dev_code'] = $this->isLocalLogMailer() ? $code : null;

        Cache::put($this->cacheKey($challengeId), $entry, now()->addSeconds(self::TTL_SECONDS));
        $this->deliverMail($entry['email'], $code, $expectedPurpose);

        return $this->publicMeta($challengeId, $entry);
    }

    public function getMeta(string $challengeId, string $expectedPurpose): ?array
    {
        $entry = Cache::get($this->cacheKey($challengeId));
        if (! is_array($entry) || ($entry['purpose'] ?? null) !== $expectedPurpose) {
            return null;
        }

        if (now()->timestamp > (int) ($entry['expires_at'] ?? 0)) {
            Cache::forget($this->cacheKey($challengeId));

            return null;
        }

        return $this->publicMeta($challengeId, $entry);
    }

    public function verify(string $challengeId, string $code, string $expectedPurpose): array
    {
        $key = $this->cacheKey($challengeId);
        $entry = Cache::get($key);

        if (! is_array($entry) || ($entry['purpose'] ?? null) !== $expectedPurpose) {
            throw new RuntimeException('Kode OTP sudah kedaluwarsa atau tidak ditemukan. Silakan minta kode baru.');
        }

        if (now()->timestamp > (int) ($entry['expires_at'] ?? 0)) {
            Cache::forget($key);
            throw new RuntimeException('Kode OTP sudah kedaluwarsa (lebih dari 5 menit). Silakan minta kode baru.');
        }

        $cleanCode = preg_replace('/\D+/', '', $code);
        $storedMac = (string) ($entry['code_mac'] ?? '');

        if (strlen($cleanCode) !== 6 || $storedMac === '' || ! hash_equals($storedMac, $this->mac($cleanCode))) {
            $entry['attempts'] = (int) ($entry['attempts'] ?? 0) + 1;

            if ($entry['attempts'] >= self::MAX_ATTEMPTS) {
                Cache::forget($key);
                throw new RuntimeException('Terlalu banyak percobaan salah. Demi keamanan, silakan ulangi dari awal.');
            }

            $remainingTtl = max(10, (int) $entry['expires_at'] - now()->timestamp);
            Cache::put($key, $entry, now()->addSeconds($remainingTtl));
            $left = self::MAX_ATTEMPTS - $entry['attempts'];

            throw new RuntimeException("Kode OTP tidak sesuai. Sisa kesempatan mencoba: {$left} kali.");
        }

        Cache::forget($key);

        return $entry;
    }

    public static function maskEmail(string $email): string
    {
        if (! str_contains($email, '@')) {
            return $email;
        }

        [$local, $domain] = explode('@', $email, 2);
        $visible = mb_substr($local, 0, min(2, mb_strlen($local)));

        return $visible . str_repeat('*', max(3, mb_strlen($local) - mb_strlen($visible))) . '@' . $domain;
    }

    private function deliverMail(string $email, string $code, string $purpose): void
    {
        $label = match ($purpose) {
            self::PURPOSE_REGISTER => 'Pendaftaran Akun Klien',
            self::PURPOSE_PASSWORD_RESET => 'Atur Ulang Password',
            self::PURPOSE_ADMIN_LOGIN => 'Verifikasi Masuk Admin',
            default => 'Verifikasi Akun',
        };

        $mailable = new OtpCodeMail($code, $label, (int) (self::TTL_SECONDS / 60));

        // Pada PHP-FPM (VPS produksi), kirim SMTP setelah respons HTTP dikirim ke klien (~35ms latency).
        if (function_exists('fastcgi_finish_request') && ! $this->isLocalLogMailer()) {
            $masked = self::maskEmail($email);
            dispatch(static function () use ($email, $mailable, $masked, $purpose): void {
                try {
                    Mail::to($email)->send($mailable);
                } catch (\Throwable $e) {
                    Log::error('Gagal mengirim email OTP: ' . $e->getMessage(), [
                        'email' => $masked,
                        'purpose' => $purpose,
                    ]);
                }
            })->afterResponse();

            return;
        }

        try {
            Mail::to($email)->send($mailable);
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim email OTP: ' . $e->getMessage(), [
                'email' => self::maskEmail($email),
                'purpose' => $purpose,
            ]);

            if (! $this->isLocalLogMailer()) {
                throw new RuntimeException('Gagal mengirim email kode OTP. Periksa konfigurasi SMTP server atau coba beberapa saat lagi.');
            }
        }
    }

    private function publicMeta(string $challengeId, array $entry): array
    {
        return [
            'challenge_id' => $challengeId,
            'email' => $entry['email'],
            'masked_email' => $entry['masked_email'],
            'resend_wait' => max(0, (int) ($entry['resend_at'] ?? 0) - now()->timestamp),
            'expires_in' => max(0, (int) ($entry['expires_at'] ?? 0) - now()->timestamp),
            'dev_code' => $entry['dev_code'] ?? null,
        ];
    }

    private function mac(string $code): string
    {
        return hash_hmac('sha256', $code, (string) config('app.key'));
    }

    private function isLocalLogMailer(): bool
    {
        return (bool) config('app.debug') && in_array(config('mail.default'), ['log', 'array'], true);
    }

    private function cacheKey(string $challengeId): string
    {
        return 'auth_otp:' . $challengeId;
    }
}
