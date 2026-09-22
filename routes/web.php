<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\MyDream\HomeController as MyDreamHomeController;
use App\Http\Controllers\MyDream\PackageController as MyDreamPackageController;
use App\Http\Controllers\MyDream\StoreController as MyDreamStoreController;
use App\Http\Controllers\MyDream\VendorController as MyDreamVendorController;

Route::get('/login', function () { return view('login.login'); })->name('login');
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
Route::middleware('auth')->group(function () {
    Route::get('/login/profile', [ClientController::class, 'profile'])->name('client.profile');
    Route::post('/login/profile', [ClientController::class, 'storeProfile'])->name('client.profile.store');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', CategoryController::class)->only(['index', 'create', 'store']);
    Route::resource('packages', PackageController::class)->only(['index', 'create', 'store']);
    Route::resource('vendors', VendorController::class)->only(['index', 'create', 'store']);
});

$frontend = FrontendController::class;
Route::get('/', [$frontend, 'home'])->name('home');
Route::get('/paket', [$frontend, 'catalog'])->name('catalog.index');
Route::get('/paket/{slug}', [$frontend, 'package'])->name('catalog.show');
Route::post('/paket/{slug}/keranjang', [$frontend, 'addToCart'])->name('cart.store');
Route::get('/vendor', [$frontend, 'vendors'])->name('vendor.index');
Route::get('/vendor/{slug}', [$frontend, 'vendor'])->name('vendor.show');
Route::get('/blog', [$frontend, 'blog'])->name('blog.index');
Route::get('/blog/{slug}', [$frontend, 'article'])->name('blog.show');
Route::get('/inspirasi', [$frontend, 'inspiration'])->name('inspiration.index');
Route::get('/keranjang', [$frontend, 'cart'])->name('cart.index');
Route::get('/checkout', [$frontend, 'checkout'])->name('checkout.index');
Route::post('/checkout', [$frontend, 'placeOrder'])->name('checkout.store');
Route::get('/pembayaran', [$frontend, 'payment'])->name('payment.index');
Route::post('/pembayaran', [$frontend, 'payment'])->name('payment.process');
Route::get('/pembayaran/sukses', [$frontend, 'paymentSuccess'])->name('payment.success');
Route::post('/pembayaran/sukses', [$frontend, 'paymentSuccess']);
Route::get('/event', [$frontend, 'event'])->name('event.index');

// Fitur storefront tambahan dari modul MyDream. Prefix mencegah bentrok
// dengan storefront utama yang sudah memakai /, /paket, dan /vendor.
Route::view('/welcome', 'welcome')->name('welcome');

Route::prefix('mydream')
    ->name('mydream.')
    ->group(function () {
        Route::get('/', [MyDreamHomeController::class, 'index'])->name('home');
        Route::get('/store', [MyDreamStoreController::class, 'index'])->name('store');
        Route::get('/paket/{slug}', [MyDreamPackageController::class, 'show'])->name('packages.show');
        Route::get('/vendor/{slug}', [MyDreamVendorController::class, 'show'])->name('vendors.show');
    });

