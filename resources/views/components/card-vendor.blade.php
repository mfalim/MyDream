@props([
    'variant' => 'paket', // 'paket' (kartu venue/paket) atau 'profil' (kartu vendor)
    'image',
    'title',
    'href' => '#',
    'tags' => [],
    'rating' => null,
    'reviews' => null,
    'location' => null,
    'vendorName' => null,
    'features' => [],
    'price' => null,
    'priceBefore' => null,
    'cicilan' => null,
    'priceFrom' => null,
    'discountBadge' => null,
    'bestSeller' => false,
])

@if ($variant === 'paket')
    <a href="{{ $href }}" class="card" style="display:block;">
        <div class="card-media">
            <img src="{{ $image }}" alt="{{ $title }}" loading="lazy">
            <div class="badges-top">
                @if ($discountBadge)
                    <span class="badge badge-maroon">{{ $discountBadge }}</span>
                @endif
                @if ($bestSeller)
                    <span class="badge badge-dark">Best Seller</span>
                @endif
            </div>
            <button type="button" class="card-fav" aria-label="Simpan ke wishlist">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
            </button>
        </div>

        <div class="card-body">
            <div class="card-meta">
                <span>{{ $location }}</span>
                @if ($rating)
                    <span class="rating">
                        <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor" style="color:var(--gold)"><path d="M10 1.5l2.6 5.6 6.1.6-4.6 4.2 1.3 6-5.4-3-5.4 3 1.3-6-4.6-4.2 6.1-.6z"/></svg>
                        {{ $rating }} ({{ $reviews }})
                    </span>
                @endif
            </div>

            <h3 class="card-title">{{ $title }}</h3>
            @if ($vendorName)
                <p class="card-vendor-name">oleh {{ $vendorName }}</p>
            @endif

            @if (count($features))
                <ul class="card-features">
                    @foreach ($features as $feature)
                        <li>
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4.5 12.75l6 6 9-13.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span>{{ $feature }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($price)
                <div class="card-price">
                    @if ($priceBefore)
                        <p class="before">Rp {{ $priceBefore }}</p>
                    @endif
                    <p class="now">Rp {{ $price }}</p>
                    @if ($cicilan)
                        <p class="cicilan">Cicilan mulai Rp {{ $cicilan }}/bln</p>
                    @endif
                </div>
            @endif

            <div class="card-actions">
                <span class="btn btn-dark btn-block">Lihat Detail Paket</span>
            </div>
        </div>
    </a>
@else
    {{-- Kartu profil vendor --}}
    <div class="card card-profile">
        <div class="card-media">
            <img src="{{ $image }}" alt="{{ $title }}" loading="lazy">
            <div class="card-tags">
                @foreach ($tags as $tag)
                    <span class="badge badge-paper">{{ $tag }}</span>
                @endforeach
            </div>
            <button type="button" class="card-fav" aria-label="Simpan ke wishlist">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
            </button>
        </div>

        <div class="card-body">
            @if ($rating)
                <p class="card-meta" style="text-transform:none;">
                    <span class="rating">
                        <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor" style="color:var(--gold)"><path d="M10 1.5l2.6 5.6 6.1.6-4.6 4.2 1.3 6-5.4-3-5.4 3 1.3-6-4.6-4.2 6.1-.6z"/></svg>
                        <strong>{{ $rating }}</strong> ({{ $reviews }} ulasan)
                    </span>
                </p>
            @endif

            <h3 class="card-title">{{ $title }}</h3>
            <p class="card-vendor-name">{{ $location }}</p>

            @if (count($features))
                <p class="small muted mt-1">{{ $features[0] }}</p>
            @endif

            @if ($priceFrom)
                <div class="card-price">
                    <p class="tiny faint" style="text-transform:uppercase;letter-spacing:.04em;">Harga mulai dari</p>
                    <p class="now">Rp {{ $priceFrom }}</p>
                </div>
            @endif

            <div class="card-actions">
                <a href="{{ $href }}" class="btn btn-outline btn-sm" style="flex:1;">Lihat Detail</a>
                <a href="#" class="btn btn-dark btn-sm" style="flex:1;">Chat Vendor</a>
            </div>
        </div>
    </div>
@endif
