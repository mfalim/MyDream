<?php

namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Site\AboutController as SiteAboutController;

class AboutController extends Controller
{
    public function show()
    {
        return response()->json(['data' => SiteAboutController::content()]);
    }
}
