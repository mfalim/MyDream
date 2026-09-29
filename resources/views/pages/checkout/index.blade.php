@extends('layouts.frontend')

@section('title', 'Checkout — MyDream Organizer')

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Keranjang', 'href' => route('cart.index')], ['label' => 'Checkout']]" />
@endsection

@section('content')

    @include('partials.booking-stepper', ['active' => 2])

    @php
        $dp = round($totalPrice * 0.3);

        $guestCapacity = $package?->guest_capacity ?? 1;

        $clientName = $client
            ? trim(($client->groom_name ?? '') . ' & ' . ($client->bride_name ?? ''))
            : 'Data pemesan belum lengkap';
    @endphp

    <section class="section">
        <div class="container">

            {{-- INFO --}}
            <div class="flex justify-between flex-wrap gap-2 mb-3" style="align-items:center;">

                <p class="small muted">
                    <span style="color:var(--gold);">ⓘ</span>
                    Lengkapi data acara untuk melanjutkan proses booking.
                </p>

                <div class="flex gap-2 small muted" style="align-items:center;">
                    <span>
                        Tipe:
                        <strong class="text-ink">
                            {{ $cart['type'] === 'package' ? 'Paket' : 'Custom Vendor' }}
                        </strong>
                    </span>

                    <a href="{{ route('cart.index') }}" class="link-arrow">
                        ← Kembali ke Keranjang
                    </a>
                </div>
            </div>

            <div class="checkout-layout" style="display:grid;grid-template-columns:1fr 360px;gap:36px;align-items:start;">

                {{-- LEFT --}}
                <div>

                    <span class="stage-tag">
                        Langkah 02 · Validasi Data & Acara
                    </span>

                    <h1 class="h1" style="font-size:1.9rem;">
                        Checkout Booking
                    </h1>

                    <p class="lede mt-1">
                        Lengkapi data venue dan acara untuk membuat booking.
                    </p>

                    {{-- CLIENT --}}
                    <div class="form-panel mt-3"
                        style="display:flex;align-items:center;gap:14px;justify-content:space-between;flex-wrap:wrap;">

                        <div class="flex gap-2" style="align-items:center;">

                            <span
                                style="
                                width:46px;
                                height:46px;
                                border-radius:50%;
                                background:var(--ink);
                                color:#F4EEE2;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                font-family:var(--font-serif);
                                font-weight:600;
                            ">
                                {{ strtoupper(substr($client?->groom_name ?? 'C', 0, 1)) }}
                            </span>

                            <div>
                                <p>
                                    <strong>{{ $clientName }}</strong>
                                </p>

                                @if ($client)
                                    <p class="small muted">
                                        Data pemesan terdaftar
                                    </p>
                                @else
                                    <p class="small text-maroon">
                                        Lengkapi profil terlebih dahulu
                                    </p>
                                @endif
                            </div>

                        </div>

                        <a href="{{ route('client.profile') }}" class="badge badge-outline">
                            Edit Profil
                        </a>

                    </div>

                    {{-- ERROR --}}
                    @if ($errors->any())
                        <div class="form-panel mt-3" style="border-color:var(--maroon);">

                            <strong class="text-maroon">
                                Periksa kembali data berikut:
                            </strong>

                            <ul class="small mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>

                        </div>
                    @endif

                    @if (session('error'))
                        <div class="form-panel mt-3" style="border-color:var(--maroon);">

                            <p class="text-maroon">
                                {{ session('error') }}
                            </p>

                        </div>
                    @endif

                    {{-- FORM --}}
                    <form id="checkoutForm" action="{{ route('checkout.store') }}" method="POST">

                        @csrf

                        {{-- EVENT --}}
                        <div class="form-panel mt-3">

                            <h2>
                                <span class="step-num">1</span>
                                Detail Waktu Acara
                            </h2>

                            <div class="form-grid">

                                <div class="field">

                                    <label>
                                        Tanggal Pelaksanaan Acara
                                    </label>

                                    <input type="date" name="event_date" value="{{ old('event_date') }}"
                                        min="{{ now()->addDay()->format('Y-m-d') }}" required>

                                </div>

                                <div class="field">

                                    <label>
                                        Estimasi Jumlah Tamu
                                    </label>

                                    <input type="number" name="guest_count"
                                        value="{{ old('guest_count', $guestCapacity) }}" min="10" max="10000"
                                        required>

                                </div>

                            </div>

                        </div>

                        {{-- VENUE --}}
                        <div class="form-panel">

                            <h2>
                                <span class="step-num">2</span>
                                Informasi Venue
                            </h2>

                            <div class="form-grid">

                                <div class="field span-2">

                                    <label>
                                        Nama Venue
                                    </label>

                                    <input type="text" name="venue_name" value="{{ old('venue_name') }}"
                                        placeholder="Contoh: Hotel Mulia Senayan" required>

                                </div>

                                <div class="field span-2">

                                    <label>
                                        Alamat Lengkap Venue
                                    </label>

                                    <textarea name="venue_address" rows="3" placeholder="Alamat lengkap venue" required>{{ old('venue_address') }}</textarea>

                                </div>

                                <div class="field">

                                    <label>
                                        Kota
                                    </label>

                                    <input type="text" name="venue_city" value="{{ old('venue_city') }}"
                                        placeholder="Contoh: Jember" required>

                                </div>

                                <div class="field">

                                    <label>
                                        Provinsi
                                    </label>

                                    <input type="text" name="venue_province" value="{{ old('venue_province') }}"
                                        placeholder="Contoh: Jawa Timur" required>

                                </div>

                            </div>

                        </div>

                        {{-- ORDER DETAIL --}}
                        <div class="form-panel">

                            <h2>
                                <span class="step-num">3</span>
                                Pilihan Anda
                            </h2>

                            @if ($package)

                                <div
                                    style="
                                    border:1px solid var(--line);
                                    border-radius:var(--radius-md);
                                    padding:16px;
                                ">

                                    <div class="flex justify-between gap-2" style="align-items:flex-start;">

                                        <div>
                                            <strong>
                                                {{ $package->name }}
                                            </strong>

                                            <p class="small muted mt-1">
                                                {{ $package->vendors->count() }}
                                                vendor dalam paket
                                            </p>
                                        </div>

                                        <strong>
                                            Rp {{ number_format($package->price, 0, ',', '.') }}
                                        </strong>

                                    </div>

                                    @if ($package->vendors->isNotEmpty())

                                        <div class="mt-2">

                                            @foreach ($package->vendors as $vendor)
                                                <span class="feature-pill">
                                                    {{ $vendor->name }}
                                                </span>
                                            @endforeach

                                        </div>

                                    @endif

                                </div>
                            @elseif ($vendors->isNotEmpty())
                                @foreach ($vendors as $vendor)
                                    <div
                                        style="
                                        border:1px solid var(--line);
                                        border-radius:var(--radius-md);
                                        padding:12px 14px;
                                        margin-bottom:8px;
                                    ">

                                        <div class="flex justify-between">

                                            <div>
                                                <strong>
                                                    {{ $vendor->name }}
                                                </strong>

                                                @if ($vendor->category)
                                                    <p class="tiny muted">
                                                        {{ $vendor->category->name }}
                                                    </p>
                                                @endif
                                            </div>

                                            <strong>
                                                Rp {{ number_format($vendor->price, 0, ',', '.') }}
                                            </strong>

                                        </div>

                                    </div>
                                @endforeach

                            @endif

                        </div>

                    </form>

                </div>

                {{-- RIGHT --}}
                <aside>

                    <div class="summary-card">

                        <p class="tiny faint" style="text-transform:uppercase;">
                            Ikhtisar Pesanan
                        </p>

                        <h3>
                            Ringkasan Pesanan
                        </h3>

                        <div class="mt-2" style="border-top:1px solid var(--line);padding-top:12px;">

                            @if ($package)
                                <div class="flex justify-between">
                                    <strong class="small">
                                        {{ $package->name }}
                                    </strong>

                                    <span class="small">
                                        Rp {{ number_format($package->price, 0, ',', '.') }}
                                    </span>
                                </div>

                                <p class="tiny muted">
                                    {{ $package->vendors->count() }} vendor
                                    · {{ $package->guest_capacity }} tamu
                                </p>
                            @else
                                <div class="flex justify-between">

                                    <strong class="small">
                                        Custom Vendor
                                    </strong>

                                    <span class="small">
                                        {{ $vendors->count() }} vendor
                                    </span>

                                </div>
                            @endif

                        </div>

                        <div class="summary-row mt-2">
                            <span>Subtotal</span>

                            <span>
                                Rp {{ number_format($totalPrice, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="summary-row">

                            <span>
                                Administrasi
                            </span>

                            <span class="text-forest">
                                Gratis
                            </span>

                        </div>

                        <div class="summary-total">

                            <span class="small muted">
                                Total Booking
                            </span>

                            <span class="value">
                                Rp {{ number_format($totalPrice, 0, ',', '.') }}
                            </span>

                        </div>

                        <p class="tiny faint" style="text-align:right;">
                            DP 30%:
                            Rp {{ number_format($dp, 0, ',', '.') }}
                        </p>

                        <label class="check-line mt-3">

                            <input type="checkbox" required>

                            <span>
                                Saya menyatakan seluruh data yang diberikan
                                sudah benar dan menyetujui Ketentuan Layanan
                                MyDream.
                            </span>

                        </label>

                        <button type="submit" form="checkoutForm" class="btn btn-dark btn-block mt-3">
                            Konfirmasi & Lanjut Pembayaran →
                        </button>

                        <p class="tiny muted text-center mt-1">
                            🛡 Reservasi akan dibuat setelah data dikonfirmasi.
                        </p>

                    </div>

                </aside>

            </div>
        </div>
    </section>

@endsection

@push('styles')
    <style>
        @media (max-width: 900px) {
            .checkout-layout {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
@endpush
