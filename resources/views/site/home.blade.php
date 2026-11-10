@extends('layouts.site')

@section('title', 'MyDream Wedding Organizer - Pasti Nikahmu Beda')
@section('description', 'Wedding organizer di Jember dan Surabaya. Konsep personal, vendor terkurasi, dan koordinasi penuh sampai hari H.')

@push('head')
    <script>document.documentElement.classList.add('js')</script>
@endpush

@section('content')

    @php
        // Foto asli di public/images/home/<nama>.jpg dipakai otomatis; kalau belum ada, pakai foto contoh.
        $u = fn (string $id, int $w = 1600) => "https://images.unsplash.com/{$id}?w={$w}&q=80&auto=format&fit=crop";
        $photo = fn (string $name, string $fallback) => \App\Support\Media::home($name, $fallback);

        $hero = [
            $photo('hero-1', $u('photo-1519741497674-611481863552')),
            $photo('hero-2', $u('photo-1522673607200-164d1b6ce486')),
            $photo('hero-3', $u('photo-1465495976277-4387d4b0b4c6')),
            $photo('hero-4', $u('photo-1519225421980-715cb0215aed')),
        ];

        // Galeri: foto asli dari cerita client yang disetujui, dilengkapi foto contoh sampai 6 buah.
        $gallery = $moments->map(fn ($m) => [
            'src' => \App\Support\Media::url($m->cover),
            'title' => $m->title,
            'meta' => $m->theme,
            'url' => route('inspiration.show', $m->slug),
        ])->all();
        $sample = [
            [$u('photo-1519225421980-715cb0215aed', 900), 'Resepsi yang hangat', 'Ballroom'],
            [$u('photo-1465495976277-4387d4b0b4c6', 900), 'Akad di taman terbuka', 'Outdoor'],
            [$u('photo-1522673607200-164d1b6ce486', 900), 'Detail yang bermakna', 'Dekorasi'],
            [$u('photo-1555244162-803834f70033', 900), 'Jamuan untuk keluarga', 'Intimate'],
            [$u('photo-1594552072238-b8a33785b261', 900), 'Setiap sudut tertata', 'Venue'],
            [$u('photo-1519741497674-611481863552', 900), 'Momen yang dikenang', 'Real Wedding'],
        ];
        foreach ($sample as [$src, $title, $meta]) {
            if (count($gallery) >= 6) { break; }
            $gallery[] = ['src' => $src, 'title' => $title, 'meta' => $meta, 'url' => route('inspiration.index')];
        }
        $spans = [0 => 'col-span-2 row-span-2', 3 => 'md:col-span-2'];

        $services = [
            ['M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5', 'Perencanaan dari Nol', 'Teman diskusi sejak awal. Kami bantu cari ide tema, susun budget, dan pilih vendor yang tepat.'],
            ['M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z', 'Pengawal Hari Acara', 'Fokus nikmati hari bahagiamu. Tim kami jaga ketepatan waktu dan pastikan seluruh vendor bergerak kompak.'],
            ['M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z', 'Vendor Terpercaya', 'Dekorator, MUA, fotografer, dan sound system terbaik tanpa perlu repot mencari satu per satu.'],
            ['M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z', 'Lamaran & Acara Keluarga', 'Pendampingan momen pra-nikah seperti lamaran, siraman, dan pengajian agar tetap khidmat dan tertata.'],
        ];

        $steps = [
            ['Ngobrol Santai', 'Ceritakan konsep impian dan gambaran budget lewat chat atau kunjungi kami langsung.'],
            ['Matangkan Rencana', 'Kami susun moodboard visual, pilihan vendor yang cocok, dan jadwal acara.'],
            ['Rapat Pemantapan', 'Duduk bersama keluarga dan seluruh vendor menyelaraskan detail 2-3 minggu sebelum acara.'],
            ['Hari Pernikahan', 'Saatnya tersenyum dan menikmati hari spesialmu dengan tenang.'],
        ];

        $testimonials = [
            ['Detail acaranya rapi banget. Acara adat Jawanya khidmat, resepsinya juga seru tanpa jeda canggung. Tim My Dream sigap sekali.', 'Erwin & Anggita', 'Adat Jawa Solo'],
            ['Bener-bener bikin tenang. Dari awal konsultasi sampai hari-H komunikasinya enak dan fleksibel banget diajak diskusi.', 'Feby & Yudo', 'Outdoor Modern'],
            ['Pilihan paling tepat buat wedding di Jember. Rundown tepat waktu dan koordinasi vendornya juara.', 'Vio & Tika', 'Ballroom Reception'],
        ];

        $marquee = ['Adat Jawa', 'Garden Party', 'Ballroom', 'Intimate Wedding', 'Outdoor Ceremony', 'Akad & Lamaran', 'Rustic', 'Minimalis'];
    @endphp

    {{-- ================= HERO ================= --}}
    <section class="relative isolate flex min-h-[calc(100svh-5rem)] items-center overflow-hidden bg-ink text-white">
        <div class="absolute inset-0 -z-10">
            @foreach ($hero as $i => $src)
                <img src="{{ $src }}" alt="" aria-hidden="true" class="hero-slide absolute inset-0 h-full w-full object-cover" style="animation-delay: {{ $i * 6 }}s"
                     @if ($i === 0) fetchpriority="high" @else loading="lazy" @endif>
            @endforeach
            <div class="absolute inset-0 bg-linear-to-r from-ink/85 via-ink/55 to-maroon-dark/40"></div>
            <div class="absolute inset-x-0 bottom-0 h-40 bg-linear-to-t from-ink/70 to-transparent"></div>
        </div>

        <div class="mx-auto w-full max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <p class="animate-fade-up inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-white/90 backdrop-blur">
                <span class="h-1.5 w-1.5 rounded-full bg-gold"></span> MyDream Wedding Organizer · Jember &amp; Surabaya
            </p>
            <h1 class="animate-fade-up mt-6 max-w-3xl font-display text-5xl font-semibold leading-[1.05] sm:text-7xl" style="animation-delay: .15s">
                Pasti Nikahmu <span class="italic text-gold">Beda!</span>
            </h1>
            <p class="animate-fade-up mt-6 max-w-xl text-lg leading-relaxed text-white/80" style="animation-delay: .3s">
                Bikin hari bahagiamu terasa lebih personal, hangat, dan tanpa beban. Kamu tinggal nikmati momennya, biar kami yang urus semua detailnya.
            </p>
            <div class="animate-fade-up mt-9 flex flex-wrap items-center gap-4" style="animation-delay: .45s">
                <x-site.button :href="route('catalog.index')" size="lg">Lihat Paket &amp; Vendor</x-site.button>
                <a href="{{ route('inspiration.index') }}" class="inline-flex items-center gap-2 rounded-full border border-white/40 px-8 py-4 text-base font-medium text-white backdrop-blur transition-colors hover:bg-white/15">Lihat Cerita Mereka</a>
            </div>

            <div class="animate-fade-up mt-14 flex flex-wrap gap-3" style="animation-delay: .6s">
                <div class="rounded-2xl border border-white/20 bg-white/10 px-5 py-3 backdrop-blur"><p class="font-display text-2xl font-semibold">{{ $stats['vendors'] }}</p><p class="text-xs text-white/70">Vendor rekanan</p></div>
                <div class="rounded-2xl border border-white/20 bg-white/10 px-5 py-3 backdrop-blur"><p class="font-display text-2xl font-semibold">{{ $stats['packages'] }}</p><p class="text-xs text-white/70">Paket pilihan</p></div>
                <div class="rounded-2xl border border-white/20 bg-white/10 px-5 py-3 backdrop-blur"><p class="font-display text-2xl font-semibold">{{ count(config('mydream.cities')) }}</p><p class="text-xs text-white/70">Kota layanan</p></div>
            </div>
        </div>

        <a href="#tentang" aria-label="Gulir ke bawah" class="absolute bottom-6 left-1/2 hidden -translate-x-1/2 sm:block">
            <span class="flex h-10 w-6 items-start justify-center rounded-full border border-white/50 pt-2"><span class="scroll-dot h-1.5 w-1.5 rounded-full bg-white"></span></span>
        </a>
    </section>

    {{-- ================= PITA TEKS ================= --}}
    <div class="overflow-hidden bg-maroon py-4 text-cream" aria-hidden="true">
        <div class="marquee-track">
            @for ($i = 0; $i < 2; $i++)
                <div class="flex shrink-0 items-center">
                    @foreach ($marquee as $word)
                        <span class="px-8 font-display text-lg italic">{{ $word }}</span><span class="text-gold">&#10022;</span>
                    @endforeach
                </div>
            @endfor
        </div>
    </div>

    {{-- ================= TENTANG ================= --}}
    <section id="tentang" class="mx-auto grid max-w-7xl items-center gap-16 px-4 py-24 sm:px-6 lg:grid-cols-2 lg:px-8">
        <div class="relative mx-auto w-full max-w-md pb-10 lg:max-w-none" data-reveal>
            <img src="{{ $photo('about-1', $u('photo-1519741497674-611481863552', 1000)) }}" alt="Pasangan pengantin bersama tim MyDream" loading="lazy" class="aspect-[4/5] w-4/5 rounded-3xl object-cover shadow-xl">
            <img src="{{ $photo('about-2', $u('photo-1522673607200-164d1b6ce486', 700)) }}" alt="Detail dekorasi pernikahan" loading="lazy" class="absolute bottom-0 right-0 aspect-square w-1/2 rounded-3xl object-cover shadow-xl ring-8 ring-cream">
            <div class="absolute left-4 top-6 rounded-2xl bg-white/90 px-4 py-3 shadow-lg backdrop-blur">
                <p class="font-display text-sm font-semibold text-maroon">MyDream Organizer</p>
                <p class="text-xs text-ink/60">Wedding organizer Jember</p>
            </div>
        </div>

        <div data-reveal style="--d: .15s">
            <p class="text-xs font-semibold uppercase tracking-widest text-emerald">Tentang My Dream</p>
            <h2 class="mt-3 font-display text-4xl font-semibold leading-tight">Ceritamu, Detail Kami.</h2>
            <p class="mt-2 italic text-ink/50">&ldquo;Karena setiap kisah cinta punya rasa dan keunikan tersendiri.&rdquo;</p>
            <p class="mt-6 leading-relaxed text-ink/70">Mempersiapkan pernikahan tidak harus bikin pusing. Di MyDream Organizer, kami mendampingi kamu mulai dari merapikan ide konsep, memilih vendor terbaik, sampai memastikan acara di hari-H berjalan mengalir.</p>

            <div class="mt-10 grid grid-cols-3 gap-4 border-t border-black/10 pt-8">
                <div>
                    <p class="font-display text-4xl font-semibold text-maroon" data-count="{{ $stats['weddings'] }}">{{ $stats['weddings'] }}</p>
                    <p class="mt-1 text-xs text-ink/60">Pernikahan ditangani</p>
                </div>
                <div>
                    <p class="font-display text-4xl font-semibold text-maroon" data-count="{{ $stats['vendors'] }}">{{ $stats['vendors'] }}</p>
                    <p class="mt-1 text-xs text-ink/60">Vendor rekanan</p>
                </div>
                <div>
                    <p class="font-display text-4xl font-semibold text-maroon" data-count="{{ $stats['packages'] }}">{{ $stats['packages'] }}</p>
                    <p class="mt-1 text-xs text-ink/60">Paket pilihan</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= TIM MYDREAM ================= --}}
    <section class="mx-auto max-w-7xl px-4 pb-24 sm:px-6 lg:px-8">
        <div class="group relative overflow-hidden rounded-[2rem]" data-reveal>
            <img src="{{ $photo('team', $u('photo-1516035069371-29a1b244cc32', 1800)) }}" alt="Tim MyDream bekerja di lokasi acara" loading="lazy" class="h-[26rem] w-full object-cover transition-transform duration-[1500ms] group-hover:scale-105 sm:h-[32rem]">
            <div class="absolute inset-0 bg-linear-to-t from-ink/85 via-ink/30 to-transparent"></div>
            <div class="absolute inset-x-0 bottom-0 p-6 text-white sm:p-10">
                <p class="text-xs font-semibold uppercase tracking-widest text-gold">Di Balik Layar</p>
                <h2 class="mt-2 max-w-xl font-display text-3xl font-semibold leading-tight sm:text-4xl">Tim MyDream hadir dari persiapan sampai hari H.</h2>
                <p class="mt-3 max-w-xl text-white/80">Satu pintu komunikasi, satu rundown, dan tim yang paham karakter venue serta vendor lokal.</p>
                <div class="mt-6"><x-site.button :href="\App\Support\Format::waLink(config('mydream.whatsapp'), 'Halo MyDream, saya ingin konsultasi pernikahan.')">Ngobrol dengan Tim Kami</x-site.button></div>
            </div>
        </div>
    </section>

    {{-- ================= MOMEN (GALERI) ================= --}}
    <section class="bg-white py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 flex flex-wrap items-end justify-between gap-4" data-reveal>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-emerald">Dokumentasi Karya</p>
                    <h2 class="mt-2 font-display text-4xl font-semibold">Momen yang Kami Ciptakan</h2>
                </div>
                <a href="{{ route('inspiration.index') }}" class="text-sm font-medium text-maroon hover:underline">Lihat semua cerita &rarr;</a>
            </div>

            <div class="grid auto-rows-[160px] grid-cols-2 gap-3 sm:auto-rows-[200px] md:auto-rows-[220px] md:grid-cols-4">
                @foreach ($gallery as $i => $item)
                    <a href="{{ $item['url'] }}" class="group relative overflow-hidden rounded-2xl bg-cream-dark {{ $spans[$i] ?? '' }}" data-reveal style="--d: {{ $i * 0.08 }}s">
                        <img src="{{ $item['src'] }}" alt="{{ $item['title'] }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-linear-to-t from-ink/75 via-transparent to-transparent opacity-80 transition-opacity group-hover:opacity-100"></div>
                        <div class="absolute inset-x-0 bottom-0 translate-y-1 p-4 text-white transition-transform duration-500 group-hover:translate-y-0">
                            <p class="text-[10px] font-semibold uppercase tracking-widest text-gold">{{ $item['meta'] }}</p>
                            <p class="font-display text-base font-semibold leading-snug">{{ $item['title'] }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= VENDOR ================= --}}
    @if ($vendors->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
            <div class="mb-10 flex items-end justify-between gap-4" data-reveal>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-emerald">Vendor Rekanan Terkurasi</p>
                    <h2 class="mt-2 font-display text-4xl font-semibold">Vendor Pilihan MyDream</h2>
                </div>
                <a href="{{ route('catalog.index', ['type' => 'vendor']) }}" class="hidden text-sm font-medium text-maroon hover:underline sm:inline">Lihat semua vendor &rarr;</a>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($vendors as $i => $vendor)
                    <div data-reveal style="--d: {{ $i * 0.1 }}s"><x-site.vendor-card :vendor="$vendor" /></div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ================= PAKET ================= --}}
    @if ($packages->isNotEmpty())
        <section class="bg-white py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-10 flex items-end justify-between gap-4" data-reveal>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-emerald">Paket Resmi</p>
                        <h2 class="mt-2 font-display text-4xl font-semibold">Paket Pilihan Terkurasi</h2>
                    </div>
                    <a href="{{ route('catalog.index', ['type' => 'package']) }}" class="hidden text-sm font-medium text-maroon hover:underline sm:inline">Jelajahi paket lainnya &rarr;</a>
                </div>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($packages as $i => $package)
                        <div data-reveal style="--d: {{ $i * 0.1 }}s"><x-site.package-card :package="$package" /></div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ================= LAYANAN ================= --}}
    <section class="bg-cream-dark py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-12 text-center" data-reveal>
                <p class="text-xs font-semibold uppercase tracking-widest text-emerald">Bantuan yang Kami Siapkan</p>
                <h2 class="mt-2 font-display text-4xl font-semibold">Untuk Harimu</h2>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($services as $i => [$icon, $title, $desc])
                    <div class="group rounded-3xl bg-white p-7 ring-1 ring-black/5 transition-all duration-300 hover:-translate-y-2 hover:shadow-xl" data-reveal style="--d: {{ $i * 0.1 }}s">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-maroon-light text-maroon transition-colors group-hover:bg-maroon group-hover:text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" /></svg>
                        </span>
                        <p class="mt-5 font-display text-xl font-semibold">{{ $title }}</p>
                        <p class="mt-2 text-sm leading-relaxed text-ink/60">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= LANGKAH ================= --}}
    <section class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
        <div class="mb-14 text-center" data-reveal>
            <p class="text-xs font-semibold uppercase tracking-widest text-emerald">Langkah Persiapan</p>
            <h2 class="mt-2 font-display text-4xl font-semibold">Perjalanan Menuju Hari Bahagiamu</h2>
        </div>
        <div class="relative grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
            <div class="absolute left-[12.5%] right-[12.5%] top-7 hidden h-px bg-linear-to-r from-transparent via-gold to-transparent lg:block" aria-hidden="true"></div>
            @foreach ($steps as $i => [$title, $desc])
                <div class="relative text-center" data-reveal style="--d: {{ $i * 0.15 }}s">
                    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border border-gold bg-cream font-display text-xl text-maroon">{{ $i + 1 }}</span>
                    <p class="mt-5 font-display text-lg font-semibold">{{ $title }}</p>
                    <p class="mt-2 text-sm leading-relaxed text-ink/60">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ================= CERITA / ARTIKEL ================= --}}
    @if ($stories->isNotEmpty())
        <section class="bg-white py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-10 flex items-end justify-between gap-4" data-reveal>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-emerald">Cerita &amp; Tips</p>
                        <h2 class="mt-2 font-display text-4xl font-semibold">Cerita yang Sudah Terwujud</h2>
                    </div>
                    <a href="{{ route('blog.index') }}" class="text-sm font-medium text-maroon hover:underline">Semua artikel &rarr;</a>
                </div>
                <div class="grid gap-8 sm:grid-cols-3">
                    @foreach ($stories as $i => $story)
                        <div data-reveal style="--d: {{ $i * 0.12 }}s"><x-site.article-card :article="$story" /></div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ================= TESTIMONI ================= --}}
    <section class="py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h2 class="mb-10 text-center font-display text-4xl font-semibold" data-reveal>Cerita Manis dari Mereka</h2>
            <div class="-mx-4 flex snap-x snap-mandatory gap-6 overflow-x-auto px-4 pb-4 sm:mx-0 sm:grid sm:grid-cols-3 sm:overflow-visible sm:px-0">
                @foreach ($testimonials as $i => [$quote, $name, $theme])
                    <blockquote class="min-w-[85%] snap-center rounded-3xl bg-white p-8 shadow-sm ring-1 ring-black/5 sm:min-w-0" data-reveal style="--d: {{ $i * 0.12 }}s">
                        <p class="font-display text-5xl leading-none text-gold">&ldquo;</p>
                        <p class="-mt-2 italic leading-relaxed text-ink/70">{{ $quote }}</p>
                        <footer class="mt-6 flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-maroon font-display text-lg text-white">{{ mb_substr($name, 0, 1) }}</span>
                            <span><span class="block text-sm font-semibold text-maroon">{{ $name }}</span><span class="block text-xs text-ink/50">{{ $theme }}</span></span>
                        </footer>
                    </blockquote>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= CTA ================= --}}
    <section class="relative isolate overflow-hidden bg-ink py-28 text-center text-white">
        <img src="{{ $photo('cta', $u('photo-1519225421980-715cb0215aed', 1800)) }}" alt="" aria-hidden="true" loading="lazy" class="absolute inset-0 -z-10 h-full w-full object-cover opacity-40">
        <div class="absolute inset-0 -z-10 bg-linear-to-b from-ink/70 via-ink/50 to-ink/80"></div>
        <div class="mx-auto max-w-3xl px-4" data-reveal>
            <p class="text-xs font-semibold uppercase tracking-widest text-gold">Mulai dari Sekarang</p>
            <h2 class="mt-4 font-display text-4xl font-semibold leading-tight sm:text-5xl">Yuk, Bikin Pernikahan Impianmu Jadi Nyata.</h2>
            <p class="mx-auto mt-5 max-w-xl text-white/75">Punya gambaran pernikahan sendiri? Ceritakan ke kami, kita mulai dari obrolan santai via WhatsApp.</p>
            <div class="mt-9 flex flex-wrap justify-center gap-4">
                <x-site.button :href="\App\Support\Format::waLink(config('mydream.whatsapp'), 'Halo MyDream, saya ingin konsultasi pernikahan.')" size="lg">Hubungi Kami via WhatsApp</x-site.button>
                <a href="{{ route('catalog.index') }}" class="inline-flex items-center rounded-full border border-white/40 px-8 py-4 text-base font-medium text-white transition-colors hover:bg-white/15">Lihat Paket</a>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            (function () {
                var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

                // Muncul saat di-scroll
                var items = document.querySelectorAll('[data-reveal]');
                if (reduce || !('IntersectionObserver' in window)) {
                    items.forEach(function (el) { el.classList.add('is-visible'); });
                } else {
                    var io = new IntersectionObserver(function (entries) {
                        entries.forEach(function (entry) {
                            if (entry.isIntersecting) { entry.target.classList.add('is-visible'); io.unobserve(entry.target); }
                        });
                    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
                    items.forEach(function (el) { io.observe(el); });
                }

                // Hitung naik untuk angka
                var counters = document.querySelectorAll('[data-count]');
                var run = function (el) {
                    var target = parseInt(el.dataset.count, 10) || 0;
                    if (reduce || target === 0) { return; }
                    var start = null;
                    var step = function (t) {
                        if (start === null) { start = t; }
                        var p = Math.min((t - start) / 1400, 1);
                        el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3))).toLocaleString('id-ID');
                        if (p < 1) { requestAnimationFrame(step); }
                    };
                    requestAnimationFrame(step);
                };
                if ('IntersectionObserver' in window) {
                    var co = new IntersectionObserver(function (entries) {
                        entries.forEach(function (entry) {
                            if (entry.isIntersecting) { run(entry.target); co.unobserve(entry.target); }
                        });
                    }, { threshold: 0.6 });
                    counters.forEach(function (el) { co.observe(el); });
                }
            })();
        </script>
    @endpush

@endsection
