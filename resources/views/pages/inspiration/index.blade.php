@extends('layouts.frontend')

@section('title', 'Inspirasi Pernikahan Terlengkap di Indonesia — MyDream Organizer')

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Inspirasi Pernikahan']]" />
@endsection

@php
    $tema = ['Semua Tema', 'Modern Minimalist', 'Tradisional Adat', 'Rustic & Garden', 'Classic Royal', 'Beach / Coastal Bali'];

    $koleksi = [
        ['image' => 'moodboard/collection-01.jpg', 'count' => '48 Inspirasi', 'title' => 'Modern Heritage: Pesona Adat dalam Sentuhan Kontemporer', 'desc' => 'Harmoni busana kebaya kutubaru champagne, ronce melati segar, dan lamaran meja perjamuan romantis.'],
        ['image' => 'moodboard/collection-02.jpg', 'count' => '62 Inspirasi', 'title' => 'Intimate Garden & Glasshouse Weddings', 'desc' => 'Instalasi flora gantung organik, pencahayaan fairylights romantis, dan jamuan meja perjamuan akrab berlatar.'],
        ['image' => 'moodboard/collection-03.jpg', 'count' => '35 Inspirasi', 'title' => 'Minimalist Parisian Chic: Gaun Clean & Bunga Organik', 'desc' => 'Siluet gaun sutra clean, veil panjang berhias mutiara halus, dipadukan buket calla lily minimalis.'],
    ];

    $galeri = [
        ['image' => 'moodboard/moodboard-01.jpg', 'tag' => 'Makeup & Hair', 'loc' => 'Sunda Modern • Jakarta', 'title' => 'Riasan Flawless Sunda Siger Natural Glow & Melati Segar', 'by' => 'Marlene Hariman'],
        ['image' => 'moodboard/moodboard-02.jpg', 'tag' => 'Dekorasi & Pelaminan', 'loc' => 'Garden Glasshouse • Jakarta', 'title' => 'Fairytale Glasshouse Floral Arch di Plataran Senayan', 'by' => 'Stupa Caspea Decor'],
        ['image' => 'moodboard/moodboard-03.jpg', 'tag' => 'Foto Prewedding', 'loc' => 'Destinasi Bali • Cliff Sunset', 'title' => 'Prewedding Sinematik Golden Hour Tebing Uluwatu', 'by' => 'Axioo Photography'],
        ['image' => 'moodboard/moodboard-04.jpg', 'tag' => 'Gaun & Kebaya', 'loc' => 'Atelier Couture • Bandung', 'title' => 'Kebaya Pengantin Bordir French Lace & Kristal Swarovski', 'by' => 'Hian Tjen Atelier'],
        ['image' => 'moodboard/moodboard-05.jpg', 'tag' => 'Venue & Setup Meja', 'loc' => 'Fine Dining Setup • Bali', 'title' => 'Table Styling Intimate Banquet dengan Warm Candlelight', 'by' => 'Evlin Decoration'],
        ['image' => 'moodboard/moodboard-06.jpg', 'tag' => 'Tata Rias Adat', 'loc' => 'Tradisional Adat • Yogyakarta', 'title' => 'Pesona Ratu Jawa Paes Ageng Berpadu Kebaya Beludru', 'by' => 'Anpa Suha Makeup'],
        ['image' => 'moodboard/moodboard-07.jpg', 'tag' => 'Cincin & Jewelry', 'loc' => 'Bespoke Jewelry • Jakarta', 'title' => 'Cincin Tunangan Solitaire Oval Diamond & Pavé Band', 'by' => 'Mondial Jeweller'],
        ['image' => 'moodboard/moodboard-08.jpg', 'tag' => 'Venue & Chapel', 'loc' => 'Destination Wedding • Uluwatu', 'title' => 'Pemberkatan Romantis Glass Chapel Tepi Samudra Hindia', 'by' => 'Tirtha Bridal Bali'],
        ['image' => 'moodboard/moodboard-09.jpg', 'tag' => 'Dekorasi & Pelaminan', 'loc' => 'Classic Royal • Jakarta', 'title' => 'Kemegahan Pelaminan Ballroom Arsitektural Kristal & Mawar Putih', 'by' => 'Suryanto Decoration'],
        ['image' => 'moodboard/moodboard-10.jpg', 'tag' => 'Gaun Pengantin', 'loc' => 'Modern Minimalist • Jakarta', 'title' => 'Gaun A-Line Sutra Mikado dengan Aksen Pita Kontemporer', 'by' => 'Yefta Gunawan'],
        ['image' => 'moodboard/moodboard-11.jpg', 'tag' => 'Tradisional Adat', 'loc' => 'Adat Minang • Padang & Jakarta', 'title' => 'Kemewahan Suntiang Emas & Songket Benang Emas Merah Marun', 'by' => 'Des Iskandar Wedding'],
        ['image' => 'moodboard/moodboard-12.jpg', 'tag' => 'Undangan & Souvenir', 'loc' => 'Artisanal Stationery • Surabaya', 'title' => 'Set Undangan Deckle Edge Paper & Gold Foil Calligraphy', 'by' => 'Papier Studio'],
        ['image' => 'moodboard/moodboard-13.jpg', 'tag' => 'Dekorasi Outdoor', 'loc' => 'Rustic Boho • Bandung', 'title' => 'Backdrop Pelaminan Organic Dried Pampas & White Orchid', 'by' => 'Kanya Decoration'],
        ['image' => 'moodboard/moodboard-14.jpg', 'tag' => 'Sepatu & Aksesori', 'loc' => 'Bridal Shoes • Jakarta', 'title' => 'Sepatu Pengantin Ivory Silk Satin dengan Brooch Kristal', 'by' => 'Rina Thoma Shoes'],
        ['image' => 'moodboard/moodboard-15.jpg', 'tag' => 'Foto Prewedding', 'loc' => 'Destinasi Bromo • Jawa Timur', 'title' => 'Prewedding Sinematik Misty Sunrise Padang Pasir Bromo', 'by' => 'Iluminen Moments'],
        ['image' => 'moodboard/moodboard-16.jpg', 'tag' => 'Kue Pengantin', 'loc' => 'Artisan Patisserie • Jakarta', 'title' => 'Four-Tier Contemporary Wafer Peony Wedding Cake', 'by' => 'LeNovelle Cake'],
    ];
