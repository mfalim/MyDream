@extends('layouts.frontend')

@section('title', 'Direktori Vendor & Destinasi Pernikahan — MyDream Organizer')

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Cari Vendor']]" />
@endsection

@php
    $navCepat = [
        'Semua Vendor',
        'Jember & Sekitarnya',
        'Jawa Timur',
        'Bali & Nusa Tenggara',
        'Destinasi Nasional Lainnya',
    ];

    $destinasi = [
        [
            'image' => 'destination/destination-01.jpg',
            'tag' => 'Destinasi #1',
            'name' => 'Jember & Sekitarnya',
            'desc' => 'Basis utama kami — gedung, kebun, dan ballroom pilihan dengan tim vendor lokal terpercaya.',
        ],
        [
            'image' => 'destination/destination-02.jpg',
            'tag' => 'Favorit Pasangan',
            'name' => 'Bali, Uluwatu & Ubud',
            'desc' => 'Venue tebing dan resort tropis untuk pernikahan destinasi yang intim dan mewah.',
        ],
        [
            'image' => 'destination/destination-03.jpg',
            'tag' => 'Bespoke Mewah',
            'name' => 'Surabaya & Jawa Timur',
            'desc' => 'Ballroom hotel bintang lima dan gedung pertemuan skala besar.',
        ],
        [
            'image' => 'destination/destination-04.jpg',
            'tag' => 'Pemandangan Sunset',
            'name' => 'Banyuwangi & Pantai',
            'desc' => 'Upacara sunset di tepi pantai dengan nuansa tropis yang syahdu.',
        ],
    ];

    $kota = [
        ['name' => 'Jember Kota', 'area' => 'Kaliwates, Sumbersari, Patrang'],
        ['name' => 'Banyuwangi', 'area' => 'Genteng, Rogojampi'],
        ['name' => 'Bondowoso', 'area' => 'Kota & sekitarnya'],
        ['name' => 'Lumajang', 'area' => 'Kota & sekitarnya'],
        ['name' => 'Surabaya', 'area' => 'Jawa Timur & Sidoarjo'],
        ['name' => 'Malang & Batu', 'area' => 'Jawa Timur Pegunungan'],
        ['name' => 'Bali', 'area' => 'Denpasar, Ubud, Uluwatu'],
        ['name' => 'Yogyakarta & Solo', 'area' => 'Jawa Tengah'],
    ];
@endphp

