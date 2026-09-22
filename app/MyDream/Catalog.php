<?php

namespace App\MyDream;

use Illuminate\Support\Collection;

/**
 * Sumber data statis (dummy) untuk seluruh halaman.
 *
 * Nanti tinggal diganti dengan Eloquent, misalnya:
 *   Package::with('vendors')->where('slug', $slug)->first()
 * selama bentuk array yang dikembalikan sama, view tidak perlu diubah.
 */
class Catalog
{
    public const WA_NUMBER = '6281233779967';

    public static function wa(string $text = ''): string
    {
        return 'https://wa.me/' . self::WA_NUMBER . ($text !== '' ? '?text=' . rawurlencode($text) : '');
    }

    public static function img(int $n): string
    {
        return 'images/mydream/ph-' . (($n - 1) % 6 + 1) . '.svg';
    }

    /* ------------------------------------------------------------------ */
    /*  KATEGORI                                                          */
    /* ------------------------------------------------------------------ */

    public static function categories(): array
    {
        return [
            'paket'      => 'Paket Pernikahan',
            'venue'      => 'Venue & Ballroom',
            'foto'       => 'Fotografi & Videografi',
            'gaun'       => 'Gaun & Busana Pengantin',
            'makeup'     => 'Makeup & Hair',
            'dekor'      => 'Dekorasi & Lighting',
            'katering'   => 'Katering & Kue',
            'wo'         => 'Wedding Organizer',
            'perhiasan'  => 'Perhiasan & Cincin',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  LANDING PAGE                                                      */
    /* ------------------------------------------------------------------ */

    public static function portfolio(): array
    {
        return [
            ['cat' => 'adat',    'label' => 'Adat Jawa Solo',   'title' => 'Pernikahan Erwin & Anggita', 'desc' => 'Sentuhan klasik adat Jawa dengan nuansa khidmat & elegan.', 'image' => self::img(1)],
            ['cat' => 'akad',    'label' => 'Outdoor Ceremony', 'title' => 'Pernikahan Feby & Yudo',     'desc' => 'Akad intim bernuansa bunga segar putih & sage.',            'image' => self::img(2)],
            ['cat' => 'modern',  'label' => 'Ballroom Reception','title' => 'Pernikahan Vio & Tika',     'desc' => 'Resepsi modern bernuansa pencahayaan glamour & hangat.',    'image' => self::img(3)],
            ['cat' => 'khusus',  'label' => 'Prosesi Khusus',   'title' => 'Prosesi Pedang Pora',        'desc' => 'Upacara sakral militer penuh kehormatan dan kebanggaan.',   'image' => self::img(4)],
            ['cat' => 'adat',    'label' => 'Tradisional Modern','title' => 'Pernikahan Jawa Klasik',    'desc' => 'Dekorasi gebyok emas & konsep kekeluargaan penuh syahdu.',  'image' => self::img(5)],
            ['cat' => 'modern',  'label' => 'Intimate Dinner',  'title' => 'Resepsi Malam Romantis',     'desc' => 'Gemerlap fairylight di kebun terbuka bernuansa akrab.',     'image' => self::img(6)],
        ];
    }

    public static function services(): array
    {
        return [
            ['title' => 'Perencanaan dari Nol', 'sub' => '(Full Planning)',        'desc' => 'Teman diskusi sejak awal. Kami bantu cari ide tema, atur perkiraan budget, pilih vendor yang tepat, sampai buat susunan acara yang detail.'],
            ['title' => 'Pengawal Hari Acara',  'sub' => '(D-Day Coordination)',   'desc' => 'Fokus nikmati hari bahagiamu. Tim kami yang akan kawal setiap jeda waktu, sambut keluarga besar, dan pastikan seluruh vendor bekerja sesuai rundown.'],
            ['title' => 'Pilihan Vendor Terpercaya', 'sub' => '',                  'desc' => 'Kami hubungkan kamu dengan dekorator, MUA, fotografer, dan sound system terbaik di Jember tanpa perlu repot mencarinya satu per satu.'],
            ['title' => 'Lamaran & Acara Keluarga', 'sub' => '',                   'desc' => 'Pendampingan untuk momen sakral pra-nikah seperti lamaran, siraman, dan pengajian agar tetap khidmat, hangat, dan tertata.'],
        ];
    }

    public static function landingTestimonials(): array
    {
        return [
            ['quote' => 'Detail acaranya rapi banget. Acara adat Jawanya khidmat, resepsi malamnya juga seru tanpa jeda yang canggung. Tim My Dream sigap sekali.', 'name' => 'Pasangan Pengantin Adat Jawa'],
            ['quote' => 'Bener-bener bikin tenang. Dari awal konsultasi sampai hari-H komunikasinya enak dan fleksibel banget diajak diskusi.', 'name' => 'Feby & Yudo'],
            ['quote' => 'Pilihan paling tepat buat wedding di Jember. Rundown bener-bener tepat waktu dan koordinasi vendornya juara.', 'name' => 'Erwin & Anggita'],
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  PAKET (gabungan beberapa vendor)                                  */
    /* ------------------------------------------------------------------ */

    public static function packages(): Collection
    {
        return collect(self::packageData())->values();
    }

    public static function package(string $slug): ?array
    {
        return self::packageData()[$slug] ?? null;
    }

    private static function packageData(): array
    {
        $steps = [
            ['title' => 'Beli Voucher',       'desc' => 'Pilih varian pax & bayar dengan MyDream Pay yang aman bergaransi.'],
            ['title' => 'Dedicated Concierge', 'desc' => 'Wedding specialist menghubungi via WhatsApp dalam 1×24 jam.'],
            ['title' => 'Kunci Tanggal & Fitting', 'desc' => 'Survey venue, food tasting katering, serta fitting gaun impian.'],
            ['title' => 'Hari Bahagia Sempurna', 'desc' => 'Pernikahan impian terlaksana dengan pengawalan WO berdedikasi tinggi.'],
        ];

        $testi = [
            ['initials' => 'NA', 'name' => 'Nadia & Ardiansyah', 'meta' => 'Pernikahan 14 September 2024 • 350 Pax', 'text' => 'Keputusan terbaik kami menggunakan paket All-In dari MyDream Store! Venue sangat magical saat malam tiba, kateringnya dipuji habis-habisan oleh keluarga besar. Tim WO sigap mengatur flow tamu 350 pax tanpa ada antrean menumpuk sama sekali.'],
            ['initials' => 'KS', 'name' => 'Kevin & Stefani',    'meta' => 'Pernikahan 22 Juni 2024 • 400 Pax',      'text' => 'Cicilan 0% MyDream Pay sangat meringankan cashflow kami berdua. Dari awal pemilihan gaun, technical meeting, sampai hari H benar-benar anti stres. Terima kasih untuk tim wedding specialist yang mendampingi kami!'],
        ];

        $short = fn (string $title, string $worth, array $points, string $icon) => [
            'icon' => $icon, 'title' => $title, 'worth' => $worth, 'points' => $points,
        ];

        return [

            /* ---------- PAKET LENGKAP (contoh halaman overview paket) ---------- */
            'o2-park-all-in-one-luxury' => [
                'slug'  => 'o2-park-all-in-one-luxury',
                'title' => 'O2 Park Surabaya — All In One Luxury Wedding Package',
                'name'  => 'O2 Park Surabaya — All In One Luxury Wedding Package',
                'tag'   => 'SURABAYA BARAT • ALL-IN PACKAGE',
                'badge' => 'HEMAT RP 26 JT', 'ribbon' => 'BEST SELLER',
                'image' => self::img(1),
                'gallery' => [self::img(1), self::img(2), self::img(3), self::img(4), self::img(5)],
                'gallery_caption' => 'Glasshouse Sanctuary & Lush Lawn Garden',
                'features' => ['Venue + katering 300 pax + dekorasi', 'Foto, video, MUA, WO & entertainment'],
                'organizer' => 'O2 Park & Event Space Surabaya',
                'area' => 'Graha Famili, Surabaya Barat',
                'rating' => 4.9, 'reviews' => 128, 'sold' => 45,
                'badges' => [['Promo Eksklusif MyDream Store', 'maroon'], ['Verified Vendor', 'gold'], ['MyDream Pay 0%', 'neutral'], ['All-In Complete Package', 'neutral']],
                'price' => 139_000_000, 'old_price' => 165_000_000, 'discount' => 16,
                'options' => [
                    ['label' => '300 Pax (Default Signature)', 'desc' => 'Buffet 300 porsi + 3 Food Stalls Lengkap', 'price' => 139_000_000, 'old_price' => 165_000_000],
                    ['label' => '500 Pax (Grand Celebration)', 'desc' => 'Buffet 500 porsi + 5 Food Stalls Lengkap', 'price' => 185_000_000, 'old_price' => 219_000_000],
                    ['label' => 'Intimate 200 Pax',            'desc' => 'Khusus Micro Wedding & Pemberkatan',      'price' => 105_000_000, 'old_price' => 124_000_000],
                ],
                'sessions' => ['Resepsi Malam', 'Resepsi Siang'],
                'facts' => [
                    ['bi-calendar-check', 'Periode acara', 'Hingga Des 2026'],
                    ['bi-clock',          'Durasi pemakaian', '6 Jam Acara + Setup'],
                    ['bi-people',         'Kapasitas tamu', '300 s/d 700 Pax'],
                    ['bi-shield-check',   'Jaminan escrow', '100% Proteksi Dana'],
                ],
                'tabs' => ['Semua Sudah Termasuk (7 Item)', 'Spesifikasi Venue & Fasilitas', 'Menu Katering & Stalls', 'Vendor Rekanan', 'S&K'],
                'inclusion_title' => 'Rincian Lengkap Paket Pernikahan All-In (300 Pax)',
                'inclusion_intro' => 'Seluruh kebutuhan pernikahan telah disusun oleh konsultan pernikahan senior MyDream bersama kurasi vendor terpercaya Surabaya. Anda tidak perlu repot mencari dan menyelaraskan vendor terpisah.',
                'inclusions' => [
                    ['icon' => 'bi-house-heart', 'title' => 'Venue & Area Eksklusif', 'worth' => 45_000_000, 'vendor' => null, 'points' => [
                        'Penggunaan penuh Glasshouse Ballroom & Lawn Garden O2 Park Surabaya selama 6 jam acara (opsi Siang/Malam).',
                        '1 Ruang VIP Rias Pengantin Full AC dengan kamar mandi privat & sofa lounge.',
                        '1 Ruang Transit Keluarga Inti & panitia (Full AC).',
                        'Kapasitas daya listrik 15.000 Watt + Full Silent Genset Backup 80 kVA.',
                        'Izin keramaian kepolisian, retribusi area, keamanan internal, dan tim kebersihan intensif.',
                    ]],
                    ['icon' => 'bi-cup-hot', 'title' => 'Katering Resepsi Premium (300 Pax)', 'worth' => 42_000_000, 'vendor' => 'dapur-nusantara', 'points' => [
                        'Prasmanan Buffet 5 menu utama: nasi putih & nasi goreng spesial, olahan daging sapi, olahan ayam, olahan ikan dori asam manis, dan sayuran pilihan.',
                        '3 macam live food stalls populer (porsi 150 pax per stall): Zuppa Soup, Roast Australian Beef, Sup Kimlo / Tom Yum.',
                        'Dessert bar: aneka pudding, fresh sliced seasonal fruits, es doger / es campur kopyor, dan ice cream stall.',
                        'Free flow mineral water premium galon & infused water lemon mint dingin.',
                        'Bonus spesial: food tasting resmi untuk 6 orang keluarga sebelum penentuan menu final.',
                    ]],
                    ['icon' => 'bi-flower1', 'title' => 'Dekorasi Floral & Ambiance Lighting', 'worth' => 32_000_000, 'vendor' => 'floral-atelier-jember', 'points' => [
                        'Backdrop pelaminan mewah bentang 8–10 meter (pilihan tema: Modern Minimalis Champagne, Romantic Botanical, atau Indonesian Modern Touch) dengan bunga segar premium.',
                        'Panggung pelaminan karpet melamin / rosewood lengkap dengan set kursi pengantin & orang tua.',
                        'Grand gate pergola entrance, walkway bunga, dan fairy lights.',
                        'Meja penerima tamu (2 set) lengkap dengan kotak angpao eksklusif bersegel kunci & standing photo gallery (5 pigura).',
                        'Set meja akad / pemberkatan suci lengkap dengan bunga meja & kursi tiffany beralas pita.',
                    ]],
                    ['icon' => 'bi-camera', 'title' => 'Dokumentasi Foto & Cinematic Video', 'worth' => 18_000_000, 'vendor' => 'sinar-lens-studio', 'points' => [
                        '2 fotografer profesional + 1 cinematic videografer D-Day (coverage s/d 8 jam kerja).',
                        '1 album kolase eksklusif cetak 20×30 cm (20 halaman hard-cover linen premium).',
                        '1 menit teaser video Reels/TikTok (same day edit siap tayang dalam 24 jam).',
                        '5–7 menit cinematic highlight film 4K + full documentation video.',
                        'Seluruh file asli tanpa kompresi dalam custom wooden USB flashdisk box.',
                    ]],
                    ['icon' => 'bi-stars', 'title' => 'Rias Pengantin (MUA) & Wardrobe Busana', 'worth' => 16_000_000, 'vendor' => 'marlene-harriman-makeup', 'points' => [
                        'Makeup & hairdo / hijabdo pengantin wanita untuk akad / matrimony dan resepsi (touch up on site).',
                        'Groom grooming & hair styling untuk mempelai pria.',
                        '1 pasang gaun pengantin ready-to-wear (atelier wedding gown) / busana pengantin tradisional modifikasi.',
                        '1 pasang jas groom lengkap kemeja, dasi, suspender & pocket square.',
                        'Makeup & hairdo untuk 2 ibu pengantin + busana beskap lengkap 2 ayah pengantin.',
                    ]],
                    ['icon' => 'bi-clipboard-check', 'title' => 'Professional Wedding Organizer (WO D-Day)', 'worth' => 10_000_000, 'vendor' => null, 'points' => [
                        '6 kru terlatih profesional on D-Day (event manager, groom assistant, bride assistant, usher leader, F&B checker, runner).',
                        'Penyusunan guidebook / rundown acara detik-per-detik, panduan panitia keluarga dan gladi bersih H-1.',
                        'Koordinasi technical meeting (TM) menyeluruh bersama semua vendor 2 minggu sebelum hari H.',
                        'Handy talkie (HT) communication kit lengkap untuk panitia keluarga inti.',
                    ]],
                    ['icon' => 'bi-music-note-beamed', 'title' => 'MC Bilingual & Mini Acoustic Band', 'worth' => 8_000_000, 'vendor' => null, 'points' => [
                        '1 professional Master of Ceremony (bilingual: Indonesia / English) berlisensi.',
                        'Acoustic ensemble 4 pemain: 1 vokalis romantis, keyboardis, pemain saxophone / biola, dan cajon / bass.',
                        'Standard professional acoustic sound system 5.000 Watt dengan wireless microphones.',
                    ]],
                ],
                'location_title' => 'O2 Park Surabaya Barat',
                'address' => 'Jl. Raya Mayjen Sungkono / Akses Graha Famili Estate Blok C-18, Dukuh Pakis, Surabaya, Jawa Timur 60225. Terletak hanya 7 menit dari Gerbang Tol Gunung Sari.',
                'map_query' => 'Graha Famili Surabaya',
                'map_note' => '150+ Lot Parkir & Valet Standby',
                'testimonials' => $testi,
                'steps' => $steps,
            ],

            /* ---------- PAKET LAIN (ringkas) ---------- */
            'oceanfront-wedding-dinner-200' => [
                'slug' => 'oceanfront-wedding-dinner-200',
                'title' => 'Oceanfront Wedding Ceremony & Dinner Package (200 Pax)',
                'name' => 'Oceanfront Wedding Ceremony & Dinner Package (200 Pax)',
                'tag' => 'BALI • VENUE PERNIKAHAN', 'badge' => 'HEMAT RP 25 JT', 'ribbon' => 'BEST SELLER',
                'image' => self::img(2), 'gallery' => [self::img(2), self::img(3), self::img(4), self::img(5), self::img(6)],
                'gallery_caption' => 'Oceanfront Lawn & Sunset Aisle',
                'features' => ['Eksklusif pemakaian venue outdoor lawn 6 jam', '5-course International Buffet Dinner untuk 200 pax', 'Menginap 2 malam di Bridal Ocean Suite'],
                'organizer' => 'AYANA Resort & Spa Bali', 'area' => 'Jimbaran, Bali',
                'rating' => 4.9, 'reviews' => 98, 'sold' => 32,
                'badges' => [['Promo Eksklusif MyDream Store', 'maroon'], ['Verified Vendor', 'gold'], ['MyDream Pay 0%', 'neutral']],
                'price' => 225_000_000, 'old_price' => 250_000_000, 'discount' => 10,
                'options' => [
                    ['label' => '200 Pax (Signature)', 'desc' => '5-course buffet dinner + live music', 'price' => 225_000_000, 'old_price' => 250_000_000],
                    ['label' => '100 Pax (Intimate)',  'desc' => 'Private dinner tepi pantai',          'price' => 148_000_000, 'old_price' => 165_000_000],
                ],
                'sessions' => ['Resepsi Malam', 'Sunset Ceremony'],
                'facts' => [['bi-calendar-check', 'Periode acara', 'Hingga Des 2026'], ['bi-clock', 'Durasi pemakaian', '6 Jam Acara'], ['bi-people', 'Kapasitas tamu', '100 s/d 250 Pax'], ['bi-shield-check', 'Jaminan escrow', '100% Proteksi Dana']],
                'tabs' => ['Semua Sudah Termasuk (3 Item)', 'Vendor Rekanan', 'S&K'],
                'inclusion_title' => 'Rincian Lengkap Paket (200 Pax)',
                'inclusion_intro' => 'Paket resepsi tepi pantai dengan akomodasi menginap untuk pengantin, disusun oleh tim MyDream bersama vendor rekanan.',
                'inclusions' => [
                    $short('Venue & Ceremony Lawn', 50_000_000, ['Eksklusif venue outdoor lawn tepi laut 6 jam.', 'Kursi tiffany, altar bunga, dan sound system ceremony.'], 'bi-house-heart'),
                    $short('Katering Dinner', 90_000_000, ['5-course International Buffet Dinner untuk 200 pax.', 'Welcome drink & wedding cake 3 tier.'], 'bi-cup-hot'),
                    $short('Akomodasi Pengantin', 35_000_000, ['2 malam di Bridal Ocean Suite.', 'Sarapan untuk 2 orang & airport transfer.'], 'bi-building'),
                ],
                'location_title' => 'AYANA Resort & Spa Bali', 'address' => 'Jl. Karang Mas Sejahtera, Jimbaran, Bali.', 'map_query' => 'AYANA Resort Bali', 'map_note' => 'Valet & shuttle tersedia',
                'testimonials' => $testi, 'steps' => $steps,
            ],

            'westin-grand-luxury-400' => [
                'slug' => 'westin-grand-luxury-400',
                'title' => 'The Westin Surabaya - Grand Luxury Package (400 Pax)',
                'name' => 'The Westin Surabaya - Grand Luxury Package (400 Pax)',
                'tag' => 'SURABAYA PUSAT • BALLROOM', 'badge' => 'HOTEL BINTANG 5', 'ribbon' => null,
                'image' => self::img(3), 'gallery' => [self::img(3), self::img(4), self::img(5), self::img(6), self::img(1)],
                'gallery_caption' => 'Grand Ballroom & Skyline View',
                'features' => ['Paket resepsi megah di Grand Ballroom', 'Pemandangan skyline kota & katering kuliner premium'],
                'organizer' => 'The Westin Surabaya', 'area' => 'Surabaya Pusat',
                'rating' => 4.8, 'reviews' => 76, 'sold' => 28,
                'badges' => [['Verified Vendor', 'gold'], ['MyDream Pay 0%', 'neutral']],
                'price' => 210_000_000, 'old_price' => 232_000_000, 'discount' => 9,
                'options' => [
                    ['label' => '400 Pax (Grand Luxury)', 'desc' => 'Buffet 400 porsi + 4 Food Stalls', 'price' => 210_000_000, 'old_price' => 232_000_000],
                    ['label' => '250 Pax (Elegant)',       'desc' => 'Buffet 250 porsi + 3 Food Stalls', 'price' => 155_000_000, 'old_price' => 170_000_000],
                ],
                'sessions' => ['Resepsi Malam', 'Resepsi Siang'],
                'facts' => [['bi-calendar-check', 'Periode acara', 'Hingga Des 2026'], ['bi-clock', 'Durasi pemakaian', '5 Jam Acara + Setup'], ['bi-people', 'Kapasitas tamu', '250 s/d 600 Pax'], ['bi-shield-check', 'Jaminan escrow', '100% Proteksi Dana']],
                'tabs' => ['Semua Sudah Termasuk (3 Item)', 'Vendor Rekanan', 'S&K'],
                'inclusion_title' => 'Rincian Lengkap Paket (400 Pax)',
                'inclusion_intro' => 'Resepsi hotel bintang 5 dengan ballroom pilar-less dan tim banquet berpengalaman.',
                'inclusions' => [
                    $short('Grand Ballroom', 60_000_000, ['Pemakaian ballroom 5 jam + setup.', '1 Bridal Suite semalam untuk pengantin.'], 'bi-house-heart'),
                    $short('Katering Premium 400 Pax', 110_000_000, ['International buffet & 4 live food stalls.', 'Dessert corner & free flow beverages.'], 'bi-cup-hot'),
                    $short('Dekorasi Pelaminan', 40_000_000, ['Pelaminan 10 meter bunga segar.', 'Aisle decoration & lighting ballroom.'], 'bi-flower1'),
                ],
                'location_title' => 'The Westin Surabaya', 'address' => 'Jl. Basuki Rahmat, Surabaya Pusat.', 'map_query' => 'The Westin Surabaya', 'map_note' => 'Valet parking 24 jam',
                'testimonials' => $testi, 'steps' => $steps,
            ],

            'rustic-haven-intimate-garden-150' => [
                'slug' => 'rustic-haven-intimate-garden-150',
                'title' => 'Rustic Haven Surabaya - Intimate Garden Package (150 Pax)',
                'name' => 'Rustic Haven Surabaya - Intimate Garden Package (150 Pax)',
                'tag' => 'SURABAYA TIMUR • GARDEN', 'badge' => 'BEST INTIMATE', 'ribbon' => null,
                'image' => self::img(4), 'gallery' => [self::img(4), self::img(5), self::img(6), self::img(1), self::img(2)],
                'gallery_caption' => 'Garden Ceremony di Senja Hari',
                'features' => ['Pernikahan kebun kasual romantis', 'Lampu gantung bohemian, family-style dinner, live acoustic'],
                'organizer' => 'Rustic Haven Surabaya', 'area' => 'Surabaya Timur',
                'rating' => 4.9, 'reviews' => 54, 'sold' => 19,
                'badges' => [['Verified Vendor', 'gold'], ['MyDream Pay 0%', 'neutral']],
                'price' => 78_000_000, 'old_price' => 88_000_000, 'discount' => 11,
                'options' => [
                    ['label' => '150 Pax (Garden Party)', 'desc' => 'Family-style dinner + live acoustic', 'price' => 78_000_000, 'old_price' => 88_000_000],
                    ['label' => '80 Pax (Micro Wedding)', 'desc' => 'Private garden dinner',              'price' => 52_000_000, 'old_price' => 58_000_000],
                ],
                'sessions' => ['Resepsi Malam', 'Garden Brunch'],
                'facts' => [['bi-calendar-check', 'Periode acara', 'Hingga Des 2026'], ['bi-clock', 'Durasi pemakaian', '5 Jam Acara'], ['bi-people', 'Kapasitas tamu', '80 s/d 180 Pax'], ['bi-shield-check', 'Jaminan escrow', '100% Proteksi Dana']],
                'tabs' => ['Semua Sudah Termasuk (3 Item)', 'Vendor Rekanan', 'S&K'],
                'inclusion_title' => 'Rincian Lengkap Paket (150 Pax)',
                'inclusion_intro' => 'Pernikahan kebun hangat dengan dekorasi bohemian dan suasana kekeluargaan.',
                'inclusions' => [
                    $short('Garden Venue', 20_000_000, ['Pemakaian taman 5 jam + ruang rias.', 'Listrik & genset cadangan.'], 'bi-house-heart'),
                    $short('Family-Style Dinner', 30_000_000, ['Set menu family-style untuk 150 pax.', 'Signature welcome drink.'], 'bi-cup-hot'),
                    $short('Dekor Bohemian & Lampu Gantung', 18_000_000, ['Pergola bunga & altar kayu.', 'String light & lentera.'], 'bi-flower1'),
                ],
                'location_title' => 'Rustic Haven Surabaya', 'address' => 'Area Surabaya Timur, Jawa Timur.', 'map_query' => 'Surabaya Timur', 'map_note' => 'Parkir luas',
                'testimonials' => $testi, 'steps' => $steps,
            ],

            'omah-heritage-javanese-royal-300' => [
                'slug' => 'omah-heritage-javanese-royal-300',
                'title' => 'Omah Heritage - Classic Javanese Royal Package (300 Pax)',
                'name' => 'Omah Heritage - Classic Javanese Royal Package (300 Pax)',
                'tag' => 'SURABAYA SELATAN • TRADISIONAL', 'badge' => 'HERITAGE ADAT', 'ribbon' => null,
                'image' => self::img(5), 'gallery' => [self::img(5), self::img(6), self::img(1), self::img(2), self::img(3)],
                'gallery_caption' => 'Pendopo Heritage & Gebyok Ukir Jati',
                'features' => ['Tata cara adat Jawa Solo/Yogya lengkap', 'Gebyok ukir jati asli, prosesi siraman, gamelan live'],
                'organizer' => 'Omah Heritage', 'area' => 'Surabaya Selatan',
                'rating' => 4.9, 'reviews' => 63, 'sold' => 24,
                'badges' => [['Verified Vendor', 'gold'], ['MyDream Pay 0%', 'neutral']],
                'price' => 125_000_000, 'old_price' => 138_000_000, 'discount' => 9,
                'options' => [
                    ['label' => '300 Pax (Royal)',      'desc' => 'Adat lengkap + gamelan live',  'price' => 125_000_000, 'old_price' => 138_000_000],
                    ['label' => '150 Pax (Akad & Adat)', 'desc' => 'Akad + panggih + resepsi kecil', 'price' => 82_000_000, 'old_price' => 90_000_000],
                ],
                'sessions' => ['Resepsi Malam', 'Resepsi Siang'],
                'facts' => [['bi-calendar-check', 'Periode acara', 'Hingga Des 2026'], ['bi-clock', 'Durasi pemakaian', '7 Jam Acara + Setup'], ['bi-people', 'Kapasitas tamu', '150 s/d 350 Pax'], ['bi-shield-check', 'Jaminan escrow', '100% Proteksi Dana']],
                'tabs' => ['Semua Sudah Termasuk (3 Item)', 'Vendor Rekanan', 'S&K'],
                'inclusion_title' => 'Rincian Lengkap Paket (300 Pax)',
                'inclusion_intro' => 'Pernikahan adat Jawa penuh makna dengan tim pemandu adat berpengalaman.',
                'inclusions' => [
                    $short('Pendopo & Dekorasi Gebyok', 45_000_000, ['Pendopo heritage 7 jam pemakaian.', 'Gebyok ukir jati & rangkaian melati.'], 'bi-house-heart'),
                    $short('Prosesi Adat Lengkap', 25_000_000, ['Siraman, midodareni, panggih, dan sungkeman.', 'Pemandu adat & pengiring gamelan live.'], 'bi-stars'),
                    $short('Katering Nusantara 300 Pax', 45_000_000, ['Menu prasmanan Nusantara & gubukan.', 'Wedang & jajan pasar.'], 'bi-cup-hot'),
                ],
                'location_title' => 'Omah Heritage', 'address' => 'Area Surabaya Selatan, Jawa Timur.', 'map_query' => 'Surabaya Selatan', 'map_note' => 'Parkir halaman luas',
                'testimonials' => $testi, 'steps' => $steps,
            ],
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  VENDOR (berdiri sendiri)                                          */
    /* ------------------------------------------------------------------ */

    public static function vendors(): Collection
    {
        return collect(self::vendorData())->values();
    }

    public static function vendor(string $slug): ?array
    {
        return self::vendorData()[$slug] ?? null;
    }

    private static function vendorData(): array
    {
        $faq = fn (string $thing) => [
            ['q' => "Bagaimana cara memesan {$thing}?", 'a' => 'Klik tombol "Minta Brosur & Cek Ketersediaan", isi tanggal dan perkiraan tamu, lalu tim wedding specialist kami menghubungi Anda via WhatsApp.'],
            ['q' => 'Bagaimana mekanisme pembayaran dengan cicilan 0% MyDream Pay?', 'a' => 'Cicilan 0% tersedia hingga 24 bulan untuk kartu kredit bank rekanan. Dana dijaga escrow MyDream Pay hingga acara terlaksana.'],
            ['q' => 'Bagaimana kebijakan bila terjadi perubahan tanggal (reschedule)?', 'a' => 'Reschedule dapat dilakukan selama tanggal pengganti tersedia dan diajukan minimal H-60 sebelum acara.'],
        ];

        $reviews = [
            ['name' => 'Kevin Sanjaya & Amanda Wijaya', 'meta' => 'Pernikahan 18 November 2025', 'text' => 'Pernikahan kami benar-benar di luar ekspektasi! Timnya kooperatif, tanggap dari hari pertama dealing sampai hari-H berjalan sempurna.'],
            ['name' => 'Dimas Pratama & Stephanie Salim', 'meta' => 'Pernikahan 14 Januari 2026', 'text' => 'Pelayanan profesional dan hasilnya memuaskan. Terima kasih telah mewujudkan pernikahan impian kami sesuai standar yang kami harapkan!'],
        ];

        $simple = function (array $v) use ($faq, $reviews) {
            return $v + [
                'badges' => [['Verified Vendor', 'gold'], ['MyDream Pay Available', 'maroon']],
                'response_rate' => 97, 'weddings' => 120,
                'reviews_list' => $reviews,
                'faq' => $faq('layanan vendor ini'),
                'rooms' => [], 'facilities' => [], 'highlights' => [],
                'gallery' => [[self::img(1), 'Portofolio Utama'], [self::img(2), 'Detail Karya'], [self::img(3), 'Hari Pernikahan'], [self::img(4), 'Behind The Scene'], [self::img(5), 'Galeri']],
                'card_badges' => ['MYDREAM PICK'],
                'price_to' => null,
                'about_title' => 'Tentang ' . $v['name'],
                'chips' => [],
            ];
        };

        return [

            /* ---------- VENDOR LENGKAP (contoh halaman overview vendor) ---------- */
            'movenpick-jember-city-centre' => [
                'slug' => 'movenpick-jember-city-centre',
                'name' => 'Mövenpick Hotel Jember City Centre',
                'category' => 'venue', 'category_label' => 'Hotel & Grand Ballroom',
                'tagline' => 'Ballroom pilar-less s/d 1.200 tamu',
                'image' => self::img(1),
                'address' => 'Jl. Gajah Mada No. 7-17, Jember, Jawa Timur',
                'city' => 'Jember',
                'rating' => 4.9, 'reviews' => 218, 'response_rate' => 99, 'weddings' => 350,
                'price_from' => 75_000_000, 'price_to' => 485_000_000,
                'card_badges' => ['TOP VENUE 2026', 'MYDREAM PAY'],
                'badges' => [['Top Verified Venue 2026', 'gold'], ['MyDream Pay Available', 'maroon'], ['5-Star Luxury Hotel', 'neutral'], ['Award Winner', 'gold']],
                'chips' => [['bi-people', 'Kapasitas 200 – 1.200 Tamu'], ['bi-building', '3 Grand Ballroom & Function Rooms'], ['bi-egg-fried', '5-Star Swiss & International Culinary']],
                'gallery' => [
                    [self::img(1), 'Grand Emerald Ballroom'],
                    [self::img(2), 'Outdoor Poolside Blessing'],
                    [self::img(3), 'Mövenpick Swiss Chocolate Hour'],
                    [self::img(4), 'Presidential Bridal Suite'],
                    [self::img(5), 'Grand Foyer'],
                ],
                'gallery_count' => 48,
                'about_title' => 'Kemewahan Keramahan Swiss di Titik Sentral Kota',
                'about' => [
                    'Berada di lokasi strategis pusat kota Jember, Mövenpick Hotel Jember City Centre menawarkan kemegahan ballroom berstandar internasional untuk momen pernikahan paling sakral dan istimewa. Menggunakan arsitektur kontemporer bergaya modern mewah dengan kehangatan khas Swiss, venue ini menghadirkan pengalaman pernikahan bintang lima tanpa cela bagi Anda dan keluarga terhormat.',
                    'Ballroom utama kami didesain tanpa pilar (pillarless) dengan plafon menjulang setinggi 8 meter dan ornamen lampu kristal megah yang memberikan fleksibilitas tanpa batas untuk dekorasi pernikahan adat tradisional maupun internasional kontemporer. Dilengkapi reputasi gastronomi dunia yang legendaris, termasuk sesi khas Mövenpick Signature Chocolate Hour, pesta resepsi Anda dipastikan menjadi kenangan manis nan tak terlupakan.',
                ],
                'highlights' => [
                    ['bi-arrows-vertical', 'Plafon Megah', '8 Meter', 'Pillarless Grand Ballroom'],
                    ['bi-door-open', 'Kenyamanan VIP', '2 Ruang', 'Dedicated VIP Holding Room'],
                    ['bi-p-circle', 'Parkir Luas', '400+ Mobil', 'Layanan Valet Tersedia'],
                    ['bi-signpost-2', 'Akses Strategis', 'Pusat Kota', '5 Menit dari Alun-alun'],
                ],
                'packages' => [
                    ['tag' => 'GRAND BALLROOM • 800 - 1.000 TAMU', 'name' => 'The Grand Swiss Ballroom Wedding Package', 'desc' => 'Paket pesta resepsi spektakuler dirancang untuk kemewahan paripurna di Emerald Grand Ballroom dengan jamuan gastronomi Swiss bintang 5.', 'price' => 485_000_000, 'pax' => '1.000', 'popular' => true,
                     'benefits' => ['Penggunaan Emerald Grand Ballroom selama 4 jam penuh', '800 porsi International Buffet & 6 Pilihan Live Food Stalls', '2 Malam menginap di Presidential Suite (termasuk sarapan)', '2 Kamar Deluxe tambahan untuk keluarga inti', 'Food Tasting eksklusif untuk 10 orang keluarga', 'Mövenpick Chocolate Hour Experience khas Swiss', 'Free flow iced tea, infused water & champagne toast tower', 'Standar daya listrik s.d 15.000 Watt & Standard Sound']],
                    ['tag' => 'SAPPHIRE BALLROOM • 300 TAMU', 'name' => 'Elegance Intimate Wedding Package', 'desc' => 'Pilihan tepat untuk perayaan intim bersama sanak keluarga dan kerabat terdekat dalam balutan suasana hangat nan berkelas.', 'price' => 198_000_000, 'pax' => '300', 'popular' => false,
                     'benefits' => ['Pemakaian Sapphire Ballroom eksklusif 4 jam', 'Prasmanan Premium 300 Porsi + 3 Food Stalls', '1 Malam Bridal Suite & 1 Deluxe Room untuk Orang Tua', 'Complimentary 3-tier Wedding Cake persembahan hotel']],
                    ['tag' => 'SKY GARDEN / DIAMOND ROOM • 100 TAMU', 'name' => 'Akad Nikah / Holy Matrimony & Blessing Package', 'desc' => 'Dirancang khusus untuk kekhidmatan prosesi sakramen pemberkatan suci pernikahan atau akad nikah penuh rasa syukur.', 'price' => 75_000_000, 'pax' => '100', 'popular' => false, 'note' => 'Termasuk pajak layanan 21%',
                     'benefits' => ['Pilihan Sky Garden semi-outdoor atau Diamond Function Hall', 'Buffet prasmanan spesial 100 porsi', 'Standar sound system prosesi & microphone nirkabel', '1 Kamar Deluxe persiapan rias pengantin 1 malam']],
                ],
                'rooms' => [
                    ['floor' => 'LANTAI 2', 'style' => 'Pillarless', 'name' => 'Emerald Grand Ballroom', 'desc' => 'Ballroom termegah berkapasitas besar dengan foyer pre-function terluas.', 'specs' => [['Standing Cocktail', '1.200 Tamu'], ['Round Table Banquet', '600 Tamu (60 Meja)'], ['Luas Ruangan', '1.050 m²'], ['Tinggi Plafon', '8,0 Meter']]],
                    ['floor' => 'LANTAI 3', 'style' => 'Modern Classic', 'name' => 'Sapphire Ballroom', 'desc' => 'Pilihan ideal untuk perayaan semi-akbar berkonsep hangat dan elegan.', 'specs' => [['Standing Cocktail', '400 Tamu'], ['Round Table Banquet', '200 Tamu (20 Meja)'], ['Luas Ruangan', '420 m²'], ['Tinggi Plafon', '5,0 Meter']]],
                    ['floor' => 'LANTAI 5', 'style' => 'Intimate Hall', 'name' => 'Diamond Function Hall', 'desc' => 'Ruang privat berdaya tampung pas untuk acara adat lamaran atau akad.', 'specs' => [['Standing Cocktail', '150 Tamu'], ['Round Table Banquet', '80 Tamu (8 Meja)'], ['Luas Ruangan', '180 m²'], ['Tinggi Plafon', '4,0 Meter']]],
                ],
                'facilities' => [
                    ['bi-snow', 'AC Central 24 Jam', 'Suhu terkontrol sejuk'],
                    ['bi-person-heart', 'Bridal Room VIP', 'Ruang ganti & makeup rias'],
                    ['bi-lightning-charge', 'Listrik s.d 20.000W', 'Cadangan genset 100%'],
                    ['bi-car-front', 'Valet Parking', 'Area drop-off luas'],
                    ['bi-wifi', 'WiFi Dedicated', 'High-speed live streaming'],
                    ['bi-person-wheelchair', 'Akses Difabel', 'Ramp ramah kursi roda'],
                    ['bi-moon-stars', 'Musholla Bersih', 'Tempat wudhu terpisah'],
                    ['bi-bell', 'Dedicated Butler', 'Pelayan pribadi pengantin'],
                ],
                'reviews_list' => [
                    ['name' => 'Kevin Sanjaya & Amanda Wijaya', 'meta' => 'Pernikahan 18 November 2025 • Emerald Grand Ballroom (850 Tamu)', 'text' => 'Pernikahan kami di Mövenpick benar-benar di luar ekspektasi! Makanannya luar biasa lezat, hampir semua keluarga dan tamu memuji kualitas roasted beef, salmon en croute, dan live station chocolate fountainnya. Tim banquet hotel dan wedding specialist sangat kooperatif, tanggap dari hari pertama dealing sampai gladi bersih dan hari-H berjalan sempurna.'],
                    ['name' => 'Dimas Pratama & Stephanie Salim', 'meta' => 'Pernikahan 14 Januari 2026 • Sapphire Ballroom (350 Tamu)', 'text' => 'Lokasi di pusat kota sangat strategis untuk keluarga besar yang datang dari luar kota. Bridal suite-nya sangat luas dan mewah. Terima kasih Mövenpick telah mewujudkan pernikahan impian kami dengan standar bintang lima yang sesungguhnya!'],
                ],
                'faq' => [
                    ['q' => 'Apakah diperbolehkan membawa vendor dekorasi atau fotografer dari luar hotel?', 'a' => 'Diperbolehkan untuk vendor dekorasi, MUA, dan dokumentasi dari luar dengan syarat mengikuti aturan loading & setup hotel serta melakukan koordinasi teknis H-14.'],
                    ['q' => 'Bagaimana mekanisme pembayaran dengan cicilan 0% MyDream Pay?', 'a' => 'Cicilan 0% tersedia hingga 24 bulan untuk kartu kredit bank rekanan. Dana Anda dilindungi escrow MyDream Pay hingga acara terlaksana.'],
                    ['q' => 'Apakah diperkenankan membawa katering tradisional khusus dari luar?', 'a' => 'Katering utama disediakan hotel. Untuk menu tradisional khusus, tersedia opsi live station kolaborasi dengan biaya tambahan (corkage) sesuai kesepakatan.'],
                    ['q' => 'Bagaimana kebijakan bila terjadi perubahan tanggal (reschedule) pernikahan?', 'a' => 'Reschedule dapat dilakukan maksimal 1 kali, diajukan minimal H-60, dan mengikuti ketersediaan ballroom pada tanggal pengganti.'],
                ],
                'capacity_options' => ['200 - 500 Tamu (Sapphire Ballroom)', '500 - 800 Tamu (Emerald Ballroom)', '800 - 1.200 Tamu (Emerald Ballroom Full)', '50 - 100 Tamu (Diamond Hall)'],
                'map_query' => 'Jember City Centre',
                'specialist' => ['MP', 'Wedding Specialist Mövenpick'],
            ],

            /* ---------- VENDOR LAIN ---------- */
            'rumah-kayu-garden-venue' => $simple([
                'slug' => 'rumah-kayu-garden-venue', 'name' => 'Rumah Kayu Garden Venue',
                'category' => 'venue', 'category_label' => 'Garden & Outdoor Venue',
                'tagline' => 'Taman terbuka untuk 100 – 400 tamu', 'image' => self::img(2),
                'address' => 'Jl. Kalimantan, Tegalboto, Jember', 'city' => 'Jember',
                'rating' => 4.8, 'reviews' => 86, 'price_from' => 35_000_000, 'price_to' => 120_000_000,
                'about' => ['Venue taman dengan pendopo kayu jati dan halaman rumput luas, cocok untuk akad outdoor, resepsi garden party, maupun pernikahan adat.'],
                'packages' => [
                    ['tag' => 'GARDEN • 150 TAMU', 'name' => 'Garden Party Package', 'desc' => 'Sewa venue taman 6 jam, listrik, dan ruang rias.', 'price' => 35_000_000, 'pax' => '150', 'popular' => true, 'benefits' => ['Pemakaian taman & pendopo 6 jam', 'Ruang rias pengantin ber-AC', 'Listrik 10.000 Watt', 'Petugas kebersihan & keamanan']],
                    ['tag' => 'GARDEN • 400 TAMU', 'name' => 'Grand Garden Package', 'desc' => 'Seluruh area taman untuk resepsi besar.', 'price' => 120_000_000, 'pax' => '400', 'popular' => false, 'benefits' => ['Pemakaian seluruh area 8 jam', '2 ruang rias & 1 ruang keluarga', 'Listrik 20.000 Watt', 'Parkir luas 200 mobil']],
                ],
                'map_query' => 'Tegalboto Jember',
            ]),
            'marlene-harriman-makeup' => $simple([
                'slug' => 'marlene-harriman-makeup', 'name' => 'Marlene Harriman Makeup',
                'category' => 'makeup', 'category_label' => 'Makeup & Hair',
                'tagline' => 'Akad & Resepsi • Makeup Ibu & Besan', 'image' => self::img(3),
                'address' => 'Pondok Indah, Jakarta (melayani Jember)', 'city' => 'Jakarta Selatan',
                'rating' => 5.0, 'reviews' => 426, 'price_from' => 18_500_000, 'price_to' => 45_000_000,
                'card_badges' => ['CELEBRITY MUA', 'BRIDESTORY'],
                'about' => ['MUA pengantin dengan spesialisasi tampilan natural glam untuk akad dan resepsi, termasuk makeup ibu, besan, dan bridesmaid.'],
                'packages' => [
                    ['tag' => 'AKAD & RESEPSI', 'name' => 'Full Day Bridal Makeup', 'desc' => 'Makeup & hairdo akad dan resepsi, termasuk touch up.', 'price' => 18_500_000, 'pax' => '1', 'popular' => true, 'benefits' => ['Makeup & hairdo pengantin wanita', 'Touch up on site', 'Makeup ibu pengantin (2 orang)', 'Trial makeup 1 kali']],
                    ['tag' => 'AKAD', 'name' => 'Akad Natural Package', 'desc' => 'Tampilan natural untuk akad nikah.', 'price' => 9_500_000, 'pax' => '1', 'popular' => false, 'benefits' => ['Makeup & hairdo pengantin wanita', 'Makeup ibu pengantin (1 orang)']],
                ],
                'map_query' => 'Pondok Indah Jakarta',
            ]),
            'ayu-bridal-hijab-mua' => $simple([
                'slug' => 'ayu-bridal-hijab-mua', 'name' => 'Ayu Bridal Hijab & MUA',
                'category' => 'makeup', 'category_label' => 'Makeup & Hair',
                'tagline' => 'Hijab makeup & busana adat Jawa', 'image' => self::img(4),
                'address' => 'Kaliwates, Jember', 'city' => 'Jember',
                'rating' => 4.9, 'reviews' => 173, 'price_from' => 6_500_000, 'price_to' => 22_000_000,
                'about' => ['Spesialis rias pengantin hijab dan adat Jawa dengan busana lengkap untuk kedua mempelai.'],
                'packages' => [
                    ['tag' => 'HIJAB & ADAT', 'name' => 'Paes Ageng & Hijab Package', 'desc' => 'Rias adat lengkap dengan busana.', 'price' => 12_000_000, 'pax' => '1', 'popular' => true, 'benefits' => ['Rias pengantin paes ageng / hijab', 'Busana pengantin pria & wanita', 'Rias 2 ibu pengantin']],
                    ['tag' => 'AKAD', 'name' => 'Akad Hijab Simple', 'desc' => 'Rias akad dengan tampilan sederhana elegan.', 'price' => 6_500_000, 'pax' => '1', 'popular' => false, 'benefits' => ['Rias pengantin hijab', 'Busana akad']],
                ],
                'map_query' => 'Kaliwates Jember',
            ]),
            'floral-atelier-jember' => $simple([
                'slug' => 'floral-atelier-jember', 'name' => 'Floral Atelier Jember',
                'category' => 'dekor', 'category_label' => 'Dekorasi & Lighting',
                'tagline' => 'Dekorasi bunga segar & pelaminan', 'image' => self::img(5),
                'address' => 'Patrang, Jember', 'city' => 'Jember',
                'rating' => 4.9, 'reviews' => 142, 'price_from' => 18_000_000, 'price_to' => 95_000_000,
                'about' => ['Dekorasi pelaminan bunga segar bergaya romantic botanical, modern minimalis, hingga adat Jawa.'],
                'packages' => [
                    ['tag' => 'PELAMINAN', 'name' => 'Romantic Botanical Package', 'desc' => 'Pelaminan bunga segar 8 meter dengan aisle.', 'price' => 32_000_000, 'pax' => '300', 'popular' => true, 'benefits' => ['Pelaminan 8 meter bunga segar', 'Aisle & gate entrance', 'Meja penerima tamu 2 set', 'Fairy lights']],
                    ['tag' => 'AKAD', 'name' => 'Akad Intimate Decoration', 'desc' => 'Dekorasi meja akad dan backdrop.', 'price' => 18_000_000, 'pax' => '100', 'popular' => false, 'benefits' => ['Backdrop akad', 'Meja akad & kursi', 'Bunga meja']],
                ],
                'map_query' => 'Patrang Jember',
            ]),
            'lumiere-lighting-decor' => $simple([
                'slug' => 'lumiere-lighting-decor', 'name' => 'Lumière Lighting & Decor',
                'category' => 'dekor', 'category_label' => 'Dekorasi & Lighting',
                'tagline' => 'Ambiance lighting & panggung', 'image' => self::img(6),
                'address' => 'Sumbersari, Jember', 'city' => 'Jember',
                'rating' => 4.7, 'reviews' => 64, 'price_from' => 12_000_000, 'price_to' => 60_000_000,
                'about' => ['Vendor lighting dan dekorasi panggung untuk suasana resepsi malam yang hangat dan elegan.'],
                'packages' => [
                    ['tag' => 'LIGHTING', 'name' => 'Warm Ambiance Lighting', 'desc' => 'Tata cahaya venue resepsi malam.', 'price' => 12_000_000, 'pax' => '300', 'popular' => true, 'benefits' => ['Fairy light ceiling', 'Uplighting dinding', 'Spotlight pelaminan']],
                    ['tag' => 'PANGGUNG', 'name' => 'Stage & Truss Package', 'desc' => 'Panggung dan truss dengan LED backdrop.', 'price' => 28_000_000, 'pax' => '500', 'popular' => false, 'benefits' => ['Panggung 6×4 m', 'Truss & LED backdrop', 'Operator lighting']],
                ],
                'map_query' => 'Sumbersari Jember',
            ]),
            'sinar-lens-studio' => $simple([
                'slug' => 'sinar-lens-studio', 'name' => 'Sinar Lens Studio',
                'category' => 'foto', 'category_label' => 'Fotografi & Videografi',
                'tagline' => 'Foto & cinematic video pernikahan', 'image' => self::img(1),
                'address' => 'Kaliwates, Jember', 'city' => 'Jember',
                'rating' => 4.9, 'reviews' => 201, 'price_from' => 8_500_000, 'price_to' => 30_000_000,
                'about' => ['Studio dokumentasi pernikahan dengan gaya candid dan cinematic, lengkap dengan album dan highlight film 4K.'],
                'packages' => [
                    ['tag' => 'FOTO & VIDEO', 'name' => 'Cinematic Full Coverage', 'desc' => 'Foto & video D-Day 8 jam.', 'price' => 18_000_000, 'pax' => '1', 'popular' => true, 'benefits' => ['2 fotografer + 1 videografer', 'Album kolase 20×30 cm', 'Highlight film 4K', 'Semua file asli']],
                    ['tag' => 'FOTO', 'name' => 'Photo Only Package', 'desc' => 'Dokumentasi foto akad atau resepsi.', 'price' => 8_500_000, 'pax' => '1', 'popular' => false, 'benefits' => ['1 fotografer 6 jam', 'Semua file edit', 'Album 20 halaman']],
                ],
                'map_query' => 'Kaliwates Jember',
            ]),
            'dapur-nusantara' => $simple([
                'slug' => 'dapur-nusantara', 'name' => 'Dapur Nusantara Catering',
                'category' => 'katering', 'category_label' => 'Katering & Kue',
                'tagline' => 'Katering prasmanan & gubukan', 'image' => self::img(2),
                'address' => 'Jember Kota', 'city' => 'Jember',
                'rating' => 4.8, 'reviews' => 158, 'price_from' => 65_000, 'price_to' => 145_000,
                'about' => ['Katering pernikahan dengan menu Nusantara dan internasional, tersedia prasmanan, gubukan, dan live station.'],
                'packages' => [
                    ['tag' => 'PRASMANAN', 'name' => 'Prasmanan Premium (per pax)', 'desc' => 'Buffet 5 menu utama dengan dessert.', 'price' => 140_000, 'pax' => '100', 'popular' => true, 'benefits' => ['5 menu utama', 'Dessert & buah', 'Minuman free flow', 'Petugas & perlengkapan']],
                    ['tag' => 'GUBUKAN', 'name' => 'Gubukan Nusantara (per pax)', 'desc' => 'Stall makanan tradisional.', 'price' => 95_000, 'pax' => '100', 'popular' => false, 'benefits' => ['4 stall gubukan', 'Wedang & jajan pasar']],
                ],
                'map_query' => 'Jember',
            ]),
        ];
    }
}
