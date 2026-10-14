@extends('layouts.site')

@section('title', 'Event - MyDream Wedding Organizer')
@section('description', 'Jadwal pameran, open house, dan workshop MyDream Wedding Organizer.')

@section('content')

    <section class="border-b border-black/5 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <p class="text-xs font-semibold uppercase tracking-widest text-emerald">Kalender Kegiatan</p>
            <h1 class="mt-2 font-display text-4xl font-semibold">Event MyDream</h1>
            <p class="mt-3 max-w-2xl text-ink/60">Ikuti pameran, open house, workshop, dan sesi konsultasi gratis dari tim MyDream.</p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($events as $event)
                <x-site.event-card :event="$event" />
            @endforeach
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-site.cta-banner heading="Ingin Diundang ke Event Berikutnya?" description="Hubungi tim kami untuk info event terbaru dari MyDream." />
    </section>

@endsection
