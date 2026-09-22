@extends('layouts.frontend')

@section('title', 'Validasi Data Pemesan & Acara — MyDream Organizer')

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Keranjang', 'href' => route('cart.index')], ['label' => 'Validasi Data']]" />
@endsection

@php
    $subtotal = 64500000; $dp = round($subtotal * 0.3);
@endphp

@section('content')

    @include('partials.booking-stepper', ['active' => 2])

    <section class="section">
        <div class="container">
            <div class="flex justify-between flex-wrap gap-2 mb-3" style="align-items:center;">
                <p class="small muted">
                    <span style="color:var(--gold);">ⓘ</span>
                    Perlu penyesuaian susunan kru atau jadwal tambahan? Konsultan kami siap membantu 24/7
                </p>
                <div class="flex gap-2 small muted" style="align-items:center;">
                    <span>ID Draft Reservasi: <strong class="text-ink">MYD-2026-JBR-088</strong></span>
                    <a href="https://wa.me/6281233779967" class="link-arrow">📞 Hubungi Concierge</a>
                    <a href="#" class="link-arrow">Simpan Draft Form</a>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 360px;gap:36px;align-items:start;" class="checkout-layout">
                <div>
                    <span class="stage-tag">Langkah 02 &middot; Dokumen &amp; Verifikasi Kontrak &middot; Siap Kunci Jadwal</span>
                    <h1 class="h1" style="font-size:1.9rem;">Validasi Data Pemesan &amp; Acara</h1>
                    <p class="lede mt-1">Lengkapi data legalitas dan jadwal akad/resepsi untuk penguncian tanggal resmi &amp; koordinasi vendor terkurasi.</p>

                    <div class="form-panel mt-3" style="display:flex;align-items:center;gap:14px;justify-content:space-between;flex-wrap:wrap;">
                        <div class="flex gap-2" style="align-items:center;">
                            <span style="width:46px;height:46px;border-radius:50%;background:var(--ink);color:#F4EEE2;display:flex;align-items:center;justify-content:center;font-family:var(--font-serif);font-weight:600;">A&amp;S</span>
                            <div>
                                <p><strong>Aditya Pratama &amp; Sarah Nadia</strong> <span class="badge badge-forest">✓ Terverifikasi</span></p>
                                <p class="small muted">aditya.sarah@wedding.id &middot; Anggota sejak Jan 2026</p>
                            </div>
                        </div>
                        <span class="badge badge-outline">OTP Aktif (+62 812-****)</span>
                    </div>

                    <form id="checkoutForm" action="{{ route('payment.index') ?? '#' }}" method="POST">
                        @csrf

                        <div class="form-panel">
                            <h2><span class="step-num">1</span>Data Pasangan &amp; Kontak Pemesan <span class="panel-tag badge badge-outline">Wajib Diisi</span></h2>
                            <p class="small muted mb-2">Nama akan dicetak langsung pada buku akad, kartu janji suci, dan lembar legalitas reservasi venue.</p>
                            <div class="form-grid">
                                <div class="field"><label>Nama Lengkap Calon Pengantin Pria</label><input type="text" value="Aditya Pratama, S.T." required></div>
                                <div class="field"><label>Nama Lengkap Calon Pengantin Wanita</label><input type="text" value="Sarah Nadia Maharani, B.A." required></div>
                                <div class="field"><label>Nomor WhatsApp Utama <span class="hint">✓ OTP Verified</span></label><input type="tel" value="+62 812-3456-7890" required></div>
                                <div class="field"><label>Email Konfirmasi Invoice</label><input type="email" value="aditya.sarah@wedding.id" required></div>
                            </div>
                        </div>

                        <div class="form-panel">
                            <h2><span class="step-num">2</span>Detail Waktu &amp; Lokasi Pernikahan <span class="panel-tag badge badge-outline">Jadwal Terkunci</span></h2>
                            <div class="form-grid">
                                <div class="field"><label>Tanggal Pelaksanaan Acara <span class="hint">Status: Tersedia</span></label><input type="date" value="2026-10-24" required></div>
                                <div class="field">
                                    <label>Sesi Waktu &amp; Rundown</label>
                                    <select><option>Akad Pagi (08:00) & Resepsi Malam (19:00)</option><option>Resepsi Siang (11:00)</option><option>Full Day (08:00 - 21:00)</option></select>
                                </div>
                                <div class="field span-2">
                                    <label>Lokasi Gedung / Ballroom <span class="hint">Mitra Resmi Rekanan MyDream</span></label>
                                    <div style="border:1px solid var(--line);border-radius:var(--radius-md);padding:14px 16px;">
                                        <div class="flex justify-between" style="align-items:flex-start;">
                                            <div>
                                                <strong>Grand Emerald Ballroom, Jember</strong>
                                                <p class="small muted">Jl. Gajah Mada, Kaliwates, Kabupaten Jember</p>
                                            </div>
                                            <a href="#" class="link-arrow">Ganti Lokasi</a>
                                        </div>
                                        <div class="flex gap-1 mt-2 flex-wrap">
                                            <span class="feature-pill">Ceiling 9,2m</span>
                                            <span class="feature-pill">VIP Valet 40 Lot</span>
                                            <span class="feature-pill">Loading Dock Privat</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="field span-2"><label>Estimasi Undangan / Tamu Hadir</label><input type="text" value="500 - 800 Tamu (Grand Scale · Direkomendasikan 12-14 Kru)"></div>
                            </div>
                        </div>

                        <div class="form-panel">
                            <h2><span class="step-num">3</span>Konsep &amp; Catatan Acara</h2>
                            <p class="small muted mb-2">Ringkasan konsep artistik untuk panduan show director dan vendor dekorasi rekanan kami.</p>
                            <div class="field">
                                <label>Arahan Artistik &amp; Tradisi Adat</label>
                                <textarea rows="3">Konsep Modern Rustic Emerald Gold dengan sentuhan prosesi adat Jawa Solo modern dan welcoming acoustic session.</textarea>
                                <p class="tiny faint mt-1" style="text-align:right;">Catatan ini otomatis dilampirkan ke Master Timeline Production.</p>
                            </div>
                        </div>

                        <div class="form-panel">
                            <h2><span class="step-num">4</span>Unggah Dokumen Legalitas (KTP Pemesan) <span class="panel-tag badge badge-outline">🔒 Enkripsi 256-bit</span></h2>
                            <p class="small muted mb-2">Dibutuhkan untuk pembuatan Surat Perjanjian Kerjasama (SPK) resmi bermaterai elektronik.</p>
                            <div class="flex justify-between" style="border:1px solid var(--line);border-radius:var(--radius-md);padding:14px 16px;align-items:center;flex-wrap:wrap;gap:10px;">
                                <div class="flex gap-2" style="align-items:center;">
                                    <span style="font-size:1.4rem;">📄</span>
                                    <div>
                                        <p><strong>KTP_Aditya_verified.pdf</strong> <span class="badge badge-forest">✓ Tervalidasi</span></p>
                                        <p class="tiny faint">1.4 MB &middot; Diunggah 18 Jan 2026 &middot; Hash SHA-256 Terlindungi</p>
                                    </div>
                                </div>
                                <div class="flex gap-1">
                                    <button type="button" class="btn btn-outline btn-sm">Pratinjau</button>
                                    <button type="button" class="btn btn-outline btn-sm">Ganti File</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <aside>
                    <div class="summary-card">
                        <p class="tiny faint" style="text-transform:uppercase;">Ikhtisar Pesanan</p>
                        <h3 class="flex justify-between" style="align-items:center;">Ringkasan Paket Terpilih <span class="badge badge-outline">2 Layanan Utama</span></h3>

                        <div class="mt-2" style="border-top:1px solid var(--line);padding-top:12px;">
                            <div class="flex justify-between"><strong class="small">Royal Emerald Grand Wedding</strong><span class="small">Rp 48.500.000</span></div>
                            <p class="tiny muted">Full Day Wedding Organizer + Lead 12 Crew Berpengalaman</p>
                            <p class="tiny faint">Inklusif: Master Rundown, Gladi Resik, Family Concierge · <span class="text-maroon">Paket Utama</span></p>
                        </div>
                        <div class="mt-2" style="border-top:1px solid var(--line);padding-top:12px;">
                            <div class="flex justify-between"><strong class="small">Cinematic 4K Video &amp; Photobook</strong><span class="small">Rp 16.000.000</span></div>
                            <p class="tiny muted">Same Day Edit (SDE) Video + 2 Master Photographers</p>
                            <p class="tiny faint">Inklusif: Drone 4K, 1 Box Velvet Album, All High-Res RAW · <span class="text-maroon">Add-on Layanan</span></p>
                        </div>

                        <div class="mt-2" style="background:var(--cream-soft);border-radius:var(--radius-sm);padding:10px 14px;">
                            <p class="small"><strong>Pelaksanaan:</strong> 24 Oktober 2026</p>
                            <p class="tiny muted">Grand Emerald Ballroom, Jember</p>
                        </div>

                        <div class="summary-row mt-2"><span>Subtotal Layanan Kontrak</span><span>Rp {{ number_format($subtotal,0,',','.') }}</span></div>
                        <div class="summary-row"><span>Biaya Administrasi &amp; Legal Escrow</span><span class="text-forest">Gratis (Promo)</span></div>
                        <div class="summary-total">
                            <span class="small muted">Total Nilai Kontrak <span class="badge badge-forest">DP 30% Tersedia</span></span>
                            <span class="value">Rp {{ number_format($subtotal,0,',','.') }}</span>
                        </div>
                        <p class="tiny faint" style="text-align:right;">Pelunasan H-14: Rp {{ number_format($dp,0,',','.') }}</p>

                        <label class="check-line mt-3">
                            <input type="checkbox" checked>
                            <span>Saya menyatakan seluruh data pernikahan telah sesuai, akurat, dan menyetujui Ketentuan Layanan &amp; Kebijakan Pembatalan MyDream.</span>
                        </label>

                        <button type="submit" form="checkoutForm" class="btn btn-dark btn-block mt-3">Konfirmasi Data &amp; Lanjut Pembayaran &rarr;</button>
                        <p class="tiny muted text-center mt-1">🛡 Jaminan Keamanan Transaksi &amp; Reservasi Terikat</p>
                    </div>

                    <div class="mt-2" style="background:var(--forest);color:#EAF1E6;border-radius:var(--radius-md);padding:18px;">
                        <p class="small"><strong>Garansi Ketepatan Vendor 100%</strong></p>
                        <p class="tiny mt-1" style="color:#C9D6C4;">Dana DP Anda disimpan dalam sistem escrow MyDream hingga hari-H tervalidasi sukses.</p>
                    </div>
                </aside>
            </div>
        </div>
    </section>

@endsection

@push('styles')
<style>
    @media (max-width: 900px) { .checkout-layout { grid-template-columns: 1fr !important; } }
</style>
@endpush
