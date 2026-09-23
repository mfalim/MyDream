{{-- resources/views/user/checkout/payment.blade.php --}}
@extends('user.layouts.app')

@section('title', 'Pembayaran')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/wo-shared.css') }}">
    <style>
        .payment-page {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .success-badge {
            background: #d1fae5;
            color: #065f46;
            padding: 16px 24px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }
        .success-badge i {
            font-size: 24px;
        }
        .booking-info {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }
        .booking-info h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .payment-options {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }
        .payment-option {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .payment-option:hover {
            border-color: #16302a;
        }
        .payment-option.selected {
            border-color: #16302a;
            background: #f0fdf4;
        }
        .payment-option h4 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .payment-option .price {
            font-size: 24px;
            font-weight: 700;
            color: #16302a;
            margin: 12px 0;
        }
        .payment-option .badge {
            display: inline-block;
            background: #f59e0b;
            color: white;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .btn-pay {
            width: 100%;
            background: #16302a;
            color: white;
            border: none;
            padding: 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 18px;
            cursor: pointer;
        }
        .btn-pay:hover {
            background: #0f1f1a;
        }
    </style>
@endpush

@section('content')
<main class="payment-page">
    
    <div class="success-badge">
        <i class="bi bi-check-circle-fill"></i>
        <div>
            <strong style="font-size: 18px;">Booking Berhasil Dibuat!</strong>
            <p style="margin: 4px 0 0; font-size: 14px;">Silakan pilih metode pembayaran untuk melanjutkan.</p>
        </div>
    </div>

    <div style="margin-bottom: 32px;">
        <h1 style="font-size: 32px; font-weight: 700; margin: 0 0 8px;">Pembayaran Booking</h1>
        <p style="color: #6b7280;">Booking ID: #{{ $booking->id }} • {{ $booking->client->groom_name }} & {{ $booking->client->bride_name }}</p>
    </div>

    <div class="booking-info">
        <h3><i class="bi bi-file-text"></i> Detail Booking</h3>
        
        <div class="info-row">
            <span>Mempelai</span>
            <strong>{{ $booking->client->groom_name }} & {{ $booking->client->bride_name }}</strong>
        </div>
        
        <div class="info-row">
            <span>Venue</span>
            <strong>{{ $booking->venue_name }}, {{ $booking->venue_city }}</strong>
        </div>
        
        <div class="info-row">
            <span>Jumlah Tamu</span>
            <strong>{{ $booking->guest_count }} Orang</strong>
        </div>
        
        @if($booking->package)
        <div class="info-row">
            <span>Paket</span>
            <strong>{{ $booking->package->name }}</strong>
        </div>
        @endif
        
        <div class="info-row" style="font-size: 20px; font-weight: 700; padding-top: 16px; border-top: 2px solid #e5e7eb; margin-top: 16px;">
            <span>Total Booking</span>
            <strong>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong>
        </div>
    </div>

    <div class="payment-options">
        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px;"><i class="bi bi-credit-card"></i> Pilih Metode Pembayaran</h3>
        
        <div class="payment-option" onclick="selectPayment('dp')">
            <span class="badge">PALING POPULER</span>
            <h4>DP 30% (Down Payment)</h4>
            <p style="color: #6b7280; font-size: 14px;">Bayar sebagian untuk mengamankan booking</p>
            <div class="price">Rp {{ number_format($dp, 0, ',', '.') }}</div>
            <p style="font-size: 13px; color: #6b7280;">Sisa pelunasan 70% dapat dibayar sebelum hari H</p>
        </div>
        
        <div class="payment-option" onclick="selectPayment('lunas')">
            <span class="badge" style="background: #10b981;">HEMAT RP {{ number_format($savings, 0, ',', '.') }}</span>
            <h4>Bayar Lunas (Diskon 3%)</h4>
            <p style="color: #6b7280; font-size: 14px;">Bayar penuh dan dapatkan diskon</p>
            <div class="price">Rp {{ number_format($lunas, 0, ',', '.') }}</div>
            <p style="font-size: 13px; color: #10b981; font-weight: 600;">✓ Hemat Rp {{ number_format($savings, 0, ',', '.') }}</p>
        </div>
        
        <div class="payment-option" onclick="selectPayment('cicilan')">
            <h4>Cicilan 6x (Tanpa Bunga)</h4>
            <p style="color: #6b7280; font-size: 14px;">Bayar dengan sistem cicilan 6 bulan</p>
            <div class="price">Rp {{ number_format($cicilan, 0, ',', '.') }}<span style="font-size: 16px; font-weight: 400; color: #6b7280;">/bulan</span></div>
            <p style="font-size: 13px; color: #6b7280;">6 pembayaran @ Rp {{ number_format($cicilan, 0, ',', '.') }}</p>
        </div>
    </div>

    <button type="button" class="btn-pay" onclick="processPayment()">
        <i class="bi bi-shield-check"></i> Lanjutkan Pembayaran
    </button>
    
    <p style="text-align: center; margin-top: 20px; color: #6b7280; font-size: 14px;">
        <i class="bi bi-shield-fill-check"></i> Pembayaran aman dan terenkripsi
    </p>

</main>

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
    alert('Redirect ke payment gateway untuk metode: ' + selectedPayment + '\n\nFitur payment gateway akan diintegrasikan dengan sistem pembayaran yang Anda gunakan.');
    // Redirect ke halaman sukses atau payment gateway
    // window.location.href = '/user/payment-success';
}

// Set default selection
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.payment-option').classList.add('selected');
});
</script>
@endsection
