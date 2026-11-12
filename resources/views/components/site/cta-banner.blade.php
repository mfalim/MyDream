@props([
    'heading' => 'Yuk, Bikin Pernikahan Impianmu Jadi Nyata.',
    'description' => null,
])

<section {{ $attributes->merge(['class' => 'rounded-3xl bg-ink px-6 py-16 text-center sm:px-16']) }}>
    <p class="text-xs font-semibold uppercase tracking-widest text-gold">Mulai dari Sekarang</p>
    <h2 class="mx-auto mt-4 max-w-2xl font-display text-3xl font-semibold leading-tight text-cream sm:text-4xl">{{ $heading }}</h2>
    @if ($description)
        <p class="mx-auto mt-4 max-w-xl text-cream/60">{{ $description }}</p>
    @endif
    <div class="mt-8">
        <x-site.button :href="\App\Support\Format::waLink(config('mydream.whatsapp'), 'Halo MyDream, saya ingin konsultasi pernikahan.')" size="lg">Hubungi Kami via WhatsApp</x-site.button>
    </div>
</section>
