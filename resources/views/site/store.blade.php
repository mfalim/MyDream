@extends('layouts.site')

@section('title', 'Store - MyDream Wedding Organizer')
@section('description', 'Paket dan vendor pilihan MyDream Wedding Organizer, dikurasi langsung oleh tim kami.')

@section('content')

    <x-site.page-hero eyebrow="Kurasi Resmi MyDream" title="Paket & Vendor Pilihan Kami"
        subtitle="Rekomendasi dari tim MyDream, dipilih berdasarkan kualitas dan pengalaman pasangan yang sudah menikah bersama kami."
        image="store" fallback="photo-1555244162-803834f70033">
        @if ($categories->isNotEmpty())
            @foreach ($categories as $category)
                <a href="{{ route('catalog.index', ['category' => $category->id]) }}" class="glass-chip">{{ $category->name }}</a>
            @endforeach
        @endif
    </x-site.page-hero>

    @if ($categories->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 pt-6 pb-4 sm:px-6 lg:px-8">
            <div class="mb-8 text-center" data-reveal="blur">
                <p class="text-xs font-semibold uppercase tracking-widest text-emerald">Cari Cepat</p>
                <h2 class="mt-2 font-display text-3xl font-semibold">Jelajahi per Kategori</h2>
            </div>
            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                @foreach ($categories->take(8) as $i => $category)
                    <a href="{{ route('catalog.index', ['category' => $category->id]) }}" data-reveal="pop" style="--d: {{ $i * 0.08 }}s"
                       class="group flex items-center gap-4 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-maroon-light font-display text-xl font-semibold text-maroon transition-all duration-300 group-hover:rotate-6 group-hover:scale-110 group-hover:bg-maroon group-hover:text-white">{{ mb_substr($category->name, 0, 1) }}</span>
                        <span class="min-w-0">
                            <span class="block truncate font-display text-base font-semibold">{{ $category->name }}</span>
                            <span class="block text-xs text-ink/50 transition-transform duration-300 group-hover:translate-x-1">Lihat vendor &rarr;</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-end justify-between gap-4" data-reveal="blur">
            <h2 class="font-display text-3xl font-semibold">Paket Pernikahan</h2>
            <a href="{{ route('catalog.index', ['type' => 'package']) }}" class="text-sm font-medium text-maroon hover:underline">Buka katalog lengkap &amp; filter &rarr;</a>
        </div>
        @if ($packages->isNotEmpty())
            <div data-scroller class="relative">
                <div class="scroller-track pb-4">
                    @foreach ($packages as $i => $package)
                        <div class="w-[84%] sm:w-[46%] lg:w-[31.5%]" data-reveal style="--d: {{ min($i, 5) * 0.1 }}s"><x-site.package-card :package="$package" /></div>
                    @endforeach
                </div>
                <button type="button" data-scroll="prev" class="scroll-btn left-0 -translate-x-1/2" aria-label="Sebelumnya">&lsaquo;</button>
                <button type="button" data-scroll="next" class="scroll-btn right-0 translate-x-1/2" aria-label="Berikutnya">&rsaquo;</button>
            </div>
        @else
            <x-site.empty-state title="Belum ada paket" description="Paket akan tampil di sini setelah ditambahkan oleh admin." />
        @endif
    </section>

    <section class="bg-white py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-end justify-between gap-4" data-reveal="blur">
                <h2 class="font-display text-3xl font-semibold">Vendor Rekanan Kami</h2>
                <a href="{{ route('catalog.index', ['type' => 'vendor']) }}" class="text-sm font-medium text-maroon hover:underline">Lihat semua vendor &rarr;</a>
            </div>
            @if ($vendors->isNotEmpty())
                <div data-scroller class="relative">
                    <div class="scroller-track pb-4">
                        @foreach ($vendors as $i => $vendor)
                            <div class="w-[72%] sm:w-[40%] lg:w-[23.5%]" data-reveal style="--d: {{ min($i, 5) * 0.1 }}s"><x-site.vendor-card :vendor="$vendor" /></div>
                        @endforeach
                    </div>
                    <button type="button" data-scroll="prev" class="scroll-btn left-0 -translate-x-1/2" aria-label="Sebelumnya">&lsaquo;</button>
                    <button type="button" data-scroll="next" class="scroll-btn right-0 translate-x-1/2" aria-label="Berikutnya">&rsaquo;</button>
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
