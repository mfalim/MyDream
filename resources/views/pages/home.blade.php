@extends('layouts.frontend')

@section('title', 'MyDream Organizer — Pasti Nikahmu Beda!')

@section('content')

    @php
        $filterCerita = ['Semua', 'Adat Tradisional', 'Pernikahan Modern', 'Akad & Lamaran', 'Prosesi Khusus'];

        $portofolio = [
            ['image' => 'portfolio/portfolio-01.jpg', 'tag' => 'Adat Jawa Solo', 'title' => 'Pernikahan Erwin & Anggita', 'desc' => 'Sentuhan klasik Adat Jawa dengan nuansa khidmat & elegan.'],
            ['image' => 'portfolio/portfolio-02.jpg', 'tag' => 'Outdoor Modern', 'title' => 'Pernikahan Fehy & Yudo', 'desc' => 'Akad intim bernuansa bunga segar putih & sage.'],
            ['image' => 'portfolio/portfolio-03.jpg', 'tag' => 'Ballroom Reception', 'title' => 'Pernikahan Vio & Tika', 'desc' => 'Resepsi modern bernuansa pencahayaan glamour & hangat.'],
            ['image' => 'portfolio/portfolio-04.jpg', 'tag' => 'Prosesi Khusus', 'title' => 'Prosesi Pedang Pora', 'desc' => 'Upacara sakral militer penuh kehormatan & kehangatan.'],
            ['image' => 'portfolio/portfolio-05.jpg', 'tag' => 'Tradisional Modern', 'title' => 'Pernikahan Hijab Klasik', 'desc' => 'Dekorasi gebyok emas & konsep kekeluargaan penuh syahdu.'],
            ['image' => 'portfolio/portfolio-06.jpg', 'tag' => 'Intimate Dinner', 'title' => 'Resepsi Malam Romantis', 'desc' => 'Gemerlap fairylight di kebun terbuka bernuansa akrab.'],
        ];

        $layananKami = [
            ['num' => '01', 'title' => 'Perencanaan dari Nol (Full Planning)', 'desc' => 'Teman diskusi sejak awal. Kami bantu cari ide tema, pilih vendor tepat, dan susun rencana acara yang cermat.'],
            ['num' => '02', 'title' => 'Pengawal Hari Acara (D-Day Coordination)', 'desc' => 'Fokus nikmati hari bahagiamu, tim kami yang atur ketepatan waktu, sambut tamu besar, dan pastikan seluruh vendor bergerak kompak.'],
            ['num' => '03', 'title' => 'Pilihan Vendor Terpercaya', 'desc' => 'Kami hubungkan kamu dengan dekorator, MUA, fotografer, dan sound system terbaik di Jember tanpa perlu repot mencari satu per satu.'],
            ['num' => '04', 'title' => 'Lamaran & Acara Keluarga', 'desc' => 'Pendampingan untuk momen sakral pra-nikah seperti lamaran, siraman, dan pengajian agar tetap khidmat, hangat, dan tertata.'],
        ];

        $bantuKami = [
            ['title' => 'Hangat & Personal', 'desc' => 'Setiap susunan acara kami sesuaikan dengan gaya kamu dan tradisi keluarga.'],
            ['title' => 'Waktu yang Terjaga', 'desc' => 'Kami pastikan prosesi adat dan resepsi berjalan tepat waktu tanpa terburu-buru.'],
            ['title' => 'Komunikasi Satu Pintu', 'desc' => 'Selalu ada perencana pernikahan yang siap diajak ngobrol dan update progres kapan saja.'],
            ['title' => 'Paham Seluk-Beluk Jember', 'desc' => 'Kami kenal baik karakter venue dan vendor lokal, sehingga koordinasi di lapangan lebih lancar.'],
        ];

        $langkah = [
            ['num' => '01', 'title' => 'Ngobrol Santai', 'desc' => 'Ceritakan konsep impian dan gambaran budget lewat chat atau kunjungi kami langsung.'],
            ['num' => '02', 'title' => 'Matangkan Rencana', 'desc' => 'Kami susun moodboard visual, pilihan vendor cocok, dan budget jadwal acara.'],
            ['num' => '03', 'title' => 'Rapat Pemantapan', 'desc' => 'Duduk bersama keluarga dan seluruh vendor menyelaraskan detail 2-3 minggu sebelum acara.'],
            ['num' => '04', 'title' => 'Hari Pernikahan', 'desc' => 'Saatnya tersenyum dan menikmati hari spesialmu dengan tenang.'],
        ];

        $testimoni = [
            ['quote' => 'Detail acaranya rapi banget. Acara adat Jawanya khidmat, resepsinya juga seru tanpa jeda canggung. Tim My Dream siap sedia!', 'name' => 'Erwin & Anggita'],
            ['quote' => 'Bener-bener bikin tenang. Dari awal konsultasi sampai hari-H komunikasinya enak dan fleksibel banget diajak diskusi.', 'name' => 'Fehy & Yudo'],
            ['quote' => 'Pilihan paling tepat buat wedding di Jember. Rundown bener-bener tepat waktu dan koordinasi vendornya juara.', 'name' => 'Vio & Tika'],
        ];
    @endphp

    {{-- ================= HERO ================= --}}
    <section class="section" style="background:radial-gradient(circle at 15% 0%, #F1E2CE 0%, transparent 45%), radial-gradient(circle at 88% 10%, #E7D4C6 0%, transparent 40%), var(--cream); padding-top:64px; padding-bottom:56px;">
        <div class="container text-center" style="max-width:760px;">
            <p class="eyebrow" style="justify-content:center;"><span></span>Part of My Dream Group · Wedding Organizer Jember</p>
            <h1 class="h1 mt-2">Pasti Nikahmu <em>BEDA!</em></h1>
            <p class="lede mt-2" style="margin-left:auto;margin-right:auto;">
                Bikin hari bahagiamu terasa lebih personal, hangat, dan tanpa beban.
                Kamu tinggal nikmati momennya, biar kami yang urus semua detailnya.
            </p>
            <div class="flex justify-between" style="justify-content:center;gap:12px;margin-top:28px;">
                <a href="#cerita" class="btn btn-dark">Lihat Cerita Mereka</a>
                <a href="{{ route('catalog.index') ?? '#' }}" class="btn btn-outline">Jelajahi Lebih Lanjut</a>
            </div>
        </div>
    </section>

    {{-- ================= TENTANG MY DREAM ================= --}}
    <section class="section section-white">
        <div class="container two-col">
            <div>
                <p class="eyebrow">Tentang My Dream</p>
                <h2 class="h2 mt-2">Ceritamu, <em>Detail Kami.</em></h2>
                <p class="italic mt-3" style="font-family:var(--font-serif);font-size:1.1rem;color:var(--ink-soft);">
                    "Karena setiap kisah cinta punya rasa dan keunikan tersendiri."
                </p>
            </div>
            <div>
                <p class="lede">
                    Mempersiapkan pernikahan tidak harus bikin pusing. Di My Dream Organizer, kami mendampingi
                    kamu mulai dari merapikan ide konsep, memilih vendor terbaik di Jember, sampai memastikan
                    acara di hari-H berjalan mengalir. Dari akad yang sakral hingga resepsi yang meriah, semua
                    kami jaga agar tetap rapi dan berkesan.
                </p>
                <div class="numbered-list mt-3">
                    <div class="item">
                        <span class="num">01/</span>
                        <div><strong>Konsep Sesuai Karakter</strong><p class="small muted mt-1">Mencerminkan nilai dan impian berdua secara presisi.</p></div>
                    </div>
                    <div class="item">
                        <span class="num">02/</span>
                        <div><strong>Koordinasi Rapi</strong><p class="small muted mt-1">Seluruh vendor terkoneksi lewat rundown yang matang.</p></div>
                    </div>
                    <div class="item">
                        <span class="num">03/</span>
                        <div><strong>Eksekusi Tanpa Ribet</strong><p class="small muted mt-1">Keluarga dan mempelai fokus menikmati hari bahagia.</p></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= DOKUMENTASI KARYA ================= --}}
    <section id="cerita" class="section section-cream">
        <div class="container">
            <div class="section-head" style="flex-direction:column;align-items:flex-start;gap:16px;">
                <div>
                    <p class="eyebrow">Dokumentasi Karya</p>
                    <h2 class="h2 mt-2">Cerita yang Sudah Terwujud</h2>
                </div>
                <div class="filter-pills">
                    @foreach ($filterCerita as $i => $f)
                        <button type="button" class="filter-pill {{ $i === 0 ? 'active' : '' }}">{{ $f }}</button>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-3">
                @foreach ($portofolio as $item)
                    <a href="{{ route('blog.index') ?? '#' }}" class="card" style="display:block;">
                        <div class="card-media">
                            <img src="{{ asset('images/'.$item['image']) }}" alt="{{ $item['title'] }}" loading="lazy">
                            <div class="badges-top"><span class="badge badge-dark">{{ $item['tag'] }}</span></div>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">{{ $item['title'] }}</h3>
                            <p class="small muted mt-1">{{ $item['desc'] }}</p>
                            <p class="link-arrow mt-2">Lihat Cerita Lengkap &rarr;</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= BANTUAN YANG KAMI SIAPKAN ================= --}}
    <section class="section section-white">
        <div class="container">
            <p class="eyebrow">Layanan Kami</p>
            <h2 class="h2 mt-2" style="max-width:26em;">Bantuan yang Kami Siapkan untuk Harimu</h2>
            <p class="lede mt-1">Pilih paket pendampingan yang paling pas dengan kebutuhan dan konsep acaramu.</p>

            <div class="service-cards mt-4">
                @foreach ($layananKami as $s)
                    <div class="service-card">
                        <p class="num">{{ $s['num'] }}</p>
                        <h4>{{ $s['title'] }}</h4>
                        <p>{{ $s['desc'] }}</p>
                        <a href="{{ route('catalog.index') ?? '#' }}" class="link-arrow">Tanya Paket Ini &rarr;</a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= CARA KAMI MEMBANTU ================= --}}
    <section class="section section-cream">
        <div class="container two-col">
            <img src="{{ asset('images/hero/hero-about.jpg') }}" alt="Cara kami membantu" style="border-radius:var(--radius-lg);width:100%;">
            <div>
                <p class="eyebrow">Cara Kami Membantu</p>
                <h2 class="h2 mt-2">Kamu nikmati momennya. Kami yang jaga detailnya.</h2>
                <ul class="mt-3" style="display:flex;flex-direction:column;gap:16px;">
                    @foreach ($bantuKami as $b)
                        <li style="display:flex;gap:10px;">
                            <span style="color:var(--gold);font-size:1.2rem;line-height:1.3;">&bull;</span>
                            <span><strong>{{ $b['title'] }}</strong><br><span class="small muted">{{ $b['desc'] }}</span></span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    {{-- ================= LANGKAH PERSIAPAN ================= --}}
    <section class="section section-white text-center">
        <div class="container">
            <p class="eyebrow" style="justify-content:center;">Langkah Persiapan</p>
            <h2 class="h2 mt-2">Perjalanan Menuju Hari Bahagiamu</h2>
            <div class="process-steps mt-4" style="text-align:left;">
                @foreach ($langkah as $l)
                    <div class="step">
                        <p class="num">{{ $l['num'] }}</p>
                        <h4>{{ $l['title'] }}</h4>
                        <p>{{ $l['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= TESTIMONIAL ================= --}}
    <section class="section section-cream text-center">
        <div class="container">
            <h2 class="h2">Cerita Manis dari Mereka</h2>
            <div class="grid grid-3 mt-4" style="text-align:left;">
                @foreach ($testimoni as $t)
                    <div class="testimonial">
                        <p class="quote">&ldquo;{{ $t['quote'] }}&rdquo;</p>
                        <p class="who">&mdash; {{ $t['name'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= CTA GELAP ================= --}}
    <section class="section section-ink text-center">
        <div class="container" style="max-width:640px;">
            <p class="eyebrow" style="justify-content:center;color:var(--gold);">Mulai dari Sekarang</p>
            <h2 class="h2 mt-2" style="color:#F4EEE2;">Yuk, Bikin Pernikahan Impianmu Jadi Nyata.</h2>
            <p class="mt-2" style="color:#B6AC98;">
                Punya gambaran pernikahan sendiri? Ceritakan ke kami, kita mulai dari obrolan santai via WhatsApp.
            </p>
            <a href="https://wa.me/6281233779967" class="btn btn-gold mt-3">Click Link untuk Melanjutkan ke WhatsApp</a>
        </div>
    </section>

@endsection
