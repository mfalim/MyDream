@extends('layouts.frontend')

@section('title', 'Pembayaran Berhasil — MyDream Organizer')

@php
    $order = ['id' => 'MYD-2026-8942', 'total' => '32.287.500', 'date' => '21 September 2026, 14.32 WIB'];
@endphp

@section('content')

    @include('partials.booking-stepper', ['active' => 3])

    <section class="section text-center">
        <div class="container" style="max-width:520px;">
            <div style="width:76px;height:76px;border-radius:50%;background:var(--forest-soft);border:1px solid var(--forest-line);display:flex;align-items:center;justify-content:center;margin:0 auto;">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="var(--forest)" stroke-width="1.8"><path d="M4.5 12.75l6 6 9-13.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>

            <h1 class="h1 mt-3" style="font-size:2rem;">Pembayaran Berhasil!</h1>
            <p class="lede mt-1" style="margin-left:auto;margin-right:auto;">
                Terima kasih, uang muka reservasimu sudah kami terima. Tim MyDream akan menghubungi kamu
                untuk penjadwalan rapat pemantapan vendor.
            </p>

            <div class="summary-card mt-4" style="text-align:left;">
                <div class="summary-row"><span>Kode Reservasi</span><span><strong>{{ $order['id'] }}</strong></span></div>
                <div class="summary-row"><span>Tanggal Pembayaran</span><span>{{ $order['date'] }}</span></div>
                <div class="summary-total"><span class="small muted">Total Dibayar</span><span class="value">Rp {{ $order['total'] }}</span></div>
            </div>

            <div class="flex gap-2 mt-3" style="justify-content:center;flex-wrap:wrap;">
                <a href="{{ route('event.index') ?? '#' }}" class="btn btn-dark">Lihat Status Acara</a>
                <a href="{{ url('/') }}" class="btn btn-outline">Kembali ke Beranda</a>
            </div>
        </div>
    </section>

@endsection
