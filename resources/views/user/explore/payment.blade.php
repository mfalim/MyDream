{{-- resources/views/user/explore/payment.blade.php --}}
@extends('user.layouts.app')

@section('title', 'Pembayaran - ' . $vendor['name'])

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/vendor-payment.css') }}"
    >
@endpush

@section('content')

<main class="payment-page">

    {{-- COUNTDOWN BANNER --}}
    <div class="payment-timer">

        <div class="payment-timer-info">

            <span class="payment-timer-icon">
                <i class="bi bi-alarm"></i>
            </span>

            <div>
                <strong>BATAS WAKTU PEMBAYARAN</strong> <span>&bull;</span> Reservasi Terkunci Sementara
                <p>Selesaikan pembayaran sebelum slot vendor dan ballroom dialihkan ke antrean berikutnya.</p>
            </div>

        </div>

        <div class="payment-timer-clock">
            <span>SISA WAKTU:</span>
            <div class="payment-timer-digits" id="paymentCountdown" data-duration="86380">
                <b id="cd-hours">23</b><i>:</i><b id="cd-minutes">59</b><i>:</i><b id="cd-seconds">40</b>
            </div>
        </div>

    </div>


    {{-- META + HEADING --}}
    <div class="payment-meta">
        Reservasi #{{ $reservationCode }} <span>&bull;</span> Tahap Akhir Konfirmasi
    </div>

    <div class="payment-heading">

        <div>
            <h1>Pembayaran Tagihan Reservasi</h1>
            <p>Pilih skema komitmen pembayaran dan kanal transaksi terproteksi garansi escrow terpadu WO PROJECT.</p>
        </div>

        <span class="payment-secure-badge">
            <i class="bi bi-shield-lock"></i>
            Protokol Keamanan Perbankan ISO 27001
        </span>

    </div>


    <div class="payment-layout">

        {{-- LEFT COLUMN --}}
        <div class="payment-main">

            {{-- STEP 1 : SKEMA --}}
            <section class="payment-section">

                <div class="payment-section-heading">
                    <span class="step-index">1</span>
                    <h2>Skema Komitmen Pembayaran</h2>
                    <span class="section-note">Fleksibel &amp; Terjadwal</span>
                </div>

                <div class="scheme-grid" id="schemeGrid">

                    <label class="scheme-card is-selected"
                        data-scheme-label="DP 30%"
                        data-amount-label="Uang Muka 30% Kontrak"
                        data-amount="{{ $dp }}"
                        data-total="{{ $dp }}">

                        <input type="radio" name="payment_scheme" value="dp" checked>

                        <span class="scheme-badge">REKOMENDASI</span>

                        <span class="scheme-name">
                            DP 30%
                            <i class="bi bi-check-circle-fill scheme-check"></i>
                        </span>

                        <strong class="scheme-price">Rp {{ number_format($dp, 0, ',', '.') }}</strong>

                        <span class="scheme-desc">Kunci jadwal &amp; vendor impian hari ini.</span>

                        <span class="scheme-footer">
                            <span>Pelunasan:</span>
                            <b>Sisa H-14 Acara</b>
                        </span>

                    </label>

                    <label class="scheme-card"
                        data-scheme-label="Lunas 100%"
                        data-amount-label="Pelunasan 100% Kontrak"
                        data-amount="{{ $lunas }}"
                        data-total="{{ $lunas }}">

                        <input type="radio" name="payment_scheme" value="lunas">

                        <span class="scheme-badge scheme-badge-gold">DISKON 3%</span>

                        <span class="scheme-name">
                            Lunas 100%
                            <i class="bi bi-check-circle-fill scheme-check"></i>
                        </span>

                        <strong class="scheme-price">Rp {{ number_format($lunas, 0, ',', '.') }}</strong>

                        <span class="scheme-desc">Hemat Rp {{ number_format($savings, 0, ',', '.') }} + VIP perks.</span>

                        <span class="scheme-footer">
                            <span>Benefit:</span>
                            <b>Bebas Biaya Admin</b>
                        </span>

                    </label>

                    <label class="scheme-card"
                        data-scheme-label="Cicilan 0%"
                        data-amount-label="Cicilan Pertama (1/6)"
                        data-amount="{{ $cicilan }}"
                        data-total="{{ $cicilan }}">

                        <input type="radio" name="payment_scheme" value="cicilan">

                        <span class="scheme-badge scheme-badge-gold">BUNGA 0%</span>

                        <span class="scheme-name">
                            Cicilan 0%
                            <i class="bi bi-check-circle-fill scheme-check"></i>
                        </span>

                        <strong class="scheme-price">Rp {{ number_format($cicilan, 0, ',', '.') }}<small>/bln</small></strong>

                        <span class="scheme-desc">Rencana keuangan lebih leluasa.</span>

                        <span class="scheme-footer">
                            <span>Tenor:</span>
                            <b>6&times; Pembayaran</b>
                        </span>

                    </label>

                </div>

            </section>


            {{-- STEP 2 : METODE --}}
            <section class="payment-section">

                <div class="payment-section-heading">
                    <span class="step-index">2</span>
                    <h2>Pilihan Metode Pembayaran</h2>
                    <span class="section-note">Enkripsi 256-bit</span>
                </div>

                <div class="method-accordion" id="methodAccordion">

                    <div class="method-item is-open" data-method="va">

                        <button type="button" class="method-header">

                            <span class="method-icon">
                                <i class="bi bi-bank"></i>
                            </span>

                            <span class="method-title">
                                Virtual Account
                                <span class="method-tag">Verifikasi Otomatis Instan</span>
                                <small>Konfirmasi otomatis tanpa upload struk manual 24/7</small>
                            </span>

                            <i class="bi bi-chevron-up method-chevron"></i>

                        </button>

                        <div class="method-body">

                            <label class="va-option is-selected">
                                <input type="radio" name="va_bank" checked>
                                <span class="va-bank-badge">BCA</span>
                                <span class="va-bank-name">BCA Virtual Account</span>
                                <span class="va-bank-note">Bebas Biaya Layanan</span>
                            </label>

                            <label class="va-option">
                                <input type="radio" name="va_bank">
                                <span class="va-bank-badge">MANDIRI</span>
                                <span class="va-bank-name">Mandiri Virtual Account</span>
                                <span class="va-bank-note">Bebas Biaya</span>
                            </label>

                            <label class="va-option">
                                <input type="radio" name="va_bank">
                                <span class="va-bank-badge">BNI</span>
                                <span class="va-bank-name">BNI Virtual Account</span>
                                <span class="va-bank-note">Bebas Biaya</span>
                            </label>

                            <label class="va-option">
                                <input type="radio" name="va_bank">
                                <span class="va-bank-badge">BRI</span>
                                <span class="va-bank-name">BRI Virtual Account (BRIVA)</span>
                                <span class="va-bank-note">Bebas Biaya</span>
                            </label>

                        </div>

                    </div>


                    <div class="method-item" data-method="qris">

                        <button type="button" class="method-header">

                            <span class="method-icon">
                                <i class="bi bi-qr-code"></i>
                            </span>

                            <span class="method-title">
                                QRIS &amp; Dompet Digital
                                <span class="method-tag method-tag-light">Semua E-Wallet</span>
                                <small>GoPay, OVO, ShopeePay, DANA, LinkAja &amp; Mobile Banking</small>
                            </span>

                            <i class="bi bi-chevron-right method-chevron"></i>

                        </button>

                        <div class="method-body">
                            <p class="method-placeholder">Kode QRIS akan ditampilkan setelah metode ini dipilih.</p>
                        </div>

                    </div>


                    <div class="method-item" data-method="card">

                        <button type="button" class="method-header">

                            <span class="method-icon">
                                <i class="bi bi-credit-card"></i>
                            </span>

                            <span class="method-title">
                                Kartu Kredit / Debit Online
                                <span class="method-tag method-tag-light">Cicilan 0% s/d 12 Bln</span>
                                <small>Visa, Mastercard, JCB, American Express dengan 3D Secure</small>
                            </span>

                            <i class="bi bi-chevron-right method-chevron"></i>

                        </button>

                        <div class="method-body">
                            <p class="method-placeholder">Formulir kartu akan ditampilkan setelah metode ini dipilih.</p>
                        </div>

                    </div>


                    <div class="method-item" data-method="paylater">

                        <button type="button" class="method-header">

                            <span class="method-icon">
                                <i class="bi bi-wallet2"></i>
                            </span>

                            <span class="method-title">
                                Cicilan Wedding &amp; PayLater
                                <small>Kredivo, Indodana, Mandiri CC 0% Cicilan Khusus Wedding</small>
                            </span>

                            <i class="bi bi-chevron-right method-chevron"></i>

                        </button>

                        <div class="method-body">
                            <p class="method-placeholder">Pilihan PayLater akan ditampilkan setelah metode ini dipilih.</p>
                        </div>

                    </div>

                </div>

            </section>


            {{-- ESCROW GUARANTEE --}}
            <div class="escrow-banner">

                <span class="escrow-icon">
                    <i class="bi bi-shield-check"></i>
                </span>

                <div>

                    <strong>WO Project Safe Escrow Guarantee</strong>

                    <p>
                        Dana Anda disimpan secara aman dalam rekening bersama (Escrow) yang diawasi oleh
                        otoritas perbankan. Dana baru akan dicairkan ke mitra vendor {{ $vendor['category'] }}
                        setelah pelaksanaan acara tuntas terkonfirmasi sesuai standar WO PROJECT.
                    </p>

                    <div class="escrow-tags">
                        <span><i class="bi bi-check-circle"></i> Proteksi 100% Kontrak</span>
                        <span><i class="bi bi-check-circle"></i> Audit Transparansi Vendor</span>
                    </div>

                </div>

            </div>

        </div>


        {{-- RIGHT SIDEBAR --}}
        <aside class="payment-sidebar">

            <div class="payment-summary-card">

                <div class="summary-top">
                    <span class="summary-code-label">KODE RESERVASI <i class="bi bi-copy"></i></span>
                    <span class="summary-status">Terkonfirmasi</span>
                </div>

                <strong class="summary-code">#{{ $reservationCode }}</strong>

                <div class="summary-package">

                    <span class="summary-package-icon">
                        <i class="bi bi-bag-check"></i>
                    </span>

                    <div>
                        <strong>{{ $vendor['service'] }} Package</strong>
                        <span><i class="bi bi-calendar-event"></i> 25 Oktober 2025</span>
                        <span><i class="bi bi-diagram-3"></i> {{ $vendor['name'] }}</span>
                    </div>

                </div>


                <div class="summary-details">

                    <span class="summary-details-title">RINCIAN PEMBAYARAN SEKARANG</span>

                    <div class="summary-row">
                        <span>Skema Terpilih:</span>
                        <strong id="summarySchemeLabel">DP 30%</strong>
                    </div>

                    <div class="summary-row">
                        <span id="summaryAmountLabel">Uang Muka 30% Kontrak:</span>
                        <strong id="summaryAmountValue">Rp {{ number_format($dp, 0, ',', '.') }}</strong>
                    </div>

                    <div class="summary-row">
                        <span>Biaya Penanganan Escrow WO:</span>
                        <strong class="summary-free">GRATIS</strong>
                    </div>

                    <div class="summary-row">
                        <span>PPN 11% (Termasuk):</span>
                        <strong>Rp 0 (All-in)</strong>
                    </div>

                </div>


                <div class="summary-total">
                    <span>TOTAL YANG DIBAYAR:</span>
                    <div>
                        <strong id="summaryTotalValue">Rp {{ number_format($dp, 0, ',', '.') }}</strong>
                        <span>IDR Net</span>
                    </div>
                </div>


                <p class="summary-guarantee">
                    <i class="bi bi-patch-check"></i>
                    Garansi 100% Pengembalian Dana jika terjadi force majeure sesuai polis perlindungan acara WO PROJECT.
                </p>


                <button type="button" class="summary-pay-btn" id="payNowBtn">
                    <i class="bi bi-lock-fill"></i>
                    Bayar Sekarang <span>&bull;</span> <span id="payNowAmount">Rp {{ number_format($dp, 0, ',', '.') }}</span>
                </button>

                <div class="summary-trust">
                    <span><i class="bi bi-shield-lock"></i> Bank Grade SSL</span>
                    <span>&bull;</span>
                    <span><i class="bi bi-patch-check"></i> BI Approved</span>
                </div>

                <div class="summary-step">Step 3 of 3 &bull; Menunggu Konfirmasi Pembayaran</div>

            </div>


            <div class="summary-help">

                <span class="summary-help-icon">
                    <i class="bi bi-headset"></i>
                </span>

                <div>
                    <strong>Kendala Pembayaran?</strong>
                    <span>Hubungi Concierge WO 24 Jam</span>
                </div>

                <a href="https://wa.me/6280000000000" target="_blank" rel="noopener" class="summary-help-btn">
                    Chat WhatsApp
                </a>

            </div>

        </aside>

    </div>

