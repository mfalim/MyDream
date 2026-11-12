@props(['amount', 'prefix' => null])

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-baseline gap-2']) }}>
    @if ($prefix)
        <span class="text-xs text-ink/50">{{ $prefix }}</span>
    @endif
    <span class="font-display text-xl font-semibold text-maroon">{{ \App\Support\Format::rupiah($amount) }}</span>
</div>
