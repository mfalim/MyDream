@extends('layouts.frontend')

@section('title', 'Keranjang Layanan Reservasi — MyDream Organizer')

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Keranjang']]" />
@endsection

@section('content')

    @include('partials.booking-stepper', ['active' => 1])

    <section class="section">
        <div class="container cart-layout" style="display:grid;grid-template-columns:1fr 360px;gap:36px;align-items:start;">

            <div>
                <span class="stage-tag">🛍 Tahap 1 dari 3</span>

                <h1 class="h1" style="font-size:2rem;">
                    Keranjang Layanan Reservasi
                </h1>

                <p class="lede mt-1">
                    Tinjau paket wedding organizer dan vendor pilihan Anda.
                </p>

                {{-- SUCCESS / ERROR --}}
                @if (session('success'))
                    <div class="alert-strip mt-3">
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert-strip mt-3">
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                {{-- EMPTY CART --}}
                @if (!$package && $vendors->isEmpty())

                    <div class="card mt-4" style="padding:50px 30px;text-align:center;">
                        <div style="font-size:2.5rem;">🛒</div>

                        <h2 class="h3 mt-2">
                            Keranjang masih kosong
                        </h2>

                        <p class="muted mt-1">
                            Pilih paket atau vendor terlebih dahulu untuk melanjutkan reservasi.
                        </p>

                        <div class="flex justify-center gap-2 mt-3" style="flex-wrap:wrap;">
                            <a href="{{ route('catalog.index') }}" class="btn btn-dark">
                                Jelajahi Paket
                            </a>

                            <a href="{{ route('vendor.index') }}" class="btn btn-outline">
                                Jelajahi Vendor
                            </a>
                        </div>
                    </div>
                @else
                    {{-- PACKAGE --}}
                    @if ($package)

                        <div class="reserve-item mt-3">

                            @php
                                $coverPhoto = $package->vendors
                                    ->flatMap(fn($vendor) => $vendor->photos)
                                    ->where('is_cover', true)
                                    ->first();

                                $image = $coverPhoto ? asset('storage/' . $coverPhoto->photo) : null;
                            @endphp

                            @if ($image)
                                <img src="{{ $image }}" alt="{{ $package->name }}">
                            @else
                                <div
                                    style="
                                        width:140px;
                                        min-height:140px;
                                        background:var(--paper-dark);
                                        display:flex;
                                        align-items:center;
                                        justify-content:center;
                                        border-radius:var(--radius-md);
                                        color:var(--muted);
                                    ">
                                    Foto belum tersedia
                                </div>
                            @endif

                            <div>

                                <div class="top-row">
                                    <div>
                                        <span class="badge badge-dark mb-1">
                                            Paket Utama
                                        </span>

                                        @if ($package->event_period)
                                            <p class="category">
                                                {{ $package->event_period }}
                                            </p>
                                        @endif

                                        <h3>
                                            {{ $package->name }}
                                        </h3>
                                    </div>
                                </div>

                                <p class="desc">
                                    Paket dengan kapasitas hingga
                                    {{ $package->guest_capacity ?? '-' }} tamu.
                                </p>

                                <div class="feature-pills">

                                    @foreach ($package->vendors as $vendor)
                                        <span class="feature-pill">
                                            {{ $vendor->name }}
                                        </span>
                                    @endforeach

                                    @if ($package->duration)
                                        <span class="feature-pill">
                                            {{ $package->duration_label }}
                                        </span>
                                    @endif

                                </div>

                                <div class="bottom-row">

                                    <div>
                                        <p class="price-label">
                                            Harga Paket
                                        </p>

                                        <p class="price-value">
                                            Rp {{ number_format($package->price, 0, ',', '.') }}
                                        </p>
                                    </div>

                                </div>

                            </div>

                            <form action="{{ route('user.cart.clear') }}" method="POST" style="align-self:flex-start;">
                                @csrf

                                <button type="submit" class="icon-btn" aria-label="Hapus paket">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="1.6">
                                        <path
                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.682-.107 1.022-.166m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </form>

                        </div>

                    @endif


                    {{-- CUSTOM VENDORS --}}
                    @if ($cart['type'] === 'custom' && $vendors->isNotEmpty())

                        <div class="mt-3">

                            <div class="flex justify-between" style="align-items:center;">

                                <div>
                                    <span class="stage-tag">
                                        Vendor Custom
                                    </span>

                                    <h2 class="h3 mt-1">
                                        Vendor Pilihan Anda
                                    </h2>
                                </div>

                            </div>

                            @foreach ($vendors as $vendor)
                                @php
                                    $coverPhoto = $vendor->photos->where('is_cover', true)->first();

                                    $image = $coverPhoto ? asset('storage/' . $coverPhoto->photo) : null;
                                @endphp

                                <div class="reserve-item mt-2">

                                    @if ($image)
                                        <img src="{{ $image }}" alt="{{ $vendor->name }}">
                                    @else
                                        <div
                                            style="
                                                width:140px;
                                                min-height:140px;
                                                background:var(--paper-dark);
                                                display:flex;
                                                align-items:center;
                                                justify-content:center;
                                                border-radius:var(--radius-md);
                                                color:var(--muted);
                                            ">
                                            Foto belum tersedia
                                        </div>
                                    @endif

                                    <div>

                                        <div class="top-row">

                                            <div>

                                                @if ($vendor->category)
                                                    <span class="badge badge-dark mb-1">
                                                        {{ $vendor->category->name }}
                                                    </span>
                                                @endif

                                                <h3>
                                                    {{ $vendor->name }}
                                                </h3>

                                            </div>

                                        </div>

                                        @if ($vendor->address)
                                            <p class="category">
                                                {{ $vendor->address }}
                                            </p>
                                        @endif

                                        @if ($vendor->description)
                                            <p class="desc">
                                                {{ $vendor->description }}
                                            </p>
                                        @endif

                                        <div class="bottom-row">

                                            <div>
                                                <p class="price-label">
                                                    Biaya Vendor
                                                </p>

                                                <p class="price-value">
                                                    Rp {{ number_format($vendor->price, 0, ',', '.') }}
                                                </p>
                                            </div>

                                        </div>

                                    </div>

                                    <form action="{{ route('user.cart.remove', $vendor->id) }}" method="POST"
                                        style="align-self:flex-start;">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="icon-btn" aria-label="Hapus vendor">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.6">
                                                <path
                                                    d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.682-.107 1.022-.166m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>

                                    </form>

                                </div>
                            @endforeach

                        </div>

                    @endif


                    {{-- ADD VENDOR --}}
                    <a href="{{ route('vendor.index') }}" class="add-vendor-btn"
                        style="display:flex;text-decoration:none;">
                        <span style="font-size:1.1rem;">+</span>
                        Tambah Vendor Rekanan Lainnya
                    </a>


                    {{-- GUARANTEE --}}
                    <div class="mt-4"
                        style="
                            background:var(--forest-soft);
                            border:1px solid var(--forest-line);
                            border-radius:var(--radius-lg);
                            padding:36px;
                        ">

                        <p class="eyebrow" style="color:var(--forest);">
                            <span style="background:var(--forest);"></span>
                            Jaminan Kualitas MyDream
                        </p>

                        <h2 class="h2 mt-2" style="max-width:26em;">
                            Mengapa Memilih Reservasi Melalui MyDream?
                        </h2>

                        <div class="grid grid-3 mt-3">

                            <div class="icon-feature left">
                                <div class="icon-circle" style="background:var(--paper);color:var(--forest);">
                                    ✓
                                </div>

                                <h4>Pendampingan Khusus</h4>

                                <p>
                                    Satu lead wedding planner berdedikasi
                                    untuk membantu proses reservasi dan persiapan.
                                </p>
                            </div>

                            <div class="icon-feature left">

                                <div class="icon-circle" style="background:var(--paper);color:var(--forest);">
                                    ✓
                                </div>

                                <h4>Transparansi Kontrak</h4>

                                <p>
                                    Setiap biaya vendor ditampilkan secara
                                    transparan tanpa data harga dummy.
                                </p>

                            </div>

                            <div class="icon-feature left">

                                <div class="icon-circle" style="background:var(--paper);color:var(--forest);">
                                    ✓
                                </div>

                                <h4>Vendor Terdaftar</h4>

                                <p>
                                    Vendor yang tampil berasal dari database
                                    MyDream.
                                </p>

                            </div>

                        </div>

                    </div>

                @endif

            </div>


            {{-- SUMMARY --}}
            <aside>

                <div class="summary-card">

                    <h3>
                        Ringkasan Biaya Reservasi
                    </h3>

                    <div class="summary-row">
                        <span>
                            {{ $package ? 'Paket' : 'Vendor Custom' }}
                        </span>

                        <span>
                            Rp {{ number_format($totalPrice, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="summary-row">
                        <span>
                            Jumlah Item
                        </span>

                        <span>
                            {{ $package ? 1 : $vendors->count() }}
                        </span>
                    </div>

                    <div class="summary-total">

                        <span class="small muted">
                            Total Estimasi
                        </span>

                        <span class="value">
                            Rp {{ number_format($totalPrice, 0, ',', '.') }}
                        </span>

                    </div>

                    <p class="tiny muted mt-1">
                        ✓ Harga dihitung berdasarkan data database.
                    </p>


                    @if ($totalPrice > 0)
                        <div class="dp-box">

                            <div class="row-top">

                                <strong class="small">
                                    Opsi Uang Muka (DP 30%)
                                </strong>

                                <span class="badge badge-forest">
                                    Reservasi
                                </span>

                            </div>

                            <p class="small muted">
                                Bayar awal untuk mengunci tanggal:
                            </p>

                            <p class="h3" style="color:var(--forest);">
                                Rp {{ number_format($totalPrice * 0.3, 0, ',', '.') }}
                            </p>

                            <p class="tiny muted">
                                Sisa pelunasan 70% mengikuti proses
                                pembayaran reservasi.
                            </p>

                        </div>


                        <a href="{{ route('user.checkout') }}" class="btn btn-dark btn-block mt-3">
                            Lanjut ke Validasi Data &rarr;
                        </a>
                    @endif


                    <ul class="trust-list">

                        <li>
                            🔒
                            Data reservasi diproses melalui sistem MyDream.
                        </li>

                        <li>
                            ✓
                            Harga vendor berasal dari database.
                        </li>

                        <li>
                            ✓
                            Detail reservasi dikonfirmasi sebelum pembayaran.
                        </li>

                    </ul>

                </div>


                <div class="help-box">

                    <span style="font-size:1.3rem;">
                        💬
                    </span>

                    <div>

                        <p class="small">
                            Ada pertanyaan seputar paket?
                        </p>

                        <a href="https://wa.me/6281233779967" class="link-arrow">
                            Konsultasi Gratis via WhatsApp
                        </a>

                    </div>

                </div>

            </aside>

        </div>
    </section>

@endsection


@push('styles')
    <style>
        @media (max-width: 900px) {
            .cart-layout {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
@endpush
