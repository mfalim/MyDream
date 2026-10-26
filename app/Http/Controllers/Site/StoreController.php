<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Package;
use App\Models\Vendor;
use Illuminate\View\View;

/**
 * Store = halaman discovery/rekomendasi (tanpa filter). Pencarian & filter ada di CatalogController.
 */
class StoreController extends Controller
{
    public function index(): View
    {
        return view('site.store', [
            'categories' => Category::orderBy('name')->get(),
            'packages' => Package::with(['vendors.photos'])->orderByDesc('is_featured')->latest()->take(6)->get(),
            'vendors' => Vendor::with(['category', 'photos'])->latest()->take(8)->get(),
        ]);
    }
}
