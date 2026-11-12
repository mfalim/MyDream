<?php

namespace App\Http\Controllers\Api\Site;

use App\Data\Site\InspirationData;
use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class InspirationController extends Controller
{
    public function index(Request $request)
    {
        $themes = InspirationData::themes();
        $theme = in_array($request->query('theme'), $themes, true) ? $request->query('theme') : null;

        return response()->json([
            'data' => collect(InspirationData::all())->when($theme, fn ($items) => $items->where('theme', $theme))->values(),
            'themes' => $themes,
            'active_theme' => $theme,
        ]);
    }

    public function show(string $slug)
    {
        $item = InspirationData::find($slug);
        abort_if(! $item, 404);
        $package = ! empty($item['package_slug']) ? Package::where('slug', $item['package_slug'])->first() : null;

        return response()->json([
            'data' => $item,
            'package' => $package,
            'related' => collect(InspirationData::all())->where('slug', '!=', $slug)->take(3)->values(),
        ]);
    }
}
