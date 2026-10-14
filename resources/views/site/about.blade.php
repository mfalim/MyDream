@extends('layouts.site')

@section('title', 'Tentang Kami - MyDream Wedding Organizer')
@section('description', 'Kenali MyDream Wedding Organizer, pendamping pernikahan di Jember dan Surabaya.')

@section('content')

    <section class="border-b border-black/5 bg-white">
        <div class="mx-auto max-w-5xl px-4 py-16 text-center sm:px-6 lg:px-8">
            <p class="text-xs font-semibold uppercase tracking-widest text-maroon">Tentang Kami</p>
            <h1 class="mt-4 font-display text-4xl font-semibold sm:text-5xl">Bagian dari MyDream Group</h1>
            <p class="mx-auto mt-4 max-w-2xl text-ink/60">Kami percaya setiap pasangan berhak merayakan hari bahagianya dengan tenang, tanpa harus memikirkan setiap detail kecil sendirian.</p>
        </div>
    </section>

    <section class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid gap-10 sm:grid-cols-2">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-emerald">Cerita Kami</p>
                <h2 class="mt-2 font-display text-2xl font-semibold">Dari Jember, untuk Setiap Pasangan</h2>
            </div>
            <p class="leading-relaxed text-ink/70">MyDream Wedding Organizer lahir dari keinginan sederhana: membuat proses merencanakan pernikahan terasa hangat, bukan melelahkan. Berbasis di Jember dan melayani hingga Surabaya, kami mendampingi pasangan mulai dari konsep, pemilihan vendor terpercaya, hingga koordinasi penuh di hari H.</p>
        </div>
    </section>

    <section class="bg-cream-dark py-16">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <h2 class="mb-10 text-center font-display text-3xl font-semibold">Nilai yang Kami Pegang</h2>
            <div class="grid gap-6 sm:grid-cols-3">
                @foreach ([
                    ['Personal & Hangat', 'Setiap konsep disesuaikan dengan cerita dan karakter pasangan, bukan template yang sama untuk semua orang.'],
                    ['Transparan', 'Rincian harga, rundown, dan status pembayaran selalu jelas, tanpa biaya tersembunyi.'],
                    ['Vendor Terkurasi', 'Kami hanya bekerja sama dengan vendor yang sudah teruji kualitas dan responsnya.'],
                ] as [$title, $desc])
                    <div class="rounded-2xl bg-white p-6 text-center shadow-sm ring-1 ring-black/5">
                        <p class="font-display text-lg font-semibold">{{ $title }}</p>
                        <p class="mt-2 text-sm leading-relaxed text-ink/60">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <x-site.cta-banner heading="Yuk, Ngobrol Santai Dulu" description="Ceritakan gambaran pernikahan impianmu, tim MyDream siap membantu dari langkah pertama." />
    </section>

@endsection
