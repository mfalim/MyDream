@extends('layouts.frontend')

@php
    // TODO: ganti dengan data dari controller berdasarkan $slug
    $paket = [
        'title' => 'Oceanfront Wedding Ceremony & Dinner Package (200 Pax)',
        'vendor' => 'AYANA Resort & Spa Bali',
        'location' => 'Jimbaran, Bali',
        'rating' => '4.9', 'reviews' => 98,
        'before' => '185.000.000', 'now' => '160.000.000', 'cicilan' => '6,6 Jt', 'discount' => 'Hemat Rp 25.000.000',
        'gallery' => ['package/package-01.jpg', 'package/package-02.jpg', 'package/package-03.jpg', 'package/package-04.jpg'],
        'description' => 'Rayakan momen sakral pernikahanmu dengan latar matahari terbenam di tepi laut Jimbaran. Paket ini mencakup venue eksklusif, dekorasi tematik, dan jamuan makan malam internasional untuk 200 tamu, lengkap dengan penginapan mewah untuk mempelai.',
        'includes' => ['Eksklusif venue outdoor selama 6 jam', '5-course International Buffet Dinner untuk 200 pax', 'Menginap 2 malam di Bridal Ocean Suite', 'Dekorasi altar & meja tamu tema tropis', 'Sound system, MC, dan tim wedding coordinator', 'Dokumentasi foto highlight 1 hari'],
    ];
@endphp

@section('title', $paket['title'].' — MyDream Organizer')

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Paket & Promo', 'href' => route('catalog.index')], ['label' => $paket['title']]]" />
@endsection

@section('content')
    <section class="section">
        <div class="container" style="display:grid;grid-template-columns:2fr 1fr;gap:40px;">

            <div>
                <div class="grid grid-2">
                    <img src="{{ asset('images/'.$paket['gallery'][0]) }}" alt="{{ $paket['title'] }}" style="grid-column:1/-1;border-radius:var(--radius-md);aspect-ratio:16/9;object-fit:cover;width:100%;">
                    @foreach (array_slice($paket['gallery'], 1) as $img)
                        <img src="{{ asset('images/'.$img) }}" alt="" style="border-radius:var(--radius-md);aspect-ratio:4/3;object-fit:cover;width:100%;">
                    @endforeach
                </div>

                <div class="mt-4">
                    <p class="tiny faint" style="text-transform:uppercase;">{{ $paket['location'] }}</p>
                    <h1 class="h1 mt-1" style="font-size:2rem;">{{ $paket['title'] }}</h1>
                    <div class="flex gap-2 mt-1" style="align-items:center;">
                        <span class="small muted">oleh <strong class="text-maroon">{{ $paket['vendor'] }}</strong></span>
                        <span class="small muted rating">★ {{ $paket['rating'] }} ({{ $paket['reviews'] }} ulasan)</span>
                    </div>

                    <p class="lede mt-3">{{ $paket['description'] }}</p>

                    <h2 class="h3 mt-4">Termasuk Dalam Paket</h2>
                    <ul class="card-features mt-2 grid grid-2" style="gap:10px;">
                        @foreach ($paket['includes'] as $item)
                            <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4.5 12.75l6 6 9-13.5" stroke-linecap="round" stroke-linejoin="round"/></svg><span>{{ $item }}</span></li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <aside>
                <div class="summary-card">
                    <span class="badge badge-maroon">{{ $paket['discount'] }}</span>
                    <p class="tiny faint mt-2" style="text-decoration:line-through;">Rp {{ $paket['before'] }}</p>
                    <p class="h2" style="color:var(--maroon);">Rp {{ $paket['now'] }}</p>
                    <p class="small muted">Cicilan mulai Rp {{ $paket['cicilan'] }}/bln</p>

                    <form class="mt-3" action="{{ route('cart.index') ?? '#' }}" method="POST">
                        @csrf
                        <div class="field">
                            <label>Tanggal Acara</label>
                            <input type="date">
                        </div>
                        <div class="field">
                            <label>Jumlah Tamu</label>
                            <input type="number" value="200">
                        </div>
                        <button type="submit" class="btn btn-dark btn-block">Tambah ke Keranjang</button>
                        <a href="{{ route('checkout.index') ?? '#' }}" class="btn btn-outline btn-block mt-1">Booking Sekarang</a>
                    </form>

                    <a href="#" class="flex justify-between mt-3 small muted" style="justify-content:center;gap:8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M21 12c0 4.556-4.03 8.25-9 8.25a9.76 9.76 0 01-2.555-.337A5.97 5.97 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/></svg>
                        Chat Vendor Langsung
                    </a>
                </div>
            </aside>
        </div>
    </section>
@endsection
