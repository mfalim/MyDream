<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\View\View;

class VendorController extends Controller
{
    public function show(string $slug): View
    {
        $vendor = Vendor::with(['category', 'photos', 'packages.vendors.photos'])->where('slug', $slug)->firstOrFail();

        $cover = $vendor->photos->firstWhere('is_cover', true) ?? $vendor->photos->first();

        return view('site.vendor.show', [
            'vendor' => $vendor,
            'cover' => $cover,
            'otherPhotos' => $vendor->photos->reject(fn ($photo) => $cover && $photo->id === $cover->id)->values(),
        ]);
    }
}
