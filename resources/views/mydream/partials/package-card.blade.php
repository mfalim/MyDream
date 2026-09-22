@php use App\MyDream\Format; @endphp
{{-- Card PAKET → klik membuka halaman overview paket --}}
<article class="md-card md-card--package h-100">
    <div class="md-card__media">
        <img src="{{ asset($package['image']) }}" alt="{{ $package['title'] }}" loading="lazy">
        @if ($package['badge'])<span class="md-tag md-tag--gold">{{ $package['badge'] }}</span>@endif
        @if ($package['ribbon'])<span class="md-tag md-tag--dark">{{ $package['ribbon'] }}</span>@endif
        <button class="md-fav" type="button" aria-label="Simpan"><i class="bi bi-heart"></i></button>
    </div>
    <div class="md-card__body">
        <div class="md-card__meta">
            <span class="md-eyebrow">{{ $package['tag'] }}</span>
            <span class="md-rate"><i class="bi bi-star-fill"></i> {{ Format::stars($package['rating']) }} <small>({{ $package['reviews'] }})</small></span>
        </div>
        <h3 class="md-card__title">
            <a href="{{ route('mydream.packages.show', $package['slug']) }}" class="stretched-link">{{ $package['title'] }}</a>
        </h3>
        <p class="md-card__by">oleh {{ $package['organizer'] }}</p>
        <ul class="md-card__features">
            @foreach ($package['features'] as $f)
                <li><i class="bi bi-check-circle"></i>{{ $f }}</li>
            @endforeach
        </ul>
        <div class="md-card__foot">
            <div><small>Mulai dari</small><strong>{{ Format::rupiah($package['price']) }}</strong></div>
            <span class="md-card__cicilan">Cicilan 0%</span>
        </div>
    </div>
</article>
