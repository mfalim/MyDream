@extends('layouts.site')

@section('title', $item['title'] . ' - Inspirasi MyDream')
@section('description', $item['description'])

@section('content')

    <section class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
        <x-site.breadcrumb :items="[
            ['label' => 'Beranda', 'href' => route('home')],
            ['label' => 'Inspirasi', 'href' => route('inspiration.index')],
            ['label' => $item['title']],
        ]" />
    </section>

    <article class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <x-site.badge variant="category">{{ $item['theme'] }}</x-site.badge>
        <h1 class="mt-3 font-display text-3xl font-semibold sm:text-4xl">{{ $item['title'] }}</h1>
        <p class="mt-2 text-sm text-ink/50">{{ $item['couple'] }} · {{ $item['city'] }}</p>

        <div class="mt-8 aspect-[16/9] overflow-hidden rounded-3xl bg-cream-dark">
            <img src="{{ $item['cover_image'] }}" alt="{{ $item['title'] }}" class="h-full w-full object-cover">
        </div>

        <p class="mt-8 leading-relaxed text-ink/70">{{ $item['description'] }}</p>

        @if (! empty($item['gallery']))
            <div class="mt-8 grid gap-4 sm:grid-cols-3">
                @foreach ($item['gallery'] as $photo)
                    <div class="aspect-square overflow-hidden rounded-2xl bg-cream-dark">
                        <img src="{{ $photo }}" alt="Galeri {{ $item['title'] }}" loading="lazy" class="h-full w-full object-cover">
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-8 grid gap-4 rounded-2xl bg-cream-dark/60 p-6 sm:grid-cols-2">
            <div>
                <p class="text-xs uppercase tracking-wide text-ink/40">Konsep</p>
                <p class="mt-1 font-display text-base font-semibold">{{ $item['concept'] }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-ink/40">Lokasi</p>
                <p class="mt-1 font-display text-base font-semibold">{{ $item['city'] }}</p>
            </div>
        </div>

        <div class="mt-10 rounded-2xl bg-maroon-light p-6 text-center">
            <p class="font-display text-lg font-semibold">Tertarik dengan Konsep Ini?</p>
            <p class="mt-1 text-sm text-ink/60">Lihat paket yang bisa mewujudkan suasana serupa untuk hari bahagiamu.</p>
            <x-site.button :href="$package ? route('catalog.show', $package->slug) : route('catalog.index')" class="mt-4">{{ $package ? 'Lihat Paket Terkait' : 'Lihat Paket & Vendor' }}</x-site.button>
        </div>
    </article>

    @if ($related->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <h2 class="mb-8 font-display text-2xl font-semibold">Inspirasi Lainnya</h2>
            <div class="grid gap-6 sm:grid-cols-3">
                @foreach ($related as $other)
                    <x-site.inspiration-card :item="$other" />
                @endforeach
            </div>
        </section>
    @endif

@endsection
