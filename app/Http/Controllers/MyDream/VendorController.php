<?php

namespace App\Http\Controllers\MyDream;

use App\Http\Controllers\Controller;

use App\MyDream\Catalog;

class VendorController extends Controller
{
    public function show(string $slug)
    {
        $vendor = Catalog::vendor($slug);
        abort_if(! $vendor, 404);

        return view('mydream.vendors.show', [
            'vendor'  => $vendor,
            'related' => Catalog::packages()->take(3)->values(),
        ]);
    }
}
