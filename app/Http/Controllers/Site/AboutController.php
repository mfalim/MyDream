<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Vendor;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function index(): View
    {
        return view('site.about', array_merge(self::content(), [
            // Angka dihitung dari database; bisa ditimpa lewat config/mydream.php -> 'stats'.
            'stats' => [
                'weddings' => config('mydream.stats.weddings') ?? Booking::where('status', 'approved')->count(),
                'vendors' => config('mydream.stats.vendors') ?? Vendor::count(),
                'packages' => config('mydream.stats.packages') ?? Package::count(),
            ],
        ]));
    }

    public static function content(): array
    {
        return [
            'title' => 'Tentang Kami - MyDream Wedding Organizer',
            'description' => 'Kenali MyDream Wedding Organizer, pendamping pernikahan di Jember dan Surabaya.',
            'hero' => [
                'eyebrow' => 'Tentang Kami',
                'title' => 'Bagian dari MyDream Group',
                'description' => 'Kami percaya setiap pasangan berhak merayakan hari bahagianya dengan tenang, tanpa harus memikirkan setiap detail kecil sendirian.',
            ],
            'story' => [
                'eyebrow' => 'Cerita Kami',
                'title' => 'Dari Jember, untuk Setiap Pasangan',
                'description' => 'MyDream Wedding Organizer lahir dari keinginan sederhana: membuat proses merencanakan pernikahan terasa hangat, bukan melelahkan. Berbasis di Jember dan melayani hingga Surabaya, kami mendampingi pasangan mulai dari konsep, pemilihan vendor terpercaya, hingga koordinasi penuh di hari H.',
            ],
            'values_title' => 'Nilai yang Kami Pegang',
            'values' => [
                ['title' => 'Personal & Hangat', 'description' => 'Setiap konsep disesuaikan dengan cerita dan karakter pasangan, bukan template yang sama untuk semua orang.'],
                ['title' => 'Transparan', 'description' => 'Rincian harga, rundown, dan status pembayaran selalu jelas, tanpa biaya tersembunyi.'],
                ['title' => 'Vendor Terkurasi', 'description' => 'Kami hanya bekerja sama dengan vendor yang sudah teruji kualitas dan responsnya.'],
            ],
            'cta' => [
                'heading' => 'Yuk, Ngobrol Santai Dulu',
                'description' => 'Ceritakan gambaran pernikahan impianmu, tim MyDream siap membantu dari langkah pertama.',
            ],
        ];
    }
}
