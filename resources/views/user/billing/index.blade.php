{{-- resources/views/user/billing/index.blade.php --}}
@extends('user.layouts.app')

@section('title', 'Tagihan & Pembayaran')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/wo-shared.css') }}">
@endpush

@section('content')
<main class="wo-page">

    @php
        $rupiah = fn ($n) => \App\Support\Format::rupiah($n);
        $badges = [
            'unpaid' => ['Belum Bayar', 'wo-badge-red', 'bi-exclamation-circle'],
            'awaiting_verification' => ['Menunggu Verifikasi', 'wo-badge-gold', 'bi-hourglass-split'],
            'dp_paid' => ['DP Terverifikasi', 'wo-badge-green', 'bi-check-circle'],
            'paid' => ['Lunas', 'wo-badge-green', 'bi-check-circle-fill'],
        ];
    @endphp

    <div class="mb-4">
        <span class="wo-eyebrow"><i class="bi bi-circle-fill"></i> Tagihan &amp; Pembayaran</span>
        <h1>Status Pembayaran Pesanan Anda</h1>
        <p>Pantau pembayaran DP dan pelunasan setiap pesanan. Pembayaran diverifikasi tim MyDream, biasanya dalam 1x24 jam.</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @forelse ($bookings as $booking)
        @php
            $total = (float) $booking->total_price;
            $paid = (float) $booking->paid_amount;
            $pending = (float) $booking->payments->where('status', 'pending')->sum('amount');
            $remaining = max(0, $total - $paid);
            [$label, $badgeClass, $icon] = $badges[$booking->payment_status] ?? $badges['unpaid'];
            $eventDate = $booking->events->max('event_date');
            $rejected = $booking->status === 'rejected';
        @endphp

        <div class="wo-card p-4 mb-3">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <strong class="d-block">{{ $booking->package?->name ?? 'Vendor pilihan sendiri' }}</strong>
                    <span class="text-muted">
                        {{ $booking->venue_name }}, {{ $booking->venue_city }}
                        @if ($eventDate) &middot; {{ $eventDate->copy()->locale('id')->translatedFormat('d F Y') }} @endif
                    </span>
                    @if ($booking->invoice_number)
                        <small class="d-block text-muted">Invoice {{ $booking->invoice_number }}</small>
                    @endif
                </div>

                <div class="text-end">
                    @if ($rejected)
                        <span class="wo-badge wo-badge-red">Pesanan ditolak admin</span>
                    @else
                        <span class="wo-badge {{ $badgeClass }}"><i class="bi {{ $icon }}"></i> {{ $label }}</span>
                    @endif
                </div>
            </div>

            <div class="row g-3 mt-2 text-muted">
                <div class="col-6 col-md-3"><small>Total</small><div class="text-dark">{{ $rupiah($total) }}</div></div>
                <div class="col-6 col-md-3"><small>Sudah terverifikasi</small><div class="text-dark">{{ $rupiah($paid) }}</div></div>
                <div class="col-6 col-md-3"><small>Menunggu verifikasi</small><div class="text-dark">{{ $rupiah($pending) }}</div></div>
                <div class="col-6 col-md-3"><small>Sisa pembayaran</small><div class="text-dark"><strong>{{ $rupiah($remaining) }}</strong></div></div>
            </div>

            @unless ($rejected)
                <div class="mt-3">
                    @if ($booking->payment_status === 'unpaid')
                        <a href="{{ route('payment.index', $booking->id) }}" class="wo-btn wo-btn-dark wo-btn-sm">Bayar Sekarang</a>
                    @elseif ($booking->payment_status === 'dp_paid')
                        <a href="{{ route('payment.index', $booking->id) }}" class="wo-btn wo-btn-dark wo-btn-sm">Lunasi Sisa Pembayaran</a>
                    @else
                        <a href="{{ route('payment.index', $booking->id) }}" class="wo-btn wo-btn-outline wo-btn-sm">Lihat Rincian</a>
                    @endif
                </div>
            @endunless
        </div>
    @empty
        <div class="wo-card p-4 text-center">
            <strong class="d-block mb-1">Belum ada pesanan</strong>
            <p class="text-muted mb-0">Pilih paket atau vendor di halaman utama untuk membuat pesanan pertama Anda.</p>
        </div>
    @endforelse

</main>
@endsection
