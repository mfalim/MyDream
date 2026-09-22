<?php

namespace App\Http\Controllers\MyDream;

use App\Http\Controllers\Controller;

use App\MyDream\Catalog;

class PackageController extends Controller
{
    public function show(string $slug)
    {
        $package = Catalog::package($slug);
        abort_if(! $package, 404);

        return view('mydream.packages.show', [
            'package' => $package,
            'related' => Catalog::packages()->where('slug', '!=', $slug)->take(3)->values(),
        ]);
    }
}
