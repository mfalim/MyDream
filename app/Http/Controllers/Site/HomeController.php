<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Booking;
use App\Models\Inspiration;
use App\Models\Package;
use App\Models\Vendor;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('site.home', [
            'vendors' => Vendor::with(['category', 'photos'])->latest()->take(4)->get(),
            'packages' => Package::with(['vendors.photos'])->orderByDesc('is_featured')->latest()->take(4)->get(),
            'stories' => Article::published()->latestFirst()->take(3)->get(),
            // Foto asli dari cerita client yang sudah disetujui (dipakai di galeri "Momen yang Kami Ciptakan").
            'moments' => Inspiration::approved()->with('photos')->orderByDesc('reviewed_at')->take(6)->get(),
            // Angka dihitung dari database; bisa ditimpa lewat config/mydream.php -> 'stats'.
            'stats' => [
                'weddings' => config('mydream.stats.weddings') ?? Booking::where('status', 'approved')->count(),
                'vendors' => config('mydream.stats.vendors') ?? Vendor::count(),
                'packages' => config('mydream.stats.packages') ?? Package::count(),
            ],
        ]);
    }
}
