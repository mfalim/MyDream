@extends('layouts.frontend')

@section('title', 'Direktori Vendor & Destinasi Pernikahan — MyDream Organizer')

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Cari Vendor']]" />
@endsection

@php
    $navCepat = ['Jember & Sekitarnya', 'Jawa Timur', 'Bali & Nusa Tenggara', 'Destinasi Nasional Lainnya'];

    $destinasi = [
        ['image' => 'destination/destination-01.jpg', 'tag' => 'Destinasi #1', 'name' => 'Jember & Sekitarnya', 'desc' => 'Basis utama kami — gedung, kebun, dan ballroom pilihan dengan tim vendor lokal terpercaya.', 'count' => '180+ Vendor'],
        ['image' => 'destination/destination-02.jpg', 'tag' => 'Favorit Pasangan', 'name' => 'Bali, Uluwatu & Ubud', 'desc' => 'Venue tebing dan resort tropis untuk pernikahan destinasi yang intim dan mewah.', 'count' => '420+ Vendor'],
        ['image' => 'destination/destination-03.jpg', 'tag' => 'Bespoke Mewah', 'name' => 'Surabaya & Jawa Timur', 'desc' => 'Ballroom hotel bintang lima dan gedung pertemuan skala besar.', 'count' => '260+ Vendor'],
        ['image' => 'destination/destination-04.jpg', 'tag' => 'Pemandangan Sunset', 'name' => 'Banyuwangi & Pantai', 'desc' => 'Upacara sunset di tepi pantai dengan nuansa tropis yang syahdu.', 'count' => '95+ Vendor'],
    ];

    $kota = [
        ['name' => 'Jember Kota', 'area' => 'Kaliwates, Sumbersari, Patrang', 'count' => '180+'],
        ['name' => 'Banyuwangi', 'area' => 'Genteng, Rogojampi', 'count' => '95+'],
        ['name' => 'Bondowoso', 'area' => 'Kota & sekitarnya', 'count' => '48+'],
        ['name' => 'Lumajang', 'area' => 'Kota & sekitarnya', 'count' => '40+'],
        ['name' => 'Surabaya', 'area' => 'Jawa Timur & Sidoarjo', 'count' => '260+'],
        ['name' => 'Malang & Batu', 'area' => 'Jawa Timur Pegunungan', 'count' => '150+'],
        ['name' => 'Bali', 'area' => 'Denpasar, Ubud, Uluwatu', 'count' => '420+'],
        ['name' => 'Yogyakarta & Solo', 'area' => 'Jawa Tengah', 'count' => '110+'],
    ];

    $vendors = [
        ['slug' => 'marlene-hariman-makeup', 'image' => 'vendor/vendor-01.jpg', 'tags' => ['Celebrity MUA'], 'rating' => '5.0', 'reviews' => 428, 'location' => 'Sumbersari, Jember', 'name' => 'Marlene Hariman Makeup', 'description' => 'Akad & Resepsi — Makeup Ibu & Besan, Up Standby.', 'price_from' => '18.500.000'],
        ['slug' => 'grand-emerald-ballroom', 'image' => 'vendor/vendor-02.jpg', 'tags' => ['Venue'], 'rating' => '4.9', 'reviews' => 88, 'location' => 'Kaliwates, Jember', 'name' => 'Grand Emerald Ballroom Jember', 'description' => 'Ballroom kapasitas 1.200 tamu dengan LED wall premium.', 'price_from' => '120.000.000'],
        ['slug' => 'lentera-photography', 'image' => 'vendor/vendor-03.jpg', 'tags' => ['Fotografi'], 'rating' => '4.9', 'reviews' => 214, 'location' => 'Jember Kota', 'name' => 'Lentera Photography', 'description' => 'Dokumentasi prewedding & hari-H bergaya candid.', 'price_from' => '7.500.000'],
        ['slug' => 'katering-nyonya-manis', 'image' => 'vendor/vendor-04.jpg', 'tags' => ['Katering'], 'rating' => '4.8', 'reviews' => 162, 'location' => 'Sumbersari, Jember', 'name' => 'Katering Nyonya Manis', 'description' => 'Menu prasmanan nusantara & internasional.', 'price_from' => '85.000/pax'],
        ['slug' => 'golden-hour-decoration', 'image' => 'vendor/vendor-05.jpg', 'tags' => ['Dekorasi'], 'rating' => '4.8', 'reviews' => 187, 'location' => 'Patrang, Jember', 'name' => 'Golden Hour Decoration', 'description' => 'Backdrop panggung elegan tema gold & ivory.', 'price_from' => '9.900.000'],
        ['slug' => 'mydream-wedding-organizer', 'image' => 'vendor/vendor-06.jpg', 'tags' => ['Wedding Organizer'], 'rating' => '5.0', 'reviews' => 341, 'location' => 'Jember Kota', 'name' => 'MyDream Wedding Organizer', 'description' => 'Perencanaan penuh dari konsep hingga hari-H.', 'price_from' => '25.000.000'],
    ];
@endphp

