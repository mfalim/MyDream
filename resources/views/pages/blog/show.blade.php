@extends('layouts.frontend')

@php
    // TODO: ganti dengan data dari controller berdasarkan $slug
    $article = [
        'title' => 'Grand Emerald Ballroom: Pernikahan Megah Berkapasitas hingga 1.200 Tamu',
        'category' => 'Venue', 'date' => '18 September 2026', 'author' => 'Tim MyDream Organizer',
        'cover' => 'blog/blog-01.jpg',
        'body' => [
            'Grand Emerald Ballroom menjadi salah satu venue paling dicari di Jember untuk resepsi pernikahan skala besar. Dengan langit-langit tinggi dan sistem tata cahaya LED yang dapat disesuaikan dengan tema acara, ballroom ini mampu menampung hingga 1.200 tamu dengan format berdiri.',
            'Selain kapasitas, venue ini juga dilengkapi ruang rias pengantin privat, area foyer untuk registrasi tamu, serta dapur katering berkapasitas besar sehingga proses penyajian hidangan tetap efisien meski jumlah tamu banyak.',
            'Bagi pasangan yang memesan lewat MyDream Organizer, tersedia paket bundling bersama vendor dekorasi dan katering rekanan dengan harga yang lebih hemat dibanding memesan terpisah.',
        ],
    ];
    $terkait = [
        ['slug' => 'swiss-chocolate-hour', 'title' => 'Swiss Chocolate Hour: Sensasi Dessert Mewah', 'image' => 'blog/blog-05.jpg'],
        ['slug' => 'presidential-suite', 'title' => 'Presidential Suite: Istirahat Mewah Sebelum Hari-H', 'image' => 'blog/blog-06.jpg'],
        ['slug' => 'panduan-memilih-venue', 'title' => 'Panduan Memilih Venue Sesuai Budget Pernikahan', 'image' => 'blog/blog-02.jpg'],
    ];
@endphp

@section('title', $article['title'].' — MyDream Organizer')

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Artikel & Blog', 'href' => route('blog.index')], ['label' => $article['title']]]" />
@endsection

@section('content')
    <article class="section">
        <div class="container blog-show-layout" style="display:grid;grid-template-columns:2fr 1fr;gap:40px;">
            <div>
                <span class="badge badge-maroon">{{ $article['category'] }}</span>
                <h1 class="h1 mt-2" style="font-size:2rem;">{{ $article['title'] }}</h1>
                <p class="small muted mt-1">{{ $article['author'] }} &middot; {{ $article['date'] }}</p>

                <img src="{{ asset('images/'.$article['cover']) }}" alt="{{ $article['title'] }}" style="border-radius:var(--radius-md);width:100%;aspect-ratio:16/9;object-fit:cover;margin-top:20px;">

                <div class="mt-3" style="max-width:42em;">
                    @foreach ($article['body'] as $paragraph)
                        <p class="lede mb-2">{{ $paragraph }}</p>
                    @endforeach
                </div>
            </div>

            <aside>
                <h3>Artikel Terkait</h3>
                <div class="mt-2" style="display:flex;flex-direction:column;gap:14px;">
                    @foreach ($terkait as $item)
                        <a href="{{ route('blog.show', $item['slug']) ?? '#' }}" class="flex gap-2">
                            <img src="{{ asset('images/'.$item['image']) }}" alt="" style="width:80px;height:64px;border-radius:var(--radius-sm);object-fit:cover;flex-shrink:0;">
                            <p class="small">{{ $item['title'] }}</p>
                        </a>
                    @endforeach
                </div>
            </aside>
        </div>
    </article>
@endsection

@push('styles')
<style>
    @media (max-width: 800px) { .blog-show-layout { grid-template-columns: 1fr !important; } }
</style>
@endpush
