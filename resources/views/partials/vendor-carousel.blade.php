{{-- Carousel horizontal vendor, menerima $heading dan $vendors. --}}
<section class="vendor-carousel">
    <div class="container home-container">
        <div class="d-flex align-items-end justify-content-between mb-4">
            <div><span class="home-kicker">VENDOR PILIHAN</span><h2 class="mb-0">{{ $heading }}</h2></div>
            <a href="{{ route('vendor.index') }}" class="carousel-link">Lihat semua <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="vendor-carousel__track">
            @foreach ($vendors as $vendor)
                <x-card-vendor variant="profil" :href="route('vendor.show', $vendor['slug'])" :image="$vendor['image']" :title="$vendor['name']" :location="$vendor['location']" :rating="$vendor['rating']" :reviews="$vendor['reviews']" :tags="$vendor['tags']" :price-from="$vendor['price_from']" />
            @endforeach
        </div>
    </div>
</section>
