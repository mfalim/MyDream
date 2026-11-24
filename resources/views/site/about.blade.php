@extends('layouts.site')

@section('title', $title)
@section('description', $description)

@section('content')

    @php
        $u = fn (string $id, int $w = 1000) => "https://images.unsplash.com/{$id}?w={$w}&q=80&auto=format&fit=crop";
        $values = [
            ['Personal & Hangat', 'Setiap konsep disesuaikan dengan cerita dan karakter pasangan, bukan template yang sama untuk semua orang.', 'M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z'],
            ['Transparan', 'Rincian harga, rundown, dan status pembayaran selalu jelas, tanpa biaya tersembunyi.', 'M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
            ['Vendor Terkurasi', 'Kami hanya bekerja sama dengan vendor yang sudah teruji kualitas dan responsnya.', 'M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z'],
        ];
        $way = [
            ['Dengar', 'Kami mulai dari cerita, karakter, dan harapan kalian berdua.'],
            ['Rancang', 'Konsep, budget, dan vendor disusun jadi satu rencana yang jelas.'],
            ['Dampingi', 'Dari rapat pemantapan sampai hari H, tim kami ada di sisi kalian.'],
        ];
    @endphp

    <x-site.page-hero eyebrow="Tentang Kami" title="Bagian dari MyDream Group" align="center" size="lg"
        subtitle="Kami percaya setiap pasangan berhak merayakan hari bahagianya dengan tenang, tanpa harus memikirkan setiap detail kecil sendirian."
        image="tentang" fallback="photo-1519225421980-715cb0215aed" />

    <section class="mx-auto grid max-w-6xl items-center gap-14 px-4 py-20 sm:px-6 lg:grid-cols-2 lg:px-8">
        <div data-reveal="left">
            <p class="text-xs font-semibold uppercase tracking-widest text-emerald">Cerita Kami</p>
            <h2 class="mt-2 font-display text-3xl font-semibold leading-tight">Dari Jember, untuk Setiap Pasangan</h2>
            <p class="mt-5 leading-relaxed text-ink/70">MyDream Wedding Organizer lahir dari keinginan sederhana: membuat proses merencanakan pernikahan terasa hangat, bukan melelahkan. Berbasis di Jember dan melayani hingga Surabaya, kami mendampingi pasangan mulai dari konsep, pemilihan vendor terpercaya, hingga koordinasi penuh di hari H.</p>
        </div>

        <div class="relative mx-auto w-full max-w-md pb-8">
            <div data-reveal="mask" class="w-4/5 overflow-hidden rounded-3xl shadow-xl">
                <img src="{{ \App\Support\Media::home('about-1', $u('photo-1519741497674-611481863552')) }}" alt="Tim MyDream bersama pasangan pengantin" loading="lazy" class="aspect-[4/5] w-full object-cover">
            </div>
            <div data-parallax="-0.08" class="absolute bottom-0 right-0 w-1/2">
                <div data-reveal="zoom" style="--d: .3s" class="overflow-hidden rounded-3xl shadow-xl ring-8 ring-cream">
                    <img src="{{ \App\Support\Media::home('about-2', $u('photo-1522673607200-164d1b6ce486', 700)) }}" alt="Detail dekorasi pernikahan" loading="lazy" class="aspect-square w-full object-cover">
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="mx-auto grid max-w-5xl grid-cols-3 gap-4 px-4 text-center sm:px-6 lg:px-8">
            @foreach ([['weddings', 'Pernikahan ditangani'], ['vendors', 'Vendor rekanan'], ['packages', 'Paket pilihan']] as $i => [$key, $label])
                <div data-reveal="pop" style="--d: {{ $i * 0.15 }}s">
                    <p class="font-display text-4xl font-semibold text-maroon sm:text-5xl" data-count="{{ $stats[$key] }}">{{ $stats[$key] }}</p>
                    <p class="mt-2 text-xs text-ink/60 sm:text-sm">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="bg-cream-dark py-20">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <h2 class="mb-12 text-center font-display text-3xl font-semibold" data-reveal="blur">Nilai yang Kami Pegang</h2>
            <div class="grid gap-6 sm:grid-cols-3" style="perspective: 1200px">
                @foreach ($values as $i => [$title, $desc, $icon])
                    <div data-reveal style="--d: {{ $i * 0.12 }}s">
                        <div data-tilt class="tilt-card group h-full rounded-2xl bg-white p-7 text-center shadow-sm ring-1 ring-black/5 hover:shadow-xl">
                            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-maroon-light text-maroon transition-all duration-300 group-hover:rotate-6 group-hover:bg-maroon group-hover:text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" /></svg>
                            </span>
                            <p class="mt-5 font-display text-lg font-semibold">{{ $title }}</p>
                            <p class="mt-2 text-sm leading-relaxed text-ink/60">{{ $desc }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-5xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="mb-12 text-center" data-reveal="blur">
            <p class="text-xs font-semibold uppercase tracking-widest text-emerald">Cara Kami Bekerja</p>
            <h2 class="mt-2 font-display text-3xl font-semibold">Tiga Langkah Sederhana</h2>
        </div>
        <div class="relative grid gap-10 sm:grid-cols-3">
            <div data-reveal="line" class="absolute left-[16.6%] right-[16.6%] top-7 hidden h-px bg-linear-to-r from-transparent via-gold to-transparent sm:block" aria-hidden="true"></div>
            @foreach ($way as $i => [$title, $desc])
                <div class="relative text-center">
                    <span data-reveal="pop" style="--d: {{ 0.2 + $i * 0.25 }}s" class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border border-gold bg-cream font-display text-xl text-maroon">{{ $i + 1 }}</span>
                    <div data-reveal style="--d: {{ 0.3 + $i * 0.25 }}s">
                        <p class="mt-5 font-display text-lg font-semibold">{{ $title }}</p>
                        <p class="mt-2 text-sm leading-relaxed text-ink/60">{{ $desc }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
        <x-site.cta-banner heading="Yuk, Ngobrol Santai Dulu" description="Ceritakan gambaran pernikahan impianmu, tim MyDream siap membantu dari langkah pertama." />
    </section>

@endsection
