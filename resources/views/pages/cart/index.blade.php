@extends('layouts.frontend')

@section('title', 'Keranjang Layanan Reservasi — MyDream Organizer')

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Keranjang']]" />
@endsection

@php
    $items = [
        ['tag' => 'Paket Utama', 'category' => 'All-in Organizer & Styling', 'image' => 'package/package-07.jpg', 'title' => 'Royal Emerald Package', 'desc' => 'Pendampingan komprehensif dari konsep visual, venue styling, MUA pengantin, hingga tim manajemen hari-H bersertifikat.', 'pills' => ['8 Kru Lapangan', 'Rundown Digital', 'VIP Consultation'], 'price_label' => 'Harga Paket Tetap', 'price' => '85.000.000'],
        ['tag' => 'Vendor Rekanan', 'category' => 'Dokumentasi & Visual', 'image' => 'package/package-06.jpg', 'title' => 'Cinematic Pre-wedding', 'desc' => 'Full day shoot (2 lokasi pilihan), FPV drone pilot bersertifikat, teaser reels 60 detik & teaser 3 menit 4K Ultra HD.', 'pills' => ['12 Jam Liputan', 'FPV Drone', '4K Master Copy'], 'price_label' => 'Biaya Layanan', 'price' => '15.000.000'],
        ['tag' => 'Entertainment', 'category' => 'Musik & Audiovisual', 'image' => 'package/package-10.jpg', 'title' => 'Live Acoustic & Sound System', 'desc' => 'Paket 5-piece akustik (vokalis pria/wanita, gitar akustik, baby grand piano elektrik, bass, cajon) + 5000 watt sound engineering.', 'pills' => ['4 Jam Pertunjukan', '5000W Line Array', 'Sound Engineer'], 'price_label' => 'Biaya Layanan', 'price' => '7.500.000'],
    ];
    $subtotal = 107500000; $voucher = 5000000; $service = 5125000; $total = $subtotal - $voucher + $service; $dp = round($total * 0.3);
@endphp

