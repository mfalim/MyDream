@extends('layouts.site')

@section('title', $article->title . ' - MyDream')
@section('description', \Illuminate\Support\Str::limit($article->summary, 150))

@section('content')

    <section class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
        <x-site.breadcrumb :items="[
            ['label' => 'Beranda', 'href' => route('home')],
            ['label' => 'Artikel', 'href' => route('blog.index')],
            ['label' => $article->title],
        ]" />
    </section>

    <article class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <x-site.badge variant="gold">{{ $article->category }}</x-site.badge>
        <h1 class="mt-3 font-display text-3xl font-semibold sm:text-4xl">{{ $article->title }}</h1>
        <p class="mt-2 text-sm text-ink/50">{{ $article->author ? 'Oleh ' . $article->author . ' · ' : '' }}{{ $article->date_label }}</p>

        @if ($article->photo)
            <div class="mt-8 aspect-[16/9] overflow-hidden rounded-3xl bg-cream-dark">
                <img src="{{ \App\Support\Media::url($article->photo) }}" alt="{{ $article->title }}" class="h-full w-full object-cover">
            </div>
        @endif

        <div class="mt-8 space-y-4 leading-relaxed text-ink/70">
            @foreach (preg_split('/\R{2,}/', trim($article->content)) as $paragraph)
                <p class="whitespace-pre-line">{{ $paragraph }}</p>
            @endforeach
        </div>

        <div class="mt-10 rounded-2xl bg-maroon-light p-6 text-center">
            <p class="font-display text-lg font-semibold">Tertarik dengan Konsep Wedding Ini?</p>
            <p class="mt-1 text-sm text-ink/60">Lihat paket yang bisa membantu mewujudkan momen serupa untuk hari bahagiamu.</p>
            <x-site.button :href="$package ? route('catalog.show', $package->slug) : route('catalog.index')" class="mt-4">{{ $package ? 'Lihat Paket: ' . $package->name : 'Lihat Paket & Vendor' }}</x-site.button>
        </div>
    </article>

    @if ($related->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <h2 class="mb-8 font-display text-2xl font-semibold">Artikel Terkait</h2>
            <div class="grid gap-8 sm:grid-cols-3">
                @foreach ($related as $other)
                    <x-site.article-card :article="$other" />
                @endforeach
            </div>
        </section>
    @endif

@endsection
