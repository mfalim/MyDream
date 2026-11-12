@extends('layouts.site')

@section('title', 'Artikel & Blog - MyDream Wedding Organizer')
@section('description', 'Cerita pernikahan nyata, tips perencanaan, dan panduan memilih vendor dari tim MyDream.')

@section('content')

    <section class="border-b border-black/5 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <p class="text-xs font-semibold uppercase tracking-widest text-emerald">Cerita &amp; Tips</p>
            <h1 class="mt-2 font-display text-4xl font-semibold">Artikel &amp; Blog MyDream</h1>
            <p class="mt-3 max-w-2xl text-ink/60">Cerita pernikahan nyata, panduan perencanaan, dan tips memilih vendor dari tim MyDream.</p>

            @if ($categories->isNotEmpty())
                <div class="mt-6 flex flex-wrap gap-2">
                    <a href="{{ route('blog.index') }}" class="rounded-full px-4 py-2 text-xs font-semibold {{ ! $activeCategory ? 'bg-maroon text-white' : 'bg-cream text-ink/70 ring-1 ring-black/10 hover:bg-maroon-light hover:text-maroon' }}">Semua</a>
                    @foreach ($categories as $category)
                        <a href="{{ route('blog.index', ['category' => $category]) }}" class="rounded-full px-4 py-2 text-xs font-medium {{ $activeCategory === $category ? 'bg-maroon text-white' : 'bg-cream text-ink/70 ring-1 ring-black/10 hover:bg-maroon-light hover:text-maroon' }}">{{ $category }}</a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        @if ($articles->isNotEmpty())
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($articles as $article)
                    <x-site.article-card :article="$article" />
                @endforeach
            </div>
        @else
            <x-site.empty-state title="Belum ada artikel" description="Artikel dan cerita pernikahan terbaru akan tampil di sini." />
        @endif
    </section>

@endsection
