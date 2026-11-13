@props([
    'heading' => 'Yuk, Bikin Pernikahan Impianmu Jadi Nyata.',
    'description' => null,
])

<section {{ $attributes->merge(['class' => 'relative isolate overflow-hidden rounded-3xl bg-ink px-6 py-16 text-center sm:px-16']) }} data-reveal="zoom">
    <div class="absolute inset-0 -z-10 bg-linear-to-br from-maroon-dark/60 via-ink to-ink" aria-hidden="true"></div>
    <div class="absolute inset-0 -z-10 overflow-hidden" aria-hidden="true">
        @foreach (range(0, 5) as $i)
            <span class="petal" style="--x: {{ ($i * 47 + 13) % 100 }}%; --s: {{ 7 + ($i * 4) % 9 }}px; --t: {{ 14 + ($i * 5) % 11 }}s; --dl: -{{ ($i * 5) % 15 }}s; --r: {{ ($i * 59) % 360 }}deg; --sw: {{ (($i * 37) % 70) - 35 }}px"></span>
        @endforeach
    </div>

    <p class="text-xs font-semibold uppercase tracking-widest text-gold">Mulai dari Sekarang</p>
    <h2 class="mx-auto mt-4 max-w-2xl font-display text-3xl font-semibold leading-tight text-cream sm:text-4xl">{{ $heading }}</h2>
    @if ($description)
        <p class="mx-auto mt-4 max-w-xl text-cream/60">{{ $description }}</p>
    @endif
    <div class="mt-8">
        <span class="pulse-ring"><x-site.button :href="\App\Support\Format::waLink(config('mydream.whatsapp'), 'Halo MyDream, saya ingin konsultasi pernikahan.')" size="lg" class="btn-shine">Hubungi Kami via WhatsApp</x-site.button></span>
    </div>
</section>
