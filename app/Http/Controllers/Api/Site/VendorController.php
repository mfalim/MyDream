<?php

namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Controller;
use App\Models\Vendor;

class VendorController extends Controller
{
    public function show(string $slug)
    {
        return response()->json(['data' => Vendor::with(['category', 'photos', 'packages.vendors.photos'])->where('slug', $slug)->firstOrFail()]);
    }
}
