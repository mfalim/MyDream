{{-- resources/views/user/explore/package-detail.blade.php --}}
@extends('user.layouts.app')

@section('title', $package->name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/wo-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vendor-overview.css') }}">
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
        Paket Wedding
        <span>›</span>
        {{ $package->name }}
    </div>


    {{-- HEADER --}}
    <section class="overview-header">

        <div class="overview-title">

            <div class="overview-tags">

                <span class="overview-tag gold">
                    <i class="bi bi-gem"></i>
                    Paket Lengkap
                </span>

                <span class="overview-tag">
                    <i class="bi bi-patch-check-fill"></i>
                    {{ $package->vendors->count() }} Vendor
                </span>

            </div>

            <h1>
                {{ $package->name }}
            </h1>

            <p class="overview-location">
                <i class="bi bi-people"></i>
                {{ $package->guest_capacity }} Tamu
                <span>•</span>
                {{ $package->duration }}
            </p>

        </div>


        <div class="overview-actions">

            <button type="button" class="outline-action">
                <i class="bi bi-heart"></i>
            </button>

            <button type="button" class="outline-action">
                <i class="bi bi-share"></i>
            </button>

            <form action="{{ route('user.cart.add-package', $package->id) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="primary-action">
                    <i class="bi bi-cart-plus"></i>
                    Pilih Paket Ini
                </button>
            </form>

        </div>

    </section>


    {{-- QUICK INFO --}}
    <section class="overview-quick-info">

        <div>
            <i class="bi bi-gem"></i>
            Paket Lengkap
        </div>

        <div>
            <i class="bi bi-people"></i>
            {{ $package->guest_capacity }} Tamu
        </div>

        <div>
            <i class="bi bi-clock"></i>
            {{ $package->duration }}
        </div>

        <div>
            <i class="bi bi-calendar"></i>
            {{ \Carbon\Carbon::parse($package->availability_date)->isoFormat('D MMMM YYYY') }}
        </div>

    </section>


    {{-- GALLERY --}}
    <section class="overview-gallery">

        <div class="gallery-main">
            @php
                $photoUrl = $package->photo ? asset('storage/' . $package->photo) : 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=1400&q=85';
            @endphp
            <img src="{{ $photoUrl }}" alt="{{ $package->name }}">

            <div class="gallery-main-caption">
                <span>Paket Wedding</span>
                <strong>{{ $package->name }}</strong>
            </div>

        </div>


        <div class="gallery-small">
            @foreach($package->vendors->take(4) as $vendor)
                @php
                    $vendorPhoto = $vendor->photos->where('is_cover', true)->first();
                    $vendorPhotoUrl = $vendorPhoto ? asset('storage/' . $vendorPhoto->photo) : 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=400&q=85';
                @endphp
                <div>
                    <img src="{{ $vendorPhotoUrl }}" alt="{{ $vendor->name }}">
                </div>
            @endforeach
        </div>

    </section>


    {{-- CONTENT --}}
    <div class="overview-layout">


        {{-- LEFT --}}
        <div class="overview-main-content">

            <section class="overview-section">

                <h2>
                    Tentang Paket {{ $package->name }}
                </h2>

                <p>
                    Paket lengkap dengan {{ $package->vendors->count() }} vendor terpilih untuk pernikahan Anda dengan kapasitas {{ $package->guest_capacity }} tamu.
                </p>

            </section>


            {{-- VENDOR LIST --}}
            <section class="overview-section">

                <div class="section-heading">

                    <div>
                        <span class="section-kicker">
                            VENDOR TERMASUK
                        </span>

                        <h2>
                            {{ $package->vendors->count() }} Vendor dalam Paket
                        </h2>
                    </div>

                </div>

                @foreach($package->vendors as $vendor)
                    <article class="package-card">

                        <div style="display: flex; gap: 20px; align-items: start;">
                            @php
                                $vendorPhoto = $vendor->photos->where('is_cover', true)->first();
                                $vendorPhotoUrl = $vendorPhoto ? asset('storage/' . $vendorPhoto->photo) : 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=300&q=85';
                            @endphp
                            <img src="{{ $vendorPhotoUrl }}" alt="{{ $vendor->name }}" style="width: 120px; height: 120px; border-radius: 8px; object-fit: cover;">
                            
                            <div style="flex: 1;">
                                <span class="package-label">
                                    {{ $vendor->category->name ?? 'Vendor' }}
                                </span>

                                <h3>
                                    {{ $vendor->name }}
                                </h3>

                                <p>
                                    {{ \Illuminate\Support\Str::limit($vendor->description, 120) }}
                                </p>

                                <div class="package-price">

                                    <div>
                                        <span>Harga Vendor</span>

                                        <strong>
                                            Rp {{ number_format($vendor->price, 0, ',', '.') }}
                                        </strong>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </article>
                @endforeach

            </section>

        </div>


        {{-- RIGHT SIDEBAR --}}
        <aside class="overview-sidebar">

            {{-- BOOKING --}}
            <div class="booking-card">

                <span class="booking-badge">
                    <i class="bi bi-gem"></i>
                    HARGA PAKET
                </span>

                <div class="booking-price">
                    <span>Total Paket</span>
                    <strong>Rp {{ number_format($package->price, 0, ',', '.') }}</strong>
                </div>


                <div class="booking-row">

                    <span>
                        <i class="bi bi-shop"></i>
                        Vendor Termasuk
                    </span>

                    <strong>
                        {{ $package->vendors->count() }} Vendor
                    </strong>

                </div>


                <div class="booking-row">

                    <span>
                        <i class="bi bi-people"></i>
                        Kapasitas
                    </span>

                    <strong>
                        {{ $package->guest_capacity }} Tamu
                    </strong>

                </div>


                <div class="booking-row">

                    <span>
                        <i class="bi bi-clock"></i>
                        Durasi
                    </span>

                    <strong>
                        {{ $package->duration }}
                    </strong>

                </div>


                <form action="{{ route('user.cart.add-package', $package->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="booking-primary">
                        <i class="bi bi-cart-plus"></i>
                        Pilih Paket Ini
                    </button>
                </form>


                <a href="{{ route('user.cart') }}" class="booking-secondary">
                    <i class="bi bi-cart"></i>
                    Lihat Keranjang
                </a>


                <small class="booking-note">
                    Semua vendor dalam paket akan masuk ke keranjang
                </small>

            </div>


            {{-- TRUST --}}
            <div class="trust-card">

                <div class="trust-icon">
                    <i class="bi bi-shield-check"></i>
                </div>

                <div>

                    <strong>
                        Paket Terverifikasi
                    </strong>

                    <p>
                        WO PROJECT memastikan kualitas semua vendor dalam paket ini.
                    </p>

                </div>

            </div>

        </aside>

    </div>

</main>
@endsection
