<?php

namespace App\Http\Controllers\Site;

use App\Data\Site\ArticleData;
use App\Http\Controllers\Controller;
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
            'stories' => ArticleData::latest(3),
        ]);
    }
}