@section('content')
    <section class="section-sm">
        <div class="container">
            <p class="eyebrow">Direktori Vendor &amp; Destinasi Pernikahan</p>
            <h1 class="h1 mt-2" style="font-size:2.1rem;">Temukan Vendor &amp; Destinasi Pernikahan Impian di Jember dan
                Sekitarnya</h1>
            <p class="lede mt-1">Jelajahi vendor pernikahan, venue, dan layanan pilihan MyDream untuk membantu persiapan
                pernikahanmu.</p>

            <form method="GET" action="{{ route('vendor.index') }}" class="flex gap-1 mt-3" style="max-width:640px;">
                <input type="text" name="q" value="{{ $q }}"
                    placeholder="Cari vendor, kota, atau layanan..." aria-label="Cari vendor"
                    style="flex:1;border:1px solid var(--line);border-radius:var(--radius-pill);padding:13px 20px;font-size:.86rem;">
                <button type="submit" class="btn btn-dark">Cari</button>
            </form>

            <div class="filter-pills mt-2">
                @foreach ($navCepat as $item)
                    @if ($item === 'Semua Vendor')
                        <a href="{{ route('vendor.index') }}"
                            class="filter-pill {{ $kategori === '' ? 'active' : '' }}">{{ $item }}</a>
                    @else
                        <span class="filter-pill">{{ $item }}</span>
                    @endif
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
                    <div class="card">
                        <div class="card-media" style="aspect-ratio:4/3.2;">
                            <img src="{{ asset('images/' . $d['image']) }}" alt="{{ $d['name'] }}">
                            <div class="badges-top"><span class="badge badge-paper">{{ $d['tag'] }}</span></div>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title" style="font-size:1.05rem;">{{ $d['name'] }}</h3>
                            <p class="tiny muted mt-1">{{ $d['desc'] }}</p>
                            <div class="flex justify-between mt-2" style="align-items:center;">
                                <span class="tiny faint">{{ $vendors->count() }} Vendor</span>
                                <span class="link-arrow">Eksplor &rarr;</span>
                            </div>
                        </div>
                    </div>
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
                <span class="badge badge-gold">{{ $vendors->count() }} Total Vendor</span>
            </div>

            <div class="city-table mt-3">
                @foreach ($kota as $k)
                    <div class="cell">
                        <div><b>{{ $k['name'] }}</b> <span class="tiny faint">{{ $k['area'] }}</span></div>
                        <span class="count">{{ $vendors->count() }} Vendor</span>
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
                <span class="link-arrow">{{ $vendors->count() }} Vendor</span>
            </div>

            @if ($vendors->isEmpty())
                <div class="card" style="padding:40px;text-align:center;">
                    <h3 class="h3">Vendor tidak ditemukan</h3>
                    <p class="muted mt-1">Coba gunakan kata kunci lain atau tampilkan semua vendor.</p>
                    <a href="{{ route('vendor.index') }}" class="btn btn-outline mt-2">Tampilkan Semua Vendor</a>
                </div>
            @else
                <div class="grid grid-3">
                    @foreach ($vendors as $vendor)
                        @php
                            $coverPhoto = $vendor->photos->first(function ($photo) {
                                return (bool) ($photo->cover ?? ($photo->is_cover ?? false));
                            });
                            $firstPhoto = $coverPhoto ?: $vendor->photos->first();
                            $image = $firstPhoto ? asset('storage/' . $firstPhoto->photo) : null;
                        @endphp

                        <a href="{{ route('vendor.show', $vendor->slug) }}" class="card" style="display:block;">
                            <div class="card-media" style="aspect-ratio:4/3;">
                                @if ($image)
                                    <img src="{{ $image }}" alt="{{ $vendor->name }}">
                                @else
                                    <div
                                        style="height:100%;display:flex;align-items:center;justify-content:center;background:var(--paper-dark);color:var(--muted);">
                                        Foto belum tersedia</div>
                                @endif
                            </div>

                            <div class="card-body">
                                @if ($vendor->category)
                                    <div class="flex gap-1 flex-wrap"><span
                                            class="badge badge-maroon">{{ $vendor->category->name }}</span></div>
                                @endif
                                <h3 class="card-title" style="font-size:1.05rem;">{{ $vendor->name }}</h3>
                                <p class="tiny muted mt-1">{{ $vendor->address ?: 'Jember, Jawa Timur' }}</p>
                                @if ($vendor->description)
                                    <p class="tiny muted mt-1">{{ $vendor->description }}</p>
                                @endif
                                <div class="flex justify-between mt-2" style="align-items:center;">
                                    <span class="tiny faint">Mulai</span>
                                    <strong class="text-maroon">Rp
                                        {{ number_format((float) $vendor->price, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="section section-cream">
        <div class="container concierge-layout"
            style="display:grid;grid-template-columns:1.3fr 1fr;gap:32px;align-items:center;">
            <div>
                <p class="eyebrow">MyDream Destination Concierge</p>
                <h2 class="h2 mt-2">Merencanakan Destination Wedding di Surabaya atau Bali</h2>
                <p class="lede mt-2">Tim specialist kami siap mengkurasi vendor lokal, membantu koordinasi venue, serta
                    mendampingi proses persiapan pernikahanmu.</p>
                <div class="flex flex-wrap gap-2 mt-3">
                    <span class="small muted">✓ Vendor Terverifikasi</span>
                    <span class="small muted">✓ Proteksi MyDream Pay</span>
                    <span class="small muted">✓ Bantuan Koordinasi Lokal</span>
                </div>
                <div class="flex gap-2 mt-3 flex-wrap">
                    <a href="https://wa.me/6281233779967" class="btn btn-maroon">Konsultasi Gratis</a>
                    <a href="{{ route('catalog.index') }}" class="btn btn-outline">Lihat Paket &amp; Promo</a>
                </div>
            </div>
            <div>
                <img src="{{ asset('images/destination/destination-02.jpg') }}" alt="Destination wedding"
                    style="border-radius:var(--radius-lg);width:100%;aspect-ratio:4/3;object-fit:cover;">
                <div class="testimonial mt-2">
                    <p class="quote small">&ldquo;MyDream membantu mewujudkan pernikahan impian kami di Bali tanpa kendala
                        koordinasi jarak jauh.&rdquo;</p>
                    <p class="who">&mdash; Randy &amp; Clarissa, Jember &middot; Bali</p>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        @media (max-width: 860px) {
            .concierge-layout {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
@endpush
