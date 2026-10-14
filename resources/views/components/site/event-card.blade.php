@props(['event'])

@php
    $labels = ['upcoming' => ['Akan Datang', 'category'], 'ongoing' => ['Sedang Berlangsung', 'maroon'], 'finished' => ['Selesai', 'neutral']];
    [$label, $variant] = $labels[$event['status']];
    $date = $event['starts_on']->locale('id')->translatedFormat('d F Y');
@endphp

<a href="{{ route('event.show', $event['slug']) }}" class="group block overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5 transition-shadow hover:shadow-lg">
    <div class="relative aspect-[4/3] overflow-hidden bg-cream-dark">
        <img src="{{ $event['image'] }}" alt="{{ $event['name'] }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105 {{ $event['status'] === 'finished' ? 'grayscale' : '' }}">
        <x-site.badge :variant="$variant" class="absolute left-3 top-3">{{ $label }}</x-site.badge>
    </div>
    <div class="space-y-1.5 p-5">
        <h3 class="font-display text-lg font-semibold leading-snug">{{ $event['name'] }}</h3>
        <p class="text-xs text-ink/50">{{ $date }} · {{ $event['time'] }}</p>
        <p class="text-xs text-ink/50">{{ $event['location'] }}</p>
        <p class="line-clamp-2 text-sm text-ink/60">{{ $event['description'] }}</p>
    </div>
</a>
