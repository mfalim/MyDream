@extends('user.layouts.app')

@section('title', $vendor->name)

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/vendor-overview.css') }}"
    >
@endpush

@section('content')

<main class="vendor-overview-page">

    {{-- BACK --}}
    <div class="overview-back">
        <a href="{{ route('user.explore-vendor') }}">
            <i class="bi bi-arrow-left"></i>
            Kembali ke Eksplor Vendor
        </a>
    </div>


    {{-- BREADCRUMB --}}
    <div class="overview-breadcrumb">
        Eksplor
        <span>›</span>
        {{ $vendor->category->name ?? 'Vendor' }}
        <span>›</span>
        {{ $vendor->name }}
    </div>


    {{-- HEADER --}}
    <section class="overview-header">

        <div class="overview-title">

            <div class="overview-tags">

                <span class="overview-tag">
                    <i class="bi bi-patch-check-fill"></i>
                    Vendor Terverifikasi
                </span>

                <span class="overview-tag gold">
                    <i class="bi bi-star-fill"></i>
                    Vendor Pilihan
                </span>

            </div>

            <h1>
                {{ $vendor->name }}
            </h1>

            <p class="overview-location">
                <i class="bi bi-geo-alt"></i>
                {{ $vendor->category->name ?? 'Vendor' }}
                <span>•</span>
                Vendor Rekanan WO PROJECT
            </p>

        </div>


        <div class="overview-actions">

            <button type="button" class="outline-action">
                <i class="bi bi-heart"></i>
            </button>

            <button type="button" class="outline-action">
                <i class="bi bi-share"></i>
            </button>

            <a href="{{ route('user.cart.add-vendor', $vendor->id) }}" 
               class="primary-action"
               onclick="event.preventDefault(); document.getElementById('add-vendor-form').submit();">
                <i class="bi bi-cart-plus"></i>
                Tambah ke Keranjang
            </a>
            
            <form id="add-vendor-form" action="{{ route('user.cart.add-vendor', $vendor->id) }}" method="POST" style="display: none;">
                @csrf
            </form>

        </div>

    </section>


    {{-- QUICK INFO --}}
    <section class="overview-quick-info">

        <div>
            <i class="bi bi-patch-check"></i>
            Terverifikasi WO PROJECT
        </div>

        <div>
            <i class="bi bi-telephone"></i>
            {{ $vendor->phone }}
        </div>

        <div>
            <i class="bi bi-geo-alt"></i>
            {{ $vendor->address }}
        </div>

    </section>


    {{-- GALLERY --}}
    <section class="overview-gallery">

        <div class="gallery-main">
            @php
                $mainPhoto = $coverPhoto ?? $vendor->photos->first();
                $photoUrl = $mainPhoto ? asset('storage/' . $mainPhoto->photo) : 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=1400&q=85';
            @endphp
            <img src="{{ $photoUrl }}" alt="{{ $vendor->name }}">

            <div class="gallery-main-caption">
                <span>{{ $vendor->category->name ?? 'Vendor' }}</span>
                <strong>{{ $vendor->name }}</strong>
            </div>

        </div>


        <div class="gallery-small">
            @foreach($otherPhotos->take(3) as $photo)
                <div>
                    <img src="{{ asset('storage/' . $photo->photo) }}" alt="">
                </div>
            @endforeach
            
            @if($otherPhotos->count() > 3)
                <div>
                    <img src="{{ asset('storage/' . $otherPhotos->skip(3)->first()->photo) }}" alt="">
                    <span class="gallery-more">
                        <i class="bi bi-images"></i>
                        Lihat Semua Foto ({{ $vendor->photos->count() }})
                    </span>
                </div>
            @elseif($otherPhotos->count() === 3)
                <div>
                    <img src="{{ $photoUrl }}" alt="">
                    <span class="gallery-more">
                        <i class="bi bi-images"></i>
                        {{ $vendor->photos->count() }} Foto
                    </span>
                </div>
            @endif

        </div>

    </section>


    {{-- CONTENT --}}
    <div class="overview-layout">


        {{-- LEFT --}}
        <div class="overview-main-content">

            {{-- DESCRIPTION --}}
            <section class="overview-section">

                <span class="section-kicker">
                    TENTANG VENDOR
                </span>

                <h2>
                    {{ $vendor->name }}
                </h2>

                <p>
                    {{ $vendor->description }}
                </p>

                <p>
                    Vendor ini merupakan bagian dari pilihan kurasi
                    WO PROJECT. Setiap vendor melalui proses verifikasi
                    untuk memastikan kualitas layanan, kesiapan teknis,
                    serta kesesuaian dengan kebutuhan acara pernikahan.
                </p>

            </section>


            {{-- HIGHLIGHTS --}}
            <section class="overview-highlights">

                <div class="highlight-card">

                    <span>
                        KATEGORI
                    </span>

                    <strong>
                        {{ $vendor->category->name ?? 'Vendor' }}
                    </strong>

                </div>


                <div class="highlight-card">

                    <span>
                        HARGA
                    </span>

                    <strong>
                        Rp {{ number_format($vendor->price, 0, ',', '.') }}
                    </strong>

                </div>


                <div class="highlight-card">

                    <span>
                        LOKASI
                    </span>

                    <strong>
                        {{ $vendor->address }}
                    </strong>

                </div>


                <div class="highlight-card">

                    <span>
                        KONTAK
                    </span>

                    <strong>
                        {{ $vendor->phone }}
                    </strong>

                </div>

            </section>


            {{-- PACKAGES --}}
            <section class="overview-section">

                <div class="section-heading">

                    <div>
                        <span class="section-kicker">
                            DETAIL HARGA
                        </span>

                        <h2>
                            Informasi Harga {{ $vendor->name }}
                        </h2>
                    </div>

                    <span class="section-note">
                        Harga dapat disesuaikan
                    </span>

                </div>


                <article class="package-card">

                    <span class="package-label">
                        HARGA STANDAR
                    </span>

                    <h3>
                        {{ $vendor->category->name ?? 'Layanan' }} Premium
                    </h3>

                    <p>
                        Paket layanan lengkap yang dapat disesuaikan
                        dengan konsep dan kebutuhan pernikahan Anda.
                    </p>

                    <div class="package-price">

                        <div>
                            <span>Harga Layanan</span>

                            <strong>
                                Rp {{ number_format($vendor->price, 0, ',', '.') }}
                            </strong>
                        </div>

                        <span class="price-note">
                            Harga mulai
                        </span>

                    </div>


                    <div class="package-features">

                        <div>
                            <i class="bi bi-check-circle"></i>
                            Vendor terverifikasi WO PROJECT
                        </div>

                        <div>
                            <i class="bi bi-check-circle"></i>
                            Koordinasi dengan wedding organizer
                        </div>

                        <div>
                            <i class="bi bi-check-circle"></i>
                            Technical coordination Hari H
                        </div>

                        <div>
                            <i class="bi bi-check-circle"></i>
                            Konsultasi kebutuhan vendor
                        </div>

                    </div>


                    <div class="package-actions">

                        <form action="{{ route('user.cart.add-vendor', $vendor->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="primary-action">
                                <i class="bi bi-cart-plus"></i>
                                Tambah ke Keranjang
                            </button>
                        </form>

                    </div>

                </article>

            </section>


            {{-- INFORMATION --}}
            <section class="overview-section">

                <span class="section-kicker">
                    INFORMASI LAYANAN
                </span>

                <h2>
                    Yang Anda Dapatkan
                </h2>

                <div class="service-list">

                    <div>
                        <i class="bi bi-check-circle-fill"></i>
                        Konsultasi sebelum acara
                    </div>

                    <div>
                        <i class="bi bi-check-circle-fill"></i>
                        Koordinasi dengan tim WO PROJECT
                    </div>

                    <div>
                        <i class="bi bi-check-circle-fill"></i>
                        Penyesuaian kebutuhan teknis venue
                    </div>

                    <div>
                        <i class="bi bi-check-circle-fill"></i>
                        Koordinasi rundown Hari H
                    </div>

                </div>

            </section>

        </div>


        {{-- RIGHT SIDEBAR --}}
        <aside class="overview-sidebar">

            <div class="booking-card">

                <span class="booking-label">
                    ESTIMASI BIAYA
                </span>

                <h3>
                    {{ $vendor->name }}
                </h3>

                <div class="booking-price">
                    Rp {{ number_format($vendor->price, 0, ',', '.') }}
                </div>

                <p>
                    Harga mulai dan dapat berubah berdasarkan
                    kebutuhan paket.
                </p>


                <div class="booking-row">

                    <span>
                        <i class="bi bi-tag"></i>
                        Kategori
                    </span>

                    <strong>
                        {{ $vendor->category->name ?? 'Vendor' }}
                    </strong>

                </div>


                <div class="booking-row">

                    <span>
                        <i class="bi bi-telephone"></i>
                        Kontak
                    </span>

                    <strong>
                        {{ $vendor->phone }}
                    </strong>

                </div>


                <form action="{{ route('user.cart.add-vendor', $vendor->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="booking-primary">
                        <i class="bi bi-cart-plus"></i>
                        Tambah ke Keranjang
                    </button>
                </form>


                <a href="{{ route('user.cart') }}" class="booking-secondary">
                    <i class="bi bi-cart"></i>
                    Lihat Keranjang
                </a>


                <small class="booking-note">
                    Vendor akan masuk ke keranjang untuk diproses booking
                </small>

            </div>


            {{-- TRUST --}}
            <div class="trust-card">

                <div class="trust-icon">
                    <i class="bi bi-shield-check"></i>
                </div>

                <div>

                    <strong>
                        Vendor Terverifikasi
                    </strong>

                    <p>
                        WO PROJECT membantu memastikan
                        kesiapan vendor untuk kebutuhan
                        teknis pernikahan.
                    </p>

                </div>

            </div>

        </aside>

    </div>


    {{-- LOCATION --}}
    <section class="location-section">

        <span class="section-kicker">
            LOKASI & KOORDINASI
        </span>

        <h2>
            Area Layanan Vendor
        </h2>

        <div class="location-card">

            <div class="location-info">

                <i class="bi bi-geo-alt-fill"></i>

                <div>
                    <strong>
                        {{ $vendor->address }}
                    </strong>

                    <p>
                        Vendor melayani kebutuhan acara
                        berdasarkan kesepakatan dan
                        koordinasi dengan WO PROJECT.
                    </p>
                </div>

            </div>

        </div>

    </section>


    {{-- CTA --}}
    <section class="overview-bottom-cta">

        <div>

            <span>
                TERTARIK DENGAN VENDOR INI?
            </span>

            <h2>
                Tambahkan {{ $vendor->name }}
                ke susunan vendor Anda.
            </h2>

            <p>
                Tim WO PROJECT akan membantu proses koordinasi
                dan memastikan vendor masuk ke dalam rundown
                resmi Hari H.
            </p>

        </div>


        <form action="{{ route('user.cart.add-vendor', $vendor->id) }}" method="POST">
            @csrf
            <button type="submit" class="primary-action">
                <i class="bi bi-cart-plus"></i>
                Tambah ke Keranjang
            </button>
        </form>

    </section>

</main>
@endsection
