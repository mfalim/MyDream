@props(['variant' => 'neutral']) {{-- category | gold | neutral --}}

@php
    $variants = [
        'category' => 'bg-emerald text-white',
        'gold' => 'bg-gold text-white',
        'neutral' => 'bg-black/5 text-ink/70',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ' . ($variants[$variant] ?? $variants['neutral'])]) }}>{{ $slot }}</span>