// Semua data dummy teman tetap dipertahankan di dalam group user.
Route::prefix('user')->name('user.')->group(function () {

        $vendors = [

            'lotus-floral-design' => [
                'name' => 'Lotus Floral Design',
                'category' => 'Dekorasi & Lighting',
                'tagline' => 'Rekanan Platinum WO',
                'rating' => '4.9',
                'reviews' => '128',
                'price' => 'Rp 45.000.000',
                'image' => 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=1400&q=85',
                'description' => 'Spesialis pelaminan megah kontemporer dan dekorasi adat modern untuk menciptakan suasana pernikahan yang elegan dan berkesan.',
                'location' => 'Jakarta Selatan',
                'capacity' => '1200 Tamu',
                'experience' => '120+ Acara Sukses',
                'service' => 'Full Decoration',
            ],

            'the-leonardi-cinema' => [
                'name' => 'The Leonardi Cinema',
                'category' => 'Foto & Sinematografi',
                'tagline' => 'Award Winning Studio',
                'rating' => '5.0',
                'reviews' => '95',
                'price' => 'Rp 28.000.000',
                'image' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1400&q=85',
                'description' => 'Studio dokumentasi pernikahan dengan layanan sinematografi premium, drone 4K, teaser dan Same Day Edit.',
                'location' => 'Jakarta',
                'capacity' => 'Full Day',
                'experience' => '95+ Acara Sukses',
                'service' => 'Photography & Cinema',
            ],

            'bennu-sorumba-mua' => [
                'name' => 'Bennu Sorumba MUA',
                'category' => 'MUA & Rias',
                'tagline' => 'Celebrity Stylist',
                'rating' => '5.0',
                'reviews' => '86',
                'price' => 'Rp 35.000.000',
                'image' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=1400&q=85',
                'description' => 'Layanan makeup artist premium dengan konsep glamor flawless untuk akad dan resepsi.',
                'location' => 'Jakarta',
                'capacity' => 'Akad & Resepsi',
                'experience' => '86+ Acara Sukses',
                'service' => 'Make Up Artist',
            ],

            'le-novelle-cake' => [
                'name' => 'Le Novelle Cake',
                'category' => 'Wedding Cake',
                'tagline' => 'Haute Patisserie',
                'rating' => '4.8',
                'reviews' => '74',
                'price' => 'Rp 18.000.000',
                'image' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?auto=format&fit=crop&w=1400&q=85',
                'description' => 'Wedding cake custom bertingkat dengan desain elegan dan detail dekorasi premium.',
                'location' => 'Jakarta',
                'capacity' => '5 Tier',
                'experience' => '74+ Acara Sukses',
                'service' => 'Wedding Cake',
            ],

            'dwiki-jazz-quintet' => [
                'name' => 'Dwiki Jazz Quintet',
                'category' => 'Hiburan & Orchestra',
                'tagline' => '7-Piece Band + MC',
                'rating' => '4.9',
                'reviews' => '68',
                'price' => 'Rp 22.000.000',
                'image' => 'https://images.unsplash.com/photo-1516280440614-37939bbacd81?auto=format&fit=crop&w=1400&q=85',
                'description' => 'Harmoni jazz lembut, string quartet untuk kirab pengantin dan custom songlist.',
                'location' => 'Jakarta',
                'capacity' => 'Full Event',
                'experience' => '68+ Acara Sukses',
                'service' => 'Live Entertainment',
            ],

            'puspa-gelato-coffee' => [
                'name' => 'Puspa Gelato & Coffee',
                'category' => 'Dessert Stall & Bar',
                'tagline' => 'Kapasitas 400 Porsi',
                'rating' => '4.9',
                'reviews' => '62',
                'price' => 'Rp 12.500.000',
                'image' => 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?auto=format&fit=crop&w=1400&q=85',
                'description' => 'Pop-up luxury dessert stall dengan gelato autentik Italia dan layanan coffee station.',
                'location' => 'Jakarta',
                'capacity' => '400 Porsi',
                'experience' => '62+ Acara Sukses',
                'service' => 'Dessert & Coffee',
            ],

            'red-ribbon-box' => [
                'name' => 'Red Ribbon Box & Co',
                'category' => 'Souvenir & Gift Box',
                'tagline' => 'Bahan Beludru & Akrilik',
                'rating' => '4.8',
                'reviews' => '54',
                'price' => 'Rp 8.000.000',
                'image' => 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?auto=format&fit=crop&w=1400&q=85',
                'description' => 'Spesialis gift box, undangan VIP hardcover, souvenir custom dan kemasan premium.',
                'location' => 'Jakarta',
                'capacity' => 'Custom Quantity',
                'experience' => '54+ Acara Sukses',
                'service' => 'Gift & Souvenir',
            ],

            'luminaire-pyrotechnics' => [
                'name' => 'Luminaire Pyrotechnics',
                'category' => 'Special Effects Hari H',
                'tagline' => 'Indoor Safe & Smoke-Free',
                'rating' => '4.9',
                'reviews' => '49',
                'price' => 'Rp 9.500.000',
                'image' => 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?auto=format&fit=crop&w=1400&q=85',
                'description' => 'Special effect untuk momen Hari H dengan cold firework fountain yang aman untuk indoor.',
                'location' => 'Jakarta',
                'capacity' => 'Indoor Safe',
                'experience' => '49+ Acara Sukses',
                'service' => 'Special Effects',
            ],

        ];


        /*
        |----------------------------------------------------------------
        | 1. DASHBOARD PERNIKAHAN
        |----------------------------------------------------------------
        */
        Route::get('/dashboard', function () {

            return view('user.dashboard.index', [
                'couple' => [
                    'names' => 'Aditya & Sarah',
                    'package_name' => 'The Royal Emerald Wedding',
                    'event_date_label' => '25 Oktober 2025',
                    'venue' => 'Grand Ballroom Hotel Mulia Senayan',
                ],
                'countdown' => ['days' => 24, 'hours' => 14, 'minutes' => 32],
                'vendorReadiness' => ['total' => 12, 'confirmed' => 9, 'waiting' => 2, 'review' => 1, 'percent' => 75],
                'budget' => [
                    'total' => 107625000,
                    'paid' => 75337500,
                    'paid_percent' => 70,
                    'remaining' => 32287500,
                ],
                'checklist' => [
                    'total' => 34,
                    'done' => 28,
                    'items' => [
                        ['icon' => 'scissors', 'title' => 'Fitting Terakhir Busana Resepsi', 'when' => '18 Okt 2025', 'where' => 'Anne Avantie Atelier Jakarta'],
                        ['icon' => 'people', 'title' => 'Technical Meeting Keluarga Inti', 'when' => '20 Okt 2025', 'where' => 'Hotel Mulia VIP Lounge'],
                    ],
                ],
                'milestones' => [
                    ['state' => 'done', 'icon' => 'check', 'status_label' => 'Selesai', 'date' => '02 Okt 2025', 'title' => 'Food Tasting & Final Banquet Selection', 'desc' => 'Menu Western & Nusantara set 800 porsi approved.', 'tag' => 'Mulia Catering'],
                    ['state' => 'active', 'icon' => 'clock', 'status_label' => 'Segera Datang', 'date' => '18 Okt 2025 (14:00 WIB)', 'title' => 'Fitting Final Busana Adat Solo Putri', 'desc' => 'Pemeriksaan detail kain prada, aksesori cunduk mentul & beskap pengantin pria.', 'action' => 'Konfirmasi Hadir'],
                    ['state' => 'pending', 'icon' => 'geo-alt', 'status_label' => 'Agenda Resmi', 'date' => '20 Okt 2025 (10:00 WIB)', 'title' => 'Technical Meeting 12 Vendor di Grand Ballroom', 'desc' => 'Penataan panggung 18 meter, alur VIP, sound system 20.000 watt & simulasi lighting.', 'tag' => 'All Vendors'],
                    ['state' => 'pending', 'icon' => 'stars', 'status_label' => 'Hari H Pernikahan', 'date' => '25 Okt 2025', 'title' => 'Akad Nikah & Resepsi Agung Aditya & Sarah', 'desc' => 'Akad Pagi (08:00) dilanjutkan Resepsi Malam (19:00 - 22:00 WIB).', 'tag' => 'Full Team WO (24 Kru)'],
                ],
                'moodboard' => [
                    ['image' => 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=700&q=80', 'title' => 'Pelaminan Utama Adat Solo', 'subtitle' => 'Konsep Modern Royal Java'],
                    ['image' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=700&q=80', 'title' => 'VIP Table Styling', 'subtitle' => 'Champagne & Emerald Botanicals'],
                    ['image' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=700&q=80', 'title' => 'Attire Harmony', 'subtitle' => 'Beludru Hitam Sulam Emas'],
                ],
                'team' => [
                    ['initials' => 'SN', 'name' => 'Sarah Nadia, S.Sn.', 'role' => 'Lead Wedding Director', 'note' => 'Pengalaman 140+ Royal Weddings'],
                    ['initials' => 'DP', 'name' => 'Dimas Prasetyo', 'role' => 'Show & Stage Director', 'note' => 'Handling Rundown & Lighting'],
                    ['initials' => 'CP', 'name' => 'Clarissa Putri', 'role' => 'Personal Bridal Liaison', 'note' => 'Pendamping Pengantin Wanita'],
                ],
                'documents' => [
                    ['title' => 'SPK Kontrak Kerja WO PROJECT', 'note' => 'Tercatat Notaris • PDF (2.4 MB)'],
                    ['title' => 'Surat Izin Loading & Sound', 'note' => 'Disetujui Hotel Mulia • PDF'],
                    ['title' => 'Master Rundown Hari H v4.2', 'note' => 'Update 12 Okt 2025 • XLSX'],
                ],
            ]);

        })->name('dashboard');


        /*
        |----------------------------------------------------------------
        | 2. KONFIRMASI VENDOR (status: confirmed / pending / rejected)
        |----------------------------------------------------------------
        */
        Route::get('/konfirmasi-vendor', function () {

            $vendorStatus = [
                ['category' => 'Dekorasi & Floral Atelier', 'name' => 'Lotus Design Atelier', 'note' => 'Tier 1 Gold Partner • Tematik Emerald Royal', 'pic' => 'Yu Cing & Tim', 'pic_role' => 'Chief Floral Scenographer', 'channel' => 'via WO Planner', 'schedule' => ['Loading: 24 Okt, 23:00 WIB', 'Final Touch: 25 Okt, 14:00 WIB'], 'status' => 'confirmed', 'status_note' => 'SPK ditandatangani 12 Agu'],
                ['category' => 'Dokumentasi Visual', 'name' => 'The Leonardi Photography', 'note' => 'Master Photographers • 4 Cam + 2 Cine + Drone', 'pic' => 'King Leonardi / Ronald', 'pic_role' => 'Creative Director', 'channel' => 'WA Direct', 'schedule' => ['Standby Akad: 06:30 WIB', 'Resepsi: s/d 22:30 WIB'], 'status' => 'confirmed', 'status_note' => 'Crew List Locked (8 Pax)'],
                ['category' => 'Katering & Jamuan VIP', 'name' => 'Puspa Catering VIP', 'note' => '800 Pax Buffet + 12 Live Food Stalls', 'pic' => 'Ibu Retno Wardani', 'pic_role' => 'Banquet Sales Director', 'channel' => 'WA Direct', 'schedule' => ['Food Prep Kitchen: 11:00 WIB', 'Serving Ready: 18:30 WIB'], 'status' => 'confirmed', 'status_note' => 'Food Test Selesai (9/10)'],
                ['category' => 'Tata Rias Pengantin', 'name' => 'Bennu Sorumba MUA & Team', 'note' => 'Signature Royal Bride Glamor Look', 'pic' => 'Bennu & Mas Danar (Manager)', 'pic_role' => 'Lead Makeup Artist', 'channel' => 'Follow-up via WO Lead', 'schedule' => ['Start Rias Akad: 04:30 WIB', 'Retouch Resepsi: 16:30 WIB'], 'status' => 'pending', 'status_note' => 'Menunggu finalisasi slot sub-tim'],
                ['category' => 'Busana & Kebaya Adat', 'name' => 'Griya Busana Suryo Handayani', 'note' => 'Adat Jawa Solo Basahan & Resepsi Modern', 'pic' => 'Ibu Suryo', 'pic_role' => 'Pemaes & Desainer Adat', 'channel' => 'WA Direct', 'schedule' => ['Delivery Busana: 24 Okt, 19:00', 'Fitting Terakhir: 18 Okt'], 'status' => 'confirmed', 'status_note' => 'Jadwal Fitting Approved'],
                ['category' => 'Master of Ceremony & Host', 'name' => 'MC Choky Sitohang', 'note' => 'Jadwal Bentrok: Liputan Khusus TVRI', 'pic' => 'Manajemen Choky S.', 'pic_role' => 'Artist Management', 'channel' => 'Digantikan Tim Serupa', 'schedule' => ['Resepsi Mulia Hotel', '18:00 - 22:00 WIB'], 'status' => 'rejected', 'status_note' => '3 Opsi Pengganti Disiapkan WO'],
                ['category' => 'Musik & Entertaining Orchestra', 'name' => 'Dwiki Jazz Bigband & Chamber', 'note' => '14-Piece Chamber + 2 Lead Vocalists', 'pic' => 'Dwiki / Tommy', 'pic_role' => 'Band Leader', 'channel' => 'WA Direct', 'schedule' => ['Soundcheck: 15:30 WIB', 'Live Performance: 19:00 - 22:00'], 'status' => 'confirmed', 'status_note' => 'Songlist Approved (24 Lagu)'],
                ['category' => 'Kue Pernikahan Megah', 'name' => 'Le Novelle Cake Atelier', 'note' => '7-Tier Architectural Fairy Castle Cake', 'pic' => 'Miyama & Susanto', 'pic_role' => 'Pastry Master', 'channel' => 'WA Direct', 'schedule' => ['Delivery: 25 Okt, 13:00 WIB', 'Spotlight Check: 16:00 WIB'], 'status' => 'confirmed', 'status_note' => 'Desain Sesuai Sketsa 3D'],
                ['category' => 'Tata Cahaya & Efek Visual', 'name' => 'Sound & Light Dynamics', 'note' => 'Dry Ice FX + Low Smoke + Moving Heads', 'pic' => 'Rian Hidayat', 'pic_role' => 'Technical Operator', 'channel' => 'WA Direct', 'schedule' => ['Rigging & Test: 08:00 WIB', 'Dry Run Cue: 16:30 WIB'], 'status' => 'confirmed', 'status_note' => 'Plotting Listrik Hotel Ok'],
                ['category' => 'Cinderamata Tamu VIP', 'name' => 'Red Ribbon Souvenir Atelier', 'note' => '500 Box Custom Ceramic Diffuser', 'pic' => 'Clarissa Tan', 'pic_role' => 'Production Lead', 'channel' => 'Tracking Shipment WO', 'schedule' => ['Delivery ke Hotel Mulia: H-2', 'Serah Terima: Souvenir Desk'], 'status' => 'pending', 'status_note' => 'Final packing & QC packaging'],
            ];

            $stats = [
                'total' => count($vendorStatus),
                'confirmed' => count(array_filter($vendorStatus, fn ($v) => $v['status'] === 'confirmed')),
                'pending' => count(array_filter($vendorStatus, fn ($v) => $v['status'] === 'pending')),
                'rejected' => count(array_filter($vendorStatus, fn ($v) => $v['status'] === 'rejected')),
            ];

            return view('user.vendor-confirmation.index', [
                'eventDateLabel' => '25 Oktober 2025',
                'venue' => 'Grand Ballroom Hotel Mulia Senayan',
                'stats' => $stats,
                'vendors' => $vendorStatus,
            ]);

        })->name('vendor-confirmation');


        /*
        |----------------------------------------------------------------
        | 3. EKSPLOR & TAMBAH VENDOR (grid)
        |----------------------------------------------------------------
        */
        Route::get('/explore-vendor', function () use ($vendors) {

            $categories = collect($vendors)->pluck('category')->unique()->values()->all();

            return view('user.explore.index', [
                'vendors' => $vendors,
                'categories' => $categories,
                'eventDateLabel' => '25 Oktober 2025',
            ]);

        })->name('explore-vendor');

        // VENDOR OVERVIEW
        Route::get('/explore-vendor/{vendor}', function ($vendor) use ($vendors) {

            abort_unless(isset($vendors[$vendor]), 404);

            return view('user.explore.overview', [
                'vendor' => $vendors[$vendor],
                'vendorSlug' => $vendor,
            ]);

        })->name('vendor-overview');

        // VENDOR PAYMENT
        Route::get('/explore-vendor/{vendor}/payment', function ($vendor) use ($vendors) {

            abort_unless(isset($vendors[$vendor]), 404);

            $vendorData = $vendors[$vendor];

            // "Rp 45.000.000" -> 45000000
            $priceNumeric = (int) preg_replace('/\D/', '', $vendorData['price']);

            $dp = (int) round($priceNumeric * 0.3);
            $lunas = (int) round($priceNumeric * 0.97); // diskon 3%
            $savings = $priceNumeric - $lunas;
            $cicilan = (int) round($priceNumeric / 6); // tenor 6x, 0% bunga

            $reservationCode = 'WO-' . date('Y') . '-' . strtoupper(substr(md5($vendor), 0, 4));

            return view('user.explore.payment', [
                'vendor' => $vendorData,
                'vendorSlug' => $vendor,
                'reservationCode' => $reservationCode,
                'dp' => $dp,
                'lunas' => $lunas,
                'savings' => $savings,
                'cicilan' => $cicilan,
            ]);

        })->name('vendor-payment');


        Route::get('/rundown-timeline', function () {

            // Event dummy per tanggal (Oktober 2025)
            $eventsByDay = [
                4  => [['type' => 'intimate', 'label' => 'Arya & Maya']],
                9  => [['type' => 'note', 'label' => 'Briefing Internal']],
                10 => [['type' => 'note', 'label' => 'Food Tasting']],
                11 => [['type' => 'rehearsal', 'label' => 'Gladi: Kevin & Lia']],
                17 => [['type' => 'note', 'label' => 'Technical Meeting']],
                18 => [['type' => 'rehearsal', 'label' => 'Gladi: Dimas & C'], ['type' => 'full', 'label' => 'Dimas & Mulia Ballroom']],
                23 => [['type' => 'rehearsal', 'label' => 'Gladi Resik A&S']],
                25 => [['type' => 'full', 'label' => '6 Acara Resepsi']],
                26 => [['type' => 'intimate', 'label' => 'Farhan & Nadia']],
                30 => [['type' => 'rehearsal', 'label' => 'Gladi Resik Nove']],
            ];

            $year = 2025;
            $month = 10;
            $firstWeekday = (int) date('N', mktime(0, 0, 0, $month, 1, $year)); // 1 (Sen) - 7 (Min)
            $daysInMonth = (int) date('t', mktime(0, 0, 0, $month, 1, $year));

            $cells = [];

            for ($i = 1; $i < $firstWeekday; $i++) {
                $cells[] = null;
            }

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $cells[] = [
                    'num' => $d,
                    'date' => sprintf('%04d-%02d-%02d', $year, $month, $d),
                    'is_peak' => $d === 25,
                    'events' => $eventsByDay[$d] ?? [],
                ];
            }

            while (count($cells) % 7 !== 0) {
                $cells[] = null;
            }

            $weeks = array_chunk($cells, 7);

            return view('user.rundown.calendar', [
                'monthLabel' => 'Oktober 2025',
                'weeks' => $weeks,
                'tabCounts' => ['all' => 14, 'today' => 2, 'rehearsal' => 4, 'upcoming' => 8],
                'highlight' => [
                    'title' => 'Sabtu, 25 Oktober 2025 • Peak Wedding Weekend',
                    'desc' => 'Semua kru (100%), koordinasi ballroom utama, dan catering vendor terkonfirmasi siaga penuh.',
                    'crew_count' => 6,
                    'date' => '2025-10-25',
                    'date_short' => '25 Okt',
                ],
                'selected' => [
                    'date' => '2025-10-25',
                    'date_short' => '25 Okt',
                    'date_label' => 'Sabtu, 25 Oktober 2025',
                    'event_count' => 6,
                    'crew_percent' => 100,
                    'crew_ready' => 54,
                    'crew_total' => 54,
                    'leads' => 6,
                    'mcs' => 6,
                    'floor' => 42,
                    'events' => [
                        [
                            'session' => 'Malam', 'time' => '19.00 - 22.00',
                            'couple' => 'Aditya Wardhana & Sarah Nadia',
                            'venue' => 'The Ritz-Carlton Mega Kuningan', 'hall' => 'Grand Ballroom',
                            'lead' => 'Sarah Nadia (Director)', 'crew' => '12 Personil (100% On-Site)',
                            'progress_label' => 'Rundown 18 Sesi Selesai',
                        ],
                        [
                            'session' => 'Siang', 'time' => '11.00 - 14.00',
                            'couple' => 'Reza Pratama & Anindya Putri',
                            'venue' => 'The Dharmawangsa Jakarta', 'hall' => 'Nusantara Garden',
                            'lead' => 'Bayu Wicaksono', 'crew' => '10 Personil Lengkap',
                            'progress_label' => 'Dekorasi: Setup 95% Set',
                        ],
                    ],
                ],
            ]);

        })->name('rundown');


        /*
        |----------------------------------------------------------------
        | 7. DETAIL & TRACKING ACARA (per tanggal, live progress)
        |----------------------------------------------------------------
        */
        Route::get('/rundown-timeline/{date}', function ($date) {

            return view('user.rundown.event-detail', [
                'event' => [
                    'date_label' => $date,
                    'title' => 'The Royal Emerald Wedding of Aditya & Sarah',
                    'hero_image' => 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=1600&q=85',
                    'live' => true,
                    'session_time' => '18:30 - 22:00 WIB',
                    'venue' => 'Grand Ballroom Hotel Mulia Senayan, Jakarta Pusat',
                    'couple' => 'Aditya Pratama, S.T & Sarah Nadia Maharani, B.',
                    'package' => 'Royal Emerald Grand + Full Cinematic 4K Drone',
                    'guests' => ['confirmed' => 590, 'total' => 650, 'percent' => 91],
                    'lead_director' => 'Sarah Nadia (Co-Lead: Dimas Wardhana)',
                    'progress' => [
                        'current_time' => '19:45 WIB',
                        'phase_label' => 'Fase 04 / 05 - Resepsi & Pesta Puncak (82%)',
                        'phase_percent' => 82,
                        'crew_count' => 16,
                        'crew_note' => '4 Floor Captains • 1 Timekeeper • Semua radio HT aktif',
                    ],
                ],
                'phases' => [
                    [
                        'state' => 'done', 'state_label' => 'Selesai', 'title' => 'Persiapan & Loading Vendor', 'time_range' => '06:00 - 14:00 WIB',
                        'items' => [
                            ['time' => '06:00', 'title' => 'Loading Dock & Perizinan Venue Ballroom', 'note' => 'Lotus Decor, Puspa Catering', 'tag' => 'Clearance OK', 'done' => true],
                            ['time' => '09:30', 'title' => 'Setup Panggung, LED Screen & Rigging Tata Cahaya', 'tag' => 'Audio-Visual Tested', 'done' => true],
                            ['time' => '12:00', 'title' => 'Pengecekan Suhu Makanan & Stand Buffet VIP', 'tag' => 'HACCP Approved', 'done' => true],
                        ],
                    ],
                    [
                        'state' => 'done', 'state_label' => 'Selesai', 'title' => 'Gladi Resik & Persiapan Rias', 'time_range' => '14:30 - 17:00 WIB',
                        'items' => [
                            ['time' => '14:30', 'title' => 'Tata Rias Pengantin & Ibu Mempelai', 'tag' => 'Flawless Touch', 'done' => true],
                            ['time' => '15:30', 'title' => 'Briefing MC, Konduktor Orkestra & Tim Dokumentasi', 'tag' => 'All Synchronized', 'done' => true],
                        ],
                    ],
                    [
                        'state' => 'done', 'state_label' => 'Selesai', 'title' => 'Akad Nikah Khidmat & Sungkeman', 'time_range' => '17:15 - 18:45 WIB',
                        'items' => [
                            ['time' => '17:30', 'title' => 'Ijab Qabul, Khutbah Nikah & Doa Bersama', 'tag' => 'Sah 1x Tarikan Nafas', 'done' => true],
                            ['time' => '18:00', 'title' => 'Penyerahan Mahar Emas & Sungkeman', 'tag' => 'Tersimpan di Brankas', 'done' => true],
                        ],
                    ],
                    [
                        'state' => 'active', 'state_label' => 'Sedang Berlangsung', 'title' => 'Resepsi Agung & Pesta Puncak', 'time_range' => '19:00 - 21:30 WIB',
                        'items' => [
                            ['time' => '19:00', 'title' => 'Kirab Pengantin Nusantara & Tarian Pagar Pengantin', 'tag' => 'Sukses 19:25 WIB', 'done' => true],
                            ['time' => '19:30', 'title' => 'Toast VIP Champagne Fountain & Pemotongan Kue', 'tag' => 'Selesai 19:42 WIB', 'done' => true],
                            ['time' => '19:45', 'title' => 'Live Orchestral Showcase by Dwiki Jazz Ensemble', 'note' => 'Pengantin berinteraksi di pelaminan; food stall 100% melayani.', 'active' => true, 'done' => false],
                            ['time' => '20:45', 'title' => 'Lempar Hand Bouquet & Sesi Foto Kolega', 'done' => false],
                        ],
                    ],
                    [
                        'state' => 'pending', 'state_label' => 'Menunggu', 'title' => 'Penutupan & Serah Terima Logistik', 'time_range' => '21:30 - 23:30 WIB',
                        'items' => [
                            ['time' => '21:30', 'title' => 'Closing Ceremony Ballroom & Pengawalan ke Presidential Suite', 'done' => false],
                            ['time' => '22:00', 'title' => 'Serah Terima Amplop, Mahar & Hadiah ke Keluarga Inti', 'tag' => 'Security Escort Ready', 'done' => false],
                        ],
                    ],
                ],
                'finance' => [
                    'total' => 107625000,
                    'status' => 'Lunas 100%',
                    'terms' => [
                        ['label' => 'Termin 1 (DP 30% Booking Fee)', 'amount' => 32287500, 'status' => 'Paid'],
                        ['label' => 'Termin 2 (Progress 40% Vendor Lock)', 'amount' => 43050000, 'status' => 'Paid'],
                        ['label' => 'Termin 3 (Pelunasan H-14 Escrow)', 'amount' => 32287500, 'status' => 'Paid'],
                    ],
                    'disbursement_status' => 'Ditahan Escrow (Rilis H+1)',
                ],
                'vendorsOnSite' => [
                    'ready' => 5, 'total' => 5,
                    'list' => [
                        ['name' => 'Lotus Design & Decoration', 'status' => '100% Siap', 'icon' => 'flower1', 'desc' => 'Panggung Pelaminan & Fresh Flower Selesai Handover', 'pic' => 'Kevin Soetanto', 'note' => 'Inspeksi: Lulus'],
                        ['name' => 'Puspa Catering VIP', 'status' => 'Disajikan', 'icon' => 'cup-hot', 'desc' => 'Buffet Utama, 4 Stall Eksklusif & VIP Family Lounge', 'pic' => '-', 'note' => 'Food Temp: 68°C'],
                        ['name' => 'The Leonardi Photography', 'status' => 'Standby Lensa', 'icon' => 'camera', 'desc' => '4 Fotografer & 2 Drone Pilot On-site Live Feed', 'pic' => '-', 'note' => 'Sync Aktif'],
                        ['name' => 'Dwiki Jazz Orchestra', 'status' => 'Soundcheck OK', 'icon' => 'music-note-beamed', 'desc' => '12 Instrumentalis + 2 Vokalis Jazz Repertoire', 'pic' => '-', 'note' => 'Ear-Monitor Ready'],
                        ['name' => 'Bennu Sorumba MUA', 'status' => 'Retouch Standby', 'icon' => 'brush', 'desc' => 'Ruang Rias Pengantin Suite 1102 (Touch up 20:15)', 'pic' => '-', 'note' => 'Suite Siap'],
                    ],
                ],
                'crew' => [
                    'total' => 12,
                    'list' => [
                        ['initials' => 'SD', 'name' => 'Show Director', 'role' => 'Show Director'],
                        ['initials' => 'SM', 'name' => 'Stage Manager', 'role' => 'Stage Manager'],
                        ['initials' => 'VIP', 'name' => 'VIP Liaison', 'role' => 'VIP Liaison'],
                        ['initials' => 'AV', 'name' => 'Sound & Light', 'role' => 'Sound & Light'],
                        ['initials' => 'RA', 'name' => 'Rendra Ardiansyah', 'role' => 'HT Ch. 01 (Direct)'],
                        ['initials' => 'KN', 'name' => 'Kartika Nuraini', 'role' => 'HT Ch. 02 (Bridal)'],
                    ],
                ],
            ]);

        })->name('rundown.detail');

    });
