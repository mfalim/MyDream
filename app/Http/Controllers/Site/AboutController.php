<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Vendor;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function index(): View
    {
        return view('site.about', [
            // Angka dihitung dari database; bisa ditimpa lewat config/mydream.php -> 'stats'.
            'stats' => [
                'weddings' => config('mydream.stats.weddings') ?? Booking::where('status', 'approved')->count(),
                'vendors' => config('mydream.stats.vendors') ?? Vendor::count(),
                'packages' => config('mydream.stats.packages') ?? Package::count(),
            ],
        ]);
    }
}
