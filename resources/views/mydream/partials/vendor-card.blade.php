@php use App\MyDream\Format; @endphp
@php use App\MyDream\Catalog; @endphp
{{-- Card VENDOR → klik membuka halaman overview vendor --}}
<article class="md-card md-card--vendor h-100">
    <div class="md-card__media md-card__media--vendor">
        <img src="{{ asset($vendor['image']) }}" alt="{{ $vendor['name'] }}" loading="lazy">
        <div class="md-card__badges">
            @foreach ($vendor['card_badges'] ?? [] as $b)
                <span class="md-tag md-tag--light">{{ $b }}</span>
            @endforeach
        </div>
        <button class="md-fav" type="button" aria-label="Simpan"><i class="bi bi-heart"></i></button>
    </div>
    <div class="md-card__body">
        <div class="md-card__meta">
            <span class="md-rate"><i class="bi bi-star-fill"></i> {{ Format::stars($vendor['rating']) }} <small>({{ $vendor['reviews'] }} ulasan)</small></span>
            <span class="md-loc"><i class="bi bi-geo-alt"></i> {{ $vendor['city'] }}</span>
        </div>
        <h3 class="md-card__title">
            <a href="{{ route('mydream.vendors.show', $vendor['slug']) }}" class="stretched-link">{{ $vendor['name'] }}</a>
        </h3>
        <p class="md-card__by">{{ $vendor['tagline'] }}</p>
        <div class="md-card__price">
            <small>Harga mulai dari</small>
            <strong>{{ Format::rupiah($vendor['price_from']) }}</strong>
        </div>
        <div class="md-card__actions">
            <a href="{{ route('mydream.vendors.show', $vendor['slug']) }}" class="btn btn-md-soft btn-sm"><i class="bi bi-eye"></i> Lihat Detail</a>
            <a href="{{ Catalog::wa('Halo MyDream, saya tertarik dengan vendor ' . $vendor['name']) }}" target="_blank" rel="noopener" class="btn btn-md-primary btn-sm"><i class="bi bi-chat-dots"></i> Chat Vendor</a>
        </div>
    </div>
</article>