@section('content')

    @include('partials.booking-stepper', ['active' => 1])

    <section class="section">
        <div class="container cart-layout" style="display:grid;grid-template-columns:1fr 360px;gap:36px;align-items:start;">
            <div>
                <span class="stage-tag">🛍 Tahap 1 dari 3</span>
                <h1 class="h1" style="font-size:2rem;">Keranjang Layanan Reservasi</h1>
                <p class="lede mt-1">Tinjau paket wedding organizer dan vendor rekanan pilihan Anda dengan transparansi biaya mutlak.</p>

                <div class="mt-3">
                    @foreach ($items as $item)
                        <div class="reserve-item">
                            <img src="{{ asset('images/'.$item['image']) }}" alt="{{ $item['title'] }}">
                            <div>
                                <div class="top-row">
                                    <div>
                                        <span class="badge badge-dark mb-1">{{ $item['tag'] }}</span>
                                        <p class="category">{{ $item['category'] }}</p>
                                        <h3>{{ $item['title'] }}</h3>
                                    </div>
                                </div>
                                <p class="desc">{{ $item['desc'] }}</p>
                                <div class="feature-pills">
                                    @foreach ($item['pills'] as $pill)
                                        <span class="feature-pill">{{ $pill }}</span>
                                    @endforeach
                                </div>
                                <div class="bottom-row">
                                    <div>
                                        <p class="price-label">{{ $item['price_label'] }}</p>
                                        <p class="price-value">Rp {{ $item['price'] }}</p>
                                    </div>
                                    <div class="qty-control">
                                        <button type="button" data-qty="minus">&minus;</button>
                                        <span data-qty-value>1</span>
                                        <button type="button" data-qty="plus">+</button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="icon-btn" aria-label="Hapus item" style="align-self:flex-start;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                            </button>
                        </div>
                    @endforeach
                </div>

                <div class="coupon-row">
                    <div class="coupon-info">
                        <span>🎟</span>
                        <div>
                            <span class="badge badge-maroon">Hemat Rp 5 Jt</span>
                            <p class="small mt-1"><strong>WOHEMAT10</strong> &middot; Promo Spesial Season 2026</p>
                        </div>
                    </div>
                    <div class="flex gap-2" style="align-items:center;">
                        <strong class="text-maroon">&minus;Rp 5.000.000</strong>
                        <a href="#" class="link-arrow">Ganti Kupon</a>
                    </div>
                </div>

                <button type="button" class="add-vendor-btn">
                    <span style="font-size:1.1rem;">+</span> Tambah Vendor Rekanan Lainnya (Catering, Souvenir, Undangan Digital)
                </button>

                <div class="mt-4" style="background:var(--forest-soft);border:1px solid var(--forest-line);border-radius:var(--radius-lg);padding:36px;">
                    <p class="eyebrow" style="color:var(--forest);"><span style="background:var(--forest);"></span>Jaminan Kualitas MyDream</p>
                    <h2 class="h2 mt-2" style="max-width:26em;">Mengapa Memilih Reservasi Melalui Atelier Kami?</h2>
                    <div class="grid grid-3 mt-3">
                        <div class="icon-feature left">
                            <div class="icon-circle" style="background:var(--paper);color:var(--forest);"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg></div>
                            <h4>Pendampingan Khusus</h4>
                            <p>Satu lead wedding planner berdedikasi untuk timeline, kurasi vendor, dan gladi resik tanpa pembagian beban acara lain.</p>
                        </div>
                        <div class="icon-feature left">
                            <div class="icon-circle" style="background:var(--paper);color:var(--forest);"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg></div>
                            <h4>Transparansi Kontrak</h4>
                            <p>Klausul jelas tanpa biaya tersembunyi. Setiap rincian teknis terdokumentasi rapi dalam portal online terpusat.</p>
                        </div>
                        <div class="icon-feature left">
                            <div class="icon-circle" style="background:var(--paper);color:var(--forest);"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H11.25m0 0V8.625c0-.621.504-1.125 1.125-1.125h.871"/></svg></div>
                            <h4>Vendor Rekanan Bintang 5</h4>
                            <p>Seluruh mitra dekorasi, MUA, dan katering melalui verifikasi standar ketat portofolio dan jaminan ketepatan waktu.</p>
                        </div>
                    </div>
                </div>
            </div>

            <aside>
                <div class="summary-card">
                    <h3>Ringkasan Biaya Reservasi</h3>
                    <div class="summary-row"><span>Subtotal (3 Layanan)</span><span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span></div>
                    <div class="summary-row discount"><span>Diskon Kupon <strong>WOHEMAT10</strong></span><span>&minus;Rp {{ number_format($voucher, 0, ',', '.') }}</span></div>
                    <div class="summary-row"><span>Service Charge & Pajak (5%)</span><span>Rp {{ number_format($service, 0, ',', '.') }}</span></div>
                    <div class="summary-total">
                        <span class="small muted">Total Estimasi</span>
                        <span class="value">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <p class="tiny muted mt-1">✓ Termasuk garansi pendampingan penuh s/d H-30</p>

                    <div class="dp-box">
                        <div class="row-top">
                            <strong class="small">Opsi Uang Muka (DP 30%)</strong>
                            <span class="badge badge-forest">Rekomendasi</span>
                        </div>
                        <p class="small muted">Bayar awal untuk kunci tanggal:</p>
                        <p class="h3" style="color:var(--forest);">Rp {{ number_format($dp, 0, ',', '.') }}</p>
                        <p class="tiny muted">Sisa pelunasan 70% dapat dicicil hingga H-14 acara pernikahan.</p>
                    </div>

                    <a href="{{ route('checkout.index') ?? '#' }}" class="btn btn-dark btn-block mt-3">Lanjut ke Validasi Data &rarr;</a>

                    <ul class="trust-list">
                        <li><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>Enkripsi Perbankan 256-bit SSL</li>
                        <li><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>100% Escrow Protection Guarantee</li>
                        <li><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0V11.25a2.25 2.25 0 012.25-2.25h13.5a2.25 2.25 0 012.25 2.25v7.5"/></svg>Bebas Reschedule s/d H-60 Hari</li>
                    </ul>
                </div>

                <div class="help-box">
                    <span style="font-size:1.3rem;">💬</span>
                    <div>
                        <p class="small">Ada pertanyaan seputar paket?</p>
                        <a href="https://wa.me/6281233779967" class="link-arrow">Konsultasi Gratis via WhatsApp</a>
                    </div>
                </div>
            </aside>
        </div>
    </section>

@endsection

@push('styles')
<style>
    @media (max-width: 900px) { .cart-layout { grid-template-columns: 1fr !important; } }
</style>
@endpush
