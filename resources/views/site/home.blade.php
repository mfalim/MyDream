@extends('layouts.site')

@section('title', 'MyDream Wedding Organizer - Pasti Nikahmu Beda')
@section('description', 'Bikin hari bahagiamu terasa lebih personal, hangat, dan tanpa beban bersama MyDream Wedding Organizer, Jember.')

@section('content')

    {{-- HERO --}}
    <section class="mx-auto max-w-7xl px-4 py-20 text-center sm:px-6 sm:py-28 lg:px-8">
        <p class="text-xs font-semibold uppercase tracking-widest text-maroon">Part of MyDream Group · Wedding Organizer Jember</p>
        <h1 class="mt-6 font-display text-4xl font-semibold leading-tight sm:text-6xl">Pasti Nikahmu <span class="italic text-maroon">Beda!</span></h1>
        <p class="mx-auto mt-6 max-w-2xl text-lg text-ink/60">Bikin hari bahagiamu terasa lebih personal, hangat, dan tanpa beban. Kamu tinggal nikmati momennya, biar kami yang urus semua detailnya.</p>
        <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
            <x-site.button :href="route('catalog.index')" size="lg">Lihat Paket &amp; Vendor</x-site.button>
            <x-site.button :href="route('store')" variant="outline" size="lg">Jelajahi Store</x-site.button>
        </div>
    </section>

    {{-- TENTANG --}}
    <section class="mx-auto grid max-w-7xl items-start gap-12 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-emerald">Tentang My Dream</p>
            <h2 class="mt-4 font-display text-3xl font-semibold leading-snug">Ceritamu, Detail Kami.</h2>
            <p class="mt-4 italic text-ink/50">&ldquo;Karena setiap kisah cinta punya rasa dan keunikan tersendiri.&rdquo;</p>
        </div>
        <div class="space-y-6">
            <p class="leading-relaxed text-ink/70">Mempersiapkan pernikahan tidak harus bikin pusing. Di MyDream Organizer, kami mendampingi kamu mulai dari merapikan ide konsep, memilih vendor terbaik, sampai memastikan acara di hari-H berjalan mengalir.</p>
            <div class="grid gap-6 sm:grid-cols-3">
                @foreach ([
                    ['Konsep Sesuai Karakter', 'Mencerminkan nilai dan impian berdua secara presisi.'],
                    ['Koordinasi Rapi', 'Seluruh vendor terkoneksi lewat rundown yang matang.'],
                    ['Eksekusi Tanpa Ribet', 'Keluarga dan mempelai fokus menikmati hari bahagia.'],
                ] as [$title, $desc])
                    <div>
                        <p class="font-display text-sm font-semibold text-maroon">{{ $title }}</p>
                        <p class="mt-1 text-xs text-ink/50">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- VENDOR PILIHAN --}}
    @if ($vendors->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-emerald">Vendor Rekanan Terkurasi</p>
                    <h2 class="mt-2 font-display text-3xl font-semibold">Vendor Pilihan MyDream</h2>
                </div>
                <a href="{{ route('catalog.index', ['type' => 'vendor']) }}" class="hidden text-sm font-medium text-maroon hover:underline sm:inline">Lihat Semua Vendor &rarr;</a>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($vendors as $vendor)
                    <x-site.vendor-card :vendor="$vendor" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- PAKET --}}
    @if ($packages->isNotEmpty())
        <section class="bg-white py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-8 flex items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-emerald">Paket Resmi</p>
                        <h2 class="mt-2 font-display text-3xl font-semibold">Paket Pilihan Terkurasi</h2>
                    </div>
                    <a href="{{ route('catalog.index', ['type' => 'package']) }}" class="hidden text-sm font-medium text-maroon hover:underline sm:inline">Jelajahi Paket Lainnya &rarr;</a>
                </div>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($packages as $package)
                        <x-site.package-card :package="$package" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- CERITA --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="mb-10 text-center">
            <p class="text-xs font-semibold uppercase tracking-widest text-emerald">Dokumentasi Karya</p>
            <h2 class="mt-2 font-display text-3xl font-semibold">Cerita yang Sudah Terwujud</h2>
        </div>
        <div class="grid gap-8 sm:grid-cols-3">
            @foreach ($stories as $story)
                <x-site.article-card :article="$story" />
            @endforeach
        </div>
    </section>

    {{-- LAYANAN --}}
    <section class="bg-cream-dark py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <p class="text-xs font-semibold uppercase tracking-widest text-emerald">Bantuan yang Kami Siapkan</p>
                <h2 class="mt-2 font-display text-3xl font-semibold">Untuk Harimu</h2>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['Perencanaan dari Nol', 'Teman diskusi sejak awal. Kami bantu cari ide tema, susun budget, dan pilih vendor yang tepat.'],
                    ['Pengawal Hari Acara', 'Fokus nikmati hari bahagiamu. Tim kami jaga ketepatan waktu dan pastikan seluruh vendor bergerak kompak.'],
                    ['Pilihan Vendor Terpercaya', 'Kami hubungkan kamu dengan dekorator, MUA, fotografer, dan sound system terbaik tanpa repot mencari satu per satu.'],
                    ['Lamaran & Acara Keluarga', 'Pendampingan momen pra-nikah seperti lamaran, siraman, dan pengajian agar tetap khidmat dan tertata.'],
                ] as $i => [$title, $desc])
                    <div class="rounded-2xl bg-white p-6 ring-1 ring-black/5">
                        <p class="font-display text-sm text-gold">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</p>
                        <p class="mt-3 font-display text-lg font-semibold">{{ $title }}</p>
                        <p class="mt-2 text-sm leading-relaxed text-ink/60">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- LANGKAH --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="mb-10 text-center">
            <p class="text-xs font-semibold uppercase tracking-widest text-emerald">Langkah Persiapan</p>
            <h2 class="mt-2 font-display text-3xl font-semibold">Perjalanan Menuju Hari Bahagiamu</h2>
        </div>
        <div class="grid gap-8 text-center sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['Ngobrol Santai', 'Ceritakan konsep impian dan gambaran budget lewat chat atau kunjungi kami langsung.'],
                ['Matangkan Rencana', 'Kami susun moodboard visual, pilihan vendor yang cocok, dan jadwal acara.'],
                ['Rapat Pemantapan', 'Duduk bersama keluarga dan seluruh vendor menyelaraskan detail 2-3 minggu sebelum acara.'],
                ['Hari Pernikahan', 'Saatnya tersenyum dan menikmati hari spesialmu dengan tenang.'],
            ] as $i => [$title, $desc])
                <div>
                    <p class="font-display text-2xl text-gold">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</p>
                    <p class="mt-3 font-display text-base font-semibold">{{ $title }}</p>
                    <p class="mt-2 text-sm leading-relaxed text-ink/60">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- TESTIMONI --}}
    <section class="bg-white py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h2 class="mb-10 text-center font-display text-3xl font-semibold">Cerita Manis dari Mereka</h2>
            <div class="grid gap-8 sm:grid-cols-3">
                @foreach ([
                    ['Detail acaranya rapi banget. Acara adat Jawanya khidmat, resepsinya juga seru tanpa jeda canggung. Tim My Dream sigap sekali.', 'Erwin & Anggita'],
                    ['Bener-bener bikin tenang. Dari awal konsultasi sampai hari-H komunikasinya enak dan fleksibel banget diajak diskusi.', 'Feby & Yudo'],
                    ['Pilihan paling tepat buat wedding di Jember. Rundown tepat waktu dan koordinasi vendornya juara.', 'Vio & Tika'],
                ] as [$quote, $name])
                    <blockquote class="rounded-2xl bg-cream p-6">
                        <p class="italic leading-relaxed text-ink/70">&ldquo;{{ $quote }}&rdquo;</p>
                        <footer class="mt-4 text-sm font-semibold text-maroon">&mdash; {{ $name }}</footer>
                    </blockquote>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <x-site.cta-banner description="Punya gambaran pernikahan sendiri? Ceritakan ke kami, kita mulai dari obrolan santai via WhatsApp." />
    </section>

@endsection
