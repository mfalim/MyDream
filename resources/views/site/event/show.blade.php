@extends('layouts.site')

@section('title', $event->title . ' - Event MyDream')
@section('description', \Illuminate\Support\Str::limit($event->description ?: 'Event MyDream Wedding Organizer.', 150))

@section('content')

    @php
        $labels = ['upcoming' => ['Akan Datang', 'category'], 'ongoing' => ['Sedang Berlangsung', 'maroon'], 'finished' => ['Selesai', 'dark']];
        [$label, $variant] = $labels[$event->status];
    @endphp

    <section class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
        <x-site.breadcrumb :items="[
            ['label' => 'Beranda', 'href' => route('home')],
            ['label' => 'Event', 'href' => route('event.index')],
            ['label' => $event->title],
        ]" />
    </section>

    <article class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="aspect-[16/9] overflow-hidden rounded-3xl bg-cream-dark">
            <img src="{{ \App\Support\Media::url($event->photo) }}" alt="{{ $event->title }}" class="h-full w-full object-cover">
        </div>

        <div class="mt-6"><x-site.badge :variant="$variant">{{ $label }}</x-site.badge></div>
        <h1 class="mt-3 font-display text-3xl font-semibold sm:text-4xl">{{ $event->title }}</h1>

        <div class="mt-6 grid gap-4 rounded-2xl bg-cream-dark/60 p-6 sm:grid-cols-3">
            <div>
                <p class="text-xs uppercase tracking-wide text-ink/40">Tanggal</p>
                <p class="mt-1 font-display text-base font-semibold">{{ $event->date_label }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-ink/40">Waktu</p>
                <p class="mt-1 font-display text-base font-semibold">{{ $event->time_label ?? 'Akan diumumkan' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-ink/40">Lokasi</p>
                <p class="mt-1 font-display text-base font-semibold">{{ $event->location ?: 'Akan diumumkan' }}</p>
            </div>
        </div>

        @if ($event->description)
            <p class="mt-8 whitespace-pre-line leading-relaxed text-ink/70">{{ $event->description }}</p>
        @endif

        @if ($vendors->isNotEmpty())
            <div class="mt-10">
                <h2 class="font-display text-xl font-semibold">Vendor yang Berpartisipasi</h2>
                <div class="mt-4 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($vendors as $vendor)
                        <x-site.vendor-card :vendor="$vendor" />
                    @endforeach
                </div>
            </div>
        @endif

        @if ($event->status !== 'finished')
            <div class="mt-10 rounded-2xl bg-maroon-light p-6 text-center">
                <p class="font-display text-lg font-semibold">Mau Ikut Hadir?</p>
                <p class="mt-1 text-sm text-ink/60">Daftarkan diri lewat WhatsApp, tim kami akan mengonfirmasi kehadiranmu.</p>
                <x-site.button :href="\App\Support\Format::waLink(config('mydream.whatsapp'), 'Halo MyDream, saya ingin mendaftar di event ' . $event->title . '.')" class="mt-4">Daftar via WhatsApp</x-site.button>
            </div>
        @endif
    </article>

@endsection
