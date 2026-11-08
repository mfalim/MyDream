@extends('user.layouts.app')

@section('title', 'Riwayat Pembelian')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/wo-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/riwayat.css') }}">
@endpush

@section('content')
<main class="wo-page history-page">
    <header class="history-heading">
        <span class="wo-eyebrow"><i class="bi bi-circle-fill"></i> Pesanan Disetujui</span>
        <h1>Riwayat Pembelian</h1>
        <p>Daftar booking Anda yang telah disetujui beserta rincian acara dan vendor.</p>
    </header>

    <section class="wo-card history-filter-card">
        <form method="GET" action="{{ route('user.history.index') }}" class="history-filters">
            <label class="history-field history-search"><span>Nama acara</span><input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama acara"></label>
            <label class="history-field"><span>Dari tanggal</span><input type="date" name="from" value="{{ request('from') }}"></label>
            <label class="history-field"><span>Sampai tanggal</span><input type="date" name="to" value="{{ request('to') }}"></label>
            <label class="history-field"><span>Vendor</span><select name="vendor"><option value="">Semua vendor</option>@foreach($vendors as $vendor)<option value="{{ $vendor->id }}" @selected((string) request('vendor') === (string) $vendor->id)>{{ $vendor->name }}</option>@endforeach</select></label>
            <div class="history-filter-actions"><button class="wo-btn wo-btn-dark" type="submit"><i class="bi bi-search"></i> Cari</button><a class="wo-btn wo-btn-outline" href="{{ route('user.history.index') }}">Reset</a></div>
        </form>
    </section>

    @if($bookings->isEmpty())
        <section class="wo-card history-empty"><span class="history-empty-icon"><i class="bi bi-receipt"></i></span><h2>Belum ada pembelian disetujui</h2><p>Booking akan tampil di sini setelah statusnya disetujui.</p></section>
    @else
        <div class="history-list">
            @foreach($bookings as $booking)
                <article class="wo-card history-card">
                    <div class="history-card-top">
                        <div><span class="wo-eyebrow">Dipesan {{ $booking->created_at?->format('d M Y') }}</span><h2>{{ $booking->package?->name ?? 'Pernikahan Custom' }}</h2></div>
                        <span class="wo-badge wo-badge-green">Disetujui</span>
                    </div>
                    <div class="history-meta"><span><i class="bi bi-geo-alt"></i> {{ $booking->venue_name }}, {{ $booking->venue_city }}</span><span><i class="bi bi-calendar-event"></i> {{ $booking->events->count() }} acara</span></div>
                    @forelse($booking->events as $event)
                        <div class="history-booking-event">
                            <div class="history-event-head"><strong>{{ $event->name }}</strong><span>{{ $event->event_date?->format('d M Y') }}</span></div>
                            @if($event->schedules->isNotEmpty())
                                <ul class="history-vendor-list">@foreach($event->schedules as $schedule)@if($schedule->vendor)<li><span>{{ $schedule->vendor->name }}</span><strong>Rp {{ number_format((float) $schedule->price, 0, ',', '.') }}</strong></li>@endif @endforeach</ul>
                            @else
                                <p class="history-muted">Belum ada vendor yang disetujui untuk acara ini.</p>
                            @endif
                            @if($event->status === 'completed' && $event->reviews->isEmpty())
                                <a class="wo-btn wo-btn-dark wo-btn-sm history-event-review" href="{{ route('user.reviews.index', ['event' => $event->id]) }}">Beri Ulasan untuk Acara <i class="bi bi-star"></i></a>
                            @elseif($event->reviews->isNotEmpty())
                                <span class="wo-badge wo-badge-green history-event-review">Acara sudah diulas</span>
                            @endif
                        </div>
                    @empty
                        <p class="history-muted">Booking ini belum memiliki rincian acara.</p>
                    @endforelse
                    <div class="history-card-bottom"><div><span>Total booking</span><strong>Rp {{ number_format((float) $booking->total_price, 0, ',', '.') }}</strong></div><div class="history-actions"><a class="wo-btn wo-btn-outline wo-btn-sm" href="{{ route('user.history.show', $booking) }}">Lihat Detail <i class="bi bi-arrow-right"></i></a></div></div>
                </article>
            @endforeach
        </div>
        <div class="history-pagination">{{ $bookings->links() }}</div>
    @endif
</main>
@endsection
