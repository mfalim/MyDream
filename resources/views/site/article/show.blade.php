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
        @if ($article->event)
            @php
                $ev = $article->event;
                $evClient = $ev->booking?->client;
                $evCouple = $evClient ? trim(($evClient->groom_name ?? '') . ' & ' . ($evClient->bride_name ?? ''), ' &') : null;
                $evDate = $ev->event_date ? $ev->event_date->locale('id')->translatedFormat('d F Y') : null;
            @endphp
            <div data-reveal="zoom" class="mb-8 rounded-2xl border border-gold/30 bg-white/80 p-5 shadow-sm">
                <div class="flex flex-wrap items-center gap-2">
                    <x-site.badge variant="maroon">Based on True Event</x-site.badge>
                    @if ($ev->event_type)
                        <span class="text-xs font-medium uppercase tracking-wider text-gold">{{ $ev->event_type }}</span>
                    @endif
                </div>
                <h2 class="mt-2 font-display text-xl font-semibold text-ink">{{ $ev->name }}</h2>
                <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-sm text-ink/70">
                    @if ($evCouple)
                        <span><strong class="text-ink">Mempelai:</strong> {{ $evCouple }}</span>
                    @endif
                    @if ($evDate)
                        <span><strong class="text-ink">Tanggal Acara:</strong> {{ $evDate }}</span>
                    @endif
                    @if ($ev->guest_count)
                        <span><strong class="text-ink">Tamu:</strong> ± {{ number_format($ev->guest_count, 0, ',', '.') }} orang</span>
                    @endif
                    @if ($package)
                        <span><strong class="text-ink">Paket:</strong> {{ $package->name }}</span>
                    @endif
                </div>
            </div>
        @endif

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
