@props(['package'])

@php
    $vendors = $package->vendors;
    $photo = $package->photo ?: $vendors->map(fn ($vendor) => $vendor->photos->first()?->photo)->filter()->first();
    $cities = $vendors->pluck('city')->filter()->unique()->implode(' · ');
@endphp

<a href="{{ route('catalog.show', $package->slug) }}" class="group block overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5 transition-shadow hover:shadow-lg">
    <div class="relative aspect-[4/3] overflow-hidden bg-cream-dark">
        <img src="{{ \App\Support\Media::url($photo) }}" alt="{{ $package->name }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
        @if ($package->is_featured)
            <x-site.badge variant="gold" class="absolute left-3 top-3">Pilihan MyDream</x-site.badge>
        @endif
    </div>
    <div class="space-y-2 p-5">
        <p class="text-xs font-medium uppercase tracking-wide text-emerald">Paket · {{ $vendors->count() }} vendor{{ $cities ? ' · ' . $cities : '' }}</p>
        <h3 class="line-clamp-2 font-display text-lg font-semibold leading-snug text-ink">{{ $package->name }}</h3>
        @if ($package->description)
            <p class="line-clamp-2 text-sm text-ink/60">{{ $package->description }}</p>
        @endif
        <x-site.price :amount="$vendors->sum('price')" prefix="Total" class="pt-2" />
    </div>
</a>
