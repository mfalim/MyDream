@extends('mydream.layouts.app')

@php use App\MyDream\Format; @endphp
@php use App\MyDream\Catalog; @endphp

@section('title', $package['title'] . ' | MyDream Store')
@section('body_class', 'page-detail page-package')
@section('with_categories', '1')

@section('content')
@php
    $first = $package['options'][0];
@endphp

<div class="container-xxl detail">

    {{-- Breadcrumb --}}
    <nav class="detail-crumb" aria-label="breadcrumb">
        <a href="{{ route('mydream.home') }}"><i class="bi bi-house"></i> Beranda</a><i class="bi bi-chevron-right"></i>
        <a href="{{ route('mydream.store') }}">MyDream Store</a><i class="bi bi-chevron-right"></i>
        <a href="{{ route('mydream.store', ['kategori' => 'paket']) }}">Paket Pernikahan</a><i class="bi bi-chevron-right"></i>
        <span>{{ $package['title'] }}</span>
    </nav>

    {{-- Judul --}}
    <header class="detail-head">
        <div>
            <div class="detail-badges">
                @foreach ($package['badges'] as [$label, $tone])
                    <span class="md-badge md-badge--{{ $tone }}">{{ $label }}</span>
                @endforeach
            </div>
            <h1 class="detail-title">{{ $package['title'] }}</h1>
            <div class="detail-meta">
                <span><i class="bi bi-building"></i> Diselenggarakan oleh <strong class="text-gold">{{ $package['organizer'] }}</strong></span>
                <span><i class="bi bi-star-fill text-warning"></i> <strong>{{ Format::stars($package['rating']) }}</strong> <a href="#ulasan">({{ $package['reviews'] }} ulasan terverifikasi)</a></span>
                <span><i class="bi bi-patch-check"></i> {{ $package['sold'] }}+ paket terjual</span>
                <span><i class="bi bi-geo-alt"></i> {{ $package['area'] }}</span>
            </div>
        </div>
        <div class="detail-actions">
            <button type="button" class="btn btn-md-outline btn-sm"><i class="bi bi-heart"></i> Simpan</button>
            <button type="button" class="btn btn-md-outline btn-sm" data-share><i class="bi bi-share"></i> Bagikan</button>
        </div>
    </header>

    <div class="row g-4 g-xl-5">
        {{-- ================= KOLOM KIRI ================= --}}
        <div class="col-lg-8">

            {{-- Galeri --}}
            <div class="detail-gallery" data-gallery>
                <div class="detail-gallery__grid">
                    <div class="detail-gallery__main">
                        <img src="{{ asset($package['gallery'][0]) }}" alt="{{ $package['title'] }}" data-gallery-main>
                        <span class="md-tag md-tag--gold"><i class="bi bi-camera"></i> Official venue photography</span>
                        <span class="detail-gallery__caption">{{ $package['gallery_caption'] }}</span>
                    </div>
                    <div class="detail-gallery__side">
                        <img src="{{ asset($package['gallery'][1]) }}" alt="Bridal suite prep">
                        <div class="detail-gallery__more">
                            <img src="{{ asset($package['gallery'][2]) }}" alt="Dekorasi">
                            <span><i class="bi bi-images"></i> +18 Foto</span>
                        </div>
                    </div>
                </div>
                <div class="detail-thumbs">
                    @foreach ($package['gallery'] as $i => $g)
                        <button type="button" class="{{ $i === 0 ? 'is-active' : '' }}" data-gallery-thumb data-src="{{ asset($g) }}">
                            <img src="{{ asset($g) }}" alt="Galeri {{ $i + 1 }}">
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Fakta ringkas --}}
            <div class="detail-facts">
                @foreach ($package['facts'] as [$icon, $label, $value])
                    <div><span class="detail-facts__icon"><i class="bi {{ $icon }}"></i></span><span><small>{{ $label }}</small><strong>{{ $value }}</strong></span></div>
                @endforeach
            </div>

            {{-- Tab anchor --}}
            <nav class="detail-tabs" aria-label="Bagian halaman">
                @foreach ($package['tabs'] as $i => $tab)
                    <a href="{{ $i === 0 ? '#inklusi' : ($i === count($package['tabs']) - 1 ? '#lokasi' : '#inklusi') }}" class="{{ $i === 0 ? 'is-active' : '' }}">{{ $tab }}</a>
                @endforeach
            </nav>

            {{-- Rincian isi paket: gabungan beberapa vendor --}}
            <section class="detail-card" id="inklusi">
                <span class="md-eyebrow">MyDream complete solution</span>
                <h2 class="detail-h2">{{ $package['inclusion_title'] }}</h2>
                <p class="detail-lead">{{ $package['inclusion_intro'] }}</p>

                <div class="d-grid gap-3">
                    @foreach ($package['inclusions'] as $inc)
                        <div class="pkg-inc">
                            <div class="pkg-inc__head">
                                <span class="pkg-inc__icon"><i class="bi {{ $inc['icon'] }}"></i></span>
                                <h3>{{ $loop->iteration }}. {{ $inc['title'] }}</h3>
                                <span class="pkg-inc__worth">Senilai {{ Format::rupiah($inc['worth']) }}</span>
                            </div>
                            <ul>
                                @foreach ($inc['points'] as $p)
                                    <li><i class="bi bi-check2"></i>{{ $p }}</li>
                                @endforeach
                            </ul>
                            @if (! empty($inc['vendor']))
                                <a href="{{ route('mydream.vendors.show', $inc['vendor']) }}" class="pkg-inc__vendor">Lihat profil vendor <i class="bi bi-arrow-right"></i></a>
                            @endif
                        </div>
                    @endforeach
                </div>

            </section>

            {{-- Lokasi --}}
            <section class="detail-card" id="lokasi">
                <div class="d-flex justify-content-between align-items-start gap-3">
                    <div>
                        <span class="md-eyebrow">Lokasi venue</span>
                        <h2 class="detail-h2">{{ $package['location_title'] }}</h2>
                    </div>
                    <a class="store-link" target="_blank" rel="noopener" href="https://www.google.com/maps/search/?api=1&query={{ urlencode($package['map_query']) }}">Buka di Google Maps <i class="bi bi-box-arrow-up-right"></i></a>
                </div>
                <p class="detail-lead">{{ $package['address'] }}</p>
                <div class="detail-map">
                    <iframe title="Peta lokasi" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                            src="https://maps.google.com/maps?q={{ urlencode($package['map_query']) }}&output=embed"></iframe>
                    <div class="detail-map__pin"><i class="bi bi-geo-alt-fill"></i><span><strong>{{ $package['location_title'] }}</strong><small>{{ $package['map_note'] }}</small></span></div>
                </div>
            </section>

            {{-- Ulasan --}}
            <section class="detail-card" id="ulasan">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                    <div>
                        <span class="md-eyebrow">Testimoni nyata</span>
                        <h2 class="detail-h2 mb-0">Ulasan Calon & Pengantin Asli</h2>
                    </div>
                    <div class="detail-score"><i class="bi bi-star-fill"></i> <strong>{{ Format::stars($package['rating']) }}</strong> / 5.0 <span>|</span> {{ $package['reviews'] }} ulasan lengkap</div>
                </div>
                <div class="d-grid gap-3">
                    @foreach ($package['testimonials'] as $t)
                        <article class="detail-review">
                            <div class="detail-review__top">
                                <span class="detail-review__avatar">{{ $t['initials'] }}</span>
                                <div><strong>{{ $t['name'] }}</strong><small>{{ $t['meta'] }}</small></div>
                                <span class="detail-review__stars">@for ($i = 0; $i < 5; $i++)<i class="bi bi-star-fill"></i>@endfor</span>
                            </div>
                            <p>"{{ $t['text'] }}"</p>
                            <span class="md-badge md-badge--green"><i class="bi bi-patch-check"></i> Pembelian terverifikasi MyDream Store</span>
                        </article>
                    @endforeach
                </div>
            </section>

            {{-- Langkah --}}
            <section class="detail-card">
                <span class="md-eyebrow">Langkah mudah</span>
                <h2 class="detail-h2">Cara Kerja Pemesanan Paket di MyDream Store</h2>
                <div class="row g-3">
                    @foreach ($package['steps'] as $s)
                        <div class="col-6 col-md-3">
                            <div class="detail-step">
                                <span>{{ $loop->iteration }}</span>
                                <strong>{{ $s['title'] }}</strong>
                                <p>{{ $s['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        {{-- ================= KOLOM KANAN (sticky) ================= --}}
        <div class="col-lg-4">
            <aside class="detail-box" data-package-box data-title="{{ $package['title'] }}" data-wa="{{ Catalog::WA_NUMBER }}">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="md-eyebrow">Harga paket spesial</span>
                    <span class="md-badge md-badge--maroon" data-discount>Diskon {{ $package['discount'] }}%</span>
                </div>

                <div class="pkg-price">
                    <strong data-price>{{ Format::rupiah($first['price']) }}</strong>
                    <s data-old>{{ Format::rupiah($first['old_price']) }}</s>
                </div>
                <p class="pkg-save"><i class="bi bi-tag"></i> Hemat <span data-save>{{ Format::rupiah($first['old_price'] - $first['price']) }}</span> dengan voucher MyDream Store</p>

                <div class="pkg-cicilan">
                    <strong><i class="bi bi-credit-card"></i> Cicilan 0% MyDream Pay</strong>
                    <span>Mulai <b data-installment>{{ Format::rupiah(round($first['price'] / 24)) }}</b> / bulan (tenor 24 bulan bebas bunga dengan kartu kredit rekanan).</span>
                </div>

                <p class="detail-box__label">Pilih opsi kapasitas undangan:</p>
                <div class="d-grid gap-2">
                    @foreach ($package['options'] as $i => $opt)
                        <label class="pkg-option">
                            <input type="radio" name="option" value="{{ $i }}" @checked($i === 0)
                                   data-price="{{ $opt['price'] }}" data-old="{{ $opt['old_price'] }}" data-label="{{ $opt['label'] }}">
                            <span class="pkg-option__body">
                                <strong>{{ $opt['label'] }}</strong>
                                <small>{{ $opt['desc'] }}</small>
                            </span>
                            <span class="pkg-option__price">{{ Format::juta($opt['price']) }}</span>
                        </label>
                    @endforeach
                </div>

                <p class="detail-box__label">Perkiraan waktu & sesi acara:</p>
                <div class="pkg-sessions">
                    @foreach ($package['sessions'] as $i => $s)
                        <label>
                            <input type="radio" name="session" value="{{ $s }}" @checked($i === 0)>
                            <span><i class="bi {{ $i === 0 ? 'bi-moon-stars' : 'bi-sun' }}"></i> {{ $s }}</span>
                        </label>
                    @endforeach
                </div>

                <button type="button" class="btn btn-md-primary w-100 mt-3" data-package-buy><i class="bi bi-bag-check"></i> Beli voucher paket sekarang</button>
                <a href="{{ Catalog::wa('Halo MyDream, saya ingin konsultasi paket ' . $package['title']) }}" target="_blank" rel="noopener" class="btn btn-md-gold-soft w-100 mt-2"><i class="bi bi-whatsapp"></i> Konsultasi gratis via WhatsApp</a>
                <a href="#" class="detail-box__pdf"><i class="bi bi-download"></i> Unduh e-brochure & rundown (PDF)</a>

                <ul class="detail-box__guarantee">
                    <li><i class="bi bi-shield-check"></i> 100% Proteksi MyDream Pay Escrow</li>
                    <li><i class="bi bi-arrow-repeat"></i> Bebas reschedule acara hingga H-60</li>
                    <li><i class="bi bi-envelope-check"></i> Konfirmasi voucher digital instan via email</li>
                </ul>

                <div class="detail-box__pay">
                    <small>Metode pembayaran resmi:</small>
                    <div>@foreach (['BCA', 'Mandiri', 'BNI', 'BRI', 'QRIS', 'Visa', 'Mastercard'] as $m)<span>{{ $m }}</span>@endforeach</div>
                </div>
            </aside>

            <div class="detail-organizer">
                <span class="detail-organizer__icon"><i class="bi bi-buildings"></i></span>
                <div><strong>{{ $package['organizer'] }}</strong><small><i class="bi bi-circle-fill"></i> Respon rata-rata: &lt; 15 menit</small></div>
                <a href="#" class="btn btn-sm btn-md-soft ms-auto">Profil</a>
            </div>
        </div>
    </div>

    {{-- Paket lain --}}
    <section class="detail-related">
        <div class="store-head">
            <div>
                <span class="md-eyebrow">Inspirasi pilihan</span>
                <h2 class="detail-h2 mb-0">Paket Pernikahan Pilihan Lainnya</h2>
            </div>
            <a href="{{ route('mydream.store', ['kategori' => 'paket']) }}" class="store-link">Lihat semua paket <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-4">
            @foreach ($related as $rel)
                <div class="col-md-6 col-xl-4">@include('mydream.partials.package-card', ['package' => $rel])</div>
            @endforeach
        </div>
    </section>
</div>
@endsection
