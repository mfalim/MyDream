@extends('layouts.site')

@section('title', 'Artikel & Blog - MyDream Wedding Organizer')
@section('description', 'Cerita pernikahan nyata, tips perencanaan, dan panduan memilih vendor dari tim MyDream.')

@section('content')

    @php
        $featured = $articles->first();
        $rest = $articles->slice(1);
    @endphp

    <x-site.page-hero eyebrow="Cerita & Tips" title="Artikel & Blog MyDream"
        subtitle="Cerita pernikahan nyata, panduan perencanaan, dan tips memilih vendor dari tim MyDream."
        image="artikel" fallback="photo-1519741497674-611481863552">
        @if ($categories->isNotEmpty())
            <a href="{{ route('blog.index') }}" class="glass-chip {{ ! $activeCategory ? 'is-active' : '' }}">Semua</a>
            @foreach ($categories as $category)
                <a href="{{ route('blog.index', ['category' => $category]) }}" class="glass-chip {{ $activeCategory === $category ? 'is-active' : '' }}">{{ $category }}</a>
            @endforeach
        @endif
    </x-site.page-hero>

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        @if ($featured)
            <a href="{{ route('blog.show', $featured->slug) }}" data-reveal="zoom" class="group grid overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-black/5 transition-all duration-300 hover:shadow-xl md:grid-cols-2">
                <div class="aspect-[4/3] overflow-hidden bg-cream-dark md:aspect-auto md:min-h-[22rem]">
                    <img src="{{ \App\Support\Media::url($featured->photo) }}" alt="{{ $featured->title }}" class="h-full w-full object-cover transition-transform duration-[1200ms] group-hover:scale-105">
                </div>
                <div class="flex flex-col justify-center gap-3 p-8 sm:p-12">
                    <span class="flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-emerald">
                        <x-site.badge variant="gold">{{ $featured->category }}</x-site.badge> Artikel Terbaru
                    </span>
                    <h2 class="font-display text-3xl font-semibold leading-tight transition-colors group-hover:text-maroon sm:text-4xl">{{ $featured->title }}</h2>
                    <p class="line-clamp-3 leading-relaxed text-ink/60">{{ $featured->summary }}</p>
                    <p class="text-xs text-ink/40">{{ $featured->author ? 'Oleh ' . $featured->author . ' · ' : '' }}{{ $featured->date_label }}</p>
                    <span class="mt-2 inline-flex items-center gap-2 text-sm font-semibold text-maroon">Baca selengkapnya <span class="transition-transform duration-300 group-hover:translate-x-2">&rarr;</span></span>
                </div>
            </a>

            @if ($rest->isNotEmpty())
                <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($rest as $article)
                        <x-site.article-card :article="$article" />
                    @endforeach
                </div>
            @endif
        @else
            <x-site.empty-state title="Belum ada artikel" description="Artikel dan cerita pernikahan terbaru akan tampil di sini." />
        @endif
    </section>

@endsection
