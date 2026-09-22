<?php

namespace App\Http\Controllers\MyDream;

use App\Http\Controllers\Controller;

use App\MyDream\Catalog;

class HomeController extends Controller
{
    public function index()
    {
        return view('mydream.home', [
            'portfolio' => Catalog::portfolio(),
            'services'  => Catalog::services(),
            'testimonials' => Catalog::landingTestimonials(),
        ]);
    }
}
