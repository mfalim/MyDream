<?php

namespace App\Data\Site;

/**
 * Data statis sementara. Belum ada tabel artikel di database; saat tabelnya dibuat,
 * ganti isi class ini dengan query Eloquent (bentuk array dipertahankan).
 */
class ArticleData
{
    public static function all(): array
    {
        $img = fn (string $id) => "https://images.unsplash.com/{$id}?w=800";

        return [
            [
                'slug' => 'pernikahan-erwin-anggita',
                'title' => 'Pernikahan Erwin & Anggita',
                'excerpt' => 'Sentuhan klasik adat Jawa dengan nuansa khidmat dan elegan di Gedung Soetrisno Hadi, Jember.',
                'category' => 'Real Wedding',
                'cover_image' => $img('photo-1519225421980-715cb0215aed'),
                'related_package_slug' => 'royal-garden-wedding',
            ],
            [
                'slug' => 'pernikahan-feby-yudo',
                'title' => 'Pernikahan Feby & Yudo',
                'excerpt' => 'Akad intim bernuansa bunga segar putih dan sage di Pine Forest Glamping, Rembangan.',
                'category' => 'Real Wedding',
                'cover_image' => $img('photo-1465495976277-4387d4b0b4c6'),
                'related_package_slug' => 'akad-minimalis-champagne',
            ],
            [
                'slug' => 'pernikahan-vio-tika',
                'title' => 'Pernikahan Vio & Tika',
                'excerpt' => 'Resepsi modern bernuansa pencahayaan glamour dan hangat di ballroom hotel.',
                'category' => 'Real Wedding',
                'cover_image' => $img('photo-1522673607200-164d1b6ce486'),
                'related_package_slug' => 'grand-ballroom-surabaya',
            ],
        ];
    }

    public static function latest(int $count = 3): array
    {
        return array_slice(self::all(), 0, $count);
    }
}
