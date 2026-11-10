<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Package;
use App\Models\PublicEvent;
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
            'upcomingEvents' => PublicEvent::published()
                ->where(function ($q) {
                    $q->whereNull('ends_on')->where('starts_on', '>=', today())
                        ->orWhere('ends_on', '>=', today());
                })
                ->orderBy('starts_on')
                ->take(3)
                ->get(),
        ]);
    }
}
