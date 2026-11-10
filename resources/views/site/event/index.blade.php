@extends('layouts.site')

@section('title', 'Event - MyDream Wedding Organizer')
@section('description', 'Jadwal pameran, open house, dan workshop MyDream Wedding Organizer.')

@section('content')

    <section class="border-b border-black/5 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <p class="text-xs font-semibold uppercase tracking-widest text-emerald">Kalender Kegiatan</p>
            <h1 class="mt-2 font-display text-4xl font-semibold">Event &amp; Pameran MyDream</h1>
            <p class="mt-3 max-w-2xl text-ink/60">Ikuti pameran pernikahan (*wedding expo*), open house venue, workshop pengantin, dan sesi konsultasi gratis dari tim MyDream.</p>

            <div class="mt-6 flex flex-wrap gap-2">
                <a href="{{ route('event.index') }}" class="rounded-full px-4 py-2 text-xs font-semibold transition-all {{ ! $activeStatus ? 'bg-maroon text-white shadow-sm' : 'bg-cream text-ink/70 ring-1 ring-black/10 hover:bg-maroon-light hover:text-maroon' }}">
                    Semua ({{ $counts['all'] ?? 0 }})
                </a>
                @if (($counts['ongoing'] ?? 0) > 0)
                    <a href="{{ route('event.index', ['status' => 'ongoing']) }}" class="rounded-full px-4 py-2 text-xs font-semibold transition-all {{ $activeStatus === 'ongoing' ? 'bg-maroon text-white shadow-sm' : 'bg-cream text-ink/70 ring-1 ring-black/10 hover:bg-maroon-light hover:text-maroon' }}">
                        🔴 Sedang Berlangsung ({{ $counts['ongoing'] }})
                    </a>
                @endif
                <a href="{{ route('event.index', ['status' => 'upcoming']) }}" class="rounded-full px-4 py-2 text-xs font-semibold transition-all {{ $activeStatus === 'upcoming' ? 'bg-maroon text-white shadow-sm' : 'bg-cream text-ink/70 ring-1 ring-black/10 hover:bg-maroon-light hover:text-maroon' }}">
                    Akan Datang ({{ $counts['upcoming'] ?? 0 }})
                </a>
                <a href="{{ route('event.index', ['status' => 'finished']) }}" class="rounded-full px-4 py-2 text-xs font-semibold transition-all {{ $activeStatus === 'finished' ? 'bg-maroon text-white shadow-sm' : 'bg-cream text-ink/70 ring-1 ring-black/10 hover:bg-maroon-light hover:text-maroon' }}">
                    Selesai ({{ $counts['finished'] ?? 0 }})
                </a>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        @if ($events->isNotEmpty())
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($events as $event)
                    <x-site.event-card :event="$event" />
                @endforeach
            </div>
        @else
            <x-site.empty-state title="Belum ada event" description="Event terbaru dari MyDream akan diumumkan di sini." />
        @endif
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-site.cta-banner heading="Ingin Diundang ke Event Berikutnya?" description="Hubungi tim kami untuk info event terbaru dari MyDream." />
    </section>

@endsection
