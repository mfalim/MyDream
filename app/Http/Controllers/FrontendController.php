<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FrontendController extends Controller
{
    public function home(): View
    {
        $filterCerita = ['Semua', 'Adat Tradisional', 'Pernikahan Modern', 'Akad & Lamaran', 'Prosesi Khusus'];

        $portfolio = [
            ['image' => 'portfolio/portfolio-01.jpg', 'cat' => 'adat', 'label' => 'Adat Jawa Solo', 'tag' => 'Adat Jawa Solo', 'title' => 'Pernikahan Erwin & Anggita', 'desc' => 'Sentuhan klasik Adat Jawa dengan nuansa khidmat & elegan.'],
            ['image' => 'portfolio/portfolio-02.jpg', 'cat' => 'modern', 'label' => 'Outdoor Modern', 'tag' => 'Outdoor Modern', 'title' => 'Pernikahan Fehy & Yudo', 'desc' => 'Akad intim bernuansa bunga segar putih & sage.'],
            ['image' => 'portfolio/portfolio-03.jpg', 'cat' => 'modern', 'label' => 'Ballroom Reception', 'tag' => 'Ballroom Reception', 'title' => 'Pernikahan Vio & Tika', 'desc' => 'Resepsi modern bernuansa pencahayaan glamour & hangat.'],
            ['image' => 'portfolio/portfolio-04.jpg', 'cat' => 'khusus', 'label' => 'Prosesi Khusus', 'tag' => 'Prosesi Khusus', 'title' => 'Prosesi Pedang Pora', 'desc' => 'Upacara sakral militer penuh kehormatan & kehangatan.'],
            ['image' => 'portfolio/portfolio-05.jpg', 'cat' => 'adat', 'label' => 'Tradisional Modern', 'tag' => 'Tradisional Modern', 'title' => 'Pernikahan Hijab Klasik', 'desc' => 'Dekorasi gebyok emas & konsep kekeluargaan penuh syahdu.'],
            ['image' => 'portfolio/portfolio-06.jpg', 'cat' => 'modern', 'label' => 'Intimate Dinner', 'tag' => 'Intimate Dinner', 'title' => 'Resepsi Malam Romantis', 'desc' => 'Gemerlap fairylight di kebun terbuka bernuansa akrab.'],
        ];

        $services = [
            ['num' => '01', 'title' => 'Perencanaan dari Nol (Full Planning)', 'sub' => null, 'desc' => 'Teman diskusi sejak awal. Kami bantu cari ide tema, pilih vendor tepat, dan susun rencana acara yang cermat.'],
            ['num' => '02', 'title' => 'Pengawal Hari Acara (D-Day Coordination)', 'sub' => null, 'desc' => 'Fokus nikmati hari bahagiamu, tim kami yang atur ketepatan waktu, sambut tamu besar, dan pastikan seluruh vendor bergerak kompak.'],
            ['num' => '03', 'title' => 'Pilihan Vendor Terpercaya', 'sub' => null, 'desc' => 'Kami hubungkan kamu dengan dekorator, MUA, fotografer, dan sound system terbaik di Jember tanpa perlu repot mencari satu per satu.'],
            ['num' => '04', 'title' => 'Lamaran & Acara Keluarga', 'sub' => null, 'desc' => 'Pendampingan untuk momen sakral pra-nikah seperti lamaran, siraman, dan pengajian agar tetap khidmat, hangat, dan tertata.'],
        ];

        $bantuKami = [
            ['title' => 'Hangat & Personal', 'desc' => 'Setiap susunan acara kami sesuaikan dengan gaya kamu dan tradisi keluarga.'],
            ['title' => 'Waktu yang Terjaga', 'desc' => 'Kami pastikan prosesi adat dan resepsi berjalan tepat waktu tanpa terburu-buru.'],
            ['title' => 'Komunikasi Satu Pintu', 'desc' => 'Selalu ada perencana pernikahan yang siap diajak ngobrol dan update progres kapan saja.'],
            ['title' => 'Paham Seluk-Beluk Jember', 'desc' => 'Kami kenal baik karakter venue dan vendor lokal, sehingga koordinasi di lapangan lebih lancar.'],
        ];

        $langkah = [
            ['num' => '01', 'title' => 'Ngobrol Santai', 'desc' => 'Ceritakan konsep impian dan gambaran budget lewat chat atau kunjungi kami langsung.'],
            ['num' => '02', 'title' => 'Matangkan Rencana', 'desc' => 'Kami susun moodboard visual, pilihan vendor cocok, dan budget jadwal acara.'],
            ['num' => '03', 'title' => 'Rapat Pemantapan', 'desc' => 'Duduk bersama keluarga dan seluruh vendor menyelaraskan detail 2-3 minggu sebelum acara.'],
            ['num' => '04', 'title' => 'Hari Pernikahan', 'desc' => 'Saatnya tersenyum dan menikmati hari spesialmu dengan tenang.'],
        ];

        $testimonials = [
            ['quote' => 'Detail acaranya rapi banget. Acara adat Jawanya khidmat, resepsinya juga seru tanpa jeda canggung. Tim My Dream siap sedia!', 'name' => 'Erwin & Anggita'],
            ['quote' => 'Bener-bener bikin tenang. Dari awal konsultasi sampai hari-H komunikasinya enak dan fleksibel banget diajak diskusi.', 'name' => 'Fehy & Yudo'],
            ['quote' => 'Pilihan paling tepat buat wedding di Jember. Rundown bener-bener tepat waktu dan koordinasi vendornya juara.', 'name' => 'Vio & Tika'],
        ];

        return view('pages.home', [
            'filterCerita' => $filterCerita,
            'portfolio' => $portfolio,
            'services' => $services,
            'testimonials' => $testimonials,
            'portofolio' => $portfolio,
            'layananKami' => $services,
            'bantuKami' => $bantuKami,
            'langkah' => $langkah,
            'testimoni' => $testimonials,
        ]);
    }

    public function catalog(Request $request): View
    {
        $filterPromo = ['Semua Promo', 'Paket All-in-One', 'Hotel Bintang 5', 'Intimate Wedding', 'Adat Tradisional', 'Bali Destination Wedding', '⚡ Flash Sale Hari Ini'];

        $voucher = [
            ['image' => 'portfolio/portfolio-01.jpg', 'save' => 'HEMAT 35%', 'tag' => 'Flash Sale', 'title' => 'Paket Wedding Premium', 'desc' => 'Penawaran khusus untuk pasangan MyDream.', 'before' => '50.000.000', 'now' => '32.500.000'],
            ['image' => 'portfolio/portfolio-02.jpg', 'save' => 'HEMAT 25%', 'tag' => 'Promo', 'title' => 'Intimate Wedding Package', 'desc' => 'Konsep intimate wedding dengan vendor pilihan.', 'before' => '30.000.000', 'now' => '22.500.000'],
            ['image' => 'portfolio/portfolio-03.jpg', 'save' => 'HEMAT 20%', 'tag' => 'Limited Deal', 'title' => 'Modern Wedding Package', 'desc' => 'Paket modern untuk pernikahan yang elegan.', 'before' => '40.000.000', 'now' => '32.000.000'],
            ['image' => 'portfolio/portfolio-04.jpg', 'save' => 'HEMAT 15%', 'tag' => 'Special Deal', 'title' => 'Traditional Wedding Package', 'desc' => 'Paket pernikahan dengan sentuhan tradisional.', 'before' => '35.000.000', 'now' => '29.750.000'],
        ];

        $q = trim((string) $request->query('q', ''));
        $kategori = $request->query('kategori');

        $packageQuery = Package::with(['vendors.category', 'vendors.photos']);
        $vendorQuery = Vendor::with(['category', 'photos', 'packages']);

        if ($q !== '') {
            $packageQuery->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%")
                    ->orWhere('event_period', 'like', "%{$q}%");
            });

            $vendorQuery->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('address', 'like', "%{$q}%");
            });
        }

        $paket = $packageQuery->latest()->get();
        $vendors = $vendorQuery->latest()->get();

        $paket->each(function (Package $package) {
            $package->setAttribute('price', (float) $package->vendors->sum('price'));
        });

        $venues = $vendors->filter(fn (Vendor $vendor) => strtolower((string) $vendor->category?->name) === 'venue')->values();
        $muas = $vendors->filter(fn (Vendor $vendor) => in_array(strtolower((string) $vendor->category?->name), ['mua', 'makeup']))->values();
        $decors = $vendors->filter(fn (Vendor $vendor) => in_array(strtolower((string) $vendor->category?->name), ['dekor', 'decoration']))->values();
        $others = $vendors->reject(fn (Vendor $vendor) => $venues->contains('id', $vendor->id) || $muas->contains('id', $vendor->id) || $decors->contains('id', $vendor->id))->values();

        if ($kategori === 'paket') {
            $isFiltered = true;
            $vendors = collect();
        } elseif ($kategori === 'venue') {
            $isFiltered = true;
            $paket = collect();
            $vendors = $venues;
        } elseif ($kategori === 'makeup') {
            $isFiltered = true;
            $paket = collect();
            $vendors = $muas;
        } elseif ($kategori === 'dekor') {
            $isFiltered = true;
            $paket = collect();
            $vendors = $decors;
        } else {
            $isFiltered = $q !== '';
        }

        $featuredPackage = $paket->first();
        $featured = $featuredPackage
            ? ['slug' => $featuredPackage->slug, 'title' => $featuredPackage->name, 'image' => $featuredPackage->photo ?: 'images/mydream/ph-01.svg']
            : ['slug' => null, 'title' => 'Paket Pernikahan MyDream', 'image' => 'images/mydream/ph-01.svg'];

        return view('pages.catalog.index', compact('filterPromo', 'voucher', 'paket', 'vendors', 'venues', 'muas', 'decors', 'others', 'featured', 'isFiltered', 'q', 'kategori'));
    }

    public function package(string $slug): View
    {
        $package = Package::with(['vendors.category', 'vendors.photos'])->where('slug', $slug)->firstOrFail();
        $packageData = $this->mapPackage($package);

        $related = Package::with(['vendors.category', 'vendors.photos'])
            ->where('id', '!=', $package->id)
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn (Package $item) => $this->mapPackage($item));

        return view('pages.catalog.show', ['package' => $packageData, 'related' => $related]);
    }

    public function vendors(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $kategori = trim((string) $request->query('kategori', ''));

        $vendorQuery = Vendor::with(['category', 'photos', 'packages']);

        if ($q !== '') {
            $vendorQuery->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('address', 'like', "%{$q}%");
            });
        }

        $kategoriMap = [
            'Venue & Ballroom' => 'venue',
            'Fotografi & Videografi' => 'fotografi',
            'Gaun & Busana Pengantin' => 'gaun',
            'Makeup & Hair' => 'mua',
            'Dekorasi & Lighting' => 'dekor',
            'Katering & Kue' => 'katering',
            'Wedding Organizer' => 'wedding organizer',
            'Perhiasan & Cincin' => 'perhiasan',
        ];

        if ($kategori !== '' && $kategori !== 'Semua Vendor') {
            $categoryName = $kategoriMap[$kategori] ?? $kategori;

            $vendorQuery->whereHas('category', function ($query) use ($categoryName) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($categoryName) . '%']);
            });
        }

        return view('pages.vendor.index', [
            'vendors' => $vendorQuery->latest()->get(),
            'q' => $q,
            'kategori' => $kategori,
        ]);
    }

    public function vendor(string $slug): View
    {
        $vendor = Vendor::with(['category', 'photos', 'packages'])
            ->where('slug', $slug)
            ->firstOrFail();

        $coverPhoto = $vendor->photos->first(fn ($photo) => (bool) ($photo->cover ?? $photo->is_cover ?? false));
        $coverPhoto ??= $vendor->photos->first();

        $otherPhotos = $vendor->photos->filter(fn ($photo) => !$coverPhoto || $photo->id !== $coverPhoto->id)->values();

        $vendor->packages->each(function (Package $package) {
            $package->setAttribute('price', (float) $package->vendors()->sum('price'));
        });

        $related = Package::with(['vendors.category', 'vendors.photos'])
            ->whereNotIn('id', $vendor->packages->pluck('id')->all())
            ->latest()
            ->limit(3)
            ->get();

        $related->each(function (Package $package) {
            $package->setAttribute('price', (float) $package->vendors->sum('price'));
        });

        return view('pages.vendor.show', compact('vendor', 'coverPhoto', 'otherPhotos', 'related'));
    }

    public function blog(): View
    {
        return view('pages.blog.index');
    }

    public function article(string $slug): View
    {
        return view('pages.blog.show', compact('slug'));
    }

    public function inspiration(): View
    {
        return view('pages.inspiration.index');
    }

    public function event(): View
    {
        return view('pages.event.index');
    }

    private function mapPackage(Package $package): array
    {
        $vendors = $package->vendors;

        $gallery = $vendors->flatMap(fn (Vendor $vendor) => $vendor->photos->pluck('photo'))
            ->filter()->unique()->values()->take(5)->all();

        if ($package->photo) {
            array_unshift($gallery, $package->photo);
        }

        if (empty($gallery)) {
            $gallery = ['images/mydream/ph-01.svg', 'images/mydream/ph-02.svg', 'images/mydream/ph-03.svg'];
        }

        while (count($gallery) < 3) {
            $gallery[] = $gallery[array_key_last($gallery)];
        }

        $inclusions = $vendors->map(function (Vendor $vendor) {
            return [
                'icon' => 'bi-heart',
                'title' => $vendor->category?->name ?? 'Vendor Pernikahan',
                'worth' => (float) ($vendor->price ?? 0),
                'points' => array_values(array_filter([$vendor->description, $vendor->address])),
                'vendor' => $vendor->slug,
            ];
        })->values()->all();

        $totalPrice = (float) $vendors->sum('price');

        return [
            'id' => $package->id,
            'slug' => $package->slug,
            'title' => $package->name,
            'name' => $package->name,
            'organizer' => 'MyDream Organizer',
            'image' => $package->photo ?: 'images/mydream/ph-01.svg',
            'rating' => 5,
            'reviews' => 0,
            'sold' => 0,
            'area' => 'Jember',
            'price' => $totalPrice,
            'badges' => [['Paket MyDream', 'gold']],
            'gallery' => $gallery,
            'gallery_caption' => $package->name,
            'facts' => [
                ['bi-people', 'Kapasitas', $package->guest_capacity ?: 'Hubungi kami'],
                ['bi-calendar-event', 'Periode', $package->event_period ?: 'Fleksibel'],
                ['bi-clock', 'Durasi', $package->duration ?: 'Sesuai paket'],
                ['bi-buildings', 'Vendor', $vendors->count() . ' vendor'],
            ],
            'tabs' => ['Isi Paket', 'Lokasi', 'Ulasan'],
            'inclusion_title' => 'Isi Paket Pernikahan',
            'inclusion_intro' => 'Paket ini menggabungkan layanan vendor yang terhubung melalui MyDream Organizer.',
            'inclusions' => $inclusions,
            'location_title' => $vendors->first()?->name ?? 'Lokasi Pernikahan',
            'address' => $vendors->first()?->address ?? 'Lokasi akan dikonfirmasi bersama wedding specialist.',
            'map_query' => $vendors->first()?->address ?? 'Jember, Jawa Timur',
            'map_note' => 'Lokasi vendor terkait paket.',
            'testimonials' => [],
            'steps' => [
                ['title' => 'Pilih Paket', 'desc' => 'Pilih paket yang sesuai dengan kebutuhan acara.'],
                ['title' => 'Konsultasi', 'desc' => 'Diskusikan detail paket bersama MyDream.'],
                ['title' => 'Konfirmasi', 'desc' => 'Tentukan vendor, tanggal, dan kebutuhan acara.'],
                ['title' => 'Acara', 'desc' => 'Nikmati acara pernikahanmu bersama MyDream.'],
            ],
            'options' => [[
                'label' => $package->guest_capacity ? $package->guest_capacity . ' Tamu' : 'Sesuai Kapasitas',
                'desc' => $package->event_period ?: 'Periode fleksibel',
                'price' => $totalPrice,
                'old_price' => $totalPrice,
            ]],
            'sessions' => array_values(array_filter([$package->event_period, 'Siang (Lunch)', 'Malam (Dinner)'])),
            'discount' => 0,
        ];
    }
}
