<?php

namespace App\Http\Controllers\Site;

use App\Data\Site\InspirationData;
use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InspirationController extends Controller
{
    public function index(Request $request): View
    {
        $themes = InspirationData::themes();
        $theme = in_array($request->query('theme'), $themes, true) ? $request->query('theme') : null;

        return view('site.inspiration.index', [
            'items' => collect(InspirationData::all())->when($theme, fn ($c) => $c->where('theme', $theme))->values(),
            'themes' => $themes,
            'activeTheme' => $theme,
        ]);
    }

    public function show(string $slug): View
    {
        $item = InspirationData::find($slug);
        abort_if(! $item, 404);

        return view('site.inspiration.show', [
            'item' => $item,
            'package' => $item['package_slug'] ? Package::where('slug', $item['package_slug'])->first() : null,
            'related' => collect(InspirationData::all())->where('slug', '!=', $slug)->take(3)->values(),
        ]);
    }
}