</main>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ---------- Countdown ---------- */
    var countdownEl = document.getElementById('paymentCountdown');
    var remaining = parseInt(countdownEl.dataset.duration, 10);
    var hoursEl = document.getElementById('cd-hours');
    var minutesEl = document.getElementById('cd-minutes');
    var secondsEl = document.getElementById('cd-seconds');

    function pad(n) {
        return n < 10 ? '0' + n : '' + n;
    }

    function renderCountdown() {
        var h = Math.floor(remaining / 3600);
        var m = Math.floor((remaining % 3600) / 60);
        var s = remaining % 60;
        hoursEl.textContent = pad(h);
        minutesEl.textContent = pad(m);
        secondsEl.textContent = pad(s);
    }

    renderCountdown();

    setInterval(function () {
        if (remaining > 0) {
            remaining--;
            renderCountdown();
        }
    }, 1000);


    /* ---------- Scheme selection ---------- */
    var schemeCards = document.querySelectorAll('.scheme-card');
    var schemeLabel = document.getElementById('summarySchemeLabel');
    var amountLabel = document.getElementById('summaryAmountLabel');
    var amountValue = document.getElementById('summaryAmountValue');
    var totalValue = document.getElementById('summaryTotalValue');
    var payAmount = document.getElementById('payNowAmount');

    function formatRupiah(value) {
        return 'Rp ' + Number(value).toLocaleString('id-ID');
    }

    schemeCards.forEach(function (card) {
        card.addEventListener('click', function () {
            schemeCards.forEach(function (c) { c.classList.remove('is-selected'); });
            card.classList.add('is-selected');
            card.querySelector('input[type="radio"]').checked = true;

            schemeLabel.textContent = card.dataset.schemeLabel;
            amountLabel.textContent = card.dataset.amountLabel + ':';
            amountValue.textContent = formatRupiah(card.dataset.amount);
            totalValue.textContent = formatRupiah(card.dataset.total);
            payAmount.textContent = formatRupiah(card.dataset.total);
        });
    });


    /* ---------- Method accordion ---------- */
    var methodItems = document.querySelectorAll('.method-item');

    methodItems.forEach(function (item) {
        var header = item.querySelector('.method-header');

        header.addEventListener('click', function () {
            var isOpen = item.classList.contains('is-open');

            methodItems.forEach(function (i) {
                i.classList.remove('is-open');
                var chev = i.querySelector('.method-chevron');
                chev.classList.remove('bi-chevron-up');
                chev.classList.add('bi-chevron-right');
            });

            if (!isOpen) {
                item.classList.add('is-open');
                var chevron = item.querySelector('.method-chevron');
                chevron.classList.remove('bi-chevron-right');
                chevron.classList.add('bi-chevron-up');
            }
        });
    });


    /* ---------- VA bank selection ---------- */
    var vaOptions = document.querySelectorAll('.va-option');

    vaOptions.forEach(function (option) {
        option.addEventListener('click', function () {
            vaOptions.forEach(function (o) { o.classList.remove('is-selected'); });
            option.classList.add('is-selected');
            var radio = option.querySelector('input[type="radio"]');
            if (radio) { radio.checked = true; }
        });
    });


    /* ---------- Pay button (placeholder) ---------- */
    var payBtn = document.getElementById('payNowBtn');
    if (payBtn) {
        payBtn.addEventListener('click', function () {
            alert('Integrasi payment gateway belum terhubung. Hubungkan endpoint pembayaran di sini.');
        });
    }

});
</script>
@endpush
