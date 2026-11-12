<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\View\View;

class PackageController extends Controller
{
    public function show(string $slug): View
    {
        $package = Package::with(['vendors.category', 'vendors.photos'])->where('slug', $slug)->firstOrFail();

        $gallery = collect([$package->photo])
            ->merge($package->vendors->flatMap(fn ($vendor) => $vendor->photos->pluck('photo')))
            ->filter()
            ->unique()
            ->values()
            ->take(5);

        $related = Package::with(['vendors.photos'])
            ->where('id', '!=', $package->id)
            ->latest()
            ->take(3)
            ->get();

        return view('site.package.show', [
            'package' => $package,
            'total' => $package->vendors->sum('price'),
            'gallery' => $gallery,
            'related' => $related,
        ]);
    }
}
