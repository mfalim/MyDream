@extends('layouts.site')

@section('title', $item->title . ' - Inspirasi MyDream')
@section('description', \Illuminate\Support\Str::limit($item->story, 150))

@section('content')

    <x-site.page-hero :eyebrow="$item->theme" :title="$item->title" :src="\App\Support\Media::url($item->cover)"
        :subtitle="$item->display_couple . ($item->city ? ' · ' . $item->city : '')" />

    <section class="mx-auto max-w-4xl px-4 pt-6 sm:px-6 lg:px-8">
        <x-site.breadcrumb :items="[
            ['label' => 'Beranda', 'href' => route('home')],
            ['label' => 'Inspirasi', 'href' => route('inspiration.index')],
            ['label' => $item->title],
        ]" />
    </section>

    <article class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid gap-4 rounded-2xl bg-cream-dark/60 p-6 sm:grid-cols-4" data-reveal>
            <div>
                <p class="text-xs uppercase tracking-wide text-ink/40">Tanggal</p>
                <p class="mt-1 font-display text-base font-semibold">{{ $item->event_date?->locale('id')->translatedFormat('d F Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-ink/40">Lokasi</p>
                <p class="mt-1 font-display text-base font-semibold">{{ $item->venue_name ?: '-' }}{{ $item->city ? ', ' . $item->city : '' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-ink/40">Tamu</p>
                <p class="mt-1 font-display text-base font-semibold">@if ($item->guest_count)<span data-count="{{ $item->guest_count }}">{{ $item->guest_count }}</span> orang @else - @endif</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-ink/40">Paket</p>
                <p class="mt-1 font-display text-base font-semibold">{{ $package?->name ?? 'Vendor pilihan' }}</p>
            </div>
        </div>

        <div data-seq class="mt-10 space-y-5 text-lg leading-relaxed text-ink/70">
            @foreach (preg_split('/\R{2,}/', trim($item->story)) as $paragraph)
                <p class="whitespace-pre-line {{ $loop->first ? 'dropcap' : '' }}">{{ $paragraph }}</p>
            @endforeach
        </div>

        @if ($item->tips)
            <div data-reveal="left" class="mt-10 rounded-2xl border-l-4 border-gold bg-white p-6 shadow-sm ring-1 ring-black/5">
                <p class="font-display text-lg font-semibold">Tips untuk Calon Pengantin</p>
                <p class="mt-2 whitespace-pre-line leading-relaxed text-ink/70">{{ $item->tips }}</p>
            </div>
        @endif

        @if ($item->photos->count() > 1)
            <div data-gallery="g" class="mt-10 grid gap-4 sm:grid-cols-3">
                @foreach ($item->photos->slice(1) as $i => $photo)
                    <div data-reveal="mask" style="--d: {{ $loop->index * 0.12 }}s; --mr: 1rem" class="aspect-square overflow-hidden rounded-2xl bg-cream-dark">
                        <img src="{{ \App\Support\Media::url($photo->photo) }}" alt="Galeri {{ $item->title }}" loading="lazy" class="h-full w-full object-cover">
                    </div>
                @endforeach
            </div>
        @endif

        @if ($item->testimonial)
            <blockquote data-reveal="zoom" class="mt-10 rounded-2xl bg-cream-dark/60 p-8 text-center">
                @if ($item->rating)
                    <p class="stars text-xl" data-reveal="stars" aria-label="Penilaian {{ $item->rating }} dari 5">
                        @for ($s = 1; $s <= 5; $s++)<span class="star" style="--i: {{ $s }}">{{ $s <= $item->rating ? '★' : '☆' }}</span>@endfor
                    </p>
                @endif
                <p class="mt-3 text-lg italic leading-relaxed text-ink/70">&ldquo;{{ $item->testimonial }}&rdquo;</p>
                <footer class="mt-3 text-sm font-semibold text-maroon">&mdash; {{ $item->display_couple }}</footer>
            </blockquote>
        @endif

        @if ($vendors->isNotEmpty())
            <div class="mt-12">
                <h2 class="font-display text-xl font-semibold">Vendor yang Bertugas</h2>
                <div class="mt-4 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($vendors as $vendor)
                        <x-site.vendor-card :vendor="$vendor" />
                    @endforeach
                </div>
            </div>
        @endif

        <x-site.share :title="$item->title" />

        <div data-reveal="zoom" class="mt-10 rounded-2xl bg-maroon-light p-6 text-center">
            <p class="font-display text-lg font-semibold">Tertarik dengan Konsep Ini?</p>
            <p class="mt-1 text-sm text-ink/60">Lihat paket yang bisa mewujudkan suasana serupa untuk hari bahagiamu.</p>
            <x-site.button :href="$package ? route('catalog.show', $package->slug) : route('catalog.index')" class="btn-shine mt-4">{{ $package ? 'Lihat Paket Terkait' : 'Lihat Paket & Vendor' }}</x-site.button>
        </div>
    </article>

    @if ($related->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <h2 class="mb-8 font-display text-2xl font-semibold">Cerita Lainnya</h2>
            <div class="grid gap-6 sm:grid-cols-3">
                @foreach ($related as $other)
                    <x-site.inspiration-card :item="$other" />
                @endforeach
            </div>
        </section>
    @endif

@endsection
