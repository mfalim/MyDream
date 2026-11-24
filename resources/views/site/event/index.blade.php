@extends('layouts.site')

@section('title', 'Event - MyDream Wedding Organizer')
@section('description', 'Jadwal pameran, open house, dan workshop MyDream Wedding Organizer.')

@section('content')

    @php
        $live = $events->firstWhere('status', 'ongoing');
        $next = $events->firstWhere('status', 'upcoming');
        $groups = ['ongoing' => 'Sedang Berlangsung', 'upcoming' => 'Akan Datang', 'finished' => 'Telah Berlalu'];
    @endphp

    <x-site.page-hero eyebrow="Kalender Kegiatan" title="Event MyDream"
        subtitle="Ikuti pameran, open house, workshop, dan sesi konsultasi gratis dari tim MyDream."
        image="event" fallback="photo-1519225421980-715cb0215aed" />

    @if ($live || $next)
        <section class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
            <div data-reveal="zoom" class="relative isolate overflow-hidden rounded-3xl bg-ink p-8 text-white sm:p-10">
                <img src="{{ \App\Support\Media::url(($live ?? $next)->photo) }}" alt="" aria-hidden="true" class="kenburns absolute inset-0 -z-10 h-full w-full object-cover opacity-30">
                <div class="absolute inset-0 -z-10 bg-linear-to-r from-ink via-ink/80 to-maroon-dark/50"></div>

                @if ($live)
                    <p class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest backdrop-blur">
                        <span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-white opacity-75"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-white"></span></span>
                        Sedang Berlangsung
                    </p>
                    <h2 class="mt-4 max-w-2xl font-display text-3xl font-semibold leading-tight sm:text-4xl">{{ $live->title }}</h2>
                    <p class="mt-2 text-white/70">{{ $live->date_label }}{{ $live->location ? ' · ' . $live->location : '' }}</p>
                    <x-site.button :href="route('event.show', $live->slug)" class="btn-shine mt-6">Lihat Detail Event</x-site.button>
                @else
                    <p class="text-xs font-semibold uppercase tracking-widest text-gold">Event Berikutnya</p>
                    <h2 class="mt-3 max-w-2xl font-display text-3xl font-semibold leading-tight sm:text-4xl">{{ $next->title }}</h2>
                    <p class="mt-2 text-white/70">{{ $next->date_label }}{{ $next->location ? ' · ' . $next->location : '' }}</p>

                    <div data-countdown="{{ $next->starts_on->format('Y-m-d') }}T00:00:00+07:00" class="mt-6 flex flex-wrap gap-3" aria-label="Hitung mundur menuju event">
                        @foreach (['d' => 'Hari', 'h' => 'Jam', 'm' => 'Menit', 's' => 'Detik'] as $key => $label)
                            <div class="cd-box"><span class="cd-num" data-cd="{{ $key }}">00</span><span class="mt-1 block text-[11px] uppercase tracking-wider text-white/60">{{ $label }}</span></div>
                        @endforeach
                    </div>
                    <x-site.button :href="route('event.show', $next->slug)" class="btn-shine mt-6">Lihat Detail Event</x-site.button>
                @endif
            </div>
        </section>
    @endif

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        @if ($events->isNotEmpty())
            @foreach ($groups as $status => $heading)
                @php $group = $events->where('status', $status); @endphp
                @if ($group->isNotEmpty())
                    <div class="{{ $loop->first ? '' : 'mt-14' }}">
                        <h2 class="mb-6 flex items-center gap-3 font-display text-2xl font-semibold" data-reveal="left">
                            {{ $heading }} <span class="rounded-full bg-maroon-light px-3 py-0.5 text-sm font-medium text-maroon">{{ $group->count() }}</span>
                        </h2>
                        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($group as $event)
                                <x-site.event-card :event="$event" />
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        @else
            <x-site.empty-state title="Belum ada event" description="Event terbaru dari MyDream akan diumumkan di sini." />
        @endif
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-site.cta-banner heading="Ingin Diundang ke Event Berikutnya?" description="Hubungi tim kami untuk info event terbaru dari MyDream." />
    </section>

@endsection
