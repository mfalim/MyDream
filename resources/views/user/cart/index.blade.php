{{-- resources/views/user/cart/index.blade.php --}}
@extends('user.layouts.app')

@section('title', 'Keranjang')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/wo-shared.css') }}">
    <style>
        .cart-page {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .cart-empty {
            text-align: center;
            padding: 80px 20px;
        }
        .cart-empty i {
            font-size: 64px;
            color: #d1d5db;
            margin-bottom: 16px;
        }
        .cart-layout {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 24px;
            margin-top: 32px;
        }
        .cart-items {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .cart-item {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            display: grid;
            grid-template-columns: 120px 1fr auto;
            gap: 20px;
            align-items: center;
        }
        .cart-item-img {
            width: 120px;
            height: 120px;
            border-radius: 8px;
            object-fit: cover;
        }
        .cart-item-info h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .cart-item-info p {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 8px;
        }
        .cart-item-price {
            font-size: 20px;
            font-weight: 700;
            color: #16302a;
            text-align: right;
        }
        .cart-item-remove {
            background: #ef4444;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 12px;
        }
        .cart-summary {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 24px;
            height: fit-content;
            position: sticky;
            top: 20px;
        }
        .cart-summary h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }
        .summary-row.total {
            font-size: 20px;
            font-weight: 700;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
            margin-top: 16px;
        }
        .btn-checkout {
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
        .btn-checkout:hover {
            background: #0f1f1a;
        }
        .btn-clear {
            width: 100%;
            background: white;
            color: #ef4444;
            border: 1px solid #ef4444;
            padding: 10px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            margin-top: 12px;
        }
    </style>
@endpush

@section('content')
<main class="cart-page">
    
    <div style="margin-bottom: 32px;">
        <a href="{{ route('user.explore-vendor') }}" class="wo-link"><i class="bi bi-arrow-left"></i> Lanjut Eksplor Vendor</a>
        <h1 style="font-size: 32px; font-weight: 700; margin: 16px 0 8px;">Keranjang Pemilihan</h1>
        <p style="color: #6b7280;">Review paket atau vendor yang sudah Anda pilih sebelum checkout.</p>
    </div>

    @if(session('success'))
        <div class="wo-badge wo-badge-green" style="margin-bottom: 20px;">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="wo-badge" style="margin-bottom: 20px; background: #fecaca; color: #dc2626;">{{ session('error') }}</div>
    @endif

    @if($cart['type'] === 'custom' && count($cart['vendor_ids']) < 3)
        <div class="wo-badge" style="margin-bottom: 20px; background: #fef3c7; color: #92400e;">
            <i class="bi bi-exclamation-triangle"></i> Minimal pilih 3 vendor untuk melanjutkan ke checkout ({{ count($cart['vendor_ids']) }}/3)
        </div>
    @endif

    @if(empty($cart['type']))
        <div class="cart-empty">
            <i class="bi bi-cart-x"></i>
            <h2>Keranjang Kosong</h2>
            <p>Belum ada paket atau vendor yang dipilih</p>
            <a href="{{ route('user.explore-vendor') }}" class="wo-btn wo-btn-dark" style="margin-top: 24px;">
                <i class="bi bi-shop"></i> Eksplor Vendor
            </a>
        </div>
    @else
        <div class="cart-layout">
            
            <div class="cart-items">
                @if($cart['type'] === 'package' && $package)
                    <div class="wo-card" style="padding: 24px;">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                            <i class="bi bi-gem" style="font-size: 24px; color: #f59e0b;"></i>
                            <div>
                                <span class="wo-badge wo-badge-gold">PAKET LENGKAP</span>
                                <h2 style="font-size: 24px; font-weight: 700; margin: 8px 0;">{{ $package->name }}</h2>
                                <p style="color: #6b7280;">{{ $package->guest_capacity }} Tamu • {{ $package->duration }}</p>
                            </div>
                        </div>
                        
                        <h3 style="margin: 20px 0 12px; font-size: 16px;">Vendor Termasuk:</h3>
                        @foreach($vendors as $vendor)
                            <div class="cart-item">
                                @php
                                    $photo = $vendor->photos->where('is_cover', true)->first();
                                    $photoUrl = $photo ? asset('storage/' . $photo->photo) : 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=300&q=80';
                                @endphp
                                <img src="{{ $photoUrl }}" alt="{{ $vendor->name }}" class="cart-item-img">
                                <div class="cart-item-info">
                                    <h3>{{ $vendor->name }}</h3>
                                    <p><i class="bi bi-tag"></i> {{ $vendor->category->name ?? 'Vendor' }}</p>
                                </div>
                                <div class="cart-item-price">
                                    Rp {{ number_format($vendor->price, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                        
                        <form action="{{ route('user.cart.clear') }}" method="POST" style="margin-top: 16px;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-clear">
                                <i class="bi bi-trash"></i> Hapus Paket
                            </button>
                        </form>
                    </div>
                @else
                    @foreach($vendors as $vendor)
                        <div class="cart-item">
                            @php
                                $photo = $vendor->photos->where('is_cover', true)->first();
                                $photoUrl = $photo ? asset('storage/' . $photo->photo) : 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=300&q=80';
                            @endphp
                            <img src="{{ $photoUrl }}" alt="{{ $vendor->name }}" class="cart-item-img">
                            <div class="cart-item-info">
                                <h3>{{ $vendor->name }}</h3>
                                <p><i class="bi bi-tag"></i> {{ $vendor->category->name ?? 'Vendor' }}</p>
                                <p><i class="bi bi-telephone"></i> {{ $vendor->phone }}</p>
                                
                                <form action="{{ route('user.cart.remove', $vendor->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="cart-item-remove">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                            <div class="cart-item-price">
                                Rp {{ number_format($vendor->price, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="cart-summary">
                <h3>Ringkasan</h3>
                
                @if($cart['type'] === 'package')
                    <div class="summary-row">
                        <span>Paket {{ $package->name }}</span>
                        <strong>Rp {{ number_format($totalPrice, 0, ',', '.') }}</strong>
                    </div>
                @else
                    <div class="summary-row">
                        <span>{{ $vendors->count() }} Vendor dipilih</span>
                        <strong>{{ $vendors->count() }}x</strong>
                    </div>
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <strong>Rp {{ number_format($totalPrice, 0, ',', '.') }}</strong>
                    </div>
                @endif
                
                <div class="summary-row total">
                    <span>Total</span>
                    <strong>Rp {{ number_format($totalPrice, 0, ',', '.') }}</strong>
                </div>
                
                @if($cart['type'] === 'custom' && count($cart['vendor_ids']) < 3)
                    <button type="button" class="btn-checkout" style="background: #d1d5db; cursor: not-allowed;" disabled>
                        <i class="bi bi-exclamation-circle"></i> Minimal 3 Vendor ({{ count($cart['vendor_ids']) }}/3)
                    </button>
                @else
                    <a href="{{ route('user.checkout') }}" class="btn-checkout">
                        <i class="bi bi-check-circle"></i> Lanjut ke Checkout
                    </a>
                @endif
                
                <form action="{{ route('user.cart.clear') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-clear">
                        <i class="bi bi-trash"></i> Kosongkan Keranjang
                    </button>
                </form>
            </div>

        </div>
    @endif

</main>
@endsection
