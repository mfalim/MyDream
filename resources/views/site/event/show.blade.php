@extends('layouts.site')

@section('title', $event->title . ' - Event MyDream')
@section('description', \Illuminate\Support\Str::limit($event->description ?: 'Event MyDream Wedding Organizer.', 150))

@section('content')

    @php
        $labels = ['upcoming' => 'Akan Datang', 'ongoing' => 'Sedang Berlangsung', 'finished' => 'Selesai'];
        $label = $labels[$event->status];
        $info = [
            ['Tanggal', $event->date_label, 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5'],
            ['Waktu', $event->time_label ?? 'Akan diumumkan', 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['Lokasi', $event->location ?: 'Akan diumumkan', 'M15 10.5a3 3 0 11-6 0 3 3 0 016 0z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z'],
        ];
    @endphp

    <x-site.page-hero :eyebrow="$label" :title="$event->title" :src="\App\Support\Media::url($event->photo)" :subtitle="$event->date_label . ($event->location ? ' · ' . $event->location : '')">
        @if ($event->status === 'upcoming')
            <div data-countdown="{{ $event->starts_on->format('Y-m-d') }}T00:00:00+07:00" class="flex flex-wrap gap-3" aria-label="Hitung mundur menuju event">
                @foreach (['d' => 'Hari', 'h' => 'Jam', 'm' => 'Menit', 's' => 'Detik'] as $key => $unit)
                    <div class="cd-box"><span class="cd-num" data-cd="{{ $key }}">00</span><span class="mt-1 block text-[11px] uppercase tracking-wider text-white/60">{{ $unit }}</span></div>
                @endforeach
            </div>
        @endif
    </x-site.page-hero>

    <section class="mx-auto max-w-5xl px-4 pt-6 sm:px-6 lg:px-8">
        <x-site.breadcrumb :items="[
            ['label' => 'Beranda', 'href' => route('home')],
            ['label' => 'Event', 'href' => route('event.index')],
            ['label' => $event->title],
        ]" />
    </section>

    <article class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid gap-4 sm:grid-cols-3">
            @foreach ($info as $i => [$name, $value, $icon])
                <div data-reveal="pop" style="--d: {{ $i * 0.12 }}s" class="group flex items-start gap-4 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-maroon-light text-maroon transition-all duration-300 group-hover:rotate-6 group-hover:bg-maroon group-hover:text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" /></svg>
                    </span>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-ink/40">{{ $name }}</p>
                        <p class="mt-1 font-display text-base font-semibold leading-snug">{{ $value }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($event->description)
            <div data-seq class="mt-10 space-y-4 text-lg leading-relaxed text-ink/70">
                @foreach (preg_split('/\R{2,}/', trim($event->description)) as $paragraph)
                    <p class="whitespace-pre-line">{{ $paragraph }}</p>
                @endforeach
            </div>
        @endif

        @if ($vendors->isNotEmpty())
            <div class="mt-12">
                <h2 class="font-display text-xl font-semibold">Vendor yang Berpartisipasi</h2>
                <div class="mt-4 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($vendors as $vendor)
                        <x-site.vendor-card :vendor="$vendor" />
                    @endforeach
                </div>
            </div>
        @endif

        <x-site.share :title="$event->title" />

        @if ($event->status !== 'finished')
            <div data-reveal="zoom" class="mt-10 rounded-2xl bg-maroon-light p-6 text-center">
                <p class="font-display text-lg font-semibold">Mau Ikut Hadir?</p>
                <p class="mt-1 text-sm text-ink/60">Daftarkan diri lewat WhatsApp, tim kami akan mengonfirmasi kehadiranmu.</p>
                <span class="pulse-ring mt-4"><x-site.button :href="\App\Support\Format::waLink(config('mydream.whatsapp'), 'Halo MyDream, saya ingin mendaftar di event ' . $event->title . '.')" class="btn-shine">Daftar via WhatsApp</x-site.button></span>
            </div>
        @endif
    </article>

@endsection
