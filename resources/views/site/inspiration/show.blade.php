@extends('layouts.site')

@section('title', $item->title . ' - Inspirasi MyDream')
@section('description', \Illuminate\Support\Str::limit($item->story, 150))

@section('content')

    <section class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
        <x-site.breadcrumb :items="[
            ['label' => 'Beranda', 'href' => route('home')],
            ['label' => 'Inspirasi', 'href' => route('inspiration.index')],
            ['label' => $item->title],
        ]" />
    </section>

    <article class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <x-site.badge variant="category">{{ $item->theme }}</x-site.badge>
        <h1 class="mt-3 font-display text-3xl font-semibold sm:text-4xl">{{ $item->title }}</h1>
        <p class="mt-2 text-sm text-ink/50">{{ $item->display_couple }}{{ $item->city ? ' · ' . $item->city : '' }}</p>

        <div class="mt-8 aspect-[16/9] overflow-hidden rounded-3xl bg-cream-dark">
            <img src="{{ \App\Support\Media::url($item->cover) }}" alt="{{ $item->title }}" class="h-full w-full object-cover">
        </div>

        <div class="mt-6 grid gap-4 rounded-2xl bg-cream-dark/60 p-6 sm:grid-cols-4">
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
                <p class="mt-1 font-display text-base font-semibold">{{ $item->guest_count ? $item->guest_count . ' orang' : '-' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-ink/40">Paket</p>
                <p class="mt-1 font-display text-base font-semibold">{{ $package?->name ?? 'Vendor pilihan' }}</p>
            </div>
        </div>

        <div class="mt-8 space-y-4 leading-relaxed text-ink/70">
            @foreach (preg_split('/\R{2,}/', trim($item->story)) as $paragraph)
                <p class="whitespace-pre-line">{{ $paragraph }}</p>
            @endforeach
        </div>

        @if ($item->tips)
            <div class="mt-8 rounded-2xl bg-white p-6 ring-1 ring-black/5">
                <p class="font-display text-lg font-semibold">Tips untuk Calon Pengantin</p>
                <p class="mt-2 whitespace-pre-line leading-relaxed text-ink/70">{{ $item->tips }}</p>
            </div>
        @endif

        @if ($item->photos->count() > 1)
            <div class="mt-8 grid gap-4 sm:grid-cols-3">
                @foreach ($item->photos->slice(1) as $photo)
                    <div class="aspect-square overflow-hidden rounded-2xl bg-cream-dark">
                        <img src="{{ \App\Support\Media::url($photo->photo) }}" alt="Galeri {{ $item->title }}" loading="lazy" class="h-full w-full object-cover">
                    </div>
                @endforeach
            </div>
        @endif

        @if ($item->testimonial)
            <blockquote class="mt-8 rounded-2xl bg-cream-dark/60 p-6">
                @if ($item->rating)
                    <p class="text-gold" aria-label="Penilaian {{ $item->rating }} dari 5">{{ str_repeat('★', $item->rating) }}{{ str_repeat('☆', 5 - $item->rating) }}</p>
                @endif
                <p class="mt-2 italic leading-relaxed text-ink/70">&ldquo;{{ $item->testimonial }}&rdquo;</p>
                <footer class="mt-3 text-sm font-semibold text-maroon">&mdash; {{ $item->display_couple }}</footer>
            </blockquote>
        @endif

        @if ($vendors->isNotEmpty())
            <div class="mt-10">
                <h2 class="font-display text-xl font-semibold">Vendor yang Bertugas</h2>
                <div class="mt-4 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($vendors as $vendor)
                        <x-site.vendor-card :vendor="$vendor" />
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-10 rounded-2xl bg-maroon-light p-6 text-center">
            <p class="font-display text-lg font-semibold">Tertarik dengan Konsep Ini?</p>
            <p class="mt-1 text-sm text-ink/60">Lihat paket yang bisa mewujudkan suasana serupa untuk hari bahagiamu.</p>
            <x-site.button :href="$package ? route('catalog.show', $package->slug) : route('catalog.index')" class="mt-4">{{ $package ? 'Lihat Paket Terkait' : 'Lihat Paket & Vendor' }}</x-site.button>
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
