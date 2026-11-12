<?php

namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Package;
use App\Models\Vendor;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['sometimes', 'string', 'max:100'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        return response()->json([
            'data' => [
                'categories' => Category::orderBy('name')->get(),
                'packages' => Package::with(['vendors.category', 'vendors.photos'])
                    ->when($filters['q'] ?? null, fn ($query, $q) => $query->where('name', 'like', "%{$q}%"))
                    ->latest()->paginate($filters['per_page'] ?? 15),
                'vendors' => Vendor::with(['category', 'photos'])
                    ->when($filters['q'] ?? null, fn ($query, $q) => $query->where('name', 'like', "%{$q}%"))
                    ->latest()->paginate($filters['per_page'] ?? 15),
            ],
        ]);
    }
}
