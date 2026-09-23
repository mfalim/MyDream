@extends('mydream.layouts.app')

@php use App\MyDream\Format; @endphp
@php use App\MyDream\Catalog; @endphp

@section('title', 'Store — Paket & Vendor Pernikahan | MyDream')
@section('body_class', 'page-store')
@section('with_categories', '1')

@section('content')

@if ($isFiltered)
    {{-- ===================== HASIL FILTER / PENCARIAN ===================== --}}
    <section class="store-section">
        <div class="container-xxl">
            <div class="store-head">
                <h1 class="store-h2">
                    @if ($kategori) {{ Catalog::categories()[$kategori] ?? 'Hasil' }} @else Hasil pencarian @endif
                    @if ($q !== '') <span class="text-muted fs-6">untuk “{{ $q }}”</span> @endif
                </h1>
                <a href="{{ route('mydream.store') }}" class="store-link"><i class="bi bi-x-circle"></i> Hapus filter</a>
            </div>

            @if ($packages->isEmpty() && $vendors->isEmpty())
                <div class="store-empty">
                    <i class="bi bi-search"></i>
                    <p>Belum ada hasil yang cocok. Coba kata kunci lain atau pilih kategori berbeda.</p>
                </div>
            @endif

            <div class="row g-4">
                @foreach ($packages as $package)
                    <div class="col-md-6 col-xl-4">@include('mydream.partials.package-card', ['package' => $package])</div>
                @endforeach
                @foreach ($vendors as $vendor)
                    <div class="col-md-6 col-xl-4">@include('mydream.partials.vendor-card', ['vendor' => $vendor])</div>
                @endforeach
            </div>
        </div>
    </section>
