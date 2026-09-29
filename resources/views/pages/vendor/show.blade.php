@extends('layouts.frontend')

@section('title', $vendor->name . ' — MyDream Organizer')

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Cari Vendor', 'href' => route('vendor.index')], ['label' => $vendor->name]]" />
@endsection

@section('content')

    {{-- Cover Vendor --}}
    <div style="height:260px;overflow:hidden;">
        @if ($coverPhoto)
            <img src="{{ asset('storage/' . $coverPhoto->photo) }}" alt="{{ $vendor->name }}"
                style="width:100%;height:100%;object-fit:cover;">
        @else
            <div
                style="
                    width:100%;
                    height:100%;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    background:var(--paper-dark);
                    color:var(--muted);
                    font-family:var(--font-serif);
                    font-size:2rem;
                ">
                {{ $vendor->name }}
            </div>
        @endif
    </div>

    <section class="container">

        {{-- Vendor Header --}}
        <div class="flex flex-wrap gap-2"
            style="
                align-items:flex-end;
                margin-top:-46px;
                padding-bottom:24px;
                border-bottom:1px solid var(--line);
            ">

            <span
                style="
                    width:100px;
                    height:100px;
                    border-radius:var(--radius-md);
                    border:4px solid var(--paper);
                    background:var(--gold);
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    font-family:var(--font-serif);
                    font-size:1.4rem;
                    color:#241C0E;
                    box-shadow:var(--shadow-md);
                ">
                {{ strtoupper(substr($vendor->name, 0, 2)) }}
            </span>

            <div style="flex:1;">

                <div class="flex gap-1 flex-wrap mb-1">

                    @if ($vendor->category)
                        <span class="badge badge-maroon">
                            {{ $vendor->category->name }}
                        </span>
                    @endif

                </div>

                <h1 class="h1" style="font-size:1.9rem;">
                    {{ $vendor->name }}
                </h1>

                <p class="small muted mt-1">
                    {{ $vendor->address }}
                </p>

            </div>

            @if ($vendor->phone)
                <a href="https://wa.me/{{ $vendor->phone }}" class="btn btn-dark" target="_blank">
                    Chat Vendor
                </a>
            @endif

        </div>

        {{-- Main Content --}}
        <div class="section vendor-show-layout"
            style="
                padding-bottom:0;
                display:grid;
                grid-template-columns:2fr 1fr;
                gap:40px;
            ">

            {{-- LEFT --}}
            <div>

                {{-- About --}}
                <h2 class="h3">
                    Tentang Vendor
                </h2>

                <p class="lede mt-2">
                    {{ $vendor->description ?: 'Deskripsi vendor belum tersedia.' }}
                </p>


                {{-- Portfolio --}}
                <h2 class="h3 mt-4">
                    Portofolio
                </h2>

                <div class="grid grid-3 mt-2" style="gap:10px;">

                    @forelse ($otherPhotos as $photo)
                        <img src="{{ asset('storage/' . $photo->photo) }}" alt="Portofolio {{ $vendor->name }}"
                            style="
                                border-radius:var(--radius-sm);
                                aspect-ratio:1/1;
                                object-fit:cover;
                                width:100%;
                            ">

                    @empty

                        <div class="muted"
                            style="
                                grid-column:1/-1;
                                padding:30px 0;
                            ">
                            Belum ada foto portofolio.
                        </div>
                    @endforelse

                </div>

            </div>


            {{-- RIGHT --}}
            <aside>

                <div class="summary-card">

                    <h3>
                        Daftar Harga
                    </h3>

                    @forelse ($vendor->packages as $package)
                        <div class="summary-row"
                            style="
                                border-bottom:1px solid var(--line-soft);
                                padding-bottom:10px;
                                margin-bottom:10px;
                            ">

                            <span>
                                {{ $package->name }}
                            </span>

                            <strong class="text-maroon">
                                Rp {{ number_format($package->price, 0, ',', '.') }}
                            </strong>

                        </div>

                    @empty

                        <p class="muted">
                            Vendor ini belum tergabung dalam paket.
                        </p>
                    @endforelse


                    {{-- TAMBAHKAN VENDOR KE KERANJANG --}}
                    <form action="{{ route('user.cart.add-vendor', $vendor->id) }}" method="POST" class="mt-3">
                        @csrf

                        <button type="submit" class="btn btn-dark btn-block w-100">
                            <i class="bi bi-cart-plus"></i>
                            Tambah ke Keranjang
                        </button>
                    </form>

                    {{-- LIHAT KERANJANG --}}
                    <a href="{{ route('user.cart') }}" class="btn btn-outline-dark btn-block w-100 mt-2">
                        <i class="bi bi-cart"></i>
                        Lihat Keranjang
                    </a>

                    {{-- KONTAK WHATSAPP --}}
                    @if ($vendor->phone)
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $vendor->phone) }}"
                            class="btn btn-outline-success btn-block w-100 mt-2" target="_blank" rel="noopener noreferrer">
                            <i class="bi bi-whatsapp"></i>
                            Chat Vendor
                        </a>
                    @endif

                </div>

            </aside>

        </div>

    </section>

@endsection


@push('styles')
    <style>
        @media (max-width: 800px) {
            .vendor-show-layout {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
@endpush
