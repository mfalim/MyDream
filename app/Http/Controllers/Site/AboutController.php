<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function index(): View
    {
        return view('site.about', self::content());
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
