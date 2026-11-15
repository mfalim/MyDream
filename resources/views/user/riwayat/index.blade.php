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
                @php
                    $paidAmount = (float) $booking->payments->where('status', 'verified')->sum('amount');
                    $verificationAmount = (float) $booking->payments->where('status', 'pending')->sum('amount');
                    $midtransPendingAmount = (float) $booking->payments->where('status', 'initiated')->sum('amount');
                    $remainingAmount = max(0, (float) $booking->total_price - $paidAmount);
                    $latestPayment = $booking->payments->first();
                    $isPaid = (float) $booking->total_price > 0 && $remainingAmount <= 0;
                    $paymentLabel = match (true) {
                        $isPaid => 'Lunas',
                        $verificationAmount > 0 => 'Menunggu verifikasi',
                        $midtransPendingAmount > 0 => 'Menunggu pembayaran Midtrans',
                        $latestPayment?->status === 'rejected' && $paidAmount > 0 => 'Pelunasan gagal',
                        $latestPayment?->status === 'rejected' => 'Pembayaran gagal',
                        $paidAmount > 0 => 'DP terverifikasi',
                        default => 'Belum dibayar',
                    };
                    $paymentBadge = match (true) {
                        $isPaid, ($paidAmount > 0 && $verificationAmount === 0 && $midtransPendingAmount === 0 && $latestPayment?->status !== 'rejected') => 'wo-badge-green',
                        $verificationAmount > 0 || $midtransPendingAmount > 0 => 'wo-badge-gold',
                        $latestPayment?->status === 'rejected' => 'wo-badge-red',
                        default => 'wo-badge-neutral',
                    };
                    $midtransLabels = ['created' => 'Transaksi dibuat', 'pending' => 'Menunggu pembayaran', 'capture' => 'Pembayaran diproses', 'challenge' => 'Menunggu pemeriksaan', 'settlement' => 'Berhasil', 'expire' => 'Kedaluwarsa', 'cancel' => 'Dibatalkan', 'deny' => 'Ditolak', 'refund' => 'Dikembalikan', 'partial_refund' => 'Pengembalian sebagian', 'request_failed' => 'Gagal membuat transaksi'];
                @endphp
                <article class="wo-card history-card">
                    <div class="history-card-top">
                        <div><span class="wo-eyebrow">Dipesan {{ $booking->created_at?->locale('id')->translatedFormat('d M Y') }}</span><h2>{{ $booking->package?->name ?? 'Pernikahan Custom' }}</h2><span class="history-invoice"><i class="bi bi-receipt"></i> Invoice {{ $booking->invoice_number ?? 'belum diterbitkan' }}</span></div>
                        <span class="wo-badge wo-badge-green">Disetujui</span>
                    </div>
                    <div class="history-meta"><span><i class="bi bi-geo-alt"></i> {{ $booking->venue_name }}, {{ $booking->venue_city }}</span><span><i class="bi bi-calendar-event"></i> {{ $booking->events->count() }} acara</span></div>
                    <div class="history-payment-summary">
                        <div><span>Status pembayaran</span><span class="wo-badge {{ $paymentBadge }}">{{ $paymentLabel }}</span>@if($latestPayment?->method === 'midtrans')<small>{{ $midtransLabels[$latestPayment->transaction_status] ?? 'Status transaksi diperbarui' }}</small>@endif</div>
                        <div><span>Terbayar</span><strong>Rp {{ number_format($paidAmount, 0, ',', '.') }}</strong></div>
                        <div><span>Sisa</span><strong>Rp {{ number_format($remainingAmount, 0, ',', '.') }}</strong></div>
                    </div>
                    @forelse($booking->events as $event)
                        <div class="history-booking-event">
                            <div class="history-event-head"><strong>{{ $event->name }}</strong><span>{{ $event->event_date?->format('d M Y') }}</span></div>
                            @if($event->schedules->isNotEmpty())
                                <ul class="history-vendor-list">@foreach($event->schedules as $schedule)@if($schedule->vendor)<li><span>{{ $schedule->vendor->name }}</span><strong>Rp {{ number_format((float) $schedule->price, 0, ',', '.') }}</strong></li>@endif @endforeach</ul>
                            @else
                                <p class="history-muted">Belum ada vendor yang disetujui untuk acara ini.</p>
                            @endif
                        </div>
                    @empty
                        <p class="history-muted">Booking ini belum memiliki rincian acara.</p>
                    @endforelse
                    <div class="history-card-bottom"><div><span>Total booking</span><strong>Rp {{ number_format((float) $booking->total_price, 0, ',', '.') }}</strong></div><div class="history-actions"><a class="wo-btn wo-btn-outline wo-btn-sm" href="{{ route('payment.index', $booking->id) }}">Invoice &amp; Pembayaran <i class="bi bi-receipt"></i></a><a class="wo-btn wo-btn-outline wo-btn-sm" href="{{ route('user.history.show', $booking) }}">Lihat Detail <i class="bi bi-arrow-right"></i></a></div></div>
                </article>
            @endforeach
        </div>
        <div class="history-pagination">{{ $bookings->links() }}</div>
    @endif
</main>
@endsection
