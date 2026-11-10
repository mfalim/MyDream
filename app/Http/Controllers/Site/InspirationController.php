<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Inspiration;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Halaman inspirasi publik: hanya cerita yang sudah disetujui admin. */
class InspirationController extends Controller
{
    public function index(Request $request): View
    {
        $themes = Inspiration::approved()->distinct()->orderBy('theme')->pluck('theme');
        $theme = $themes->contains($request->query('theme')) ? $request->query('theme') : null;

        return view('site.inspiration.index', [
            'items' => Inspiration::approved()->with('photos')
                ->when($theme, fn ($q) => $q->where('theme', $theme))
                ->orderByDesc('reviewed_at')->orderByDesc('id')->get(),
            'themes' => $themes,
            'activeTheme' => $theme,
        ]);
    }

    public function show(string $slug): View
    {
        $item = Inspiration::approved()
            ->with(['photos', 'package', 'booking.events.schedules.vendor.category', 'booking.events.schedules.vendor.photos'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('site.inspiration.show', [
            'item' => $item,
            'package' => $item->package,
            'vendors' => $item->usedVendors(),
            'related' => Inspiration::approved()->with('photos')->where('id', '!=', $item->id)->latest('id')->take(3)->get(),
        ]);
    }
}
