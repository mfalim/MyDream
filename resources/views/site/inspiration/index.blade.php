@extends('layouts.site')

@section('title', 'Inspirasi Pernikahan - MyDream Wedding Organizer')
@section('description', 'Inspirasi konsep pernikahan nyata dari pasangan yang sudah menikah bersama MyDream.')

@section('content')

    <section class="border-b border-black/5 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <p class="text-xs font-semibold uppercase tracking-widest text-emerald">Dokumentasi Karya</p>
            <h1 class="mt-2 font-display text-4xl font-semibold">Inspirasi Pernikahan Nyata</h1>
            <p class="mt-3 max-w-2xl text-ink/60">Konsep dan cerita dari pasangan yang sudah mempercayakan hari bahagianya kepada MyDream.</p>

            <div class="mt-6 flex flex-wrap gap-2">
                <a href="{{ route('inspiration.index') }}" class="rounded-full px-4 py-2 text-xs font-semibold {{ ! $activeTheme ? 'bg-maroon text-white' : 'bg-cream text-ink/70 ring-1 ring-black/10 hover:bg-maroon-light hover:text-maroon' }}">Semua</a>
                @foreach ($themes as $theme)
                    <a href="{{ route('inspiration.index', ['theme' => $theme]) }}" class="rounded-full px-4 py-2 text-xs font-medium {{ $activeTheme === $theme ? 'bg-maroon text-white' : 'bg-cream text-ink/70 ring-1 ring-black/10 hover:bg-maroon-light hover:text-maroon' }}">{{ $theme }}</a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        @if ($items->isNotEmpty())
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($items as $item)
                    <x-site.inspiration-card :item="$item" />
                @endforeach
            </div>
        @else
            <x-site.empty-state title="Belum ada inspirasi untuk tema ini" description="Coba pilih tema lain." />
        @endif
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-site.cta-banner heading="Punya Gambaran Pernikahan Sendiri?" description="Ceritakan ke kami, tim MyDream siap mewujudkannya menjadi rundown dan moodboard nyata." />
    </section>

@endsection
