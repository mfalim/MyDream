@extends('mydream.layouts.app')

@section('title', 'MyDream Organizer — Pasti Nikahmu BEDA!')
@section('body_class', 'page-home')

@section('content')

{{-- ============ HERO ============ --}}
<section class="home-hero">
    <div class="container-xxl text-center">
        <span class="home-hero__eyebrow"><i class="bi bi-dot"></i> Part of My Dream Group · Wedding Organizer Jember</span>
        <h1 class="home-hero__title">Pasti Nikahmu <em>BEDA!</em></h1>
        <p class="home-hero__lead">Bikin hari bahagiamu terasa lebih personal, hangat, dan tanpa beban.<br class="d-none d-md-inline"> Kamu tinggal nikmati momennya, biar kami yang urus semua detailnya.</p>
        <div class="home-hero__cta">
            <a href="#portofolio" class="btn btn-md-dark">Lihat cerita mereka</a>
            {{-- Tombol ini membawa user ke halaman STORE --}}
            <a href="{{ route('mydream.store') }}" class="btn btn-md-outline">Jelajahi lebih lanjut <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    </div>
</section>

{{-- ============ TENTANG ============ --}}
<section class="home-about" id="tentang">
    <div class="container-xxl">
        <div class="row gy-4">
            <div class="col-lg-4">
                <span class="md-eyebrow">Tentang My Dream</span>
                <h2 class="home-h2 mt-2">Ceritamu, Detail Kami.</h2>
                <p class="home-quote">"Karena setiap kisah cinta punya rasa dan keunikan tersendiri."</p>
            </div>
            <div class="col-lg-8">
                <p class="home-about__text">Mempersiapkan pernikahan tidak harus bikin pusing. Di My Dream Organizer, kami mendampingi kamu mulai dari merapikan ide konsep, memilih vendor terbaik di Jember, sampai memastikan acara di hari-H berjalan mengalir. Dari akad yang sakral hingga resepsi yang meriah, semua kami jaga tetap rapi dan terarah.</p>
                <div class="row gy-3 home-about__points">
                    <div class="col-md-4"><strong>Konsep sesuai karakter</strong><span>Mencerminkan nilai dan impian berdua secara presisi.</span></div>
                    <div class="col-md-4"><strong>Koordinasi rapi</strong><span>Seluruh vendor tersinergi lewat rundown yang matang.</span></div>
                    <div class="col-md-4"><strong>Eksekusi tanpa ribet</strong><span>Keluarga dan mempelai fokus menikmati hari bahagia.</span></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============ PORTOFOLIO ============ --}}
<section class="home-section" id="portofolio">
    <div class="container-xxl">
        <div class="text-center mb-4">
            <span class="md-eyebrow">Dokumentasi karya</span>
            <h2 class="home-h2 mt-2">Cerita yang Sudah Terwujud</h2>
        </div>

        <div class="home-tabs" role="tablist">
            <button class="is-active" data-filter="all" type="button">Semua</button>
            <button data-filter="adat" type="button">Adat Tradisional</button>
            <button data-filter="modern" type="button">Pernikahan Modern</button>
            <button data-filter="akad" type="button">Akad & Lamaran</button>
            <button data-filter="khusus" type="button">Prosesi Khusus</button>
        </div>

        <div class="row g-4">
            @foreach ($portfolio as $item)
                <div class="col-md-6 col-lg-4" data-category="{{ $item['cat'] }}">
                    <a href="#" class="home-work">
                        <div class="home-work__media">
                            <img src="{{ asset($item['image']) }}" alt="{{ $item['title'] }}" loading="lazy">
                            <span class="md-tag md-tag--light">{{ $item['label'] }}</span>
                        </div>
                        <div class="home-work__body">
                            <h3>{{ $item['title'] }}</h3>
                            <p>{{ $item['desc'] }}</p>
                            <span class="home-work__link">Lihat cerita lengkap <i class="bi bi-arrow-right"></i></span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ LAYANAN ============ --}}
