{{-- Hero halaman. image = nama foto di public/images/home/page-<nama>.jpg (opsional); src = URL foto langsung (mis. sampul artikel). --}}
@props([
    'eyebrow' => null,
    'title',
    'subtitle' => null,
    'image' => null,
    'src' => null,
    'fallback' => 'photo-1519741497674-611481863552',
    'align' => 'left',
    'size' => 'md',
])

@php
    $bg = $src ?: \App\Support\Media::home('page-' . $image, "https://images.unsplash.com/{$fallback}?w=1800&q=75&auto=format&fit=crop");
    $plain = html_entity_decode((string) $title, ENT_QUOTES | ENT_HTML5);
    $words = preg_split('/\s+/', trim($plain)) ?: [];
    $center = $align === 'center';
    $pad = $size === 'lg' ? 'py-24 sm:py-32' : 'py-16 sm:py-24';

    $wave = 'M0,34';
    for ($i = 0; $i < 8; $i++) {
        $x = $i * 360;
        $wave .= ' C' . ($x + 120) . ',52 ' . ($x + 240) . ',16 ' . ($x + 360) . ',34';
    }
    $wave .= ' L2880,60 L0,60 Z';
@endphp

<section data-hero data-page-hero class="relative isolate overflow-hidden bg-ink text-white">
    <div data-hero-bg class="absolute -inset-y-[10%] inset-x-0 -z-10 will-change-transform">
        <img src="{{ $bg }}" alt="" aria-hidden="true" class="kenburns h-full w-full object-cover opacity-55" fetchpriority="high">
        <div class="absolute inset-0 bg-linear-to-r from-ink/85 via-ink/50 to-maroon-dark/40"></div>
        <div class="absolute inset-x-0 bottom-0 h-32 bg-linear-to-t from-ink/70 to-transparent"></div>
    </div>

    <div class="absolute inset-0 -z-10 overflow-hidden" aria-hidden="true">
        @foreach (range(0, 6) as $i)
            <span class="petal" style="--x: {{ ($i * 41 + 9) % 100 }}%; --s: {{ 7 + ($i * 5) % 10 }}px; --t: {{ 15 + ($i * 6) % 12 }}s; --dl: -{{ ($i * 4) % 17 }}s; --r: {{ ($i * 67) % 360 }}deg; --sw: {{ (($i * 31) % 80) - 40 }}px"></span>
        @endforeach
    </div>

    <div data-hero-content class="mx-auto max-w-7xl px-4 {{ $pad }} sm:px-6 lg:px-8 {{ $center ? 'text-center' : '' }}">
        @if ($eyebrow)
            <p class="hero-in inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-white/90 backdrop-blur" style="--d: .05s">
                <span class="h-1.5 w-1.5 rounded-full bg-gold"></span> {{ html_entity_decode((string) $eyebrow, ENT_QUOTES | ENT_HTML5) }}
            </p>
        @endif

        <h1 class="mt-5 font-display text-4xl font-semibold leading-[1.1] sm:text-6xl {{ $center ? 'mx-auto max-w-4xl' : 'max-w-3xl' }}" aria-label="{{ $plain }}">
            @foreach ($words as $i => $word)
                <span class="word-mask" aria-hidden="true"><span class="word" style="--d: {{ 0.12 + $i * 0.08 }}s">{{ $word }}</span></span>
            @endforeach
        </h1>
        <span class="title-rule {{ $center ? 'mx-auto' : '' }}"></span>

        @if ($subtitle)
            <p class="hero-in mt-5 max-w-2xl text-lg leading-relaxed text-white/80 {{ $center ? 'mx-auto' : '' }}" style="--d: .55s">{{ html_entity_decode((string) $subtitle, ENT_QUOTES | ENT_HTML5) }}</p>
        @endif

        @if (trim((string) $slot) !== '')
            <div class="hero-in mt-7 {{ $center ? 'flex flex-wrap justify-center gap-2' : '' }}" style="--d: .75s">{{ $slot }}</div>
        @endif
    </div>

    <div class="wave-wrap" aria-hidden="true">
        <svg class="wave-svg" viewBox="0 0 2880 60" preserveAspectRatio="none"><path d="{{ $wave }}" fill="currentColor" /></svg>
    </div>
</section>
