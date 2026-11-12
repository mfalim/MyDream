@props(['vendor'])

@php
    $cover = $vendor->photos->firstWhere('is_cover', true) ?? $vendor->photos->first();
    $location = $vendor->city ?: \Illuminate\Support\Str::limit((string) $vendor->address, 40);
@endphp

<a href="{{ route('vendor.show', $vendor->slug) }}" class="group block overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5 transition-shadow hover:shadow-lg">
    <div class="aspect-square overflow-hidden bg-cream-dark">
        <img src="{{ \App\Support\Media::url($cover?->photo ?? $vendor->photo) }}" alt="{{ $vendor->name }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
    </div>
    <div class="space-y-1.5 p-5">
        <x-site.badge variant="category">{{ $vendor->category?->name ?? 'Vendor' }}</x-site.badge>
        <h3 class="font-display text-base font-semibold leading-snug text-ink">{{ $vendor->name }}</h3>
        <p class="text-xs text-ink/50">{{ $location }}</p>
        <x-site.price :amount="$vendor->price" prefix="Mulai dari" class="pt-2" />
    </div>
</a>
