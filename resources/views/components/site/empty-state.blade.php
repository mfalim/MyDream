@props(['title', 'description' => null])

<div {{ $attributes->merge(['class' => 'rounded-3xl bg-cream-dark/60 p-10 text-center']) }}>
    <p class="font-display text-lg font-semibold text-ink">{{ $title }}</p>
    @if ($description)
        <p class="mt-2 text-sm text-ink/60">{{ $description }}</p>
    @endif
</div>
