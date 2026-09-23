{{--
    Partial: grid 3 kartu paket/venue.
    Props (via @include): $heading, $venues
--}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex items-end justify-between mb-6">
        <h2 class="font-display text-2xl sm:text-3xl font-semibold text-ink">
            {!! $heading !!}
        </h2>
        <a href="{{ route('catalog.index') ?? '#' }}" class="text-xs font-medium text-maroon hover:text-rose transition-colors whitespace-nowrap">
            Lihat Semua &rarr;
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach ($venues as $venue)
            <x-card-vendor variant="paket" :href="route('catalog.show', $venue['slug']) ?? '#'" :image="$venue['image']"
                :discount-badge="$venue['discount'] ?? null" :best-seller="$venue['best_seller'] ?? false"
                :location="$venue['location']" :rating="$venue['rating']" :reviews="$venue['reviews']"
                :title="$venue['title']" :vendor-name="$venue['vendor']" :features="$venue['features']" />
        @endforeach
    </div>
</section>
