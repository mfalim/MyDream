{{-- resources/views/user/checkout/index.blade.php --}}
@extends('user.layouts.app')

@section('title', 'Checkout')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/wo-shared.css') }}">
    <style>
        .checkout-page {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .checkout-layout {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 24px;
            margin-top: 32px;
        }
        .form-section {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }
        .form-section h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: #374151;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
        }
        .form-group textarea {
            min-height: 80px;
            resize: vertical;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .order-summary {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 24px;
            height: fit-content;
            position: sticky;
            top: 20px;
        }
        .order-summary h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .summary-total {
            display: flex;
            justify-content: space-between;
            padding-top: 16px;
            margin-top: 16px;
            border-top: 2px solid #e5e7eb;
            font-size: 20px;
            font-weight: 700;
        }
        .btn-submit {
            width: 100%;
            background: #16302a;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }
        .btn-submit:hover {
            background: #0f1f1a;
        }
    </style>
@endpush

@section('content')
<main class="checkout-page">
    
    <div style="margin-bottom: 32px;">
        <a href="{{ route('user.cart') }}" class="wo-link"><i class="bi bi-arrow-left"></i> Kembali ke Keranjang</a>
        <h1 style="font-size: 32px; font-weight: 700; margin: 16px 0 8px;">Checkout Booking</h1>
        <p style="color: #6b7280;">Lengkapi data untuk membuat booking pernikahan Anda.</p>
    </div>

    @if(session('error'))
        <div class="wo-badge" style="margin-bottom: 20px; background: #fecaca; color: #dc2626;">{{ session('error') }}</div>
    @endif

    <form action="{{ route('user.checkout.store') }}" method="POST">
        @csrf
        
        <div class="checkout-layout">
            
            <div>
                {{-- Data Venue --}}
                <div class="form-section">
                    <h3><i class="bi bi-building"></i> Informasi Venue & Acara</h3>
                    
                    <div class="form-group">
                        <label for="event_date">Tanggal Acara <span style="color: red;">*</span></label>
                        <input type="date" name="event_date" id="event_date" value="{{ old('event_date') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                        @error('event_date')
                            <small style="color: #ef4444;">{{ $message }}</small>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="venue_name">Nama Venue <span style="color: red;">*</span></label>
                        <input type="text" name="venue_name" id="venue_name" value="{{ old('venue_name') }}" placeholder="Contoh: Hotel Mulia Senayan" required>
                        @error('venue_name')
                            <small style="color: #ef4444;">{{ $message }}</small>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="venue_address">Alamat Lengkap Venue <span style="color: red;">*</span></label>
                        <textarea name="venue_address" id="venue_address" required>{{ old('venue_address') }}</textarea>
                        @error('venue_address')
                            <small style="color: #ef4444;">{{ $message }}</small>
                        @enderror
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="venue_city">Kota <span style="color: red;">*</span></label>
                            <input type="text" name="venue_city" id="venue_city" value="{{ old('venue_city') }}" required>
                            @error('venue_city')
                                <small style="color: #ef4444;">{{ $message }}</small>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="venue_province">Provinsi <span style="color: red;">*</span></label>
                            <input type="text" name="venue_province" id="venue_province" value="{{ old('venue_province') }}" required>
                            @error('venue_province')
                                <small style="color: #ef4444;">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="guest_count">Perkiraan Jumlah Tamu <span style="color: red;">*</span></label>
                        <input type="number" name="guest_count" id="guest_count" value="{{ old('guest_count') }}" min="1" placeholder="Contoh: 500" required>
                        @error('guest_count')
                            <small style="color: #ef4444;">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="order-summary">
                <h3>Ringkasan Pesanan</h3>
                
                @if($cart['type'] === 'package' && $package)
                    <div class="summary-item">
                        <div>
                            <strong>{{ $package->name }}</strong>
                            <p style="font-size: 13px; color: #6b7280; margin-top: 4px;">{{ $vendors->count() }} vendor termasuk</p>
                        </div>
                        <strong>Rp {{ number_format($totalPrice, 0, ',', '.') }}</strong>
                    </div>
                @else
                    @foreach($vendors as $vendor)
                        <div class="summary-item">
                            <div>
                                <strong>{{ $vendor->name }}</strong>
                                <p style="font-size: 13px; color: #6b7280; margin-top: 4px;">{{ $vendor->category->name ?? 'Vendor' }}</p>
                            </div>
                            <strong>Rp {{ number_format($vendor->price, 0, ',', '.') }}</strong>
                        </div>
                    @endforeach
                @endif
                
                <div class="summary-total">
                    <span>Total</span>
                    <strong>Rp {{ number_format($totalPrice, 0, ',', '.') }}</strong>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="bi bi-check-circle"></i> Buat Booking & Lanjut Pembayaran
                </button>
                
                <p style="font-size: 13px; color: #6b7280; text-align: center; margin-top: 16px;">
                    Dengan melanjutkan, Anda menyetujui syarat dan ketentuan WO PROJECT
                </p>
            </div>

        </div>
    </form>

</main>
@endsection
