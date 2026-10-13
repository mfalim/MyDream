@props(['article'])

<a href="{{ route('blog.show', $article['slug']) }}" class="group block">
    <div class="aspect-[4/3] overflow-hidden rounded-2xl bg-cream-dark">
        <img src="{{ $article['cover_image'] }}" alt="{{ $article['title'] }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
    </div>
    <div class="mt-4 space-y-1.5">
        <x-site.badge variant="gold">{{ $article['category'] }}</x-site.badge>
        <h3 class="font-display text-lg font-semibold leading-snug text-ink transition-colors group-hover:text-maroon">{{ $article['title'] }}</h3>
        <p class="line-clamp-2 text-sm text-ink/60">{{ $article['excerpt'] }}</p>
        <p class="text-xs font-medium text-maroon">Lihat Cerita Lengkap &rarr;</p>
    </div>
</a>
