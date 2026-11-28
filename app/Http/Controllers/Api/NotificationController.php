<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $items = AppNotification::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (AppNotification $n) => [
                'id' => (string) $n->id,
                'kind' => $n->kind,
                'title' => $n->title,
                'body' => $n->body,
                'route' => $n->route,
                'created_at' => $n->created_at?->toIso8601String(),
                'read_at' => $n->read_at?->toIso8601String(),
            ]);

        return response()->json(['data' => $items]);
    }

    public function markRead(Request $request, string $id): JsonResponse
    {
        if (ctype_digit($id)) {
            AppNotification::query()
                ->where('user_id', $request->user()->id)
                ->where('id', (int) $id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        AppNotification::query()
            ->where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function saveDeviceToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['nullable', 'string', 'max:512'],
        ]);

        $user = $request->user();
        $token = filled($validated['token'] ?? null) ? trim($validated['token']) : null;

        if ($token !== null) {
            User::query()
                ->where('fcm_token', $token)
                ->where('id', '!=', $user->id)
                ->update(['fcm_token' => null]);
        }

        $user->update(['fcm_token' => $token]);

        return response()->json(['success' => true]);
    }
}
