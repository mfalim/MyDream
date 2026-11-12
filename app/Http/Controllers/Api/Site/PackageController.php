<?php

namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['sometimes', 'string', 'max:100'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        return response()->json(Package::with(['vendors.category', 'vendors.photos'])
            ->when($filters['q'] ?? null, fn ($query, $q) => $query->where('name', 'like', "%{$q}%"))
            ->latest()->paginate($filters['per_page'] ?? 15));
    }

    public function show(string $slug)
    {
        $package = Package::with(['vendors.category', 'vendors.photos'])->where('slug', $slug)->firstOrFail();

        return response()->json(['data' => $package]);
    }
}