@else

    {{-- ===================== HERO ===================== --}}
    <section class="store-hero">
        <div class="container-xxl">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <span class="store-hero__kicker"><i class="bi bi-gem"></i> Curated deals & wedding packages marketplace</span>
                    <h1 class="store-hero__title">Wujudkan Pernikahan Impian. <em>Hemat hingga 50%</em> dengan MyDream Organizer</h1>
                    <p class="store-hero__lead">Kurasi eksklusif, voucher paket ballroom, fotografer legendaris, hingga destinasi honeymoon impian dengan proteksi penuh dan jaminan transaksi aman.</p>

                    <div class="store-trust">
                        <div><i class="bi bi-shield-check"></i><span><strong>MyDream Pay</strong>Dana aman bergaransi</span></div>
                        <div><i class="bi bi-credit-card"></i><span><strong>Cicilan 0%</strong>Hingga 24 bulan multi-bank</span></div>
                        <div><i class="bi bi-calendar2-check"></i><span><strong>Bebas Reschedule</strong>Fleksibilitas tanggal kerja</span></div>
                    </div>

                    <div class="d-flex flex-wrap gap-3 mt-4">
                        <a href="#paket" class="btn btn-md-primary btn-lg">Jelajahi paket <i class="bi bi-arrow-right ms-1"></i></a>
                        <a href="#vendor" class="btn btn-md-outline btn-lg"><i class="bi bi-lightning-charge"></i> Flash sale hari ini</a>
                    </div>
                </div>

                <div class="col-lg-5">
                    <a href="{{ route('mydream.packages.show', $featured['slug']) }}" class="store-feature">
                        <div class="store-feature__stars"><i class="bi bi-star-fill"></i> 18.400+ pasangan terbantu <small>Review bintang 4,9/5,0</small></div>
                        <img src="{{ asset($featured['image']) }}" alt="{{ $featured['title'] }}">
                        <div class="store-feature__caption">
                            <span class="store-feature__icon"><i class="bi bi-gift"></i></span>
                            <span>
                                <small>Penawaran eksklusif musim ini</small>
                                <strong>Grand Ballroom Package 500 Pax</strong>
                                <em>Hemat Rp 45.000.000</em>
                            </span>
                            <i class="bi bi-arrow-right-circle-fill ms-auto"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== PAKET (gabungan vendor) ===================== --}}
    <section class="store-section" id="paket">
        <div class="container-xxl">
            <div class="store-head">
                <h2 class="store-h2">Paket Pernikahan Terbaik di Jawa Timur</h2>
                <a href="{{ route('mydream.store', ['kategori' => 'paket']) }}" class="store-link">Lihat semua paket <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="row g-4">
                @foreach ($packages->take(3) as $package)
                    <div class="col-md-6 col-xl-4">@include('mydream.partials.package-card', ['package' => $package])</div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== VENUE (vendor berdiri sendiri) ===================== --}}
    <section class="store-section store-section--flush" id="vendor">
        <div class="container-xxl">
            <div class="store-head">
                <h2 class="store-h2">Cari Tempat Pernikahan Terbaik di Jember</h2>
                <a href="{{ route('mydream.store', ['kategori' => 'venue']) }}" class="store-link">Lihat semua venue <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="row g-4">
                @foreach ($venues as $vendor)
                    <div class="col-md-6 col-xl-4">@include('mydream.partials.vendor-card', ['vendor' => $vendor])</div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== BANNER MYDREAM PAY ===================== --}}
    <section class="store-section">
        <div class="container-xxl">
            <div class="store-banner">
                <div>
                    <span class="store-banner__kicker"><i class="bi bi-lock"></i> MyDream Wedding Fair Online</span>
                    <h2>Cashback hingga <em>Rp 10 Juta</em> dengan MyDream Pay</h2>
                    <p>Nikmati fasilitas cicilan 0% hingga 24 bulan dari 15+ bank ternama di Indonesia, proteksi garansi transaksi 100%, serta voucher diskon langsung untuk pemesanan vendor pilihan hari ini.</p>
                </div>
                <div class="store-banner__actions">
                    <a href="{{ Catalog::wa('Halo MyDream, saya mau klaim voucher cashback MyDream Pay') }}" target="_blank" rel="noopener" class="btn btn-md-primary"><i class="bi bi-ticket-perforated"></i> Klaim voucher cashback</a>
                    <a href="#" class="btn btn-md-ghost">Pelajari cara kerja pay</a>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== MUA (scroller) ===================== --}}
    <section class="store-section store-section--tint">
        <div class="container-xxl">
            <div class="store-head">
                <h2 class="store-h2">MUA Pernikahan Terbaik di Indonesia</h2>
                <a href="{{ route('mydream.store', ['kategori' => 'makeup']) }}" class="store-link">Selengkapnya <i class="bi bi-arrow-right"></i></a>
            </div>
            @include('mydream.store._scroller', ['items' => $muas])
        </div>
    </section>

    {{-- ===================== DEKOR (scroller) ===================== --}}
    <section class="store-section">
        <div class="container-xxl">
            <div class="store-head">
                <h2 class="store-h2">Dekor Terbaik di Indonesia</h2>
                <a href="{{ route('mydream.store', ['kategori' => 'dekor']) }}" class="store-link">Selengkapnya <i class="bi bi-arrow-right"></i></a>
            </div>
            @include('mydream.store._scroller', ['items' => $decors])
        </div>
    </section>

    {{-- ===================== VENDOR LAINNYA ===================== --}}
    @if ($others->isNotEmpty())
    <section class="store-section store-section--flush">
        <div class="container-xxl">
            <div class="store-head">
                <h2 class="store-h2">Vendor Pilihan Lainnya</h2>
            </div>
            <div class="row g-4">
                @foreach ($others as $vendor)
                    <div class="col-md-6 col-xl-4">@include('mydream.partials.vendor-card', ['vendor' => $vendor])</div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ===================== BUTUH BANTUAN ===================== --}}
    <section class="store-section">
        <div class="container-xxl">
            <a class="store-help" href="{{ Catalog::wa('Halo MyDream, saya butuh bantuan memilih paket pernikahan') }}" target="_blank" rel="noopener">
                Butuh Bantuan Terbaik? <em>Click Here….</em>
            </a>
        </div>
    </section>

    {{-- ===================== BLOG ===================== --}}
    <section class="store-blog">
        <div class="container-xxl">
            <h2 class="store-blog__title">Persiapkan Pernikahan <em>Terbaik</em> di Jember</h2>
            <p class="store-blog__lead">Blog ini membantu Anda untuk mengenal lebih dalam MyDream Organizer terbaik di Jember.</p>
            <div class="row g-4 align-items-stretch">
                <div class="col-lg-7">
                    <div class="store-blog__hero">
                        <img src="{{ asset('images/mydream/ph-1.svg') }}" alt="Grand Emerald Ballroom">
                        <span>Grand Emerald Ballroom<br><strong>Pernikahan Megah Berkapasitas hingga 1.200 Tamu</strong></span>
                    </div>
                </div>
                <div class="col-lg-5">
                    <h3 class="store-blog__h3">Artikel Terbaru Tentang MyDream</h3>
                    <a href="#" class="btn btn-md-soft btn-sm mb-4"><i class="bi bi-images"></i> Lihat semua 48 foto & video</a>
                    <h3 class="store-blog__h3">Event Mendatang</h3>
                    <div class="store-blog__thumb"><img src="{{ asset('images/mydream/ph-4.svg') }}" alt="Presidential Bridal Suite"><span>Presidential Bridal Suite</span></div>
                </div>
            </div>
        </div>
    </section>
@endif

@endsection
