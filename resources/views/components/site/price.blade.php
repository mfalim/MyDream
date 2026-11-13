@props(['amount', 'prefix' => null, 'animate' => false])

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-baseline gap-2']) }}>
    @if ($prefix)
        <span class="text-xs text-ink/50">{{ $prefix }}</span>
    @endif
    <span class="font-display text-xl font-semibold text-maroon" @if ($animate) data-count="{{ (int) $amount }}" data-prefix="Rp " @endif>{{ \App\Support\Format::rupiah($amount) }}</span>
</div>
