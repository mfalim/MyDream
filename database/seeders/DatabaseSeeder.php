<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Vendor;
use App\Models\VendorPhoto;
use App\Models\Package;
use App\Models\Client;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Event;
use App\Models\Schedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $img = fn (string $id) => "https://images.unsplash.com/{$id}?w=900";

        // 1. ADMIN USER (Sesuai permintaan: alief.nug21@gmail.com)
        $adminUser = User::updateOrCreate(
            ['email' => 'alief.nug21@gmail.com'],
            [
                'name' => 'Alief Nugroho',
                'username' => 'alief',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'approved',
                'avatar' => 'https://ui-avatars.com/api/?name=Alief+Nugroho&background=E53E3E&color=fff',
            ]
        );

        // 2. CLIENT USER & PROFILE (Untuk pengetesan mobile / web client)
        $clientUser = User::updateOrCreate(
            ['email' => 'client@mydream.com'],
            [
                'name' => 'Budi Santoso',
                'username' => 'budisantoso',
                'password' => Hash::make('password'),
                'role' => 'client',
                'status' => 'approved',
                'avatar' => 'https://ui-avatars.com/api/?name=Budi+Santoso&background=3182CE&color=fff',
            ]
        );

        $client = Client::updateOrCreate(
            ['users_id' => $clientUser->id],
            [
                'groom_name' => 'Budi Santoso',
                'bride_name' => 'Siti Rahma',
                'groom_phone' => '081234567890',
                'bride_phone' => '081234567891',
                'email' => 'client@mydream.com',
            ]
        );

        // 3. KATEGORI VENDOR
        $categoriesMap = [];
        $categoriesList = [
            'Venue & Ballroom',
            'Dekorasi & Lighting',
            'Fotografi & Videografi',
            'Katering & Kue',
            'Makeup & Hair',
            'Gaun & Busana Pengantin',
            'Entertainment & Sound',
            'Wedding Organizer',
        ];

        foreach ($categoriesList as $catName) {
            $cat = Category::firstOrCreate(['name' => $catName]);
            $categoriesMap[$catName] = $cat->id;
        }

        // 4. VENDOR & AKUN USER VENDOR (Menyiapkan akun untuk masing-masing vendor)
        $vendorsData = [
            [
                'slug' => 'samudra-atelier-decor',
                'name' => 'Samudra Atelier Decor',
                'category' => 'Dekorasi & Lighting',
                'city' => 'Jember',
                'address' => 'Jl. Slamet Riyadi No. 42, Patrang, Jember',
                'phone' => '081233450000',
                'price' => 18500000,
                'instagram' => '@samudra.atelier',
                'guest_capacity' => null,
                'bank_name' => 'BCA',
                'bank_account_number' => '1234567890',
                'bank_account_holder' => 'PT Samudra Dekorasi Indonesia',
                'description' => 'Spesialis dekorasi pelaminan adat Jawa-Madura, modern minimalis, dan floral kontemporer.',
                'user' => [
                    'name' => 'Samudra Atelier Vendor',
                    'username' => 'samudradecor',
                    'email' => 'samudra@mydream.com',
                ],
                'photos' => ['photo-1519741497674-611481863552', 'photo-1465495976277-4387d4b0b4c6'],
            ],
            [
                'slug' => 'marlene-hariman-makeup',
                'name' => 'Marlene Hariman Makeup',
                'category' => 'Makeup & Hair',
                'city' => 'Jember',
                'address' => 'Jl. Kalimantan No. 18, Tegalboto, Jember',
                'phone' => '081233450001',
                'price' => 4500000,
                'instagram' => '@marlenehariman.mua',
                'guest_capacity' => null,
                'bank_name' => 'Mandiri',
                'bank_account_number' => '1410012345678',
                'bank_account_holder' => 'Marlene Hariman',
                'description' => 'Rias pengantin akad dan resepsi dengan tampilan natural glam, termasuk makeup ibu pengantin.',
                'user' => [
                    'name' => 'Marlene Hariman',
                    'username' => 'marlenemua',
                    'email' => 'marlene@mydream.com',
                ],
                'photos' => ['photo-1487412947147-5cebf100ffc2'],
            ],
            [
                'slug' => 'axioo-photography',
                'name' => 'Axioo Photography',
                'category' => 'Fotografi & Videografi',
                'city' => 'Surabaya',
                'address' => 'Jl. Raya Darmo No. 88, Surabaya',
                'phone' => '081233450002',
                'price' => 9500000,
                'instagram' => '@axioophotography',
                'guest_capacity' => null,
                'bank_name' => 'BCA',
                'bank_account_number' => '0887654321',
                'bank_account_holder' => 'PT Axioo Citra Abadi',
                'description' => 'Dokumentasi pre-wedding dan wedding day bergaya candid dan cinematic dengan tim fotografer profesional.',
                'user' => [
                    'name' => 'Axioo Photography',
                    'username' => 'axioophoto',
                    'email' => 'axioo@mydream.com',
                ],
                'photos' => ['photo-1516035069371-29a1b244cc32'],
            ],
            [
                'slug' => 'stupa-caspea-decor',
                'name' => 'Stupa Caspea Decor',
                'category' => 'Dekorasi & Lighting',
                'city' => 'Surabaya',
                'address' => 'Jl. Mayjen Sungkono No. 102, Surabaya',
                'phone' => '081233450003',
                'price' => 45000000,
                'instagram' => '@stupacaspeadecor',
                'guest_capacity' => null,
                'bank_name' => 'BCA',
                'bank_account_number' => '2345678901',
                'bank_account_holder' => 'Stupa Caspea Decor',
                'description' => 'Dekorasi grand ballroom, instalasi floral botanikal megah, dan ambience lighting bertaraf internasional.',
                'user' => [
                    'name' => 'Stupa Caspea Decor',
                    'username' => 'stupacaspea',
                    'email' => 'stupacaspea@mydream.com',
                ],
                'photos' => ['photo-1522673607200-164d1b6ce486'],
            ],
            [
                'slug' => 'atelier-han-tjen',
                'name' => 'Atelier Han Tjen',
                'category' => 'Gaun & Busana Pengantin',
                'city' => 'Surabaya',
                'address' => 'Jl. Basuki Rahmat No. 54, Surabaya',
                'phone' => '081233450004',
                'price' => 25000000,
                'instagram' => '@atelierhantjen',
                'guest_capacity' => null,
                'bank_name' => 'Mandiri',
                'bank_account_number' => '1320098765432',
                'bank_account_holder' => 'Atelier Han Tjen',
                'description' => 'Busana pengantin custom lace dengan fitting pribadi dan desain haute couture elegan.',
                'user' => [
                    'name' => 'Atelier Han Tjen',
                    'username' => 'hantjengown',
                    'email' => 'hantjen@mydream.com',
                ],
                'photos' => ['photo-1594552072238-b8a33785b261'],
            ],
            [
                'slug' => 'umara-catering',
                'name' => 'Umara Catering',
                'category' => 'Katering & Kue',
                'city' => 'Jember',
                'address' => 'Jl. Gajah Mada No. 120, Jember',
                'phone' => '081233450005',
                'price' => 65000000,
                'instagram' => '@umaracatering',
                'guest_capacity' => 500,
                'bank_name' => 'BNI',
                'bank_account_number' => '0345678912',
                'bank_account_holder' => 'CV Umara Boga Rasa',
                'description' => 'Katering prasmanan dan gubukan menu Nusantara pilihan untuk resepsi hingga 500 tamu.',
                'user' => [
                    'name' => 'Umara Catering',
                    'username' => 'umaracatering',
                    'email' => 'umara@mydream.com',
                ],
                'photos' => ['photo-1555244162-803834f70033'],
            ],
            [
                'slug' => 'grand-ballroom-mulia',
                'name' => 'Grand Ballroom Mulia',
                'category' => 'Venue & Ballroom',
                'city' => 'Surabaya',
                'address' => 'Jl. Embong Malang No. 25, Surabaya',
                'phone' => '081233450006',
                'price' => 85000000,
                'instagram' => null,
                'guest_capacity' => 600,
                'bank_name' => 'BCA',
                'bank_account_number' => '4567890123',
                'bank_account_holder' => 'PT Mulia Graha Sejahtera',
                'description' => 'Ballroom pilar-less mewah berkapasitas hingga 600 tamu dengan fasilitas ruang VIP dan sound system lengkap.',
                'user' => [
                    'name' => 'Grand Ballroom Mulia',
                    'username' => 'muliaballroom',
                    'email' => 'mulia@mydream.com',
                ],
                'photos' => ['photo-1519225421980-715cb0215aed'],
            ],
            [
                'slug' => 'gedung-soetrisno-hadi',
                'name' => 'Gedung Soetrisno Hadi',
                'category' => 'Venue & Ballroom',
                'city' => 'Jember',
                'address' => 'Jl. Sultan Agung No. 67, Jember',
                'phone' => '081233450007',
                'price' => 30000000,
                'instagram' => null,
                'guest_capacity' => 400,
                'bank_name' => 'Bank Jatim',
                'bank_account_number' => '0032145678',
                'bank_account_holder' => 'Yayasan Soetrisno Hadi',
                'description' => 'Gedung serbaguna strategis di pusat kota Jember dengan halaman parkir luas, ideal untuk akad dan resepsi.',
                'user' => [
                    'name' => 'Gedung Soetrisno Hadi',
                    'username' => 'soetrisnohadi',
                    'email' => 'soetrisno@mydream.com',
                ],
                'photos' => ['photo-1519741497674-611481863552'],
            ],
        ];

        $vendorModelMap = [];
        foreach ($vendorsData as $vData) {
            // 4a. Buat Akun User Vendor
            $vendorUser = User::updateOrCreate(
                ['email' => $vData['user']['email']],
                [
                    'name' => $vData['user']['name'],
                    'username' => $vData['user']['username'],
                    'password' => Hash::make('password'),
                    'role' => 'vendor',
                    'status' => 'approved',
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($vData['name']) . '&background=ED8936&color=fff',
                ]
            );

            // 4b. Buat Record Vendor dan kaitkan user_id
            $vendor = Vendor::updateOrCreate(
                ['slug' => $vData['slug']],
                [
                    'user_id' => $vendorUser->id,
                    'name' => $vData['name'],
                    'category_id' => $categoriesMap[$vData['category']],
                    'city' => $vData['city'],
                    'address' => $vData['address'],
                    'phone' => $vData['phone'],
                    'price' => $vData['price'],
                    'guest_capacity' => $vData['guest_capacity'],
                    'bank_name' => $vData['bank_name'],
                    'bank_account_number' => $vData['bank_account_number'],
                    'bank_account_holder' => $vData['bank_account_holder'],
                    'instagram' => $vData['instagram'],
                    'description' => $vData['description'],
                ]
            );

            $vendorModelMap[$vData['slug']] = $vendor;

            // 4c. Vendor Photos
            foreach ($vData['photos'] as $idx => $photoId) {
                VendorPhoto::updateOrCreate(
                    [
                        'vendor_id' => $vendor->id,
                        'photo' => $img($photoId),
                    ],
                    [
                        'is_cover' => $idx === 0,
                        'sort_order' => $idx,
                    ]
                );
            }
        }

        // 5. PAKET PERNIKAHAN (Packages)
        $packagesData = [
            [
                'slug' => 'royal-garden-wedding',
                'name' => 'Royal Garden Wedding',
                'description' => 'Resepsi taman dengan venue, dekorasi, rias, dan katering lengkap untuk keluarga besar.',
                'guest_capacity' => 300,
                'duration' => 8,
                'is_featured' => true,
                'photo' => $img('photo-1519741497674-611481863552'),
                'vendors' => ['gedung-soetrisno-hadi', 'samudra-atelier-decor', 'marlene-hariman-makeup', 'umara-catering'],
            ],
            [
                'slug' => 'akad-minimalis-champagne',
                'name' => 'Akad Minimalis Champagne',
                'description' => 'Akad intim dengan dekorasi backdrop champagne dan rias pengantin natural.',
                'guest_capacity' => 80,
                'duration' => 4,
                'is_featured' => true,
                'photo' => $img('photo-1465495976277-4387d4b0b4c6'),
                'vendors' => ['samudra-atelier-decor', 'marlene-hariman-makeup'],
            ],
            [
                'slug' => 'grand-ballroom-surabaya',
                'name' => 'Grand Ballroom Surabaya',
                'description' => 'Resepsi ballroom megah: venue, dekorasi, busana pengantin, dan dokumentasi.',
                'guest_capacity' => 500,
                'duration' => 6,
                'is_featured' => true,
                'photo' => $img('photo-1522673607200-164d1b6ce486'),
                'vendors' => ['grand-ballroom-mulia', 'stupa-caspea-decor', 'axioo-photography', 'atelier-han-tjen'],
            ],
            [
                'slug' => 'busana-dan-dokumentasi-surabaya',
                'name' => 'Busana & Dokumentasi Surabaya',
                'description' => 'Paket busana pengantin custom dan dokumentasi foto-video.',
                'guest_capacity' => null,
                'duration' => 8,
                'is_featured' => false,
                'photo' => $img('photo-1594552072238-b8a33785b261'),
                'vendors' => ['atelier-han-tjen', 'axioo-photography'],
            ],
        ];

        $packageModelMap = [];
        foreach ($packagesData as $pkg) {
            $package = Package::updateOrCreate(
                ['slug' => $pkg['slug']],
                [
                    'name' => $pkg['name'],
                    'description' => $pkg['description'],
                    'guest_capacity' => $pkg['guest_capacity'],
                    'duration' => $pkg['duration'],
                    'event_period' => 'Sepanjang tahun',
                    'availability_date' => now()->addMonths(12)->toDateString(),
                    'photo' => $pkg['photo'],
                    'is_featured' => $pkg['is_featured'],
                ]
            );

            $packageModelMap[$pkg['slug']] = $package;

            $vendorIds = [];
            foreach ($pkg['vendors'] as $vSlug) {
                if (isset($vendorModelMap[$vSlug])) {
                    $vendorIds[$vendorModelMap[$vSlug]->id] = ['status' => 'active'];
                }
            }
            $package->vendors()->sync($vendorIds);
        }

        // 6. CONTOH PESANAN, PEMBAYARAN & RUNDOWN ACARA KLIEN
        // (Booking & Event aktif untuk akun Budi Santoso)
        $mainPackage = $packageModelMap['royal-garden-wedding'];

        $booking = Booking::create([
            'client_id' => $client->id,
            'package_id' => $mainPackage->id,
            'invoice_number' => 'INV-' . now()->format('Ymd') . '-0001',
            'venue_name' => 'Gedung Soetrisno Hadi',
            'venue_address' => 'Jl. Sultan Agung No. 67, Jember',
            'venue_city' => 'Jember',
            'venue_province' => 'Jawa Timur',
            'guest_count' => 300,
            'total_price' => 45000000,
            'paid_amount' => 1000000,
            'status' => 'approved',
            'payment_status' => 'dp_paid',
            'notes' => 'Paket Royal Garden Wedding untuk pernikahan Budi & Siti',
        ]);

        // Pembayaran DP terverifikasi
        Payment::create([
            'booking_id' => $booking->id,
            'type' => 'dp',
            'amount' => 1000000,
            'method' => 'midtrans',
            'status' => 'verified',
            'transaction_status' => 'settlement',
            'midtrans_order_id' => 'PAY-INV001-DP-' . time(),
            'verified_at' => now()->subDay(),
            'admin_note' => 'DP Midtrans otomatis terverifikasi sistem',
        ]);

        // Event / Resepsi
        $eventDate = now()->addMonths(2);
        $event = Event::create([
            'booking_id' => $booking->id,
            'package_id' => $mainPackage->id,
            'name' => 'Resepsi Pernikahan Budi & Siti',
            'event_type' => 'resepsi',
            'event_date' => $eventDate->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '14:00:00',
            'guest_count' => 300,
            'status' => 'scheduled',
            'notes' => 'Acara utama resepsi adat Jawa kontemporer',
        ]);

        // Schedules / Rundown Timeline (tanpa member_id, karena member belum dikaitkan)
        $schedulesData = [
            [
                'vendor_slug' => 'samudra-atelier-decor',
                'activity' => 'Loading & Penataan Dekorasi Pelaminan',
                'location' => 'Gedung Soetrisno Hadi',
                'start_time' => $eventDate->copy()->setTime(6, 0),
                'end_time' => $eventDate->copy()->setTime(9, 30),
                'price' => 18500000,
                'status' => 'approved',
                'notes' => 'Pastikan panggung dan bunga pelaminan selesai sebelum akad/resepsi',
            ],
            [
                'vendor_slug' => 'marlene-hariman-makeup',
                'activity' => 'Tata Rias Pengantin & Orang Tua',
                'location' => 'Ruang Rias Gedung Soetrisno Hadi',
                'start_time' => $eventDate->copy()->setTime(6, 30),
                'end_time' => $eventDate->copy()->setTime(9, 45),
                'price' => 4500000,
                'status' => 'approved',
                'notes' => 'Rias akad dilanjutkan touch-up resepsi',
            ],
            [
                'vendor_slug' => 'umara-catering',
                'activity' => 'Loading Katering & Setup Prasmanan',
                'location' => 'Area Dining Gedung Soetrisno Hadi',
                'start_time' => $eventDate->copy()->setTime(8, 30),
                'end_time' => $eventDate->copy()->setTime(10, 0),
                'price' => 20000000,
                'status' => 'approved',
                'notes' => 'Setup 4 line prasmanan dan 5 food stalls',
            ],
            [
                'vendor_slug' => 'gedung-soetrisno-hadi',
                'activity' => 'Gladi Bersih & Sound Check',
                'location' => 'Main Hall Gedung Soetrisno Hadi',
                'start_time' => $eventDate->copy()->setTime(9, 0),
                'end_time' => $eventDate->copy()->setTime(9, 45),
                'price' => 2000000,
                'status' => 'approved',
                'notes' => 'Tes mic, lighting panggung, dan musik latar',
            ],
        ];

        foreach ($schedulesData as $sData) {
            $v = $vendorModelMap[$sData['vendor_slug']] ?? null;
            Schedule::create([
                'event_id' => $event->id,
                'vendor_id' => $v?->id,
                'member_id' => null, // Member dibiarkan kosong sesuai permintaan
                'activity' => $sData['activity'],
                'location' => $sData['location'],
                'start_time' => $sData['start_time'],
                'end_time' => $sData['end_time'],
                'price' => $sData['price'],
                'paid_amount' => 0,
                'payment_status' => 'unpaid',
                'status' => $sData['status'],
                'notes' => $sData['notes'],
            ]);
        }

        // 7. ARTIKEL, INSPIRASI & PUBLIC EVENTS
        $this->call([
            ArticleSeeder::class,
            InspirationSeeder::class,
            PublicEventSeeder::class,
        ]);

        $this->command->info('========================================================');
        $this->command->info('DUMMY DATA BERHASIL DIBUAT DENGAN BERSIH & KONSISTEN!');
        $this->command->info('========================================================');
        $this->command->info('Akun Admin  : alief.nug21@gmail.com (Password: password)');
        $this->command->info('Akun Client : client@mydream.com (Password: password)');
        $this->command->info('Akun Vendor :');
        foreach ($vendorsData as $v) {
            $this->command->info('  - ' . str_pad($v['user']['email'], 25) . ' | user: ' . str_pad($v['user']['username'], 16) . ' | ' . $v['name']);
        }
        $this->command->info('Member      : TIDAK DISERTAKAN (Siap dikaitkan manual oleh Anda)');
        $this->command->info('========================================================');
    }
}
