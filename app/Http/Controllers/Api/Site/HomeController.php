<?php

namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Package;
use App\Models\PublicEvent;
use App\Models\Vendor;

class HomeController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => [
                'categories' => Category::orderBy('name')->get(),
                'featured_packages' => Package::with('vendors')->latest()->limit(6)->get(),
                'featured_vendors' => Vendor::with(['category', 'photos'])->latest()->limit(6)->get(),
                'articles' => Article::published()->latestFirst()->limit(3)->get(),
                'events' => PublicEvent::published()->orderBy('starts_on')->limit(3)->get(),
            ],
        ]);
    }
}
