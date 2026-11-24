@props(['title' => ''])

@php
    $shareUrl = url()->current();
    $whatsapp = 'https://wa.me/?text=' . rawurlencode(trim($title . ' ' . $shareUrl));
@endphp

<div {{ $attributes->merge(['class' => 'mt-8 flex flex-wrap items-center gap-3 text-sm']) }}>
    <span class="text-ink/50">Bagikan:</span>
    <a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="inline-flex items-center rounded-full border border-emerald px-4 py-1.5 font-semibold text-emerald transition-colors hover:bg-emerald hover:text-white">WhatsApp</a>
    <button type="button" data-copy="{{ $shareUrl }}" class="copy-btn">Salin tautan</button>
</div>
