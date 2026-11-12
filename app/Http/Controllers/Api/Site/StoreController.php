<?php

namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Package;
use App\Models\Vendor;

class StoreController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => [
                'categories' => Category::orderBy('name')->get(),
                'packages' => Package::with(['vendors.photos'])->orderByDesc('is_featured')->latest()->limit(6)->get(),
                'vendors' => Vendor::with(['category', 'photos'])->latest()->limit(8)->get(),
            ],
        ]);
    }
}
