@extends('layouts.site')

@section('title', 'Inspirasi Pernikahan - MyDream Wedding Organizer')
@section('description', 'Inspirasi konsep pernikahan nyata dari pasangan yang sudah menikah bersama MyDream.')

@section('content')

    <x-site.page-hero eyebrow="Cerita Nyata dari Client" title="Inspirasi Pernikahan"
        subtitle="Konsep dan cerita dari pasangan yang sudah menikah bersama MyDream, ditulis langsung oleh mereka dan ditinjau tim kami."
        image="inspirasi" fallback="photo-1522673607200-164d1b6ce486">
        @if ($themes->isNotEmpty())
            <a href="{{ route('inspiration.index') }}" class="glass-chip {{ ! $activeTheme ? 'is-active' : '' }}">Semua</a>
            @foreach ($themes as $theme)
                <a href="{{ route('inspiration.index', ['theme' => $theme]) }}" class="glass-chip {{ $activeTheme === $theme ? 'is-active' : '' }}">{{ $theme }}</a>
            @endforeach
        @endif
    </x-site.page-hero>

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        @if ($items->isNotEmpty())
            <p class="mb-8 text-sm text-ink/60" data-reveal><span class="font-semibold text-ink" data-count="{{ $items->count() }}">{{ $items->count() }}</span> cerita{{ $activeTheme ? ' bertema ' . $activeTheme : '' }}</p>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($items as $item)
                    <x-site.inspiration-card :item="$item" />
                @endforeach
            </div>
        @else
            <x-site.empty-state title="Belum ada cerita" description="Cerita dari pasangan yang sudah menikah bersama MyDream akan tampil di sini." />
        @endif
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-site.cta-banner heading="Punya Gambaran Pernikahan Sendiri?" description="Ceritakan ke kami, tim MyDream siap mewujudkannya menjadi rundown dan moodboard nyata." />
    </section>

@endsection
