@props(['item'])

<a href="{{ route('inspiration.show', $item['slug']) }}" class="group block overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5 transition-shadow hover:shadow-lg">
    <div class="relative aspect-[4/3] overflow-hidden bg-cream-dark">
        <img src="{{ $item['cover_image'] }}" alt="{{ $item['title'] }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
        <x-site.badge variant="category" class="absolute left-3 top-3">{{ $item['theme'] }}</x-site.badge>
    </div>
    <div class="space-y-1.5 p-5">
        <h3 class="font-display text-lg font-semibold leading-snug">{{ $item['title'] }}</h3>
        <p class="text-xs text-ink/50">{{ $item['couple'] }} · {{ $item['city'] }}</p>
        <p class="line-clamp-2 text-sm text-ink/60">{{ $item['description'] }}</p>
    </div>
</a>
