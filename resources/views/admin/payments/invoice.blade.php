@extends('layouts.admin')

@section('title', 'Invoice & Kwitansi - ' . $booking->invoice_number)
@section('page-title', 'Invoice & Kwitansi Pembayaran')

@push('styles')
    @vite('resources/css/admin/finance/index.css')
@endpush

@section('content')
@php
    $rupiah = fn ($n) => 'Rp ' . number_format((float) ($n ?? 0), 0, ',', '.');
    $total = (float) $booking->total_price;
    $paid = (float) $booking->paid_amount;
    $remaining = max(0, $total - $paid);
    $payStatus = $booking->payment_status ?: 'unpaid';
    $stampLabel = match ($payStatus) {
        'paid' => 'LUNAS / PAID IN FULL',
        'dp_paid' => 'DP TERVERIFIKASI',
        'awaiting_verification' => 'MENUNGGU VERIFIKASI',
        default => 'BELUM TERBAYAR',
    };
    $firstEvent = $booking->events->first();
    $vendorSchedules = $booking->events->flatMap->schedules->whereNotNull('vendor_id');
@endphp

<div class="invoice-shell">
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <a href="{{ route('admin.payments.index', ['tab' => 'bookings']) }}" class="btn-fin-outline">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali ke Pusat Keuangan</span>
        </a>
        <button type="button" onclick="window.print()" class="btn-fin-primary">
            <i class="bi bi-printer"></i>
            <span>Cetak Invoice / Simpan PDF</span>
        </button>
    </div>

    <div class="invoice-sheet">
        <div class="invoice-top">
            <div>
                <div class="invoice-brand-title">MyDream Wedding Organizer</div>
                <div class="text-muted small mt-1">
                    Dokumen Tagihan &amp; Kwitansi Bukti Pembayaran Resmi<br>
                    WhatsApp: {{ config('mydream.whatsapp_display') }} · Email: {{ config('mydream.email') }}
                </div>
            </div>
            <div class="text-end">
                <div class="invoice-stamp {{ $payStatus }}">{{ $stampLabel }}</div>
                <div class="fw-bold mt-2 fs-5 num-tabular">{{ $booking->invoice_number }}</div>
                <div class="small text-muted">Tanggal Cetak: {{ now()->locale('id')->translatedFormat('d F Y') }}</div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="text-uppercase small fw-bold text-muted mb-1">Diterbitkan Untuk Klien (Mempelai)</div>
                <div class="fs-5 fw-bold">{{ $booking->client?->groom_name ?? 'N/A' }} &amp; {{ $booking->client?->bride_name ?? 'N/A' }}</div>
                <div class="small text-secondary mt-1">
                    Email: {{ $booking->client?->email ?? '-' }}<br>
                    No. Telepon: {{ $booking->client?->groom_phone ?? $booking->client?->bride_phone ?? '-' }}
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="text-uppercase small fw-bold text-muted mb-1">Detail Pelaksanaan Acara</div>
                <div class="fw-bold">{{ $booking->package?->name ?? 'Paket Custom Vendor Pilihan' }}</div>
                <div class="small text-secondary mt-1">
                    Tanggal Hari-H: <strong>{{ $firstEvent?->event_date?->locale('id')->translatedFormat('d F Y') ?? '-' }}</strong><br>
                    Lokasi Venue: {{ $booking->venue_name }} ({{ $booking->venue_city }}) · {{ $booking->guest_count }} Tamu
                </div>
            </div>
        </div>

        {{-- RINCIAN PAKET & VENDOR --}}
        <h6 class="fw-bold text-uppercase small text-muted mb-2">1. Rincian Komponen Paket &amp; Vendor Acara</h6>
        <div class="table-responsive mb-4">
            <table class="finance-table border">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Vendor / Layanan</th>
                        <th>Kategori</th>
                        <th class="text-end">Harga Katalog</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vendorSchedules as $idx => $s)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td class="fw-semibold">{{ $s->vendor?->name ?? $s->activity }}</td>
                            <td>{{ $s->vendor?->category?->name ?? 'Layanan Pernikahan' }}</td>
                            <td class="text-end num-tabular">{{ $rupiah($s->price ?? $s->vendor?->price ?? $s->effective_fee) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">Paket Pernikahan MyDream</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-light">
                        <th colspan="3" class="text-end">Total Nilai Kontrak Pernikahan</th>
                        <th class="text-end num-tabular fs-6">{{ $rupiah($total) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- RIWAYAT PEMBAYARAN KLIEN (KWITANSI) --}}
        <h6 class="fw-bold text-uppercase small text-muted mb-2">2. Riwayat Pembayaran &amp; Verifikasi Bukti Transfer</h6>
        <div class="table-responsive mb-4">
            <table class="finance-table border">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Tahapan Pembayaran</th>
                        <th>Metode &amp; Rekening</th>
                        <th>Status Verifikasi</th>
                        <th class="text-end">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($booking->payments as $payment)
                        @php
                            $tLabel = [
                                'dp' => 'Down Payment (DP)',
                                'full' => 'Pembayaran Lunas (100%)',
                                'remaining' => 'Pelunasan Sisa Tagihan',
                            ][$payment->type] ?? strtoupper($payment->type);
                        @endphp
                        <tr>
                            <td>{{ $payment->created_at?->locale('id')->translatedFormat('d M Y, H:i') }}</td>
                            <td class="fw-semibold">{{ $tLabel }}</td>
                            <td class="small">
                                @if ($payment->method === 'midtrans')
                                    Midtrans Gateway ({{ $payment->midtrans_order_id ?? 'Otomatis' }})
                                @else
                                    {{ $payment->bank_info ?: 'Transfer Bank' }}
                                @endif
                            </td>
                            <td>
                                <span class="fin-badge {{ $payment->status }}">
                                    {{ $payment->status === 'verified' ? 'Terverifikasi Sah' : ($payment->status === 'pending' ? 'Menunggu Verifikasi' : ($payment->status === 'initiated' ? 'Diproses Midtrans' : 'Ditolak')) }}
                                </span>
                                @if ($payment->proof_url)
                                    <a href="{{ $payment->proof_url }}" target="_blank" class="ms-1 small text-decoration-underline no-print">Lihat Struk</a>
                                @endif
                            </td>
                            <td class="text-end num-tabular">{{ $rupiah($payment->amount) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">Belum ada transaksi pembayaran tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- RINGKASAN AKHIR --}}
        <div class="row justify-content-end">
            <div class="col-md-5">
                <div class="p-3 rounded-3 border bg-light">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Nilai Kontrak:</span>
                        <span class="num-tabular">{{ $rupiah($total) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Terverifikasi Masuk:</span>
                        <span class="num-tabular text-success">{{ $rupiah($paid) }}</span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Sisa Tagihan Klien:</span>
                        <span class="num-tabular fs-5 {{ $remaining > 0 ? 'text-danger' : 'text-success' }}">
                            {{ $remaining > 0 ? $rupiah($remaining) : 'Rp 0 (LUNAS)' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