@section('content')

    <section class="section-sm">
        <div class="container">
            <p class="eyebrow">Direktori Vendor &amp; Destinasi Pernikahan</p>
            <h1 class="h1 mt-2" style="font-size:2.1rem;">Temukan Vendor &amp; Destinasi Pernikahan Impian di Jember dan Sekitarnya</h1>
            <p class="lede mt-1">
                Jelajahi ratusan vendor pernikahan terverifikasi, venue eksklusif, dan paket pernikahan impian di
                Jember, Jawa Timur, hingga destinasi favorit seperti Bali — dengan jaminan keamanan transaksi MyDream Pay.
            </p>

            <div class="flex gap-1 mt-3" style="max-width:640px;">
                <input type="text" placeholder="Cari kota, kecamatan, atau destinasi (cth: Jember, Bali, Banyuwangi)..." style="flex:1;border:1px solid var(--line);border-radius:var(--radius-pill);padding:13px 20px;font-size:.86rem;">
                <button type="button" class="btn btn-dark">Cari Lokasi</button>
            </div>

            <div class="filter-pills mt-2">
                @foreach ($navCepat as $i => $n)
                    <button type="button" class="filter-pill {{ $i === 0 ? 'active' : '' }}">{{ $n }}</button>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-sm">
        <div class="container">
            <p class="eyebrow">Kurasi Spesial Editor</p>
            <h2 class="h2 mt-2">Destinasi Pernikahan Terfavorit 2026</h2>
            <div class="grid grid-4 mt-3">
                @foreach ($destinasi as $d)
                    <a href="{{ route('vendor.index') ?? '#' }}" class="card" style="display:block;">
                        <div class="card-media" style="aspect-ratio:4/3.2;">
                            <img src="{{ asset('images/'.$d['image']) }}" alt="{{ $d['name'] }}">
                            <div class="badges-top"><span class="badge badge-paper">{{ $d['tag'] }}</span></div>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title" style="font-size:1.05rem;">{{ $d['name'] }}</h3>
                            <p class="tiny muted mt-1">{{ $d['desc'] }}</p>
                            <div class="flex justify-between mt-2" style="align-items:center;">
                                <span class="tiny faint">{{ $d['count'] }}</span>
                                <span class="link-arrow">Eksplor &rarr;</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section section-cream">
        <div class="container">
            <div class="flex justify-between" style="align-items:center;">
                <div>
                    <p class="eyebrow">Direktori Lengkap</p>
                    <h2 class="h2 mt-2">Jelajahi Berdasarkan Kawasan &amp; Kota</h2>
                </div>
                <span class="badge badge-gold">1.300+ Total Vendor</span>
            </div>

            <div class="city-table mt-3">
                @foreach ($kota as $k)
                    <div class="cell">
                        <div><b>{{ $k['name'] }}</b><span class="tiny faint">{{ $k['area'] }}</span></div>
                        <span class="count">{{ $k['count'] }} Vendor</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-head">
                <div>
                    <p class="eyebrow">Vendor Pilihan Kami</p>
                    <h2 class="h2 mt-2">Profil Vendor Terverifikasi</h2>
                </div>
                <a href="#" class="link-arrow">Lihat Semua Vendor &rarr;</a>
            </div>
            <div class="grid grid-3">
                @foreach ($vendors as $v)
                    <x-card-vendor variant="profil" :href="route('vendor.show', $v['slug']) ?? '#'" :image="asset('images/'.$v['image'])"
                        :tags="$v['tags']" :rating="$v['rating']" :reviews="$v['reviews']"
                        :location="$v['location']" :title="$v['name']" :features="[$v['description']]"
                        :price-from="$v['price_from']" />
                @endforeach
            </div>
        </div>
    </section>

    <section class="section section-cream">
        <div class="container concierge-layout" style="display:grid;grid-template-columns:1.3fr 1fr;gap:32px;align-items:center;">
            <div>
                <p class="eyebrow">MyDream Destination Concierge</p>
                <h2 class="h2 mt-2">Merencanakan Destination Wedding di Surabaya atau Bali</h2>
                <p class="lede mt-2">
                    Tim specialist kami siap mengkurasi vendor lokal berizin resmi, mengurus negosiasi ballroom
                    atau venue eksklusif, serta mengamankan seluruh transaksi dengan cicilan 0% dan jaminan
                    garansi MyDream Pay.
                </p>
                <div class="flex flex-wrap gap-2 mt-3">
                    <span class="small muted">✓ 100% Vendor Terverifikasi</span>
                    <span class="small muted">✓ Proteksi MyDream Pay</span>
                    <span class="small muted">✓ Bantuan Koordinasi Lokal</span>
                </div>
                <div class="flex gap-2 mt-3 flex-wrap">
                    <a href="https://wa.me/6281233779967" class="btn btn-maroon">Konsultasi Gratis dengan Specialist</a>
                    <a href="#" class="btn btn-outline">Panduan Destination Wedding (PDF)</a>
                </div>
            </div>
            <div>
                <img src="{{ asset('images/destination/destination-02.jpg') }}" alt="Destination wedding" style="border-radius:var(--radius-lg);width:100%;aspect-ratio:4/3;object-fit:cover;">
                <div class="testimonial mt-2">
                    <p class="quote small">&ldquo;MyDream membantu mewujudkan pernikahan impian kami di Bali tanpa kendala koordinasi jarak jauh.&rdquo;</p>
                    <p class="who">&mdash; Randy &amp; Clarissa, Jember &middot; Bali</p>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('styles')
<style>
    @media (max-width: 860px) { .concierge-layout { grid-template-columns: 1fr !important; } }
</style>
@endpush
