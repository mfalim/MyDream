@extends('user.layouts.app')

@section('title', 'Detail Riwayat Pembelian')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/wo-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/riwayat.css') }}">
@endpush

@section('content')
<main class="wo-page history-page">
    <a class="wo-link history-back" href="{{ route('user.history.index') }}"><i class="bi bi-arrow-left"></i> Kembali ke Riwayat</a>
    <header class="history-heading">
        <span class="wo-eyebrow"><i class="bi bi-circle-fill"></i> Detail Pembelian</span>
        <h1>{{ $booking->package?->name ?? 'Pernikahan Custom' }}</h1>
        <p>Dibuat {{ $booking->created_at?->format('d F Y') }} <span>&bull;</span> <span class="wo-badge wo-badge-green">Booking Disetujui</span></p>
    </header>

    <div class="history-detail-grid">
        <section class="wo-card">
            <div class="wo-section-head"><div><span class="wo-eyebrow">Rincian Acara</span><h2>Acara dan Vendor</h2></div><span class="wo-badge wo-badge-neutral">{{ $booking->events->count() }} acara</span></div>
            @forelse($booking->events as $event)
                <div class="history-booking-event history-detail-event">
                    <div class="history-event-head"><div><strong>{{ $event->name }}</strong><span>{{ $event->event_date?->format('d F Y') }}</span></div><span class="wo-badge {{ $event->status === 'completed' ? 'wo-badge-green' : 'wo-badge-neutral' }}">{{ $event->status === 'completed' ? 'Selesai' : ucfirst($event->status) }}</span></div>
                    @forelse($event->schedules as $schedule)
                        @if($schedule->vendor)<div class="history-detail-vendor"><div><strong>{{ $schedule->vendor->name }}</strong><span>{{ $schedule->vendor->category?->name ?? 'Vendor' }}</span></div><strong>Rp {{ number_format((float) $schedule->price, 0, ',', '.') }}</strong></div>@endif
                    @empty
                        <p class="history-muted">Belum ada vendor yang disetujui pada acara ini.</p>
                    @endforelse
                    @if($event->status === 'completed' && $event->reviews->isEmpty())
                        <a class="wo-btn wo-btn-dark wo-btn-sm history-event-review" href="{{ route('user.reviews.index', ['event' => $event->id]) }}">Beri Ulasan untuk Acara <i class="bi bi-star"></i></a>
                    @elseif($event->reviews->isNotEmpty())
                        <span class="wo-badge wo-badge-green history-event-review">Acara sudah diulas</span>
                    @endif
                </div>
            @empty
                <p class="history-muted">Booking ini belum memiliki rincian acara.</p>
            @endforelse
            <div class="history-total"><span>Total booking</span><strong>Rp {{ number_format((float) $booking->total_price, 0, ',', '.') }}</strong></div>
        </section>

        <aside class="wo-card history-venue">
            <span class="wo-eyebrow">Informasi Booking</span>
            <h2>{{ $booking->venue_name }}</h2>
            <p><i class="bi bi-geo-alt"></i> {{ $booking->venue_address }}<br>{{ $booking->venue_city }}, {{ $booking->venue_province }}</p>
            <div class="history-venue-meta"><span>Jumlah tamu</span><strong>{{ number_format($booking->guest_count, 0, ',', '.') }} orang</strong></div>
            @if($booking->package)<div class="history-venue-meta"><span>Paket</span><strong>{{ $booking->package->name }}</strong></div>@endif
            <div class="history-venue-meta"><span>Status booking</span><span class="wo-badge wo-badge-green">Disetujui</span></div>
        </aside>
    </div>
</main>
@endsection
