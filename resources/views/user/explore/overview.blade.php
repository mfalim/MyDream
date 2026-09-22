@extends('user.layouts.app')

@section('title', $vendor['name'])

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
        {{ $vendor['category'] }}
        <span>›</span>
        {{ $vendor['name'] }}
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
                {{ $vendor['name'] }}
            </h1>

            <p class="overview-location">
                <i class="bi bi-geo-alt"></i>
                {{ $vendor['category'] }}
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

            <a href="{{ route('user.vendor-payment', $vendorSlug) }}" class="primary-action">
                <i class="bi bi-plus-circle"></i>
                Pilih Vendor
            </a>

        </div>

    </section>


    {{-- QUICK INFO --}}
    <section class="overview-quick-info">

        <div>
            <i class="bi bi-star-fill"></i>

            <strong>
                {{ $vendor['rating'] }}
            </strong>

            <span>
                ({{ $vendor['reviews'] }} ulasan)
            </span>
        </div>

        <div>
            <i class="bi bi-patch-check"></i>
            Terverifikasi WO PROJECT
        </div>

        <div>
            <i class="bi bi-calendar-check"></i>
            {{ $vendor['experience'] }}
        </div>

        <div>
            <i class="bi bi-people"></i>
            {{ $vendor['capacity'] }}
        </div>

    </section>


    {{-- GALLERY --}}
    <section class="overview-gallery">

        <div class="gallery-main">

            <img
                src="{{ $vendor['image'] }}"
                alt="{{ $vendor['name'] }}"
            >

            <div class="gallery-main-caption">

                <span>
                    {{ $vendor['category'] }}
                </span>

                <strong>
                    {{ $vendor['name'] }}
                </strong>

            </div>

        </div>


        <div class="gallery-small">

            <div>
                <img
                    src="{{ $vendor['image'] }}"
                    alt=""
                >
            </div>

            <div>
                <img
                    src="{{ $vendor['image'] }}"
                    alt=""
                >
            </div>

            <div>
                <img
                    src="{{ $vendor['image'] }}"
                    alt=""
                >
            </div>

            <div>
                <img
                    src="{{ $vendor['image'] }}"
                    alt=""
                >

                <span class="gallery-more">
                    <i class="bi bi-images"></i>
                    Lihat Semua Foto
                </span>

            </div>

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
                    {{ $vendor['name'] }}
                </h2>

                <p>
                    {{ $vendor['description'] }}
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
                        {{ $vendor['category'] }}
                    </strong>

                </div>


                <div class="highlight-card">

                    <span>
                        PENGALAMAN
                    </span>

                    <strong>
                        {{ $vendor['experience'] }}
                    </strong>

                </div>


                <div class="highlight-card">

                    <span>
                        KAPASITAS
                    </span>

                    <strong>
                        {{ $vendor['capacity'] }}
                    </strong>

                </div>


                <div class="highlight-card">

                    <span>
                        LAYANAN
                    </span>

                    <strong>
                        {{ $vendor['service'] }}
                    </strong>

                </div>

            </section>


            {{-- PACKAGES --}}
            <section class="overview-section">

                <div class="section-heading">

                    <div>
                        <span class="section-kicker">
                            PILIHAN PAKET
                        </span>

                        <h2>
                            Paket Layanan {{ $vendor['name'] }}
                        </h2>
                    </div>

                    <span class="section-note">
                        Harga dapat disesuaikan
                    </span>

                </div>


                <article class="package-card">

                    <span class="package-label">
                        PAKET REKOMENDASI WO PROJECT
                    </span>

                    <h3>
                        {{ $vendor['service'] }} Premium Package
                    </h3>

                    <p>
                        Paket layanan lengkap yang dapat disesuaikan
                        dengan konsep dan kebutuhan pernikahan Anda.
                    </p>

                    <div class="package-price">

                        <div>
                            <span>Investasi Paket</span>

                            <strong>
                                {{ $vendor['price'] }}
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

                        <a href="{{ route('user.vendor-payment', $vendorSlug) }}" class="primary-action">
                            <i class="bi bi-plus-circle"></i>
                            Pilih Paket Ini
                        </a>

                        <button class="secondary-action">
                            Lihat Detail Paket
                        </button>

                    </div>

                </article>


                <article class="package-card">

                    <span class="package-label">
                        PAKET CUSTOM
                    </span>

                    <h3>
                        Custom {{ $vendor['service'] }}
                    </h3>

                    <p>
                        Sesuaikan layanan dengan konsep, jumlah tamu,
                        durasi acara dan kebutuhan teknis Anda.
                    </p>

                    <div class="package-price">

                        <div>
                            <span>Estimasi mulai</span>

                            <strong>
                                {{ $vendor['price'] }}
                            </strong>
                        </div>

                    </div>

                    <button class="secondary-action">
                        Konsultasikan Paket
                    </button>

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
                    {{ $vendor['name'] }}
                </h3>

                <div class="booking-price">
                    {{ $vendor['price'] }}
                </div>

                <p>
                    Harga mulai dan dapat berubah berdasarkan
                    kebutuhan paket.
                </p>


                <div class="booking-row">

                    <span>
                        <i class="bi bi-calendar-event"></i>
                        Tanggal Pernikahan
                    </span>

                    <strong>
                        25 Oktober 2025
                    </strong>

                </div>


                <div class="booking-row">

                    <span>
                        <i class="bi bi-people"></i>
                        Kapasitas
                    </span>

                    <strong>
                        {{ $vendor['capacity'] }}
                    </strong>

                </div>


                <a href="{{ route('user.vendor-payment', $vendorSlug) }}" class="booking-primary">
                    <i class="bi bi-plus-circle"></i>
                    Pilih Vendor Ini
                </a>


                <button class="booking-secondary">
                    <i class="bi bi-chat-dots"></i>
                    Chat Wedding Specialist
                </button>


                <small class="booking-note">
                    Vendor akan masuk ke daftar koordinasi
                    setelah Anda melakukan pemilihan.
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
                        {{ $vendor['location'] }}
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
                Tambahkan {{ $vendor['name'] }}
                ke susunan vendor Anda.
            </h2>

            <p>
                Tim WO PROJECT akan membantu proses koordinasi
                dan memastikan vendor masuk ke dalam rundown
                resmi Hari H.
            </p>

        </div>


        <a href="{{ route('user.vendor-payment', $vendorSlug) }}" class="primary-action">
            <i class="bi bi-plus-circle"></i>
            Pilih Vendor Ini
        </a>

    </section>

</main>

@endsection
