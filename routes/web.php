<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\OrganizerEventController;
use App\Http\Controllers\Admin\TrackingController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\MyDream\HomeController as MyDreamHomeController;
use App\Http\Controllers\MyDream\PackageController as MyDreamPackageController;
use App\Http\Controllers\MyDream\StoreController as MyDreamStoreController;
use App\Http\Controllers\MyDream\VendorController as MyDreamVendorController;
use App\Http\Controllers\User\RundownController;
use App\Http\Controllers\User\ExploreController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;

Route::get('/login', function () { return view('login.login'); })->name('login');
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/login/profile', [ClientController::class, 'profile'])->name('client.profile');
    Route::post('/login/profile', [ClientController::class, 'storeProfile'])->name('client.profile.store');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', CategoryController::class)->only(['index', 'create', 'store']);

    Route::resource('packages', PackageController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    Route::resource('event_day', BookingController::class)->only(['index', 'create', 'store']);
    Route::get('/event/{id}', [BookingController::class, 'show'])->name('event.show');
    Route::get('/event/{id}/edit', [BookingController::class, 'edit'])->name('event.edit');
    Route::put('/event/{id}', [BookingController::class, 'update'])->name('event.update');
    Route::get('/booking/{booking}/event-days', [BookingController::class, 'getEventDays'])->name('booking.event-days');

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::get('/events/by-month', [CalendarController::class, 'getEventsByMonth'])->name('events.by-month');
    Route::get('/events/by-date', [CalendarController::class, 'getEventsByDate'])->name('events.by-date');

    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');
    Route::get('/tracking/{id}', [TrackingController::class, 'show'])->name('tracking.show');
    Route::post('/tracking/schedule/{scheduleId}/status', [TrackingController::class, 'updateScheduleStatus'])->name('tracking.schedule.status');
    Route::post('/tracking/booking/{bookingId}/status', [TrackingController::class, 'updateBookingStatus'])->name('tracking.booking.status');

    Route::resource('organizer-event', OrganizerEventController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    Route::resource('vendors', VendorController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    Route::resource('members', MemberController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::post('/members/options/positions', [MemberController::class, 'storePosition'])->name('members.options.positions.store');
    Route::post('/members/options/specializations', [MemberController::class, 'storeSpecialization'])->name('members.options.specializations.store');
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
Route::prefix('user')->name('user.')->middleware('auth')->group(function () {

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
        Route::get('/explore-vendor', [ExploreController::class, 'index'])->name('explore-vendor')->withoutMiddleware('auth');
        Route::get('/explore-vendor/vendor/{id}', [ExploreController::class, 'show'])->name('vendor-overview')->withoutMiddleware('auth');
        Route::get('/explore-vendor/package/{id}', [ExploreController::class, 'showPackage'])->name('package-overview')->withoutMiddleware('auth');

        /*
        |----------------------------------------------------------------
        | 4. KERANJANG & CHECKOUT
        |----------------------------------------------------------------
        */
        Route::get('/cart', [CartController::class, 'index'])->name('cart');
        Route::post('/cart/package/{packageId}', [CartController::class, 'addPackage'])->name('cart.add-package');
        Route::post('/cart/vendor/{vendorId}', [CartController::class, 'addVendor'])->name('cart.add-vendor');
        Route::delete('/cart/vendor/{vendorId}', [CartController::class, 'remove'])->name('cart.remove');
        Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
        Route::get('/payment/{bookingId}', [CheckoutController::class, 'payment'])->name('payment');


        /*
        |----------------------------------------------------------------
        | 5. RUNDOWN & TIMELINE
        |----------------------------------------------------------------
        */
        Route::get('/rundown-timeline', [RundownController::class, 'index'])->name('rundown');
        Route::get('/rundown-timeline/create', [RundownController::class, 'create'])->name('rundown.create');
        Route::post('/rundown-timeline', [RundownController::class, 'store'])->name('rundown.store');
        Route::get('/rundown-timeline/{date}', [RundownController::class, 'show'])->name('rundown.detail');

    });
