<?php

namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Controller;
use App\Models\Inspiration;
use Illuminate\Http\Request;

class InspirationController extends Controller
{
    public function index(Request $request)
    {
        $themes = Inspiration::approved()->distinct()->orderBy('theme')->pluck('theme');
        $theme = $themes->contains($request->query('theme')) ? $request->query('theme') : null;

        $items = Inspiration::approved()
            ->with('photos')
            ->when($theme, fn ($q) => $q->where('theme', $theme))
            ->orderByDesc('reviewed_at')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'data' => $items,
            'themes' => $themes,
            'active_theme' => $theme,
        ]);
    }

    public function show(string $slug)
    {
        $item = Inspiration::approved()
            ->with(['photos', 'package'])
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Inspiration::approved()
            ->with('photos')
            ->where('id', '!=', $item->id)
            ->latest('id')
            ->take(3)
            ->get();

        return response()->json([
            'data' => $item,
            'package' => $item->package,
            'related' => $related,
        ]);
    }
}
