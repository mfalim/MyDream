<?php

namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Controller;
use App\Models\Vendor;

class VendorController extends Controller
{
    public function show(string $slug)
    {
        return response()->json(['data' => Vendor::availableForBooking()
            ->with(['category', 'photos', 'packages' => fn ($query) => $query->availableForBooking()->with('vendors.photos')])
            ->where('slug', $slug)
            ->firstOrFail()]);
    }
}
