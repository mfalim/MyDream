@extends('user.layouts.app')

@section('title', 'Detail Riwayat Pembelian')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/wo-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/riwayat.css') }}">
@endpush

@section('content')
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
    $paymentTypes = ['dp' => 'DP', 'full' => 'Pelunasan penuh', 'remaining' => 'Sisa pelunasan'];
    $paymentStatuses = ['verified' => 'Terverifikasi', 'pending' => 'Menunggu verifikasi', 'initiated' => 'Menunggu pembayaran', 'rejected' => 'Ditolak'];
    $midtransLabels = ['created' => 'Transaksi dibuat', 'pending' => 'Menunggu pembayaran', 'capture' => 'Pembayaran diproses', 'challenge' => 'Menunggu pemeriksaan', 'settlement' => 'Berhasil', 'expire' => 'Kedaluwarsa', 'cancel' => 'Dibatalkan', 'deny' => 'Ditolak', 'refund' => 'Dikembalikan', 'partial_refund' => 'Pengembalian sebagian', 'request_failed' => 'Gagal membuat transaksi'];
@endphp
<main class="wo-page history-page">
    <a class="wo-link history-back" href="{{ route('user.history.index') }}"><i class="bi bi-arrow-left"></i> Kembali ke Riwayat</a>
    <header class="history-heading">
        <span class="wo-eyebrow"><i class="bi bi-circle-fill"></i> Detail Pembelian</span>
        <h1>{{ $booking->package?->name ?? 'Pernikahan Custom' }}</h1>
        <p>Dibuat {{ $booking->created_at?->locale('id')->translatedFormat('d F Y') }} <span>&bull;</span> <span class="wo-badge wo-badge-green">Booking Disetujui</span></p>
        <p class="history-invoice"><i class="bi bi-receipt"></i> Invoice {{ $booking->invoice_number ?? 'belum diterbitkan' }}</p>
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
            <div class="history-venue-meta"><span>Status pembayaran</span><span class="wo-badge {{ $paymentBadge }}">{{ $paymentLabel }}</span></div>
            <a class="wo-btn {{ $isPaid ? 'wo-btn-outline' : 'wo-btn-dark' }} history-payment-link" href="{{ route('payment.index', $booking->id) }}">{{ $isPaid ? 'Lihat Pembayaran' : ($paidAmount > 0 ? 'Lunasi Sisa' : 'Bayar Sekarang') }} <i class="bi bi-arrow-up-right"></i></a>
        </aside>

        <section class="wo-card history-payments">
            <div class="wo-section-head"><div><span class="wo-eyebrow">Riwayat Transaksi</span><h2>Pembayaran</h2></div><span class="wo-badge {{ $paymentBadge }}">{{ $paymentLabel }}</span></div>
            <div class="history-payment-totals">
                <div><span>Total booking</span><strong>Rp {{ number_format((float) $booking->total_price, 0, ',', '.') }}</strong></div>
                <div><span>Sudah dibayar</span><strong>Rp {{ number_format($paidAmount, 0, ',', '.') }}</strong></div>
                <div><span>Sisa pembayaran</span><strong>Rp {{ number_format($remainingAmount, 0, ',', '.') }}</strong></div>
                @if($verificationAmount > 0)<div><span>Menunggu verifikasi manual</span><strong>Rp {{ number_format($verificationAmount, 0, ',', '.') }}</strong></div>@endif
                @if($midtransPendingAmount > 0)<div><span>Transaksi Midtrans berjalan</span><strong>Rp {{ number_format($midtransPendingAmount, 0, ',', '.') }}</strong></div>@endif
            </div>
            @forelse($booking->payments as $payment)
                @php
                    $statusClass = $payment->status === 'verified' ? 'wo-badge-green' : (in_array($payment->status, ['pending', 'initiated'], true) ? 'wo-badge-gold' : ($payment->status === 'rejected' ? 'wo-badge-red' : 'wo-badge-neutral'));
                    $methodLabel = $payment->method === 'midtrans' ? 'Midtrans' : ucfirst(str_replace('_', ' ', $payment->method));
                @endphp
                <article class="history-payment-row">
                    <div><strong>{{ $paymentTypes[$payment->type] ?? ucfirst($payment->type) }}</strong><span>{{ $methodLabel }} · {{ $payment->created_at?->locale('id')->translatedFormat('d M Y, H:i') }}</span>@if($payment->method === 'midtrans')<small>{{ $midtransLabels[$payment->transaction_status] ?? 'Status transaksi diperbarui' }}@if($payment->midtrans_order_id) · Ref {{ $payment->midtrans_order_id }}@endif</small>@endif @if($payment->verified_at)<small>Terverifikasi {{ $payment->verified_at->locale('id')->translatedFormat('d M Y, H:i') }}</small>@endif @if($payment->admin_note)<small>Catatan: {{ $payment->admin_note }}</small>@endif</div>
                    <div class="history-payment-amount"><strong>Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</strong><span class="wo-badge {{ $statusClass }}">{{ $paymentStatuses[$payment->status] ?? ucfirst($payment->status) }}</span></div>
                    @if($payment->events->isNotEmpty())
                        <ol class="history-payment-timeline">
                            @foreach($payment->events as $paymentEvent)
                                <li><span>{{ $paymentEvent->message ?? 'Status transaksi diperbarui.' }}</span><time>{{ $paymentEvent->created_at?->locale('id')->translatedFormat('d M Y, H:i') }}</time></li>
                            @endforeach
                        </ol>
                    @endif
                </article>
            @empty
                <p class="history-muted history-payment-empty">Belum ada transaksi pembayaran untuk booking ini.</p>
            @endforelse
        </section>
    </div>
</main>
@endsection
