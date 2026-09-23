@extends('layouts.frontend')

@section('title', 'Pembayaran Tagihan Reservasi — MyDream Organizer')

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Validasi Data', 'href' => route('checkout.index')], ['label' => 'Pembayaran']]" />
@endsection

@php
    $dp = 32287500; $lunas = 104396250; $cicilan = 17937500;
@endphp

@section('content')

    @include('partials.booking-stepper', ['active' => 3])

    <section class="section-sm">
        <div class="container">
            <div class="alert-strip">
                <div class="flex gap-2" style="align-items:center;">
                    <span style="font-size:1.4rem;">⏱</span>
                    <div>
                        <p class="small"><strong>Batas Waktu Pembayaran</strong> &middot; Reservasi Terkunci Sementara</p>
                        <p class="tiny muted">Selesaikan pembayaran sebelum slot vendor dan ballroom dialihkan ke antrean berikutnya.</p>
                    </div>
                </div>
                <div class="countdown-box">
                    <span class="tiny" style="text-transform:uppercase;font-weight:700;color:var(--maroon);">Sisa Waktu</span>
                    <div class="countdown-clock" data-countdown="86380">
                        <span class="seg" data-seg="h">23</span><span class="colon">:</span>
                        <span class="seg" data-seg="m">59</span><span class="colon">:</span>
                        <span class="seg" data-seg="s">40</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" style="padding-top:24px;">
        <div class="container payment-layout" style="display:grid;grid-template-columns:1fr 360px;gap:36px;align-items:start;">
            <div>
                <p class="tiny faint" style="text-transform:uppercase;">Reservasi #MYD-2026-8942 &middot; Tahap Akhir Konfirmasi</p>
                <h1 class="h1" style="font-size:1.9rem;">Pembayaran Tagihan Reservasi</h1>
                <p class="lede mt-1">Pilih skema komitmen pembayaran dan kanal transaksi terproteksi garansi escrow terpadu MyDream.</p>
                <span class="badge badge-forest mt-2">🛡 Protokol Keamanan Perbankan ISO 27001</span>

                <div class="form-panel mt-3">
                    <h2><span class="step-num">1</span>Skema Komitmen Pembayaran <span class="panel-tag badge badge-outline">Fleksibel &amp; Terjadwal</span></h2>
                    <div class="choice-cards mt-2">
                        <label class="choice-card">
                            <input type="radio" name="skema" checked>
                            <span class="badge badge-gold tag">Rekomendasi</span>
                            <p><strong>DP 30%</strong></p>
                            <p class="amount">Rp {{ number_format($dp,0,',','.') }}</p>
                            <p class="note">Kunci jadwal &amp; vendor impian hari ini.</p>
                            <p class="tiny faint mt-1">Pelunasan: Sisa H-14 Acara</p>
                        </label>
                        <label class="choice-card">
                            <input type="radio" name="skema">
                            <span class="badge badge-forest tag">Diskon 3%</span>
                            <p><strong>Lunas 100%</strong></p>
                            <p class="amount">Rp {{ number_format($lunas,0,',','.') }}</p>
                            <p class="note">Hemat Rp 3.228.750 + VIP perks.</p>
                            <p class="tiny faint mt-1">Benefit: Bebas Biaya Admin</p>
                        </label>
                        <label class="choice-card">
                            <input type="radio" name="skema">
                            <span class="badge badge-outline tag">Bunga 0%</span>
                            <p><strong>Cicilan 0%</strong></p>
                            <p class="amount">Rp {{ number_format($cicilan,0,',','.') }}/bln</p>
                            <p class="note">Rencana keuangan lebih leluasa.</p>
                            <p class="tiny faint mt-1">Tenor: 6x Pembayaran</p>
                        </label>
                    </div>
                </div>

                <div class="form-panel">
                    <h2><span class="step-num">2</span>Pilihan Metode Pembayaran <span class="panel-tag badge badge-outline">Enkripsi 256-bit</span></h2>

                    <div class="method-list mt-2">
                        <div class="method-group">
                            <div class="method-head">
                                <span class="icon-circle">🏦</span>
                                <div>
                                    Virtual Account <span class="badge badge-forest">Verifikasi Otomatis Instan</span>
                                    <p class="tiny muted">Konfirmasi otomatis tanpa upload struk manual, 24/7</p>
                                </div>
                            </div>
                            <div class="method-options">
                                <label class="method-option"><input type="radio" name="va" checked><span class="bank-code">BCA</span> BCA Virtual Account <span class="free">Bebas Biaya Layanan</span></label>
                                <label class="method-option"><input type="radio" name="va"><span class="bank-code">MANDIRI</span> Mandiri Virtual Account <span class="free">Bebas Biaya</span></label>
                                <label class="method-option"><input type="radio" name="va"><span class="bank-code">BNI</span> BNI Virtual Account <span class="free">Bebas Biaya</span></label>
                                <label class="method-option"><input type="radio" name="va"><span class="bank-code">BRI</span> BRI Virtual Account (BRIVA) <span class="free">Bebas Biaya</span></label>
                            </div>
                        </div>

                        <div class="method-simple">
                            <div class="flex gap-2" style="align-items:center;">
                                <span class="icon-circle" style="background:var(--gold);">📱</span>
                                <div><strong>QRIS &amp; Dompet Digital</strong> <span class="badge badge-outline">Semua E-Wallet</span><p class="tiny muted">GoPay, OVO, ShopeePay, DANA, LinkAja &amp; Mobile Banking</p></div>
                            </div>
                            <span>&rsaquo;</span>
                        </div>
                        <div class="method-simple">
                            <div class="flex gap-2" style="align-items:center;">
                                <span class="icon-circle" style="background:var(--maroon);">💳</span>
                                <div><strong>Kartu Kredit / Debit Online</strong> <span class="badge badge-outline">Cicilan 0% s/d 12 Bln</span><p class="tiny muted">Visa, Mastercard, JCB, American Express dengan 3D Secure</p></div>
                            </div>
                            <span>&rsaquo;</span>
                        </div>
                        <div class="method-simple">
                            <div class="flex gap-2" style="align-items:center;">
                                <span class="icon-circle" style="background:var(--ink);">🧾</span>
                                <div><strong>Cicilan Wedding &amp; PayLater</strong><p class="tiny muted">Kredivo, Indodana, Mandiri CC 0% Cicilan Khusus Wedding</p></div>
                            </div>
                            <span>&rsaquo;</span>
                        </div>
                    </div>
                </div>

                <div style="background:var(--forest);color:#EAF1E6;border-radius:var(--radius-lg);padding:26px;">
                    <p><strong>MyDream Safe Escrow Guarantee ⚙</strong></p>
                    <p class="small mt-1" style="color:#C9D6C4;">
                        Dana Anda disimpan aman dalam rekening bersama (escrow) yang diawasi otoritas perbankan.
                        Dana baru akan dicairkan ke mitra vendor ballroom &amp; dekorasi setelah pelaksanaan acara
                        tuntas terkonfirmasi sesuai standar MyDream.
                    </p>
                    <div class="flex gap-2 mt-2 flex-wrap tiny">
                        <span>✓ Proteksi 100% Kontrak</span>
                        <span>✓ Audit Transparansi Vendor</span>
                    </div>
                </div>
            </div>

            <aside>
                <div class="summary-card">
                    <div class="flex justify-between"><p class="tiny faint" style="text-transform:uppercase;">Kode Reservasi 📋</p><span class="badge badge-forest">Terkonfirmasi</span></div>
                    <p class="h3">#MYD-2026-8942</p>

                    <div class="flex gap-2 mt-2" style="align-items:center;border-top:1px solid var(--line);padding-top:12px;">
                        <img src="{{ asset('images/package/package-07.jpg') }}" alt="" style="width:52px;height:52px;border-radius:var(--radius-sm);object-fit:cover;">
                        <div>
                            <p class="small"><strong>Grand Ballroom &amp; Catering Package</strong></p>
                            <p class="tiny muted">📅 18 Oktober 2026</p>
                            <p class="tiny faint">3 Layanan Terintegrasi (Venue, MUA, Dekor)</p>
                        </div>
                    </div>

                    <p class="tiny faint mt-3" style="text-transform:uppercase;">Rincian Pembayaran Sekarang</p>
                    <div class="summary-row"><span>Skema Terpilih</span><span>DP (30%)</span></div>
                    <div class="summary-row"><span>Uang Muka 30% Kontrak</span><span>Rp {{ number_format($dp,0,',','.') }}</span></div>
                    <div class="summary-row"><span>Biaya Penanganan Escrow</span><span class="text-forest">Gratis</span></div>
                    <div class="summary-row"><span>PPN 11% (Termasuk)</span><span>Rp 0 (All-in)</span></div>

                    <div class="summary-total">
                        <span class="small muted">Total yang Dibayar</span>
                        <span class="value">Rp {{ number_format($dp,0,',','.') }}</span>
                    </div>
                    <p class="tiny faint" style="text-align:right;">IDR Net</p>

                    <div class="dp-box mt-2">
                        <p class="tiny">Garansi 100% pengembalian dana jika terjadi force majeure sesuai polis perlindungan acara MyDream.</p>
                    </div>

                    <a href="{{ route('payment.success') ?? '#' }}" class="btn btn-dark btn-block mt-3">🔒 Bayar Sekarang &middot; Rp {{ number_format($dp,0,',','.') }}</a>
                    <p class="tiny muted text-center mt-1">🛡 Bank Grade SSL &middot; ✅ BI Approved</p>
                    <p class="tiny faint text-center">Step 3 of 3 &middot; Menunggu Konfirmasi Pembayaran</p>
                </div>

                <div class="help-box">
                    <span style="font-size:1.3rem;">💬</span>
                    <div>
                        <p class="small">Kendala pembayaran?</p>
                        <a href="https://wa.me/6281233779967" class="link-arrow">Hubungi Concierge MyDream 24 Jam</a>
                    </div>
                </div>
            </aside>
        </div>
    </section>

@endsection

@push('styles')
<style>
    @media (max-width: 900px) { .payment-layout { grid-template-columns: 1fr !important; } }
</style>
@endpush
