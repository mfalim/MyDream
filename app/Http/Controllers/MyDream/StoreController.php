<?php

namespace App\Http\Controllers\MyDream;

use App\Http\Controllers\Controller;

use App\MyDream\Catalog;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $kategori = $request->query('kategori');
        $q = trim((string) $request->query('q', ''));
        $isFiltered = $kategori || $q !== '';

        $packages = Catalog::packages();
        $vendors  = Catalog::vendors();

        if ($isFiltered) {
            // kategori "paket" = hanya paket; kategori lain = hanya vendor kategori tsb
            if ($kategori === 'paket') {
                $vendors = collect();
            } elseif ($kategori) {
                $packages = collect();
                $vendors  = $vendors->where('category', $kategori);
            }

            if ($q !== '') {
                $needle = mb_strtolower($q);
                $match  = fn ($item) => str_contains(mb_strtolower($item['name'] ?? $item['title']), $needle);
                $packages = $packages->filter($match);
                $vendors  = $vendors->filter($match);
            }

            return view('mydream.store.index', [
                'isFiltered' => true,
                'kategori'   => $kategori,
                'q'          => $q,
                'packages'   => $packages->values(),
                'vendors'    => $vendors->values(),
            ]);
        }

        return view('mydream.store.index', [
            'isFiltered' => false,
            'kategori'   => null,
            'q'          => '',
            'featured'   => $packages->first(),
            'packages'   => $packages->values(),
            'venues'     => $vendors->where('category', 'venue')->values(),
            'muas'       => $vendors->where('category', 'makeup')->values(),
            'decors'     => $vendors->where('category', 'dekor')->values(),
            'others'     => $vendors->whereNotIn('category', ['venue', 'makeup', 'dekor'])->values(),
        ]);
    }
}
