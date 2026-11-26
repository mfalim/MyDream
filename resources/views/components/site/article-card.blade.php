@props(['article']) {{-- App\Models\Article --}}

<a href="{{ route('blog.show', $article->slug) }}" class="group block transition-transform duration-300 hover:-translate-y-1">
    <div class="aspect-[4/3] overflow-hidden rounded-2xl bg-cream-dark">
        <img src="{{ \App\Support\Media::url($article->photo) }}" alt="{{ $article->title }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
    </div>
    <div class="mt-4 space-y-1.5">
        <div class="flex flex-wrap items-center gap-1.5">
            <x-site.badge variant="gold">{{ $article->category }}</x-site.badge>
            @if ($article->event)
                <x-site.badge variant="maroon">True Event</x-site.badge>
            @endif
        </div>
        @if ($article->rating)
            <span class="inline-flex items-center gap-1 text-sm font-semibold text-amber-600" aria-label="{{ $article->rating }} dari 5 bintang">★ {{ $article->rating }}/5</span>
        @endif
        <h3 class="font-display text-lg font-semibold leading-snug transition-colors group-hover:text-maroon">{{ $article->title }}</h3>
        <p class="line-clamp-2 text-sm text-ink/60">{{ $article->summary }}</p>
        <p class="text-xs font-medium text-maroon">Baca selengkapnya &rarr;</p>
    </div>
</a>
