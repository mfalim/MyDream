@extends('layouts.frontend')

@section('title', 'Pembayaran Tagihan Reservasi — MyDream Organizer')

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Checkout', 'href' => route('checkout.index')], ['label' => 'Pembayaran']]" />
@endsection

@section('content')

    @php
        $totalPrice = (float) $booking->total_price;

        $dp = (int) round($totalPrice * 0.3);
        $lunas = (int) round($totalPrice * 0.97);
        $cicilan = (int) round($totalPrice / 6);
        $savings = (int) ($totalPrice - $lunas);

        $clientName = $booking->client ? $booking->client->groom_name . ' & ' . $booking->client->bride_name : '-';

        $packageName = $booking->package?->name ?? 'Custom Vendor';

        $eventDate = $booking->event?->event_date
            ? \Carbon\Carbon::parse($booking->event->event_date)->translatedFormat('d F Y')
            : '-';
    @endphp

    @include('partials.booking-stepper', ['active' => 3])

    {{-- PAYMENT DEADLINE --}}
    <section class="section-sm">
        <div class="container">

            <div class="alert-strip">

                <div class="flex gap-2" style="align-items:center;">

                    <span style="font-size:1.4rem;">⏱</span>

                    <div>
                        <p class="small">
                            <strong>Batas Waktu Pembayaran</strong>
                            &middot;
                            Reservasi Terkunci Sementara
                        </p>

                        <p class="tiny muted">
                            Selesaikan pembayaran untuk mengamankan booking dan vendor yang telah dipilih.
                        </p>
                    </div>

                </div>

                <div class="countdown-box">

                    <span class="tiny"
                        style="
                            text-transform:uppercase;
                            font-weight:700;
                            color:var(--maroon);
                        ">
                        Sisa Waktu
                    </span>

                    <div class="countdown-clock" data-countdown="86380">
                        <span class="seg" data-seg="h">23</span>
                        <span class="colon">:</span>
                        <span class="seg" data-seg="m">59</span>
                        <span class="colon">:</span>
                        <span class="seg" data-seg="s">40</span>
                    </div>

                </div>

            </div>

        </div>
    </section>

    {{-- MAIN --}}
    <section class="section" style="padding-top:24px;">

        <div class="container payment-layout"
            style="
                display:grid;
                grid-template-columns:1fr 360px;
                gap:36px;
                align-items:start;
            ">

            {{-- LEFT --}}
            <div>

                <p class="tiny faint" style="text-transform:uppercase;">
                    Reservasi #MYD-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}
                    &middot;
                    Tahap Akhir Konfirmasi
                </p>

                <h1 class="h1" style="font-size:1.9rem;">
                    Pembayaran Tagihan Reservasi
                </h1>

                <p class="lede mt-1">
                    Pilih skema pembayaran dan lanjutkan ke metode transaksi
                    yang tersedia untuk mengamankan reservasi Anda.
                </p>

                <span class="badge badge-forest mt-2">
                    🛡 Pembayaran Aman & Terenkripsi
                </span>


                {{-- PAYMENT SCHEME --}}
                <div class="form-panel mt-3">

                    <h2>
                        <span class="step-num">1</span>
                        Skema Komitmen Pembayaran

                        <span class="panel-tag badge badge-outline">
                            Fleksibel & Terjadwal
                        </span>
                    </h2>

                    <div class="choice-cards mt-2">

                        {{-- DP --}}
                        <label class="choice-card" onclick="selectPayment('dp', this)">

                            <input type="radio" name="skema" value="dp" checked>

                            <span class="badge badge-gold tag">
                                Rekomendasi
                            </span>

                            <p>
                                <strong>DP 30%</strong>
                            </p>

                            <p class="amount">
                                Rp {{ number_format($dp, 0, ',', '.') }}
                            </p>

                            <p class="note">
                                Kunci jadwal dan vendor pilihan Anda.
                            </p>

                            <p class="tiny faint mt-1">
                                Pelunasan sebelum hari H
                            </p>

                        </label>


                        {{-- LUNAS --}}
                        <label class="choice-card" onclick="selectPayment('lunas', this)">

                            <input type="radio" name="skema" value="lunas">

                            <span class="badge badge-forest tag">
                                Diskon 3%
                            </span>

                            <p>
                                <strong>Lunas 100%</strong>
                            </p>

                            <p class="amount">
                                Rp {{ number_format($lunas, 0, ',', '.') }}
                            </p>

                            <p class="note">
                                Bayar penuh dan dapatkan potongan harga.
                            </p>

                            <p class="tiny faint mt-1">
                                Hemat Rp {{ number_format($savings, 0, ',', '.') }}
                            </p>

                        </label>


                        {{-- CICILAN --}}
                        <label class="choice-card" onclick="selectPayment('cicilan', this)">

                            <input type="radio" name="skema" value="cicilan">

                            <span class="badge badge-outline tag">
                                Tanpa Bunga
                            </span>

                            <p>
                                <strong>Cicilan 6x</strong>
                            </p>

                            <p class="amount">
                                Rp {{ number_format($cicilan, 0, ',', '.') }}
                                <span style="font-size:.8rem;">
                                    /bln
                                </span>
                            </p>

                            <p class="note">
                                Atur pembayaran menjadi 6 kali.
                            </p>

                            <p class="tiny faint mt-1">
                                Tenor: 6x Pembayaran
                            </p>

                        </label>

                    </div>

                </div>


                {{-- PAYMENT METHOD --}}
                <div class="form-panel">

                    <h2>
                        <span class="step-num">2</span>
                        Pilihan Metode Pembayaran

                        <span class="panel-tag badge badge-outline">
                            Enkripsi 256-bit
                        </span>
                    </h2>

                    <div class="method-list mt-2">

                        {{-- VA --}}
                        <div class="method-group">

                            <div class="method-head">

                                <span class="icon-circle">
                                    🏦
                                </span>

                                <div>

                                    <strong>
                                        Virtual Account
                                    </strong>

                                    <span class="badge badge-forest">
                                        Verifikasi Otomatis
                                    </span>

                                    <p class="tiny muted">
                                        Konfirmasi otomatis setelah pembayaran berhasil.
                                    </p>

                                </div>

                            </div>

                            <div class="method-options">

                                <label class="method-option">
                                    <input type="radio" name="payment_method" value="bca" checked>

                                    <span class="bank-code">
                                        BCA
                                    </span>

                                    BCA Virtual Account

                                    <span class="free">
                                        Bebas Biaya
                                    </span>
                                </label>


                                <label class="method-option">

                                    <input type="radio" name="payment_method" value="mandiri">

                                    <span class="bank-code">
                                        MANDIRI
                                    </span>

                                    Mandiri Virtual Account

                                    <span class="free">
                                        Bebas Biaya
                                    </span>

                                </label>


                                <label class="method-option">

                                    <input type="radio" name="payment_method" value="bni">

                                    <span class="bank-code">
                                        BNI
                                    </span>

                                    BNI Virtual Account

                                    <span class="free">
                                        Bebas Biaya
                                    </span>

                                </label>


                                <label class="method-option">

                                    <input type="radio" name="payment_method" value="bri">

                                    <span class="bank-code">
                                        BRI
                                    </span>

                                    BRI Virtual Account

                                    <span class="free">
                                        Bebas Biaya
                                    </span>

                                </label>

                            </div>

                        </div>


                        {{-- QRIS --}}
                        <div class="method-simple">

                            <div class="flex gap-2" style="align-items:center;">

                                <span class="icon-circle" style="background:var(--gold);">
                                    📱
                                </span>

                                <div>

                                    <strong>
                                        QRIS & Dompet Digital
                                    </strong>

                                    <span class="badge badge-outline">
                                        E-Wallet
                                    </span>

                                    <p class="tiny muted">
                                        GoPay, OVO, ShopeePay, DANA,
                                        LinkAja & Mobile Banking
                                    </p>

                                </div>

                            </div>

                            <span>&rsaquo;</span>

                        </div>


                        {{-- CARD --}}
                        <div class="method-simple">

                            <div class="flex gap-2" style="align-items:center;">

                                <span class="icon-circle" style="background:var(--maroon);">
                                    💳
                                </span>

                                <div>

                                    <strong>
                                        Kartu Kredit / Debit Online
                                    </strong>

                                    <span class="badge badge-outline">
                                        3D Secure
                                    </span>

                                    <p class="tiny muted">
                                        Visa, Mastercard, JCB dan kartu debit online.
                                    </p>

                                </div>

                            </div>

                            <span>&rsaquo;</span>

                        </div>


                        {{-- PAYLATER --}}
                        <div class="method-simple">

                            <div class="flex gap-2" style="align-items:center;">

                                <span class="icon-circle" style="background:var(--ink);">
                                    🧾
                                </span>

                                <div>

                                    <strong>
                                        Cicilan & PayLater
                                    </strong>

                                    <p class="tiny muted">
                                        Tersedia sesuai metode pembayaran yang didukung.
                                    </p>

                                </div>

                            </div>

                            <span>&rsaquo;</span>

                        </div>

                    </div>

                </div>


                {{-- SECURITY --}}
                <div
                    style="
                        background:var(--forest);
                        color:#EAF1E6;
                        border-radius:var(--radius-lg);
                        padding:26px;
                    ">

                    <p>
                        <strong>
                            MyDream Safe Payment
                        </strong>
                    </p>

                    <p class="small mt-1" style="color:#C9D6C4;">
                        Pembayaran Anda diproses melalui kanal pembayaran
                        yang aman. Status booking akan diperbarui setelah
                        pembayaran berhasil dikonfirmasi.
                    </p>

                    <div class="flex gap-2 mt-2 flex-wrap tiny">

                        <span>
                            ✓ Proteksi Transaksi
                        </span>

                        <span>
                            ✓ Konfirmasi Pembayaran
                        </span>

                        <span>
                            ✓ Booking Tercatat
                        </span>

                    </div>

                </div>

            </div>


            {{-- RIGHT SUMMARY --}}
            <aside>

                <div class="summary-card">

                    <div class="flex justify-between" style="align-items:center;">

                        <p class="tiny faint" style="text-transform:uppercase;">
                            Kode Reservasi 📋
                        </p>

                        <span class="badge badge-forest">
                            Booking Dibuat
                        </span>

                    </div>

                    <p class="h3">
                        #MYD-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}
                    </p>


                    {{-- BOOKING --}}
                    <div class="mt-2"
                        style="
                            border-top:1px solid var(--line);
                            padding-top:12px;
                        ">

                        <p class="small">
                            <strong>
                                {{ $packageName }}
                            </strong>
                        </p>

                        <p class="tiny muted">
                            📍 {{ $booking->venue_name }}
                        </p>

                        <p class="tiny muted">
                            📅 {{ $eventDate }}
                        </p>

                        <p class="tiny faint">
                            {{ number_format($booking->guest_count, 0, ',', '.') }}
                            tamu
                        </p>

                    </div>


                    {{-- PAYMENT SUMMARY --}}
                    <p class="tiny faint mt-3" style="text-transform:uppercase;">
                        Rincian Pembayaran
                    </p>

                    <div class="summary-row">

                        <span>
                            Skema
                        </span>

                        <span id="summaryScheme">
                            DP 30%
                        </span>

                    </div>

                    <div class="summary-row">

                        <span>
                            Total Booking
                        </span>

                        <span>
                            Rp {{ number_format($totalPrice, 0, ',', '.') }}
                        </span>

                    </div>

                    <div class="summary-row">

                        <span>
                            Pembayaran
                        </span>

                        <span id="summaryAmount">
                            Rp {{ number_format($dp, 0, ',', '.') }}
                        </span>

                    </div>

                    <div class="summary-row">

                        <span>
                            Biaya Admin
                        </span>

                        <span class="text-forest">
                            Gratis
                        </span>

                    </div>


                    <div class="summary-total">

                        <span class="small muted">
                            Total yang Dibayar
                        </span>

                        <span class="value" id="totalPayment">
                            Rp {{ number_format($dp, 0, ',', '.') }}
                        </span>

                    </div>

                    <p class="tiny faint" style="text-align:right;">
                        IDR Net
                    </p>


                    {{-- DP INFO --}}
                    <div class="dp-box mt-2">

                        <p class="tiny">
                            Pembayaran awal sebesar
                            <strong>
                                Rp {{ number_format($dp, 0, ',', '.') }}
                            </strong>
                            akan digunakan sebagai pembayaran DP booking.
                        </p>

                    </div>


                    {{-- PAY BUTTON --}}
                    <button type="button" id="payButton" class="btn btn-dark btn-block mt-3"
                        onclick="processPayment()">
                        🔒 Bayar Sekarang ·
                        Rp {{ number_format($dp, 0, ',', '.') }}
                    </button>

                    <p class="tiny muted text-center mt-1">
                        🛡 Pembayaran Aman &middot; Konfirmasi Otomatis
                    </p>

                    <p class="tiny faint text-center">
                        Step 3 of 3 · Menunggu Pembayaran
                    </p>

                </div>


                {{-- HELP --}}
                <div class="help-box">

                    <span style="font-size:1.3rem;">
                        💬
                    </span>

                    <div>

                        <p class="small">
                            Kendala pembayaran?
                        </p>

                        <a href="https://wa.me/6281233779967" class="link-arrow">
                            Hubungi Concierge MyDream
                        </a>

                    </div>

                </div>

            </aside>

        </div>

    </section>

@endsection


@push('styles')
    <style>
        .payment-option {
            cursor: pointer;
            transition:
                border-color .2s ease,
                background .2s ease,
                transform .2s ease;
        }

        .payment-option:hover {
            transform: translateY(-2px);
        }

        .payment-option.selected {
            border-color: var(--forest) !important;
            background: var(--cream-soft);
        }

        .method-option {
            cursor: pointer;
        }

        @media (max-width: 900px) {

            .payment-layout {
                grid-template-columns: 1fr !important;
            }

        }
    </style>
@endpush


@push('scripts')
    <script>
        let selectedPayment = 'dp';

        function selectPayment(type) {
            selectedPayment = type;

            document.querySelectorAll('.payment-option').forEach(el => {
                el.classList.remove('selected');
            });

            event.currentTarget.classList.add('selected');
        }

        function processPayment() {
            window.location.href = "{{ route('payment.success', $booking->id) }}";
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.payment-option').classList.add('selected');
        });
    </script>
@endpush
