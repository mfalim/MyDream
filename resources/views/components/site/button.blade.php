@props([
    'href' => null,
    'variant' => 'primary', // primary | outline | ghost
    'size' => 'md',         // sm | md | lg
    'type' => 'button',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-full font-medium transition-colors '
          . 'focus:outline-none focus-visible:ring-2 focus-visible:ring-maroon focus-visible:ring-offset-2 disabled:opacity-50';

    $variants = [
        'primary' => 'bg-maroon text-white hover:bg-maroon-dark',
        'outline' => 'border border-maroon text-maroon hover:bg-maroon-light',
        'ghost' => 'text-ink hover:bg-black/5',
    ];

    $sizes = [
        'sm' => 'px-4 py-2 text-sm',
        'md' => 'px-6 py-3 text-sm',
        'lg' => 'px-8 py-4 text-base',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
