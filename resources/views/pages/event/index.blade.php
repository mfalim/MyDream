@extends('layouts.frontend')

@section('title', 'MyDream Wedding Fair & Events 2026 — MyDream Organizer')

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Wedding Fair & Events']]" />
@endsection

@php
    $filterPameran = ['Semua Pameran (Upcoming & On-Going)', 'MyDream Fair (Mega Exhibition)', 'MyDream Market (Pop-up & Outdoor)', 'Hotel & Venue Open House', 'Virtual Wedding Showcase'];

    $events = [
        ['image' => 'fair/fair-01.jpg', 'badge' => 'Mega Event', 'access' => 'Gratis Masuk', 'date' => '24 - 27 Juli 2026', 'title' => 'MyDream Fair 2026 (Mega Exhibition)', 'loc' => 'GOR Kaliwates, Jember', 'desc' => 'Pameran bridal terbesar dengan 350+ vendor papan atas, grand prize, cashback transaksi.', 'tags' => ['350+ Vendor', 'Free Shuttle Bus']],
        ['image' => 'fair/fair-02.jpg', 'badge' => 'Pop-Up Fair', 'access' => 'Gratis Masuk', 'date' => '14 - 17 Agustus 2026', 'title' => 'MyDream Market 2026: Outdoor Edition', 'loc' => 'Taman Kota Jember', 'desc' => 'Konsep santai intimate garden party. Menampilkan 180+ vendor kreatif, live acoustic, food market.', 'tags' => ['180+ Vendor', 'Garden Concept']],
        ['image' => 'fair/fair-03.jpg', 'badge' => 'Open House Hotel', 'access' => 'RSVP Terbatas', 'date' => '29 - 31 Agustus 2026', 'title' => 'Grand Wedding Open House by Aston Jember', 'loc' => 'Aston Ballroom, Jember Pusat', 'desc' => 'Eksklusif food tasting 10 menu istimewa, simulasi dekorasi ballroom termewah.', 'tags' => ['Free Food Tasting', 'Diskon 15% Venue']],
        ['image' => 'fair/fair-04.jpg', 'badge' => 'Regional Fair', 'access' => 'Gratis Masuk', 'date' => '12 - 14 September 2026', 'title' => 'Surabaya Wedding Fest 2026', 'loc' => 'Grand City Convex, Surabaya', 'desc' => 'Pameran vendor pernikahan terbesar se-Jawa Timur. Kolaborasi 120+ vendor dekorasi, MUA, katering.', 'tags' => ['120+ Vendor Jatim', 'Cicilan 0% 18 Bln']],
        ['image' => 'fair/fair-05.jpg', 'badge' => 'Destination Fair', 'access' => 'RSVP Terbatas', 'date' => '26 - 29 September 2026', 'title' => 'Bali Destination Wedding Showcase 2026', 'loc' => 'The Mulia Resort & Villas, Nusa Dua', 'desc' => 'Panduan lengkap wedding impian di Bali. Konsultasi spesialis destinasi, villa & cliff venue, honeymoon deal.', 'tags' => ['Villa & Cliff Venue', 'Vendor Specialist']],
        ['image' => 'fair/fair-06.jpg', 'badge' => 'Heritage & Adat', 'access' => 'Gratis Masuk', 'date' => '10 - 12 Oktober 2026', 'title' => 'Jember Heritage & Tradisional Wedding Fair', 'loc' => 'Pendopo Wahyawibawagraha, Jember', 'desc' => 'Perayaan busana pengantin adat Nusantara lengkap. Kebaya runway, pakar adat budaya.', 'tags' => ['Kebaya Runway', 'Pakar Adat Budaya']],
    ];
@endphp

