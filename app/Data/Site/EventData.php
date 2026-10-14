<?php

namespace App\Data\Site;

use Carbon\Carbon;

/**
 * Event publik MyDream (pameran, open house, workshop). Berbeda dari tabel `events` di database
 * yang dipakai untuk acara pernikahan klien. Data statis sementara; tanggal dibuat relatif terhadap
 * hari ini supaya status (akan datang / berlangsung / selesai) selalu terhitung otomatis.
 */
class EventData
{
    public static function all(): array
    {
        $img = fn (string $id) => "https://images.unsplash.com/{$id}?w=900";
        $today = Carbon::today();

        $events = [
            [
                'slug' => 'wedding-expo-jember',
                'name' => 'MyDream Wedding Expo',
                'description' => 'Pameran tahunan MyDream yang menghadirkan vendor rekanan dalam satu tempat: konsultasi paket, penawaran khusus, dan tanya jawab langsung dengan wedding planner.',
                'image' => $img('photo-1519741497674-611481863552'),
                'starts_on' => $today->copy()->addDays(30),
                'ends_on' => $today->copy()->addDays(31),
                'time' => '10.00 - 20.00 WIB',
                'location' => 'Jember',
                'vendor_slugs' => ['samudra-atelier-decor', 'marlene-hariman-makeup', 'umara-catering'],
            ],
            [
                'slug' => 'open-house-grand-ballroom',
                'name' => 'Open House Grand Ballroom',
                'description' => 'Kunjungi venue bersama tim MyDream, lengkap dengan simulasi dekorasi dan tasting menu katering.',
                'image' => $img('photo-1522673607200-164d1b6ce486'),
                'starts_on' => $today->copy()->addDays(12),
                'ends_on' => $today->copy()->addDays(12),
                'time' => '13.00 - 17.00 WIB',
                'location' => 'Surabaya',
                'vendor_slugs' => ['grand-ballroom-mulia', 'stupa-caspea-decor'],
            ],
            [
                'slug' => 'konsultasi-gratis-vendor',
                'name' => 'Konsultasi Gratis Bersama Vendor',
                'description' => 'Sesi konsultasi gratis dengan vendor rekanan, cocok untuk pasangan yang baru mulai merencanakan pernikahan.',
                'image' => $img('photo-1465495976277-4387d4b0b4c6'),
                'starts_on' => $today->copy()->subDay(),
                'ends_on' => $today->copy()->addDay(),
                'time' => '09.00 - 15.00 WIB',
                'location' => 'Kantor MyDream, Jember',
                'vendor_slugs' => ['atelier-han-tjen'],
            ],
            [
                'slug' => 'workshop-merangkai-bunga',
                'name' => 'Workshop Merangkai Bunga Pernikahan',
                'description' => 'Belajar dasar merangkai buket dan dekorasi bunga segar bersama florist rekanan MyDream.',
                'image' => $img('photo-1519225421980-715cb0215aed'),
                'starts_on' => $today->copy()->subDays(45),
                'ends_on' => $today->copy()->subDays(45),
                'time' => '10.00 - 12.00 WIB',
                'location' => 'Jember',
                'vendor_slugs' => ['samudra-atelier-decor'],
            ],
        ];

        return array_map(function (array $event) use ($today) {
            $event['status'] = match (true) {
                $event['ends_on']->lt($today) => 'finished',
                $event['starts_on']->gt($today) => 'upcoming',
                default => 'ongoing',
            };

            return $event;
        }, $events);
    }

    public static function find(string $slug): ?array
    {
        foreach (self::all() as $event) {
            if ($event['slug'] === $slug) {
                return $event;
            }
        }

        return null;
    }
}