@endphp

@section('content')

    <section class="section-sm">
        <div class="container">
            <div class="flex justify-between flex-wrap gap-2" style="align-items:flex-start;">
                <div style="max-width:640px;">
                    <p class="eyebrow">Real Weddings &amp; Curated Atelier Gallery</p>
                    <h1 class="h1 mt-2" style="font-size:2.2rem;">Inspirasi Pernikahan Terlengkap di Indonesia</h1>
                    <p class="lede mt-1">
                        Temukan ribuan ide gaun pengantin, dekorasi pelaminan, cincin tunangan, venue ballroom &amp; outdoor,
                        hingga tata rias adat dan modern dari pernikahan nyata karya vendor terkurasi Jember dan sekitarnya.
                    </p>
                </div>
                <div class="text-center" style="flex-shrink:0;">
                    <p class="small muted">42.850+ Ide Terkoleksi</p>
                    <button type="button" class="btn btn-maroon mt-1">📌 Moodboard Saya</button>
                </div>
            </div>

            <div class="mt-3" style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:12px;">
                <input type="text" placeholder="Cari gaun lace, siger Sunda, atau pelaminan..." style="border:1px solid var(--line);border-radius:var(--radius-pill);padding:12px 18px;font-size:.86rem;">
                <select style="border:1px solid var(--line);border-radius:var(--radius-pill);padding:12px 18px;font-size:.86rem;"><option>Semua Kategori</option></select>
                <select style="border:1px solid var(--line);border-radius:var(--radius-pill);padding:12px 18px;font-size:.86rem;"><option>Indonesia (Semua)</option></select>
            </div>

            <div class="filter-pills mt-2">
                @foreach ($tema as $i => $t)
                    <button type="button" class="filter-pill {{ $i === 0 ? 'active' : '' }}">{{ $t }}</button>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-sm section-cream">
        <div class="container">
            <div class="section-head">
                <div>
                    <p class="eyebrow">Kurasi Editor MyDream</p>
                    <h2 class="h2 mt-2">Koleksi Papan Inspirasi Tren 2026</h2>
                </div>
                <a href="#" class="link-arrow">Lihat Semua 24 Koleksi &rarr;</a>
            </div>

            <div class="grid grid-3">
                @foreach ($koleksi as $k)
                    <a href="#" class="card" style="display:block;">
                        <div class="card-media" style="aspect-ratio:4/3;">
                            <img src="{{ asset('images/'.$k['image']) }}" alt="{{ $k['title'] }}">
                            <div class="badges-top"><span class="badge badge-paper">{{ $k['count'] }}</span></div>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title" style="font-size:1.05rem;">{{ $k['title'] }}</h3>
                            <p class="small muted mt-1">{{ $k['desc'] }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-head">
                <div>
                    <p class="eyebrow">Galeri Real Wedding</p>
                    <h2 class="h2 mt-2">Semua Ide &amp; Foto Pernikahan Terbaru</h2>
                </div>
                <p class="small muted">Menampilkan {{ count($galeri) }} dari 42.850 foto &middot; Filter Aktif (2)</p>
            </div>

            <div class="masonry">
                @foreach ($galeri as $g)
                    <div class="m-item">
                        <img src="{{ asset('images/'.$g['image']) }}" alt="{{ $g['title'] }}" loading="lazy">
                        <span class="badge badge-dark m-badge">{{ $g['tag'] }}</span>
                        <button type="button" class="card-fav m-fav" aria-label="Simpan"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg></button>
                        <div class="m-overlay"><p>{{ $g['loc'] }}</p></div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-4">
                <button type="button" class="btn btn-outline">Muat Lebih Banyak Inspirasi</button>
                <p class="small muted mt-2">Halaman 1 dari 85</p>
            </div>
        </div>
    </section>

    <section class="section section-cream">
        <div class="container two-col">
            <div>
                <p class="eyebrow">MyDream Moodboard Studio</p>
                <h2 class="h2 mt-2">Kumpulkan Ide Impian Anda dalam Satu Papan Moodboard Interaktif</h2>
                <p class="lede mt-2">
                    Simpan foto gaun favorit, palet dekorasi, hingga referensi riasan pengantin. Bagikan langsung
                    papan inspirasi Anda kepada wedding planner atau vendor pilihan untuk mendapatkan estimasi
                    penawaran yang presisi.
                </p>
                <div class="flex gap-2 mt-3 flex-wrap">
                    <button type="button" class="btn btn-maroon">📌 Buat Moodboard Saya</button>
                    <a href="https://wa.me/6281233779967" class="btn btn-outline">Konsultasikan dengan Specialist</a>
                </div>
            </div>
            <div class="grid grid-2" style="gap:10px;">
                <img src="{{ asset('images/moodboard/moodboard-02.jpg') }}" alt="" style="border-radius:var(--radius-md);aspect-ratio:1/1;object-fit:cover;grid-row:1/3;">
                <img src="{{ asset('images/moodboard/moodboard-04.jpg') }}" alt="" style="border-radius:var(--radius-md);aspect-ratio:1/1;object-fit:cover;">
                <img src="{{ asset('images/moodboard/moodboard-07.jpg') }}" alt="" style="border-radius:var(--radius-md);aspect-ratio:1/1;object-fit:cover;">
            </div>
        </div>
    </section>

@endsection
