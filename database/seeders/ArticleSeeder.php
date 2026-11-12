<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Package;
use Illuminate\Database\Seeder;

/**
 * Artikel contoh. Aman dijalankan berulang (updateOrCreate berdasarkan slug), tidak menghapus apa pun.
 *   php artisan db:seed --class=ArticleSeeder
 */
class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $img = fn (string $id) => "https://images.unsplash.com/{$id}?w=900";

        $articles = [
            [
                'slug' => 'pernikahan-erwin-anggita',
                'title' => 'Pernikahan Erwin & Anggita',
                'category' => 'Real Wedding',
                'excerpt' => 'Sentuhan klasik adat Jawa dengan nuansa khidmat dan elegan di Gedung Soetrisno Hadi, Jember.',
                'photo' => $img('photo-1519225421980-715cb0215aed'),
                'published_at' => '2026-05-24',
                'package' => 'royal-garden-wedding',
                'content' => "Erwin dan Anggita memilih menghadirkan prosesi adat Jawa Solo secara utuh, mulai dari siraman, midodareni, hingga akad di pagi hari. Tim MyDream mendampingi keluarga sejak tiga bulan sebelum hari H agar setiap tahapan adat berjalan sesuai tradisi.\n\nDekorasi pelaminan memadukan ukiran kayu jati dengan rangkaian melati segar, menghadirkan suasana khidmat namun tetap hangat. Resepsi malam harinya dilanjutkan dengan konsep ballroom bernuansa emas.",
            ],
            [
                'slug' => 'pernikahan-feby-yudo',
                'title' => 'Pernikahan Feby & Yudo',
                'category' => 'Real Wedding',
                'excerpt' => 'Akad intim bernuansa bunga segar putih dan sage di Pine Forest Glamping, Rembangan.',
                'photo' => $img('photo-1465495976277-4387d4b0b4c6'),
                'published_at' => '2026-05-22',
                'package' => 'akad-minimalis-champagne',
                'content' => "Feby dan Yudo ingin merayakan hari bahagia mereka jauh dari keramaian kota. Pilihan jatuh pada Pine Forest Glamping di Rembangan, dengan udara sejuk pegunungan sebagai latar alami.\n\nDekorasi minimalis berpalet putih dan sage dipilih agar selaras dengan hijaunya venue. Tim MyDream membantu koordinasi logistik outdoor, termasuk rencana cadangan jika cuaca berubah.",
            ],
            [
                'slug' => 'pernikahan-vio-tika',
                'title' => 'Pernikahan Vio & Tika',
                'category' => 'Real Wedding',
                'excerpt' => 'Resepsi modern bernuansa pencahayaan glamour dan hangat di ballroom hotel.',
                'photo' => $img('photo-1522673607200-164d1b6ce486'),
                'published_at' => '2026-05-20',
                'package' => 'grand-ballroom-surabaya',
                'content' => "Vio dan Tika menginginkan resepsi yang megah namun tetap terasa personal. Ballroom hotel di Surabaya dipilih karena kapasitasnya luas dan pencahayaan lampu gantung kristalnya yang ikonik.\n\nTim dekorasi merancang panggung bertingkat dengan aksen floral emas, sementara tim katering menyiapkan menu untuk 400 tamu undangan.",
            ],
            [
                'slug' => 'tips-merencanakan-pernikahan-budget-terbatas',
                'title' => '5 Tips Merencanakan Pernikahan dengan Budget Terbatas',
                'category' => 'Tips & Rencana',
                'excerpt' => 'Tetap bisa mendapatkan pernikahan impian tanpa menguras tabungan. Ini strategi dari tim MyDream.',
                'photo' => $img('photo-1594552072238-b8a33785b261'),
                'published_at' => '2026-06-02',
                'package' => 'akad-minimalis-champagne',
                'content' => "1. Tentukan prioritas utama, misalnya venue, dokumentasi, atau katering, lalu alokasikan budget terbesar ke sana.\n\n2. Pilih tanggal di luar musim ramai (hari kerja atau bulan non-peak) agar harga vendor lebih bersahabat.\n\n3. Gunakan paket gabungan dari wedding organizer dibanding memesan vendor satu per satu.\n\n4. Batasi jumlah tamu sesuai kapasitas venue yang realistis.\n\n5. Diskusikan pembayaran DP bertahap agar arus kas tetap sehat menjelang hari H.",
            ],
            [
                'slug' => 'checklist-persiapan-pernikahan-h-6-bulan',
                'title' => 'Checklist Persiapan Pernikahan H-6 Bulan',
                'category' => 'Tips & Rencana',
                'excerpt' => 'Panduan langkah demi langkah agar tidak ada detail yang terlewat menjelang hari bahagia.',
                'photo' => $img('photo-1555244162-803834f70033'),
                'published_at' => '2026-06-10',
                'package' => 'royal-garden-wedding',
                'content' => "H-6 bulan: tentukan tanggal, venue, dan booking WO atau vendor utama.\n\nH-4 bulan: fitting awal busana, pilih tema dekorasi, mulai susun daftar tamu.\n\nH-2 bulan: cetak undangan, konfirmasi menu katering, gladi kotor rundown.\n\nH-2 minggu: pelunasan vendor, konfirmasi akhir jumlah tamu, briefing keluarga dan kru.\n\nH-1: gladi resik di lokasi dan pastikan seluruh perlengkapan sudah siap.",
            ],
            [
                'slug' => 'cara-memilih-vendor-fotografi-yang-tepat',
                'title' => 'Cara Memilih Vendor Fotografi yang Tepat',
                'category' => 'Vendor Stories',
                'excerpt' => 'Portofolio bagus saja tidak cukup. Kenali gaya dan cara kerja fotografer sebelum booking.',
                'photo' => $img('photo-1519741497674-611481863552'),
                'published_at' => '2026-06-18',
                'package' => 'busana-dan-dokumentasi-surabaya',
                'content' => "Perhatikan gaya editing di portofolio, apakah natural, cinematic, atau vintage, dan sesuaikan dengan seleramu.\n\nTanyakan lama pengerjaan foto jadi, misalnya same day edit atau beberapa minggu.\n\nPastikan ada minimal dua fotografer di hari H agar momen penting tidak terlewat.\n\nDiskusikan hak cipta foto dan format file yang akan diterima (RAW, JPEG, cetak album).",
            ],
        ];

        foreach ($articles as $data) {
            $packageSlug = $data['package'];
            unset($data['package']);

            Article::updateOrCreate(
                ['slug' => $data['slug']],
                $data + [
                    'author' => 'Tim MyDream',
                    'is_published' => true,
                    'package_id' => Package::where('slug', $packageSlug)->value('id'),
                ]
            );
        }
    }
}
