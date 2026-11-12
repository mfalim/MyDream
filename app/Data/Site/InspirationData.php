<?php

namespace App\Data\Site;

/** Data statis sementara untuk galeri inspirasi (belum ada tabelnya). */
class InspirationData
{
    public static function all(): array
    {
        $img = fn (string $id, int $w = 900) => "https://images.unsplash.com/{$id}?w={$w}";
        $g = fn (array $ids) => array_map(fn ($id) => $img($id, 600), $ids);

        return [
            [
                'slug' => 'erwin-anggita-adat-jawa-solo',
                'title' => 'Adat Jawa Solo yang Khidmat',
                'couple' => 'Erwin & Anggita',
                'city' => 'Jember',
                'theme' => 'Adat Jawa Solo',
                'concept' => 'Prosesi adat Jawa Solo lengkap: siraman, midodareni, hingga akad.',
                'description' => 'Sentuhan klasik adat Jawa dengan nuansa khidmat dan elegan di Gedung Soetrisno Hadi. Setiap detail prosesi dijaga agar tetap sakral namun hangat bagi keluarga besar.',
                'cover_image' => $img('photo-1519225421980-715cb0215aed'),
                'gallery' => $g(['photo-1519741497674-611481863552', 'photo-1465495976277-4387d4b0b4c6', 'photo-1522673607200-164d1b6ce486']),
                'package_slug' => 'royal-garden-wedding',
            ],
            [
                'slug' => 'feby-yudo-outdoor-modern',
                'title' => 'Akad Outdoor di Tengah Hutan Pinus',
                'couple' => 'Feby & Yudo',
                'city' => 'Jember',
                'theme' => 'Outdoor Modern',
                'concept' => 'Akad intim di kebun dengan dekorasi bunga segar putih dan sage.',
                'description' => 'Suasana sejuk pegunungan membuat acara terasa personal. Palet putih dan sage menyatu dengan hijau alam sekitar.',
                'cover_image' => $img('photo-1465495976277-4387d4b0b4c6'),
                'gallery' => $g(['photo-1519225421980-715cb0215aed', 'photo-1522673607200-164d1b6ce486', 'photo-1594552072238-b8a33785b261']),
                'package_slug' => 'akad-minimalis-champagne',
            ],
            [
                'slug' => 'vio-tika-ballroom-reception',
                'title' => 'Resepsi Ballroom Bernuansa Glamour',
                'couple' => 'Vio & Tika',
                'city' => 'Surabaya',
                'theme' => 'Ballroom Reception',
                'concept' => 'Ballroom megah dengan lampu gantung kristal dan panggung bertingkat.',
                'description' => 'Resepsi modern bernuansa pencahayaan hangat. Lampu gantung kristal membuat setiap sudut ruangan terasa mewah.',
                'cover_image' => $img('photo-1522673607200-164d1b6ce486'),
                'gallery' => $g(['photo-1555244162-803834f70033', 'photo-1519741497674-611481863552', 'photo-1465495976277-4387d4b0b4c6']),
                'package_slug' => 'grand-ballroom-surabaya',
            ],
            [
                'slug' => 'rangga-stefanie-minimalist-garden',
                'title' => 'Garden Wedding Minimalis',
                'couple' => 'Rangga & Stefanie',
                'city' => 'Surabaya',
                'theme' => 'Minimalist Garden',
                'concept' => 'Taman minimalis berpalet champagne ivory dengan aksen daun eucalyptus.',
                'description' => 'Pelaminan taman yang tenang namun tetap elegan, cocok untuk pasangan yang menyukai kesederhanaan.',
                'cover_image' => $img('photo-1594552072238-b8a33785b261'),
                'gallery' => $g(['photo-1519225421980-715cb0215aed', 'photo-1465495976277-4387d4b0b4c6']),
                'package_slug' => 'busana-dan-dokumentasi-surabaya',
            ],
            [
                'slug' => 'dimas-winona-rustic-terracotta',
                'title' => 'Dekorasi Hangat Warm Terracotta',
                'couple' => 'Dimas & Winona',
                'city' => 'Jember',
                'theme' => 'Rustic Terracotta',
                'concept' => 'Bunga kering, mawar peach, dan lampu gantung temaram di kebun terbuka.',
                'description' => 'Harmoni warna terracotta dan peach menghadirkan suasana intim yang hangat untuk resepsi sore hingga malam.',
                'cover_image' => $img('photo-1519741497674-611481863552'),
                'gallery' => $g(['photo-1522673607200-164d1b6ce486', 'photo-1555244162-803834f70033']),
                'package_slug' => null,
            ],
            [
                'slug' => 'kevin-amanda-intimate-dinner',
                'title' => 'Resepsi Makan Malam Intim',
                'couple' => 'Kevin & Amanda',
                'city' => 'Surabaya',
                'theme' => 'Intimate Dinner',
                'concept' => 'Makan malam keluarga dengan cahaya lilin dan meja panjang.',
                'description' => 'Resepsi romantis bernuansa fairy light, untuk pasangan yang mengutamakan momen dekat bersama keluarga.',
                'cover_image' => $img('photo-1555244162-803834f70033'),
                'gallery' => $g(['photo-1519225421980-715cb0215aed', 'photo-1594552072238-b8a33785b261']),
                'package_slug' => 'akad-minimalis-champagne',
            ],
        ];
    }

    public static function themes(): array
    {
        return array_values(array_unique(array_column(self::all(), 'theme')));
    }

    public static function find(string $slug): ?array
    {
        foreach (self::all() as $item) {
            if ($item['slug'] === $slug) {
                return $item;
            }
        }

        return null;
    }
}
