@extends('layouts.site')

@section('title', $article->title . ' - MyDream')
@section('description', \Illuminate\Support\Str::limit($article->summary, 150))

@section('content')

    @php
        $paragraphs = preg_split('/\R{2,}/', trim($article->content));
        $minutes = max(1, (int) ceil(str_word_count(strip_tags($article->content)) / 200));
    @endphp

    <x-site.page-hero :eyebrow="$article->category" :title="$article->title" :src="\App\Support\Media::url($article->photo)"
        :subtitle="($article->author ? 'Oleh ' . $article->author . ' · ' : '') . $article->date_label . ' · ' . $minutes . ' menit baca'" />

    <section class="mx-auto max-w-3xl px-4 pt-6 sm:px-6 lg:px-8">
        <x-site.breadcrumb :items="[
            ['label' => 'Beranda', 'href' => route('home')],
            ['label' => 'Artikel', 'href' => route('blog.index')],
            ['label' => $article->title],
        ]" />
    </section>

    <article class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <div data-seq class="space-y-5 text-lg leading-relaxed text-ink/70">
            @foreach ($paragraphs as $paragraph)
                <p class="whitespace-pre-line {{ $loop->first ? 'dropcap' : '' }}">{{ $paragraph }}</p>
            @endforeach
        </div>

        <x-site.share :title="$article->title" />

        <div data-reveal="zoom" class="mt-10 rounded-2xl bg-maroon-light p-6 text-center">
            <p class="font-display text-lg font-semibold">Tertarik dengan Konsep Wedding Ini?</p>
            <p class="mt-1 text-sm text-ink/60">Lihat paket yang bisa membantu mewujudkan momen serupa untuk hari bahagiamu.</p>
            <x-site.button :href="$package ? route('catalog.show', $package->slug) : route('catalog.index')" class="btn-shine mt-4">{{ $package ? 'Lihat Paket: ' . $package->name : 'Lihat Paket & Vendor' }}</x-site.button>
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
