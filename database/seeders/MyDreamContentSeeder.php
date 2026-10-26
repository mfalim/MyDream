<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Package;
use App\Models\Vendor;
use App\Models\VendorPhoto;
use Illuminate\Database\Seeder;

/**
 * Data contoh untuk halaman publik. Aman dijalankan berulang (updateOrCreate berdasarkan slug)
 * dan tidak menghapus apa pun. Jalankan:
 *   php artisan db:seed --class=MyDreamContentSeeder
 */
class MyDreamContentSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [];
        foreach ([
            'Venue & Ballroom', 'Fotografi & Videografi', 'Gaun & Busana Pengantin', 'Makeup & Hair',
            'Dekorasi & Lighting', 'Katering & Kue', 'Wedding Organizer', 'Perhiasan & Cincin',
        ] as $name) {
            $categories[$name] = Category::firstOrCreate(['name' => $name])->id;
        }

        $img = fn (string $id) => "https://images.unsplash.com/{$id}?w=900";

        $vendors = [
            ['samudra-atelier-decor', 'Samudra Atelier Decor', 'Dekorasi & Lighting', 'Jember', 'Jl. Slamet Riyadi No. 42, Patrang, Jember', '081233450000', 18500000, '@samudra.atelier',
                'Spesialis dekorasi pelaminan adat Jawa-Madura, modern minimalis, dan floral kontemporer.', ['photo-1519741497674-611481863552', 'photo-1465495976277-4387d4b0b4c6']],
            ['marlene-hariman-makeup', 'Marlene Hariman Makeup', 'Makeup & Hair', 'Jember', 'Jl. Kalimantan, Tegalboto, Jember', '081233450001', 4500000, '@marlenehariman.mua',
                'Rias pengantin akad dan resepsi dengan tampilan natural glam, termasuk makeup ibu pengantin.', ['photo-1487412947147-5cebf100ffc2']],
            ['axioo-photography', 'Axioo Photography', 'Fotografi & Videografi', 'Surabaya', 'Jl. Raya Darmo, Surabaya', '081233450002', 9500000, '@axioophotography',
                'Dokumentasi pre-wedding dan wedding day bergaya candid dan cinematic.', ['photo-1516035069371-29a1b244cc32']],
            ['stupa-caspea-decor', 'Stupa Caspea Decor', 'Dekorasi & Lighting', 'Surabaya', 'Jl. Mayjen Sungkono, Surabaya', '081233450003', 45000000, '@stupacaspeadecor',
                'Dekorasi grand ballroom, instalasi floral botanikal, dan ambience lighting.', ['photo-1522673607200-164d1b6ce486']],
            ['atelier-han-tjen', 'Atelier Han Tjen', 'Gaun & Busana Pengantin', 'Surabaya', 'Jl. Basuki Rahmat, Surabaya', '081233450004', 25000000, '@atelierhantjen',
                'Busana pengantin custom lace dengan fitting pribadi.', ['photo-1594552072238-b8a33785b261']],
            ['umara-catering', 'Umara Catering', 'Katering & Kue', 'Jember', 'Jl. Gajah Mada, Jember', '081233450005', 65000000, '@umaracatering',
                'Katering prasmanan dan gubukan menu Nusantara untuk resepsi hingga 500 tamu.', ['photo-1555244162-803834f70033']],
            ['grand-ballroom-mulia', 'Grand Ballroom Mulia', 'Venue & Ballroom', 'Surabaya', 'Jl. Embong Malang, Surabaya', '081233450006', 85000000, null,
                'Ballroom pilar-less untuk resepsi hingga 600 tamu dengan area VIP.', ['photo-1519225421980-715cb0215aed']],
            ['gedung-soetrisno-hadi', 'Gedung Soetrisno Hadi', 'Venue & Ballroom', 'Jember', 'Jl. Sultan Agung, Jember', '081233450007', 30000000, null,
                'Gedung serbaguna dengan halaman luas, cocok untuk akad dan resepsi adat.', ['photo-1519741497674-611481863552']],
        ];

        $vendorIds = [];
        foreach ($vendors as [$slug, $name, $category, $city, $address, $phone, $price, $instagram, $description, $photos]) {
            $vendor = Vendor::updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'category_id' => $categories[$category],
                'city' => $city,
                'address' => $address,
                'phone' => $phone,
                'price' => $price,
                'instagram' => $instagram,
                'description' => $description,
            ]);
            $vendorIds[$slug] = $vendor->id;

            foreach ($photos as $i => $photoId) {
                VendorPhoto::updateOrCreate(
                    ['vendor_id' => $vendor->id, 'photo' => $img($photoId)],
                    ['is_cover' => $i === 0, 'sort_order' => $i]
                );
            }
        }

        $packages = [
            ['royal-garden-wedding', 'Royal Garden Wedding', 'Resepsi taman dengan venue, dekorasi, rias, dan katering lengkap untuk keluarga besar.', 300, 8, true,
                'photo-1519741497674-611481863552', ['gedung-soetrisno-hadi', 'samudra-atelier-decor', 'marlene-hariman-makeup', 'umara-catering']],
            ['akad-minimalis-champagne', 'Akad Minimalis Champagne', 'Akad intim dengan dekorasi backdrop champagne dan rias pengantin natural.', 80, 4, true,
                'photo-1465495976277-4387d4b0b4c6', ['samudra-atelier-decor', 'marlene-hariman-makeup']],
            ['grand-ballroom-surabaya', 'Grand Ballroom Surabaya', 'Resepsi ballroom megah: venue, dekorasi, busana pengantin, dan dokumentasi.', 500, 6, true,
                'photo-1522673607200-164d1b6ce486', ['grand-ballroom-mulia', 'stupa-caspea-decor', 'axioo-photography', 'atelier-han-tjen']],
            ['busana-dan-dokumentasi-surabaya', 'Busana & Dokumentasi Surabaya', 'Paket busana pengantin custom dan dokumentasi foto-video.', null, 8, false,
                'photo-1594552072238-b8a33785b261', ['atelier-han-tjen', 'axioo-photography']],
        ];

        foreach ($packages as [$slug, $name, $description, $capacity, $duration, $featured, $photoId, $members]) {
            $package = Package::updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'description' => $description,
                'guest_capacity' => $capacity,
                'duration' => $duration,
                'event_period' => 'Sepanjang tahun',
                'availability_date' => now()->addMonths(12)->toDateString(),
                'photo' => $img($photoId),
                'is_featured' => $featured,
            ]);

            $package->vendors()->syncWithoutDetaching(
                collect($members)->mapWithKeys(fn ($s) => [$vendorIds[$s] => ['status' => 'active']])->all()
            );
        }
    }
}
