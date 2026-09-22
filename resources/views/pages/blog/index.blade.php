@extends('layouts.frontend')

@section('title', 'Artikel & Blog Pernikahan — MyDream Organizer')

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Artikel & Blog']]" />
@endsection

@php
    $featured = ['slug' => 'grand-emerald-ballroom', 'image' => 'blog/blog-01.jpg', 'category' => 'Venue', 'title' => 'Grand Emerald Ballroom: Pernikahan Megah Berkapasitas hingga 1.200 Tamu', 'excerpt' => 'Intip fasilitas dan konsep ballroom terbesar di Jember yang menjadi favorit calon pengantin tahun ini.', 'date' => '18 September 2026'];

    $articles = [
        ['slug' => 'panduan-memilih-venue', 'image' => 'blog/blog-02.jpg', 'category' => 'Tips', 'title' => 'Panduan Memilih Venue Sesuai Budget Pernikahan', 'date' => '15 September 2026'],
        ['slug' => 'tren-dekorasi-outdoor', 'image' => 'blog/blog-03.jpg', 'category' => 'Dekorasi', 'title' => '7 Tren Dekorasi Pernikahan Outdoor 2026', 'date' => '10 September 2026'],
        ['slug' => 'cara-kerja-mydream-pay', 'image' => 'blog/blog-04.jpg', 'category' => 'Pembayaran', 'title' => 'Cara Kerja MyDream Pay untuk Cicilan Vendor', 'date' => '4 September 2026'],
        ['slug' => 'swiss-chocolate-hour', 'image' => 'blog/blog-05.jpg', 'category' => 'Katering', 'title' => 'Swiss Chocolate Hour: Sensasi Dessert Mewah', 'date' => '28 Agustus 2026'],
        ['slug' => 'presidential-suite', 'image' => 'blog/blog-06.jpg', 'category' => 'Venue', 'title' => 'Presidential Suite: Istirahat Mewah Sebelum Hari-H', 'date' => '20 Agustus 2026'],
        ['slug' => 'checklist-h-1-bulan', 'image' => 'blog/blog-07.jpg', 'category' => 'Tips', 'title' => 'Checklist Persiapan Pernikahan H-1 Bulan', 'date' => '12 Agustus 2026'],
    ];
@endphp

@section('content')

    <section class="section-sm">
        <div class="container">
            <p class="eyebrow">Artikel &amp; Blog</p>
            <h1 class="h1 mt-2" style="font-size:2.1rem;">Blog &amp; Inspirasi Pernikahan</h1>
            <p class="lede mt-1">Tips, tren, dan cerita di balik vendor pilihan untuk membantumu merencanakan hari bahagia.</p>
        </div>
    </section>

    <section class="container">
        <a href="{{ route('blog.show', $featured['slug']) ?? '#' }}" class="card featured-blog" style="display:grid;grid-template-columns:1fr 1fr;gap:0;">
            <div class="card-media" style="aspect-ratio:16/10;border-radius:0;">
                <img src="{{ asset('images/'.$featured['image']) }}" alt="{{ $featured['title'] }}">
            </div>
            <div class="card-body" style="padding:28px;">
                <span class="badge badge-maroon">{{ $featured['category'] }}</span>
                <h2 class="h2 mt-2">{{ $featured['title'] }}</h2>
                <p class="small muted mt-2">{{ $featured['excerpt'] }}</p>
                <p class="tiny faint mt-3">{{ $featured['date'] }}</p>
            </div>
        </a>
    </section>

    <section class="section">
        <div class="container">
            <div class="grid grid-3">
                @foreach ($articles as $article)
                    <a href="{{ route('blog.show', $article['slug']) ?? '#' }}" class="card" style="display:block;">
                        <div class="card-media" style="aspect-ratio:16/10;">
                            <img src="{{ asset('images/'.$article['image']) }}" alt="{{ $article['title'] }}">
                        </div>
                        <div class="card-body">
                            <p class="tiny text-maroon" style="text-transform:uppercase;font-weight:700;">{{ $article['category'] }}</p>
                            <h3 class="card-title" style="font-size:1.05rem;">{{ $article['title'] }}</h3>
                            <p class="tiny faint mt-2">{{ $article['date'] }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

@endsection

@push('styles')
<style>
    @media (max-width: 700px) { .featured-blog { grid-template-columns: 1fr !important; } }
</style>
@endpush
