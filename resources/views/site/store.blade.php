@extends('layouts.site')

@section('title', 'Store - MyDream Wedding Organizer')
@section('description', 'Paket dan vendor pilihan MyDream Wedding Organizer, dikurasi langsung oleh tim kami.')

@section('content')

    <section class="border-b border-black/5 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <p class="text-xs font-semibold uppercase tracking-widest text-emerald">Kurasi Resmi MyDream</p>
            <h1 class="mt-2 font-display text-4xl font-semibold">Paket &amp; Vendor Pilihan Kami</h1>
            <p class="mt-3 max-w-2xl text-ink/60">Rekomendasi dari tim MyDream, dipilih berdasarkan kualitas dan pengalaman pasangan yang sudah menikah bersama kami.</p>

            @if ($categories->isNotEmpty())
                <div class="mt-6 flex flex-wrap gap-2">
                    @foreach ($categories as $category)
                        <a href="{{ route('catalog.index', ['category' => $category->id]) }}" class="rounded-full bg-cream px-4 py-2 text-xs font-medium text-ink/70 ring-1 ring-black/10 transition-colors hover:bg-maroon-light hover:text-maroon">{{ $category->name }}</a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-end justify-between gap-4">
            <h2 class="font-display text-3xl font-semibold">Paket Pernikahan</h2>
            <a href="{{ route('catalog.index', ['type' => 'package']) }}" class="text-sm font-medium text-maroon hover:underline">Buka katalog lengkap &amp; filter &rarr;</a>
        </div>
        @if ($packages->isNotEmpty())
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($packages as $package)
                    <x-site.package-card :package="$package" />
                @endforeach
            </div>
        @else
            <x-site.empty-state title="Belum ada paket" description="Paket akan tampil di sini setelah ditambahkan oleh admin." />
        @endif
    </section>

    <section class="bg-white py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-end justify-between gap-4">
                <h2 class="font-display text-3xl font-semibold">Vendor Rekanan Kami</h2>
                <a href="{{ route('catalog.index', ['type' => 'vendor']) }}" class="text-sm font-medium text-maroon hover:underline">Lihat semua vendor &rarr;</a>
            </div>
            @if ($vendors->isNotEmpty())
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($vendors as $vendor)
                        <x-site.vendor-card :vendor="$vendor" />
                    @endforeach
                </div>
            @else
                <x-site.empty-state title="Belum ada vendor" description="Vendor akan tampil di sini setelah ditambahkan." />
            @endif
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <x-site.cta-banner heading="Butuh Bantuan Memilih Paket?" description="Konsultasi gratis dengan tim MyDream via WhatsApp." />
    </section>

@endsection
