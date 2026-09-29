@extends('layouts.frontend')

@section('title', 'Pembayaran Berhasil — MyDream Organizer')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Pembayaran', 'href' => route('payment.index', $booking->id)],
        ['label' => 'Pembayaran Berhasil'],
    ]" />
@endsection

@section('content')

    @include('partials.booking-stepper', ['active' => 3])

    <section class="section text-center">
        <div class="container" style="max-width:560px;">

            {{-- SUCCESS ICON --}}
            <div
                style="
                    width:76px;
                    height:76px;
                    border-radius:50%;
                    background:var(--forest-soft);
                    border:1px solid var(--forest-line);
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    margin:0 auto;
                ">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="var(--forest)" stroke-width="1.8">
                    <path d="M4.5 12.75l6 6 9-13.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>

            <h1 class="h1 mt-3" style="font-size:2rem;">
                Pembayaran Berhasil!
            </h1>

            <p class="lede mt-1" style="margin-left:auto;margin-right:auto;">
                Terima kasih. Uang muka reservasi kamu sudah diterima.
                Tim MyDream akan menghubungi kamu untuk proses persiapan acara berikutnya.
            </p>

            {{-- BOOKING SUMMARY --}}
            <div class="summary-card mt-4" style="text-align:left;">

                <div class="summary-row">
                    <span>Kode Reservasi</span>
                    <span>
                        <strong>#MYD-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</strong>
                    </span>
                </div>

                <div class="summary-row">
                    <span>Status Booking</span>
                    <span>
                        <span class="badge badge-forest">
                            Pembayaran Diterima
                        </span>
                    </span>
                </div>

                <div class="summary-row">
                    <span>Mempelai</span>
                    <span>
                        {{ $booking->client->groom_name }}
                        &
                        {{ $booking->client->bride_name }}
                    </span>
                </div>

                <div class="summary-row">
                    <span>Tanggal Acara</span>
                    <span>
                        {{ \Carbon\Carbon::parse($booking->event_date)->translatedFormat('d F Y') }}
                    </span>
                </div>

                <div class="summary-row">
                    <span>Total Booking</span>
                    <span>
                        Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                    </span>
                </div>

                <div class="summary-total">
                    <span class="small muted">
                        DP Dibayarkan
                    </span>

                    <span class="value">
                        Rp {{ number_format($dp, 0, ',', '.') }}
                    </span>
                </div>

            </div>

            {{-- NEXT STEP --}}
            <div class="mt-3"
                style="
                    background:var(--cream-soft);
                    border:1px solid var(--line);
                    border-radius:var(--radius-md);
                    padding:16px;
                    text-align:left;
                ">
                <p class="small">
                    <strong>Langkah berikutnya</strong>
                </p>

                <p class="tiny muted mt-1">
                    Tim MyDream akan melakukan konfirmasi vendor,
                    jadwal, dan persiapan rundown acara melalui akun kamu.
                </p>
            </div>

            {{-- ACTION --}}
            <div class="flex gap-2 mt-3" style="justify-content:center;flex-wrap:wrap;">
                <a href="{{ route('event.index') }}" class="btn btn-dark">
                    Lihat Status Acara
                </a>

                <a href="{{ url('/') }}" class="btn btn-outline">
                    Kembali ke Beranda
                </a>
            </div>

        </div>
    </section>

@endsection
