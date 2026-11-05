<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;

class AuthController extends Controller
{
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

        $nextStep = match ($user->role) {
            'client' => is_null($user->client) ? 'complete_profile' : 'dashboard',
            'member' => Member::where('user_id', $user->id)->exists() ? 'dashboard' : 'complete_profile',
            default => 'dashboard',
        };

        $data = $user->toArray();
        $data['next_step'] = $nextStep;

        return response()->json(['data' => $data]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out.']);
    }
}
