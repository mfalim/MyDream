<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Package;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    public function index()
    {
        $vendors = Vendor::with('category')->get();
        $packages = Package::with('vendors.category')->get();
        
        $categories = Vendor::with('category')
            ->get()
            ->pluck('category.name')
            ->unique()
            ->filter()
            ->values()
            ->toArray();
        
        return view('user.explore.index', [
            'vendors' => $vendors,
            'packages' => $packages,
            'categories' => $categories,
            'eventDateLabel' => now()->addMonths(2)->isoFormat('D MMMM YYYY'),
        ]);
    }
    
    public function show($id)
    {
        $vendor = Vendor::with(['category', 'photos'])->findOrFail($id);
        
        $coverPhoto = $vendor->photos->where('is_cover', true)->first();
        $otherPhotos = $vendor->photos->where('is_cover', false)->sortBy('sort_order');
        
        return view('user.explore.overview', [
            'vendor' => $vendor,
            'coverPhoto' => $coverPhoto,
            'otherPhotos' => $otherPhotos,
        ]);
    }
    
    public function showPackage($id)
    {
        $package = Package::with(['vendors.category', 'vendors.photos'])->findOrFail($id);
        
        return view('user.explore.package-detail', [
            'package' => $package,
        ]);
    }
}