@section('content')

    <section class="section-sm">
        <div class="container">
            <p class="eyebrow">Pameran Pernikahan Terbesar &amp; Terpercaya di Jember</p>
            <h1 class="h1 mt-2" style="font-size:2.2rem;">MyDream Wedding Fair &amp; Events <em>2026</em></h1>
            <p class="lede mt-1">
                Temukan ratusan vendor terkurasi, penawaran harga spesial pameran, konsultasi langsung gratis,
                serta kesempatan memenangkan grand prize dalam pameran pernikahan terdepan di Jember dan Jawa Timur.
            </p>
            <div class="flex gap-2 mt-3">
                <a href="#jadwal" class="btn btn-maroon">Daftar Free Pass</a>
                <a href="#jadwal" class="btn btn-outline">Jadwal &amp; Lokasi</a>
            </div>
        </div>
    </section>

    <section class="section-sm">
        <div class="container">
            <div class="card featured-fair" style="display:grid;grid-template-columns:1.1fr 1fr;">
                <div class="card-media" style="aspect-ratio:auto;border-radius:0;">
                    <img src="{{ asset('images/fair/fair-main.jpg') }}" alt="MyDream Fair 2026" style="height:100%;">
                    <div class="badges-top"><span class="badge badge-maroon">Segera Hadir (H-18)</span><span class="badge badge-dark">Mega Edition 2026</span></div>
                </div>
                <div class="card-body" style="padding:28px;">
                    <span class="badge badge-outline">Official MyDream Exhibition</span>
                    <h2 class="h2 mt-2">MyDream Fair 2026</h2>
                    <p class="italic muted" style="font-family:var(--font-serif);">Luxury Atelier Edition</p>
                    <ul class="mt-2" style="display:flex;flex-direction:column;gap:6px;font-size:.84rem;color:var(--ink-soft);">
                        <li>📅 24 - 27 Juli 2026 (Kamis - Minggu)</li>
                        <li>📍 GOR Kaliwates, Jember</li>
                        <li>🕐 10:00 - 22:00 WIB &middot; Akses Masuk Gratis</li>
                    </ul>
                    <ul class="card-features mt-2">
                        <li><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4.5 12.75l6 6 9-13.5" stroke-linecap="round" stroke-linejoin="round"/></svg><span>350+ vendor flagship terkemuka: ballroom, dekorasi mewah, gaun, dokumentasi, katering &amp; cincin kawin</span></li>
                        <li><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4.5 12.75l6 6 9-13.5" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Cashback s/d Rp 15.000.000 eksklusif pembayaran MyDream Pay</span></li>
                        <li><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4.5 12.75l6 6 9-13.5" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Grand Prize: 1 unit sepeda motor listrik &amp; paket honeymoon Bali 4H3M</span></li>
                        <li><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4.5 12.75l6 6 9-13.5" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Fasilitas: free shuttle bus, valet VIP, nursery &amp; kids lounge</span></li>
                    </ul>
                    <div class="flex gap-2 mt-3 flex-wrap">
                        <a href="#" class="btn btn-maroon">Klaim E-Pass Gratis</a>
                        <a href="#" class="btn btn-outline">Denah Booth &amp; Vendor</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="jadwal" class="section">
        <div class="container">
            <div class="filter-pills">
                @foreach ($filterPameran as $i => $f)
                    <button type="button" class="filter-pill {{ $i === 0 ? 'active' : '' }}">{{ $f }}</button>
                @endforeach
            </div>
            <div class="flex gap-1 mt-2 flex-wrap">
                <select style="border:1px solid var(--line);border-radius:var(--radius-pill);padding:9px 16px;font-size:.8rem;"><option>Semua Kota (Indonesia)</option></select>
                <select style="border:1px solid var(--line);border-radius:var(--radius-pill);padding:9px 16px;font-size:.8rem;"><option>Semua Bulan (2026)</option></select>
                <select style="border:1px solid var(--line);border-radius:var(--radius-pill);padding:9px 16px;font-size:.8rem;"><option>Jenis Tiket: Semua</option></select>
                <button type="button" class="btn btn-gold btn-sm">Terapkan Filter</button>
            </div>

            <div class="section-head mt-3">
                <div>
                    <p class="eyebrow">Jadwal Kalender Terdekat</p>
                    <h2 class="h2 mt-2">Daftar Pameran Pernikahan Pilihan</h2>
                </div>
                <p class="small muted">Menampilkan {{ count($events) }} pameran terverifikasi</p>
            </div>

            <div class="grid grid-3">
                @foreach ($events as $e)
                    <div class="card">
                        <div class="card-media">
                            <img src="{{ asset('images/'.$e['image']) }}" alt="{{ $e['title'] }}">
                            <div class="badges-top"><span class="badge badge-dark">{{ $e['badge'] }}</span><span class="badge badge-paper">{{ $e['access'] }}</span></div>
                        </div>
                        <div class="card-body">
                            <p class="tiny text-maroon" style="text-transform:uppercase;font-weight:700;">{{ $e['date'] }}</p>
                            <h3 class="card-title" style="font-size:1.05rem;">{{ $e['title'] }}</h3>
                            <p class="tiny muted mt-1">📍 {{ $e['loc'] }}</p>
                            <p class="small muted mt-1">{{ $e['desc'] }}</p>
                            <div class="flex gap-1 flex-wrap mt-2">
                                @foreach ($e['tags'] as $t)<span class="feature-pill">{{ $t }}</span>@endforeach
                            </div>
                            <div class="card-actions">
                                <span class="btn btn-dark btn-block">Klaim E-Pass</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section section-cream text-center">
        <div class="container">
            <p class="eyebrow" style="justify-content:center;">Pengalaman Eksklusif</p>
            <h2 class="h2 mt-2">Mengapa Mengunjungi Pameran MyDream?</h2>
            <div class="grid grid-4 mt-4">
                <div class="icon-feature">
                    <div class="icon-circle">%</div>
                    <h4>Promo Spesial Pameran</h4>
                    <p>Diskon eksklusif hingga puluhan juta selama pameran berlangsung.</p>
                </div>
                <div class="icon-feature">
                    <div class="icon-circle">🍽</div>
                    <h4>Konsultasi &amp; Food Tasting Gratis</h4>
                    <p>Temui wedding planner, desainer gaun, dan nikmati food tasting katering pilihan.</p>
                </div>
                <div class="icon-feature">
                    <div class="icon-circle">🛡</div>
                    <h4>Aman dengan MyDream Pay</h4>
                    <p>Jaminan rekening escrow dana, perlindungan pembatalan 0% bunga dengan kartu kredit.</p>
                </div>
                <div class="icon-feature">
                    <div class="icon-circle">🎁</div>
                    <h4>Grand Prize &amp; Flash Rewards</h4>
                    <p>Setiap transaksi kelipatan tertentu berhadiah undian mobil, motor listrik, hingga voucher souvenir.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container two-col">
            <div>
                <p class="eyebrow">Atraksi &amp; Aktivitas</p>
                <h2 class="h2 mt-2">Bukan Sekadar Pameran, Ini Perayaan Cinta &amp; Inspirasi</h2>
                <p class="lede mt-1">Nikmati susunan program panggung interaktif yang dirancang khusus untuk memperkaya visi pernikahanmu.</p>
                <ul class="numbered-list mt-2">
                    <li class="item"><span class="num">👗</span><div><strong>Bridal Fashion Runway</strong><p class="small muted mt-1">Peragaan busana pengantin karya desainer kenamaan setiap hari pukul 16.00 &amp; 19.00 WIB.</p></div></li>
                    <li class="item"><span class="num">🎙</span><div><strong>Interactive Workshop &amp; Talkshow</strong><p class="small muted mt-1">Sesi bedah konsultan finansial bersertifikat dan panduan memilih perhiasan berkilau.</p></div></li>
                    <li class="item"><span class="num">🎶</span><div><strong>Live Music Lounge &amp; Gourmet Corner</strong><p class="small muted mt-1">Area santai berpadu alunan acoustic band pernikahan, artisanal coffee, dan area istirahat nyaman.</p></div></li>
                </ul>
            </div>
            <div class="grid grid-2" style="gap:10px;">
                @foreach (range(1,4) as $i)
                    <img src="{{ asset('images/fair/fair-gallery-0'.$i.'.jpg') }}" alt="Dokumentasi acara" style="border-radius:var(--radius-md);aspect-ratio:1/1;object-fit:cover;width:100%;">
                @endforeach
            </div>
        </div>
    </section>

    <section class="section section-ink">
        <div class="container exhibitor-layout" style="display:grid;grid-template-columns:1.3fr 1fr;gap:32px;align-items:center;">
            <div>
                <p class="eyebrow" style="color:var(--gold);">MyDream for Business &amp; Vendors</p>
                <h2 class="h2 mt-2" style="color:#F4EEE2;">Ingin Menjadi Partisipan atau Buka Booth di Pameran MyDream?</h2>
                <p class="mt-2" style="color:#B6AC98;">Jangkau lebih dari 15.000 calon pengantin berdaya beli tinggi di setiap penyelenggaraan acara kami.</p>
                <div class="flex gap-2 mt-3 flex-wrap tiny" style="color:#B6AC98;">
                    <span>15.000+ Pengunjung/Acara</span> &middot;
                    <span>96% Kepuasan Exhibitor</span>
                </div>
                <div class="flex gap-2 mt-3 flex-wrap">
                    <a href="https://wa.me/6281233779967" class="btn btn-gold">Daftar Sebagai Exhibitor</a>
                    <a href="#" class="btn btn-outline-light">Unduh Media Kit &amp; Denah</a>
                </div>
            </div>
            <img src="{{ asset('images/fair/fair-05.jpg') }}" alt="" style="border-radius:var(--radius-lg);width:100%;aspect-ratio:4/3;object-fit:cover;">
        </div>
    </section>

@endsection

@push('styles')
<style>
    @media (max-width: 800px) { .featured-fair, .exhibitor-layout { grid-template-columns: 1fr !important; } }
</style>
@endpush
