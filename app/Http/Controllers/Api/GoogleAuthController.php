<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function loginWithGoogle(Request $request): JsonResponse
    {
        $request->validate([
            'id_token' => ['required', 'string'],
        ]);

        try {
            $googleUser = Socialite::driver('google')
                ->userFromToken($request->id_token);

            $user = User::createOrLogin($googleUser);

            if (in_array($user->status, ['pending', 'rejected'])) {
                return response()->json([
                    'success' => false,
                    'message' => $user->status === 'pending'
                        ? 'Akun menunggu verifikasi admin.'
                        : 'Akun ditolak oleh admin.',
                ], 403);
            }

            $nextStep = match ($user->role) {
                'client' => is_null(
                    Client::findClientByIdUser($user->id)
                ) ? 'complete_profile' : 'dashboard',

                'member' => Member::where('user_id', $user->id)
                    ->exists() ? 'dashboard' : 'complete_profile',

                default => 'dashboard',
            };

            $token = $user->createToken('flutter-token')
                ->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil.',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'role' => $user->role,
                    'next_step' => $nextStep,
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Token Google tidak valid atau login gagal.',
            ], 401);
        }
    }
}
