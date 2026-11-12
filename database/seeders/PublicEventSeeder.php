<?php

namespace Database\Seeders;

use App\Models\PublicEvent;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

/**
 * Event contoh. Aman dijalankan berulang (updateOrCreate berdasarkan slug), tidak menghapus apa pun.
 *   php artisan db:seed --class=PublicEventSeeder
 */
class PublicEventSeeder extends Seeder
{
    public function run(): void
    {
        $img = fn (string $id) => "https://images.unsplash.com/{$id}?w=900";

        $events = [
            [
                'slug' => 'wedding-expo-jember',
                'title' => 'MyDream Wedding Expo',
                'description' => 'Pameran tahunan MyDream yang menghadirkan vendor rekanan dalam satu tempat: konsultasi paket, penawaran khusus, dan tanya jawab langsung dengan wedding planner.',
                'photo' => $img('photo-1519741497674-611481863552'),
                'starts_on' => now()->addDays(30)->toDateString(),
                'ends_on' => now()->addDays(31)->toDateString(),
                'start_time' => '10:00:00',
                'end_time' => '20:00:00',
                'location' => 'Jember',
                'vendors' => ['samudra-atelier-decor', 'marlene-hariman-makeup', 'umara-catering'],
            ],
            [
                'slug' => 'open-house-grand-ballroom',
                'title' => 'Open House Grand Ballroom',
                'description' => 'Kunjungi venue bersama tim MyDream, lengkap dengan simulasi dekorasi dan tasting menu katering.',
                'photo' => $img('photo-1522673607200-164d1b6ce486'),
                'starts_on' => now()->addDays(12)->toDateString(),
                'ends_on' => null,
                'start_time' => '13:00:00',
                'end_time' => '17:00:00',
                'location' => 'Surabaya',
                'vendors' => ['grand-ballroom-mulia', 'stupa-caspea-decor'],
            ],
            [
                'slug' => 'konsultasi-gratis-vendor',
                'title' => 'Konsultasi Gratis Bersama Vendor',
                'description' => 'Sesi konsultasi gratis dengan vendor rekanan, cocok untuk pasangan yang baru mulai merencanakan pernikahan.',
                'photo' => $img('photo-1465495976277-4387d4b0b4c6'),
                'starts_on' => now()->subDay()->toDateString(),
                'ends_on' => now()->addDay()->toDateString(),
                'start_time' => '09:00:00',
                'end_time' => '15:00:00',
                'location' => 'Kantor MyDream, Jember',
                'vendors' => ['atelier-han-tjen'],
            ],
            [
                'slug' => 'workshop-merangkai-bunga',
                'title' => 'Workshop Merangkai Bunga Pernikahan',
                'description' => 'Belajar dasar merangkai buket dan dekorasi bunga segar bersama florist rekanan MyDream.',
                'photo' => $img('photo-1519225421980-715cb0215aed'),
                'starts_on' => now()->subDays(45)->toDateString(),
                'ends_on' => null,
                'start_time' => '10:00:00',
                'end_time' => '12:00:00',
                'location' => 'Jember',
                'vendors' => ['samudra-atelier-decor'],
            ],
        ];

        foreach ($events as $data) {
            $vendorSlugs = $data['vendors'];
            unset($data['vendors']);

            $event = PublicEvent::updateOrCreate(['slug' => $data['slug']], $data + ['is_published' => true]);
            $event->vendors()->syncWithoutDetaching(Vendor::whereIn('slug', $vendorSlugs)->pluck('id')->all());
        }
    }
}
