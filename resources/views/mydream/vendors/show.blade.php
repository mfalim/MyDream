@extends('mydream.layouts.app')

@php use App\MyDream\Format; @endphp
@php use App\MyDream\Catalog; @endphp

@section('title', $vendor['name'] . ' | MyDream Store')
@section('body_class', 'page-detail page-vendor')
@section('with_categories', '1')

@section('content')
@php
    $categories = Catalog::categories();
    $hasRooms = ! empty($vendor['rooms']);
    $hasFacilities = ! empty($vendor['facilities']);
    $guestOptions = $vendor['capacity_options'] ?? ['≤ 100 Tamu', '100 – 300 Tamu', '300 – 500 Tamu', '500+ Tamu'];
    $priceLabel = isset($vendor['price_to']) && $vendor['price_to']
        ? Format::juta($vendor['price_from']) . ' – ' . Format::juta($vendor['price_to']) . ' nett'
        : Format::rupiah($vendor['price_from']);
    if ($vendor['price_from'] < 1_000_000) {
        $priceLabel = Format::rupiah($vendor['price_from']) . ' – ' . Format::rupiah($vendor['price_to']) . ' / pax';
    }
@endphp

<div class="container-xxl detail">

    {{-- Breadcrumb --}}
    <nav class="detail-crumb" aria-label="breadcrumb">
        <a href="{{ route('mydream.home') }}">Beranda</a><i class="bi bi-chevron-right"></i>
        <a href="{{ route('mydream.store', ['kategori' => $vendor['category']]) }}">{{ $categories[$vendor['category']] ?? 'Vendor' }}</a><i class="bi bi-chevron-right"></i>
        <span>{{ $vendor['city'] }}</span><i class="bi bi-chevron-right"></i>
        <span>{{ $vendor['name'] }}</span>
    </nav>

    {{-- Judul --}}
    <header class="detail-head">
        <div>
            <div class="detail-badges">
                @foreach ($vendor['badges'] as [$label, $tone])
                    <span class="md-badge md-badge--{{ $tone }}">{{ $label }}</span>
                @endforeach
            </div>
            <h1 class="detail-title">{{ $vendor['name'] }}</h1>
            <p class="detail-sub"><strong class="text-gold">{{ $vendor['category_label'] }}</strong> <span><i class="bi bi-geo-alt"></i> {{ $vendor['address'] }}</span></p>
            <div class="detail-meta">
                <span class="detail-rating"><i class="bi bi-star-fill"></i> <strong>{{ Format::stars($vendor['rating']) }}</strong> <small>({{ $vendor['reviews'] }} ulasan terverifikasi)</small></span>
                <span><i class="bi bi-lightning-charge"></i> {{ $vendor['response_rate'] }}% Respons Rate (&lt; 15 menit)</span>
                <span><i class="bi bi-trophy"></i> {{ $vendor['weddings'] }}+ Pernikahan Terselenggara</span>
            </div>
            @if (! empty($vendor['chips']))
                <div class="detail-chips">
                    @foreach ($vendor['chips'] as [$icon, $text])
                        <span><i class="bi {{ $icon }}"></i> {{ $text }}</span>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="detail-actions detail-actions--stack">
            <div class="d-flex gap-2">
                <a href="#cek" class="btn btn-md-primary btn-sm">Minta brosur & pricelist</a>
                <button type="button" class="btn btn-md-outline btn-sm" aria-label="Simpan"><i class="bi bi-heart"></i></button>
                <button type="button" class="btn btn-md-outline btn-sm" aria-label="Bagikan" data-share><i class="bi bi-share"></i></button>
            </div>
            <a href="{{ Catalog::wa('Halo MyDream, saya ingin chat wedding specialist untuk ' . $vendor['name']) }}" target="_blank" rel="noopener" class="btn btn-md-outline btn-sm w-100"><i class="bi bi-chat-dots"></i> Chat wedding specialist</a>
        </div>
    </header>

    {{-- Galeri --}}
    <div class="vendor-gallery">
        @foreach (array_slice($vendor['gallery'], 0, 5) as [$src, $caption])
            <figure class="{{ $loop->first ? 'is-main' : '' }}">
                <img src="{{ asset($src) }}" alt="{{ $caption }}" loading="lazy">
                <figcaption>{{ $caption }}</figcaption>
            </figure>
        @endforeach
        <button type="button" class="vendor-gallery__all"><i class="bi bi-images"></i> Lihat semua {{ $vendor['gallery_count'] ?? 48 }} foto & video</button>
    </div>

    {{-- Sub-navigasi --}}
    <nav class="detail-tabs detail-tabs--sticky" aria-label="Bagian halaman">
        <a href="#tentang" class="is-active"><i class="bi bi-dot"></i> Tentang</a>
        <a href="#paket">Paket & Harga</a>
        @if ($hasRooms)<a href="#kapasitas">Kapasitas Ballroom</a>@endif
        @if ($hasFacilities)<a href="#fasilitas">Fasilitas Termasuk</a>@endif
        <a href="#ulasan">Ulasan Pasangan ({{ $vendor['reviews'] }})</a>
        <a href="#faq">Kebijakan & FAQ</a>
    </nav>

    <div class="row g-4 g-xl-5">
        {{-- ================= KOLOM KIRI ================= --}}
        <div class="col-lg-8">

            {{-- Tentang --}}
            <section class="vendor-block" id="tentang">
                <span class="md-eyebrow">Tentang {{ $vendor['category'] === 'venue' ? 'venue' : 'vendor' }}</span>
                <h2 class="detail-h2">{{ $vendor['about_title'] }}</h2>
                @foreach ($vendor['about'] as $p)
                    <p class="detail-lead">{{ $p }}</p>
                @endforeach

                @if (! empty($vendor['highlights']))
                    <div class="vendor-highlights">
                        @foreach ($vendor['highlights'] as [$icon, $label, $value, $desc])
                            <div>
                                <i class="bi {{ $icon }}"></i>
                                <small>{{ $label }}</small>
                                <strong>{{ $value }}</strong>
                                <span>{{ $desc }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            {{-- Paket & harga vendor (berdiri sendiri, bukan paketan) --}}
            <section class="vendor-block" id="paket">
                <div class="d-flex justify-content-between align-items-end flex-wrap gap-2 mb-3">
                    <div>
                        <span class="md-eyebrow">Pilihan layanan</span>
                        <h2 class="detail-h2 mb-0">Layanan & Pricelist Resmi 2026</h2>
                    </div>
                    <span class="vendor-guarantee"><i class="bi bi-patch-check"></i> Garansi harga resmi MyDream</span>
                </div>

                <div class="d-grid gap-3">
                    @foreach ($vendor['packages'] as $pkg)
                        <article class="vendor-pkg {{ ! empty($pkg['popular']) ? 'is-popular' : '' }}">
                            @if (! empty($pkg['popular']))<span class="vendor-pkg__ribbon"><i class="bi bi-star"></i> Paling populer</span>@endif
                            <span class="md-eyebrow">{{ $pkg['tag'] }}</span>
                            <h3>{{ $pkg['name'] }}</h3>
                            <p>{{ $pkg['desc'] }}</p>

                            <div class="vendor-pkg__price">
                                <div>
                                    <small>Investasi paket (nett)</small>
                                    <strong>{{ Format::rupiah($pkg['price']) }}</strong>
                                </div>
                                <div class="text-end">
                                    @if ($pkg['price'] >= 5_000_000)
                                        <span class="md-badge md-badge--maroon">MyDream Pay 0%</span>
                                        <span class="d-block mt-1">Mulai {{ Format::rupiah(round($pkg['price'] / 24)) }} / bulan (24x)</span>
                                    @elseif (! empty($pkg['note']))
                                        <span>{{ $pkg['note'] }}</span>
                                    @endif
                                </div>
                            </div>

                            <ul class="vendor-pkg__benefits">
                                @foreach ($pkg['benefits'] as $b)
                                    <li><i class="bi bi-check-circle"></i>{{ $b }}</li>
                                @endforeach
                            </ul>

                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ Catalog::wa('Halo MyDream, saya ingin menanyakan ' . $pkg['name'] . ' di ' . $vendor['name']) }}" target="_blank" rel="noopener"
                                   class="btn {{ ! empty($pkg['popular']) ? 'btn-md-primary' : 'btn-md-soft' }} btn-sm">
                                    {{ ! empty($pkg['popular']) ? 'Minta penawaran paket ini' : 'Lihat detail paket ' . $pkg['pax'] . ' pax' }}
                                </a>
                                @if (! empty($pkg['popular']))<a href="#" class="btn btn-md-soft btn-sm">Unduh rincian PDF lengkap</a>@endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            {{-- Kapasitas ruangan (hanya bila vendor punya ruangan, mis. venue) --}}
            @if ($hasRooms)
            <section class="vendor-block" id="kapasitas">
                <span class="md-eyebrow">Spesifikasi ruangan</span>
                <h2 class="detail-h2">Kapasitas & Dimensi Ballroom</h2>
                <div class="row g-3">
                    @foreach ($vendor['rooms'] as $room)
                        <div class="col-md-4">
                            <div class="vendor-room h-100">
                                <div class="d-flex justify-content-between"><span class="md-badge md-badge--gold">{{ $room['floor'] }}</span><small>{{ $room['style'] }}</small></div>
                                <h3>{{ $room['name'] }}</h3>
                                <p>{{ $room['desc'] }}</p>
                                <dl>
                                    @foreach ($room['specs'] as [$k, $v])
                                        <div><dt>{{ $k }}</dt><dd>{{ $v }}</dd></div>
                                    @endforeach
                                </dl>
                                <a href="#" class="btn btn-md-soft btn-sm w-100">Lihat layout denah</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
            @endif

            {{-- Fasilitas --}}
            @if ($hasFacilities)
            <section class="vendor-block" id="fasilitas">
                <span class="md-eyebrow">Kelengkapan venue</span>
                <h2 class="detail-h2">Fasilitas Hotel & Layanan Pendukung</h2>
                <div class="row g-3">
                    @foreach ($vendor['facilities'] as [$icon, $title, $desc])
                        <div class="col-6 col-md-3">
                            <div class="vendor-facility h-100"><i class="bi {{ $icon }}"></i><span><strong>{{ $title }}</strong><small>{{ $desc }}</small></span></div>
                        </div>
                    @endforeach
                </div>
            </section>
            @endif

            {{-- Ulasan --}}
            <section class="vendor-block" id="ulasan">
                <div class="d-flex justify-content-between align-items-end flex-wrap gap-2 mb-3">
                    <div>
                        <span class="md-eyebrow">Ulasan nyata pasangan</span>
                        <h2 class="detail-h2 mb-0">Pengalaman dari Pengantin {{ \Illuminate\Support\Str::before($vendor['name'], ' ') }}</h2>
                    </div>
                    <div class="detail-score"><strong>{{ Format::stars($vendor['rating']) }}</strong> <span class="text-warning">★★★★★</span> berdasarkan {{ $vendor['reviews'] }} ulasan</div>
                </div>
                <div class="d-grid gap-3">
                    @foreach ($vendor['reviews_list'] as $r)
                        <article class="detail-review">
                            <div class="detail-review__top">
                                <div><strong>{{ $r['name'] }}</strong> <span class="md-badge md-badge--gold">MyDream Verified Bride</span><small>{{ $r['meta'] }}</small></div>
                                <span class="detail-review__stars">@for ($i = 0; $i < 5; $i++)<i class="bi bi-star-fill"></i>@endfor</span>
                            </div>
                            <p>"{{ $r['text'] }}"</p>
                        </article>
                    @endforeach
                </div>
            </section>

            {{-- FAQ --}}
            <section class="vendor-block" id="faq">
                <span class="md-eyebrow">Panduan calon pengantin</span>
                <h2 class="detail-h2">Pertanyaan yang Sering Diajukan (FAQ)</h2>
                <div class="accordion md-accordion" id="faqList">
                    @foreach ($vendor['faq'] as $i => $f)
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}" aria-expanded="false">{{ $f['q'] }}</button>
                            </h3>
                            <div id="faq{{ $i }}" class="accordion-collapse collapse" data-bs-parent="#faqList">
                                <div class="accordion-body">{{ $f['a'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        {{-- ================= KOLOM KANAN (sticky) ================= --}}
        <div class="col-lg-4">
            <div class="detail-sticky">
                <aside class="detail-box" id="cek" data-vendor-form data-vendor="{{ $vendor['name'] }}" data-wa="{{ Catalog::WA_NUMBER }}">
                    <span class="md-eyebrow">Cek ketersediaan & penawaran</span>
                    <h3 class="detail-box__name">{{ $vendor['name'] }}</h3>
                    <small class="text-muted">Mulai dari layanan:</small>
                    <p class="detail-box__range">{{ $priceLabel }}</p>

                    <div class="vendor-pay">
                        <strong><i class="bi bi-shield-check"></i> Garansi perlindungan MyDream Pay</strong>
                        <ul>
                            <li>100% Escrow Protection garansi uang aman</li>
                            <li>Cicilan bunga 0% s.d 24 bulan seluruh bank</li>
                            <li>Peluang cashback s.d Rp 10.000.000</li>
                        </ul>
                    </div>

                    <label class="detail-box__label" for="v-date">Perkiraan tanggal acara</label>
                    <input id="v-date" type="date" name="date" class="form-control md-input">

                    <label class="detail-box__label" for="v-guests">Perkiraan jumlah tamu undangan</label>
                    <select id="v-guests" name="guests" class="form-select md-input">
                        @foreach ($guestOptions as $g)<option>{{ $g }}</option>@endforeach
                    </select>

                    <p class="detail-box__label">Waktu pelaksanaan</p>
                    <div class="pkg-sessions">
                        <label><input type="radio" name="session" value="Siang (Lunch)"><span><i class="bi bi-sun"></i> Siang (Lunch)</span></label>
                        <label><input type="radio" name="session" value="Malam (Dinner)" checked><span><i class="bi bi-moon-stars"></i> Malam (Dinner)</span></label>
                    </div>

                    <button type="button" class="btn btn-md-primary w-100 mt-3" data-vendor-send><i class="bi bi-envelope-paper"></i> Minta brosur & cek ketersediaan</button>
                    <a href="{{ Catalog::wa('Halo MyDream, saya ingin chat tim wedding ' . $vendor['name']) }}" target="_blank" rel="noopener" class="btn btn-md-soft w-100 mt-2"><i class="bi bi-whatsapp"></i> Chat WhatsApp tim wedding</a>

                    <div class="detail-organizer detail-organizer--flat">
                        <span class="detail-review__avatar">{{ $vendor['specialist'][0] ?? 'MD' }}</span>
                        <div><strong>{{ $vendor['specialist'][1] ?? 'Wedding Specialist MyDream' }}</strong><small class="text-success"><i class="bi bi-circle-fill"></i> Online sekarang (fast response)</small></div>
                    </div>
                </aside>

                <div class="detail-box detail-box--plain mt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="md-eyebrow">Lokasi</span>
                        <a class="store-link" target="_blank" rel="noopener" href="https://www.google.com/maps/search/?api=1&query={{ urlencode($vendor['map_query']) }}">Buka peta <i class="bi bi-box-arrow-up-right"></i></a>
                    </div>
                    <p class="small mb-2">{{ $vendor['address'] }}</p>
                    <div class="detail-map detail-map--small">
                        <iframe title="Peta lokasi" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                                src="https://maps.google.com/maps?q={{ urlencode($vendor['map_query']) }}&output=embed"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Jaminan --}}
    <section class="vendor-assure">
        <div>
            <span class="md-eyebrow">Rencanakan dengan tenang</span>
            <h2>Jaminan Perlindungan Transaksi 100% MyDream Organizer</h2>
            <p>Semua transaksi pembayaran untuk {{ $vendor['name'] }} yang diproses melalui MyDream Pay dilindungi sepenuhnya oleh garansi pelaksanaan acara resmi MyDream.</p>
        </div>
        <a href="{{ Catalog::wa('Halo MyDream, saya ingin konsultasi gratis tentang ' . $vendor['name']) }}" target="_blank" rel="noopener" class="btn btn-md-gold">Konsultasi gratis sekarang</a>
    </section>

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
