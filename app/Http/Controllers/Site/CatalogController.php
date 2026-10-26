<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Package;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

/**
 * Catalog = pencarian & filter seluruh paket dan vendor. Semua filter lewat query string
 * dan bisa dikombinasikan: ?type=vendor&city=Jember&price=25to50&category=3&q=dekor
 */
class CatalogController extends Controller
{
    /** key => [label, min, max] */
    private const PRICE_BRACKETS = [
        'under25' => ['Di bawah Rp25 Juta', null, 25_000_000],
        '25to50' => ['Rp25 - 50 Juta', 25_000_000, 50_000_000],
        '50to100' => ['Rp50 - 100 Juta', 50_000_000, 100_000_000],
        '100to250' => ['Rp100 - 250 Juta', 100_000_000, 250_000_000],
        'over250' => ['Di atas Rp250 Juta', 250_000_000, null],
    ];

    public function index(Request $request): View
    {
        $type = in_array($request->query('type'), ['package', 'vendor'], true) ? $request->query('type') : 'all';
        $city = in_array($request->query('city'), config('mydream.cities'), true) ? $request->query('city') : null;
        $priceKey = array_key_exists((string) $request->query('price'), self::PRICE_BRACKETS) ? $request->query('price') : null;
        $categoryId = $request->integer('category') ?: null;
        $q = trim((string) $request->query('q', ''));

        [, $min, $max] = $priceKey ? self::PRICE_BRACKETS[$priceKey] : [null, null, null];

        // Kota vendor: kolom city, atau (untuk data lama yang belum diisi) teks alamat.
        $inCity = fn ($query) => $query->where(
            fn ($w) => $w->where('city', $city)->orWhere('address', 'like', "%{$city}%")
        );

        $packages = collect();
        if ($type !== 'vendor') {
            $packages = Package::query()
                ->with(['vendors.category', 'vendors.photos'])
                ->when($q !== '', fn ($query) => $query->where(
                    fn ($w) => $w->where('name', 'like', "%{$q}%")->orWhere('description', 'like', "%{$q}%")
                ))
                ->when($city, fn ($query) => $query->whereHas('vendors', $inCity))
                ->when($categoryId, fn ($query) => $query->whereHas('vendors', fn ($v) => $v->where('category_id', $categoryId)))
                ->latest()
                ->get()
                ->filter(fn ($package) => $this->withinPrice($package->vendors->sum('price'), $min, $max))
                ->values();
        }

        $vendors = collect();
        if ($type !== 'package') {
            $vendors = Vendor::query()
                ->with(['category', 'photos'])
                ->when($q !== '', fn ($query) => $query->where(
                    fn ($w) => $w->where('name', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%")
                        ->orWhere('address', 'like', "%{$q}%")
                ))
                ->when($city, $inCity)
                ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
                ->when($min !== null, fn ($query) => $query->where('price', '>=', $min))
                ->when($max !== null, fn ($query) => $query->where('price', '<=', $max))
                ->latest()
                ->get();
        }

        $categories = Category::orderBy('name')->get();

        $active = array_filter([
            'type' => $type !== 'all' ? $type : null,
            'city' => $city,
            'category' => $categoryId,
            'price' => $priceKey,
            'q' => $q !== '' ? $q : null,
        ]);

        $chips = [];
        foreach ($active as $key => $value) {
            $chips[] = [
                'label' => match ($key) {
                    'type' => $value === 'package' ? 'Paket' : 'Vendor',
                    'category' => $categories->firstWhere('id', $value)?->name ?? 'Kategori',
                    'price' => self::PRICE_BRACKETS[$value][0],
                    'q' => '"' . $value . '"',
                    default => $value,
                },
                'url' => route('catalog.index', Arr::except($active, [$key])),
            ];
        }

        return view('site.catalog.index', [
            'packages' => $packages,
            'vendors' => $vendors,
            'categories' => $categories,
            'filters' => ['type' => $type, 'city' => $city, 'category' => $categoryId, 'price' => $priceKey, 'q' => $q],
            'priceBrackets' => array_map(fn ($b) => $b[0], self::PRICE_BRACKETS),
            'chips' => $chips,
        ]);
    }

    /** /vendor = katalog dengan tipe vendor (menjaga URL & nama route lama). */
    public function vendors(Request $request): RedirectResponse
    {
        $params = array_merge($request->only(['q', 'city', 'price', 'category']), ['type' => 'vendor']);

        // Parameter lama ?kategori=<nama kategori>
        if ($request->filled('kategori') && empty($params['category'])) {
            $category = Category::where('name', 'like', '%' . $request->query('kategori') . '%')->first();
            if ($category) {
                $params['category'] = $category->id;
            }
        }

        return redirect()->route('catalog.index', array_filter($params));
    }

    private function withinPrice(int|float|string $price, ?int $min, ?int $max): bool
    {
        $price = (float) $price;

        return ($min === null || $price >= $min) && ($max === null || $price <= $max);
    }
}
