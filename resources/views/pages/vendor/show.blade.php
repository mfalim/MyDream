@extends('layouts.frontend')

@php
    // TODO: ganti dengan data dari controller berdasarkan $slug
    $vendor = [
        'name' => 'Marlene Hariman Makeup',
        'tags' => ['Celebrity MUA', 'MyDream Partner'],
        'location' => 'Sumbersari, Jember',
        'rating' => '5.0', 'reviews' => 428,
        'cover' => 'vendor/vendor-cover-01.jpg',
        'about' => 'Marlene Hariman Makeup telah dipercaya lebih dari 400 pasangan sejak 2014. Spesialis dalam soft glam look yang natural namun tetap flawless di depan kamera, dengan tim asisten profesional untuk keluarga besar.',
        'portfolio' => ['vendor/portfolio-01.jpg', 'vendor/portfolio-02.jpg', 'vendor/portfolio-03.jpg', 'vendor/portfolio-04.jpg', 'vendor/portfolio-05.jpg', 'vendor/portfolio-06.jpg'],
        'packages' => [
            ['name' => 'Paket Akad — 1 Look', 'price' => '6.500.000'],
            ['name' => 'Paket Akad & Resepsi — 2 Look', 'price' => '12.800.000'],
            ['name' => 'Paket Lengkap + Ibu & Besan', 'price' => '18.500.000'],
        ],
    ];
@endphp

@section('title', $vendor['name'].' — MyDream Organizer')

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Cari Vendor', 'href' => route('vendor.index')], ['label' => $vendor['name']]]" />
@endsection

@section('content')

    <div style="height:260px;overflow:hidden;">
        <img src="{{ asset('images/'.$vendor['cover']) }}" alt="{{ $vendor['name'] }}" style="width:100%;height:100%;object-fit:cover;">
    </div>

    <section class="container">
        <div class="flex flex-wrap gap-2" style="align-items:flex-end;margin-top:-46px;padding-bottom:24px;border-bottom:1px solid var(--line);">
            <span style="width:100px;height:100px;border-radius:var(--radius-md);border:4px solid var(--paper);background:var(--gold);display:flex;align-items:center;justify-content:center;font-family:var(--font-serif);font-size:1.4rem;color:#241C0E;box-shadow:var(--shadow-md);">MH</span>
            <div style="flex:1;">
                <div class="flex gap-1 flex-wrap mb-1">
                    @foreach ($vendor['tags'] as $tag)<span class="badge badge-maroon">{{ $tag }}</span>@endforeach
                </div>
                <h1 class="h1" style="font-size:1.9rem;">{{ $vendor['name'] }}</h1>
                <p class="small muted mt-1">{{ $vendor['location'] }} &middot; ★ {{ $vendor['rating'] }} ({{ $vendor['reviews'] }} ulasan)</p>
            </div>
            <a href="https://wa.me/6281233779967" class="btn btn-dark">Chat Vendor</a>
        </div>

        <div class="section vendor-show-layout" style="padding-bottom:0;display:grid;grid-template-columns:2fr 1fr;gap:40px;">
            <div>
                <h2 class="h3">Tentang Vendor</h2>
                <p class="lede mt-2">{{ $vendor['about'] }}</p>

                <h2 class="h3 mt-4">Portofolio</h2>
                <div class="grid grid-3 mt-2" style="gap:10px;">
                    @foreach ($vendor['portfolio'] as $img)
                        <img src="{{ asset('images/'.$img) }}" alt="Portofolio {{ $vendor['name'] }}" style="border-radius:var(--radius-sm);aspect-ratio:1/1;object-fit:cover;width:100%;">
                    @endforeach
                </div>
            </div>

            <aside>
                <div class="summary-card">
                    <h3>Daftar Harga</h3>
                    @foreach ($vendor['packages'] as $paket)
                        <div class="summary-row" style="border-bottom:1px solid var(--line-soft);padding-bottom:10px;">
                            <span>{{ $paket['name'] }}</span>
                            <strong class="text-maroon">Rp {{ $paket['price'] }}</strong>
                        </div>
                    @endforeach
                    <a href="{{ route('checkout.index') ?? '#' }}" class="btn btn-dark btn-block mt-2">Booking Vendor Ini</a>
                </div>
            </aside>
        </div>
    </section>

@endsection

@push('styles')
<style>
    @media (max-width: 800px) { .vendor-show-layout { grid-template-columns: 1fr !important; } }
</style>
@endpush
