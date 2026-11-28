<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Booking;
use App\Models\Member;
use App\Models\Package;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class NotificationService
{
    public const TOPIC_EXCLUSIVE_PACKAGES = 'exclusive_packages';

    public function notifyUser(
        ?User $user,
        string $kind,
        string $title,
        string $body,
        ?string $route = null
    ): void {
        if (! $user) {
            return;
        }

        AppNotification::create([
            'user_id' => $user->id,
            'kind' => $kind,
            'title' => $title,
            'body' => $body,
            'route' => $route,
        ]);

        $token = $user->fcm_token;
        if (! filled($token)) {
            return;
        }

        dispatch(function () use ($token, $kind, $title, $body, $route) {
            $this->sendFcmPayload([
                'token' => $token,
                'notification' => ['title' => $title, 'body' => $body],
                'data' => array_filter([
                    'kind' => $kind,
                    'route' => $route ?? '',
                ], fn ($v) => $v !== ''),
            ], $token);
        })->afterResponse();
    }

    public function notifyBookingClient(
        Booking $booking,
        string $kind,
        string $title,
        string $body,
        string $route = '/my-bookings'
    ): void {
        $booking->loadMissing('client.user');
        $this->notifyUser($booking->client?->user, $kind, $title, $body, $route);
    }

    public function notifyMember(
        ?Member $member,
        string $kind,
        string $title,
        string $body,
        string $route = '/member/dashboard'
    ): void {
        if (! $member) {
            return;
        }

        $member->loadMissing('user');
        $this->notifyUser($member->user, $kind, $title, $body, $route);
    }

    public function broadcastExclusivePackage(Package $package): void
    {
        $title = 'Paket Eksklusif Baru: ' . $package->name;
        $capacity = $package->guest_capacity ? " untuk {$package->guest_capacity} tamu" : '';
        $body = "Koleksi paket pernikahan unggulan{$capacity} kini tersedia. Ketuk untuk melihat detail vendor & penawaran.";
        $route = '/package/' . ($package->slug ?: $package->id);
        $now = now();

        $clientIds = User::query()
            ->where('role', 'client')
            ->where('status', 'approved')
            ->pluck('id');

        foreach ($clientIds->chunk(500) as $chunk) {
            $rows = [];
            foreach ($chunk as $userId) {
                $rows[] = [
                    'user_id' => $userId,
                    'kind' => 'promo',
                    'title' => $title,
                    'body' => $body,
                    'route' => $route,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            AppNotification::insert($rows);
        }

        dispatch(function () use ($title, $body, $route) {
            $this->sendFcmPayload([
                'topic' => self::TOPIC_EXCLUSIVE_PACKAGES,
                'notification' => ['title' => $title, 'body' => $body],
                'data' => [
                    'kind' => 'promo',
                    'route' => $route,
                ],
            ]);
        })->afterResponse();
    }

    private function sendFcmPayload(array $messageBody, ?string $targetToken = null): void
    {
        $credentials = $this->loadCredentials();
        if (! $credentials) {
            return;
        }

        $projectId = $credentials['project_id'] ?? null;
        if (! filled($projectId)) {
            return;
        }

        try {
            $accessToken = $this->resolveAccessToken($credentials);
            if (! $accessToken) {
                return;
            }

            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->timeout(10)
                ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                    'message' => $messageBody + [
                        'android' => [
                            'priority' => 'high',
                            'notification' => [
                                'channel_id' => 'mydream_high_importance',
                                'sound' => 'default',
                            ],
                        ],
                    ],
                ]);

            if ($targetToken && in_array($response->status(), [404, 410], true)) {
                User::where('fcm_token', $targetToken)->update(['fcm_token' => null]);
            } elseif (! $response->successful()) {
                Log::warning('FCM push failed.', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
            }
        } catch (Throwable $e) {
            Log::warning('FCM push exception: ' . $e->getMessage());
        }
    }

    private function loadCredentials(): ?array
    {
        $path = (string) config('services.firebase.credentials', '');
        if ($path === '' || ! is_file($path) || ! is_readable($path)) {
            return null;
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        return is_array($decoded) ? $decoded : null;
    }

    private function resolveAccessToken(array $credentials): ?string
    {
        $clientEmail = $credentials['client_email'] ?? null;
        $privateKey = $credentials['private_key'] ?? null;
        if (! filled($clientEmail) || ! filled($privateKey)) {
            return null;
        }

        $cacheKey = 'fcm_oauth_token_' . md5($clientEmail);

        return Cache::remember($cacheKey, 3000, function () use ($clientEmail, $privateKey) {
            $now = time();
            $header = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $claims = $this->base64UrlEncode(json_encode([
                'iss' => $clientEmail,
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600,
            ]));

            $unsigned = "{$header}.{$claims}";
            $signature = '';
            if (! openssl_sign($unsigned, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
                return null;
            }

            $jwt = "{$unsigned}." . $this->base64UrlEncode($signature);
            $response = Http::asForm()->timeout(10)->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            return $response->successful() ? $response->json('access_token') : null;
        });
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