<section class="home-section home-section--tint" id="layanan">
    <div class="container-xxl">
        <div class="text-center mb-5">
            <h2 class="home-h2">Bantuan yang Kami Siapkan untuk Harimu</h2>
            <p class="home-sub">Pilih paket pendampingan yang paling pas dengan kebutuhan dan konsep acaramu.</p>
        </div>
        <div class="row g-3">
            @foreach ($services as $i => $s)
                <div class="col-md-6 col-lg-3">
                    <div class="home-service h-100">
                        <span class="home-service__no">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3>{{ $s['title'] }}</h3>
                        @if ($s['sub'])<em>{{ $s['sub'] }}</em>@endif
                        <p>{{ $s['desc'] }}</p>
                        <a href="{{ \App\MyDream\Catalog::wa('Halo My Dream, saya ingin tanya paket ' . $s['title']) }}" target="_blank" rel="noopener">Tanya paket ini <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ CARA KAMI MEMBANTU ============ --}}
<section class="home-section">
    <div class="container-xxl">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <div class="home-photo"><img src="{{ asset('images/mydream/ph-5.svg') }}" alt="Prosesi sungkeman" loading="lazy"></div>
            </div>
            <div class="col-lg-7">
                <span class="md-eyebrow">Cara kami membantu</span>
                <h2 class="home-h2 mt-2 mb-4">Kamu nikmati momennya. Kami yang jaga detailnya.</h2>
                <ul class="home-list">
                    <li><strong>Hangat & Personal</strong><span>Setiap susunan acara kami sesuaikan dengan gaya kamu dan tradisi keluarga.</span></li>
                    <li><strong>Waktu yang Terjaga</strong><span>Kami pastikan prosesi adat dan resepsi berjalan tepat waktu tanpa terasa terburu-buru.</span></li>
                    <li><strong>Komunikasi Satu Pintu</strong><span>Selalu ada perencana pernikahan yang siap diajak ngobrol dan update progres kapan saja.</span></li>
                    <li><strong>Paham Seluk-Beluk Jember</strong><span>Kami kenal baik karakter venue dan vendor lokal, sehingga koordinasi di lapangan lebih lancar.</span></li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- ============ LANGKAH PERSIAPAN ============ --}}
<section class="home-section home-section--tint">
    <div class="container-xxl">
        <div class="text-center mb-5">
            <span class="md-eyebrow">Langkah persiapan</span>
            <h2 class="home-h2 mt-2">Perjalanan Menuju Hari Bahagiamu</h2>
        </div>
        <div class="row g-4 text-center">
            @foreach ([
                ['Ngobrol Santai', 'Ceritakan konsep impian dan gambaran budget lewat chat santai atau ketemu langsung.'],
                ['Matangkan Rencana', 'Kami susun moodboard visual, pilihkan vendor yang cocok, dan buat jadwal acara.'],
                ['Rapat Pematangan', 'Duduk bersama keluarga dan semua vendor untuk menyelaraskan detail 2–3 minggu sebelum acara.'],
                ['Hari Pernikahan', 'Saatnya tersenyum dan menikmati momen spesialmu dengan tenang.'],
            ] as $i => [$t, $d])
                <div class="col-md-6 col-lg-3">
                    <span class="home-step__no">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <h3 class="home-step__title">{{ $t }}</h3>
                    <p class="home-step__desc">{{ $d }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ TESTIMONI ============ --}}
<section class="home-section">
    <div class="container-xxl">
        <h2 class="home-h2 text-center mb-5">Cerita Manis dari Mereka</h2>
        <div class="row g-4">
            @foreach ($testimonials as $t)
                <div class="col-lg-4">
                    <blockquote class="home-quote-card h-100">
                        <p>"{{ $t['quote'] }}"</p>
                        <footer>— {{ $t['name'] }}</footer>
                    </blockquote>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ CTA ============ --}}
<section class="home-cta">
    <div class="container-xxl text-center">
        <span class="home-cta__eyebrow">Mulai dari sekarang</span>
        <h2>Yuk, Bikin Pernikahan Impianmu Jadi Nyata.</h2>
        <p>Punya gambaran pernikahan sendiri? Ceritakan ke kami, kita mulai dari obrolan santai via WhatsApp.</p>
        <a href="{{ route('mydream.store') }}" class="btn btn-md-gold btn-lg">Lanjutkan ke website</a>
    </div>
</section>

@endsection
