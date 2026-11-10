<?php

namespace Database\Seeders;

use App\Models\Inspiration;
use App\Models\InspirationPhoto;
use App\Models\Package;
use Illuminate\Database\Seeder;

/**
 * Contoh cerita yang sudah disetujui (tanpa pesanan asli). Aman dijalankan berulang.
 *   php artisan db:seed --class=InspirationSeeder
 */
class InspirationSeeder extends Seeder
{
    public function run(): void
    {
        $img = fn (string $id) => "https://images.unsplash.com/{$id}?w=900";

        $items = [
            ['erwin-anggita-adat-jawa-solo', 'Adat Jawa Solo yang Khidmat', 'Adat Jawa Solo', 'Erwin & Anggita', 'Jember', 'Gedung Soetrisno Hadi', 400, 'royal-garden-wedding',
                'Sentuhan klasik adat Jawa dengan nuansa khidmat dan elegan. Setiap detail prosesi dijaga agar tetap sakral namun hangat bagi keluarga besar.',
                ['photo-1519225421980-715cb0215aed', 'photo-1519741497674-611481863552', 'photo-1465495976277-4387d4b0b4c6']],
            ['feby-yudo-outdoor-modern', 'Akad Outdoor di Tengah Hutan Pinus', 'Outdoor Modern', 'Feby & Yudo', 'Jember', 'Pine Forest Glamping', 80, 'akad-minimalis-champagne',
                'Suasana sejuk pegunungan membuat acara terasa personal. Palet putih dan sage menyatu dengan hijau alam sekitar.',
                ['photo-1465495976277-4387d4b0b4c6', 'photo-1519225421980-715cb0215aed', 'photo-1522673607200-164d1b6ce486']],
            ['vio-tika-ballroom-reception', 'Resepsi Ballroom Bernuansa Glamour', 'Ballroom Reception', 'Vio & Tika', 'Surabaya', 'Grand Ballroom', 400, 'grand-ballroom-surabaya',
                'Resepsi modern bernuansa pencahayaan hangat. Lampu gantung kristal membuat setiap sudut ruangan terasa mewah.',
                ['photo-1522673607200-164d1b6ce486', 'photo-1555244162-803834f70033', 'photo-1519741497674-611481863552']],
        ];

        foreach ($items as [$slug, $title, $theme, $couple, $city, $venue, $guests, $packageSlug, $story, $photos]) {
            $item = Inspiration::updateOrCreate(['slug' => $slug], [
                'title' => $title,
                'theme' => $theme,
                'couple_names' => $couple,
                'show_couple_names' => true,
                'city' => $city,
                'venue_name' => $venue,
                'event_date' => now()->subMonths(5)->toDateString(),
                'guest_count' => $guests,
                'package_id' => Package::where('slug', $packageSlug)->value('id'),
                'story' => $story,
                'testimonial' => 'Tim MyDream membuat semuanya berjalan tenang dari awal sampai hari H.',
                'rating' => 5,
                'status' => 'approved',
                'submitted_at' => now()->subMonths(4),
                'reviewed_at' => now()->subMonths(4),
            ]);

            foreach ($photos as $i => $photoId) {
                InspirationPhoto::updateOrCreate(
                    ['inspiration_id' => $item->id, 'photo' => $img($photoId)],
                    ['sort_order' => $i]
                );
            }
        }
    }
}
