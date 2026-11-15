<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Login / Register via Google untuk Mobile Flutter.
     * Mendukung:
     * - id_token (dari package google_sign_in)
     * - access_token (OAuth2 access token)
     * - code (Authorization code dari redirect OAuth Google)
     */
    public function loginWithGoogle(Request $request): JsonResponse
    {
        $request->validate([
            'id_token' => ['nullable', 'string', 'required_without_all:access_token,code,token'],
            'access_token' => ['nullable', 'string', 'required_without_all:id_token,code,token'],
            'code' => ['nullable', 'string', 'required_without_all:id_token,access_token,token'],
            'token' => ['nullable', 'string', 'required_without_all:id_token,access_token,code'],
            'redirect_uri' => ['nullable', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $googleUser = null;

            if ($request->filled('code')) {
                $googleUser = $this->resolveGoogleUserFromCode(
                    $request->input('code'),
                    $request->input('redirect_uri')
                );
            } else {
                $rawToken = $request->input('id_token')
                    ?? $request->input('access_token')
                    ?? $request->input('token');
                $googleUser = $this->resolveGoogleUser($rawToken);
            }

            if (! $googleUser || empty($googleUser->email)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token atau kode Google tidak valid atau verifikasi gagal.',
                ], 401);
            }

            return $this->issueAuthResponse(
                $googleUser,
                $request->input('device_name', 'flutter-token')
            );
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses login Google: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Redirect ke halaman Google OAuth (fallback / web-flow untuk Mobile Flutter).
     */
    public function redirectToGoogle(Request $request)
    {
        $sessionId = $request->query('session_id', '');

        return Socialite::driver('google')
            ->stateless()
            ->with([
                'state' => 'mobile:' . $sessionId,
                'prompt' => 'select_account',
            ])
            ->redirect();
    }

    /**
     * Polling hasil login Google OAuth (ketika callback diterima langsung oleh server Laravel).
     */
    public function pollAuthResult(Request $request): JsonResponse
    {
        $sessionId = (string) $request->query('session_id', '');
        if ($sessionId === '') {
            return response()->json([
                'success' => false,
                'message' => 'session_id wajib diisi.',
            ], 422);
        }

        $cached = Cache::pull('mobile_google_auth:' . $sessionId);
        if (! $cached) {
            return response()->json([
                'success' => false,
                'status' => 'pending',
                'data' => ['status' => 'pending'],
            ], 202);
        }

        $statusCode = $cached['status_code'] ?? 200;
        unset($cached['status_code']);

        return response()->json($cached, $statusCode);
    }

    /**
     * Buat / update user, validasi status & profil sesuai alur Web GoogleAuthController,
     * dan terbitkan token Sanctum.
     */
    public function issueAuthResponse(object $googleUser, string $deviceName = 'flutter-token'): JsonResponse
    {
        $user = User::createOrLogin($googleUser);

        if ($user->status === 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda masih menunggu verifikasi admin.',
            ], 403);
        }

        if ($user->status === 'rejected') {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda ditolak oleh admin.',
            ], 403);
        }

        $client = Client::where('users_id', $user->id)->first();
        $member = $user->role === 'member'
            ? Member::where('user_id', $user->id)->first()
            : null;

        $nextStep = match ($user->role) {
            'client' => is_null($client) ? 'complete_profile' : 'dashboard',
            'member' => ($member && !empty($member->call_sign)) ? 'dashboard' : 'complete_profile',
            default => 'dashboard',
        };

        $redirectTo = match ($user->role) {
            'client' => is_null($client) ? '/login/profile' : '/user/dashboard',
            'member' => ($member && !empty($member->call_sign)) ? '/member/dashboard' : '/member/profile/complete',
            'admin' => '/admin/dashboard',
            'vendor' => '/vendor/dashboard',
            default => '/user/dashboard',
        };

        $token = $user->createToken($deviceName)->plainTextToken;
        $user->setRelation('client', $client);
        if ($member) {
            $user->setRelation('member', $member);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'user' => $user,
                'client' => $client,
                'member' => $member,
                'token' => $token,
                'token_type' => 'Bearer',
                'role' => $user->role,
                'next_step' => $nextStep,
                'redirect_to' => $redirectTo,
            ],
        ]);
    }

    /**
     * Tukar authorization code Google menjadi profil user Google.
     */
    protected function resolveGoogleUserFromCode(string $code, ?string $redirectUri = null): ?object
    {
        try {
            $response = Http::asForm()->timeout(15)->post('https://oauth2.googleapis.com/token', [
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $redirectUri ?: config('services.google.redirect'),
            ]);

            if (! $response->successful()) {
                Log::warning('Gagal menukar kode OAuth Google: ' . $response->body());
                return null;
            }

            $tokenData = $response->json();
            $idToken = $tokenData['id_token'] ?? null;
            $accessToken = $tokenData['access_token'] ?? null;

            if ($idToken) {
                $user = $this->resolveGoogleUser($idToken);
                if ($user) {
                    return $user;
                }
            }

            if ($accessToken) {
                return $this->resolveGoogleUser($accessToken);
            }
        } catch (\Throwable $e) {
            Log::error('Exception saat menukar kode OAuth Google: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Verifikasi ID token atau Access Token Google dan ambil profil pengguna.
     */
    protected function resolveGoogleUser(string $token): ?object
    {
        // 1. Coba verifikasi dengan Socialite driver (stateless)
        try {
            $socialiteUser = Socialite::driver('google')->stateless()->userFromToken($token);
            if ($socialiteUser && ! empty($socialiteUser->getEmail())) {
                return (object) [
                    'id' => $socialiteUser->getId(),
                    'name' => $socialiteUser->getName() ?: explode('@', $socialiteUser->getEmail())[0],
                    'email' => $socialiteUser->getEmail(),
                    'avatar' => $socialiteUser->getAvatar(),
                ];
            }
        } catch (\Throwable $e) {
            Log::info('Socialite userFromToken gagal, beralih ke endpoint Google: ' . $e->getMessage());
        }

        // 2. Verifikasi via endpoint resmi Google tokeninfo (untuk JWT id_token)
        try {
            $response = Http::timeout(10)->get('https://oauth2.googleapis.com/tokeninfo', [
                'id_token' => $token,
            ]);

            if ($response->successful()) {
                $payload = $response->json();
                $issuer = $payload['iss'] ?? '';

                if (in_array($issuer, ['https://accounts.google.com', 'accounts.google.com'], true)) {
                    $webClientId = trim(config('services.google.client_id') ?? '');
                    $androidClientId = trim(config('services.google.android_client_id') ?? '');
                    $allowedClients = array_values(array_filter([$webClientId, $androidClientId]));

                    $aud = $payload['aud'] ?? '';
                    $azp = $payload['azp'] ?? '';

                    if (! empty($allowedClients)) {
                        $isAudMatch = in_array($aud, $allowedClients, true) || in_array($azp, $allowedClients, true);
                        if (! $isAudMatch) {
                            Log::warning("Google token audience mismatch. aud: {$aud}, azp: {$azp}");
                            return null;
                        }
                    }

                    return (object) [
                        'id' => $payload['sub'] ?? null,
                        'name' => $payload['name'] ?? ($payload['email'] ? explode('@', $payload['email'])[0] : 'User'),
                        'email' => $payload['email'] ?? null,
                        'avatar' => $payload['picture'] ?? null,
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::error('Gagal verifikasi id_token Google via HTTP: ' . $e->getMessage());
        }

        // 3. Fallback via endpoint userinfo Google (jika yang dikirim adalah OAuth2 access_token)
        try {
            $userInfoResponse = Http::withToken($token)
                ->timeout(10)
                ->get('https://www.googleapis.com/oauth2/v3/userinfo');

            if ($userInfoResponse->successful()) {
                $payload = $userInfoResponse->json();
                if (! empty($payload['email'])) {
                    return (object) [
                        'id' => $payload['sub'] ?? null,
                        'name' => $payload['name'] ?? explode('@', $payload['email'])[0],
                        'email' => $payload['email'],
                        'avatar' => $payload['picture'] ?? null,
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::error('Gagal verifikasi access_token Google via userinfo: ' . $e->getMessage());
        }

        return null;
    }
}
