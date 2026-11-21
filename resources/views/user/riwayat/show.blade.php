@extends('user.layouts.app')

@section('title', 'Invoice & Pembayaran')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/wo-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/riwayat.css') }}">
@endpush

@section('content')
@php
    $paidAmount = (float) ($paymentSummary['paid'] ?? $booking->payments->where('status', 'verified')->sum('amount'));
    $verificationAmount = (float) $booking->payments->where('status', 'pending')->sum('amount');
    $midtransPendingAmount = (float) $booking->payments->where('status', 'initiated')->sum('amount');
    $remainingAmount = (float) ($paymentSummary['remaining'] ?? max(0, (float) $booking->total_price - $paidAmount));
    $latestPayment = $booking->payments->first();
    $isPaid = (float) $booking->total_price > 0 && $remainingAmount <= 0;

    $bookingStatusLabel = match ($booking->status) {
        'approved' => 'Booking Disetujui',
        'rejected' => 'Booking Ditolak',
        default => 'Menunggu Persetujuan',
    };
    $bookingStatusBadge = match ($booking->status) {
        'approved' => 'wo-badge-green',
        'rejected' => 'wo-badge-red',
        default => 'wo-badge-gold',
    };

    $paymentLabel = match (true) {
        $isPaid => 'Lunas',
        $verificationAmount > 0 => 'Menunggu verifikasi',
        $midtransPendingAmount > 0 => 'Menunggu pembayaran Midtrans',
        $latestPayment?->status === 'rejected' && $paidAmount > 0 => 'Pelunasan gagal',
        $latestPayment?->status === 'rejected' => 'Pembayaran gagal',
        $paidAmount > 0 => 'DP terverifikasi',
        default => 'Belum dibayar',
    };
    $paymentBadge = match (true) {
        $isPaid, ($paidAmount > 0 && $verificationAmount === 0 && $midtransPendingAmount === 0 && $latestPayment?->status !== 'rejected') => 'wo-badge-green',
        $verificationAmount > 0 || $midtransPendingAmount > 0 => 'wo-badge-gold',
        $latestPayment?->status === 'rejected' => 'wo-badge-red',
        default => 'wo-badge-neutral',
    };

    $paymentTypes = ['dp' => 'DP', 'full' => 'Pelunasan penuh', 'remaining' => 'Sisa pelunasan'];
    $paymentStatuses = ['verified' => 'Terverifikasi', 'pending' => 'Menunggu verifikasi', 'initiated' => 'Menunggu pembayaran', 'rejected' => 'Ditolak'];
    $midtransLabels = [
        'created' => 'Transaksi dibuat',
        'pending' => 'Menunggu pembayaran',
        'capture' => 'Pembayaran diproses',
        'challenge' => 'Menunggu pemeriksaan',
        'settlement' => 'Berhasil',
        'expire' => 'Kedaluwarsa',
        'cancel' => 'Dibatalkan',
        'deny' => 'Ditolak',
        'refund' => 'Dikembalikan',
        'partial_refund' => 'Pengembalian sebagian',
        'request_failed' => 'Gagal membuat transaksi',
    ];

    $defaultScheme = old('scheme', array_key_first($options));
    $selectedOption = $options[$defaultScheme] ?? (count($options) ? reset($options) : null);
    $deadline = $paymentSummary['deadline'] ?? null;
    $isOverdue = (bool) ($paymentSummary['is_overdue'] ?? false);
@endphp

<main class="wo-page history-page">
    <div class="history-top-bar no-print">
        <a class="wo-link" href="{{ route('user.history.index') }}"><i class="bi bi-arrow-left"></i> Kembali ke Riwayat</a>
        <button type="button" class="wo-btn wo-btn-outline wo-btn-sm" onclick="window.print()">
            <i class="bi bi-printer"></i> Cetak Invoice
        </button>
    </div>

    @if(session('success'))
        <div class="history-alert history-alert-success no-print" role="status">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="history-alert history-alert-error no-print" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
        </div>
    @endif

    <header class="history-heading">
        <span class="wo-eyebrow"><i class="bi bi-circle-fill"></i> Invoice &amp; Pembayaran</span>
        <h1>{{ $booking->package?->name ?? 'Pernikahan Custom' }}</h1>
        <p>
            Dibuat {{ $booking->created_at?->locale('id')->translatedFormat('d F Y') }}
            <span>&bull;</span>
            <span class="wo-badge {{ $bookingStatusBadge }}">{{ $bookingStatusLabel }}</span>
            <span>&bull;</span>
            <span class="wo-badge {{ $paymentBadge }}">{{ $paymentLabel }}</span>
        </p>
        <p class="history-invoice"><i class="bi bi-receipt"></i> Invoice {{ $booking->invoice_number ?? 'belum diterbitkan' }} · Reservasi #{{ $booking->id }}</p>
    </header>

    <div class="history-detail-grid">
        {{-- PANEL PEMBAYARAN MIDTRANS (Tampil paling atas jika masih ada tagihan) --}}
        @if(count($options))
            <section class="wo-card history-pay-panel no-print" id="bayar">
                <div class="wo-section-head">
                    <div>
                        <span class="wo-eyebrow">Pembayaran Instan</span>
                        <h2>Bayar via Midtrans</h2>
                    </div>
                    <span class="wo-badge wo-badge-green"><i class="bi bi-shield-check"></i> Verifikasi Otomatis</span>
                </div>

                <div class="history-scheme-grid {{ count($options) === 1 ? 'is-single' : '' }}">
                    @foreach($options as $key => $option)
                        <label class="history-scheme-card">
                            <input type="radio" name="scheme" value="{{ $key }}" data-label="{{ $option['label'] }}" data-amount="{{ $option['amount'] }}" @checked($defaultScheme === $key)>
                            <div class="history-scheme-body">
                                <span class="history-scheme-title">{{ $option['label'] }}</span>
                                <strong class="history-scheme-amount">Rp {{ number_format((float) $option['amount'], 0, ',', '.') }}</strong>
                                <small>{{ $option['note'] }}</small>
                            </div>
                        </label>
                    @endforeach
                </div>

                <div class="history-pay-summary-bar" id="midtrans-box" data-pay-url="{{ route('payment.pay', $booking->id) }}" data-csrf="{{ csrf_token() }}">
                    <div class="history-pay-summary-info">
                        <div><span>Skema dipilih</span><strong id="sum-scheme">{{ $selectedOption['label'] ?? '-' }}</strong></div>
                        <div><span>Dibayar sekarang</span><strong class="is-highlight" id="sum-pay">Rp {{ number_format((float) ($selectedOption['amount'] ?? 0), 0, ',', '.') }}</strong></div>
                        <div><span>Sisa setelah bayar</span><strong id="sum-left">Rp {{ number_format(max(0, $remainingAmount - ($selectedOption['amount'] ?? 0)), 0, ',', '.') }}</strong></div>
                    </div>

                    <div class="history-pay-action-wrap">
                        <p id="midtrans-payment-error" class="history-inline-error d-none" role="alert"></p>
                        <button type="button" class="wo-btn wo-btn-dark history-midtrans-btn" id="midtrans-pay-button">
                            <i class="bi bi-credit-card-2-front"></i> Bayar Sekarang via Midtrans
                        </button>
                        <small class="history-muted">Mendukung QRIS, Virtual Account (BCA, BNI, BRI, Mandiri), GoPay, ShopeePay, dan Kartu.</small>
                    </div>
                </div>

                <details class="history-proof-details">
                    <summary><i class="bi bi-paperclip"></i> Lampirkan Foto / Struk Bukti Pembayaran (Opsional)</summary>
                    <form action="{{ route('payment.pay', $booking->id) }}" method="POST" enctype="multipart/form-data" class="history-proof-form">
                        @csrf
                        <input type="hidden" name="scheme" id="manual-scheme-input" value="{{ $defaultScheme }}">
                        <input type="hidden" name="payment_channel" value="midtrans">
                        <p class="history-muted">Tidak wajib. Unggah screenshot/struk jika ingin menyimpan arsip bukti pembayaran tambahan (JPG/PNG/PDF, maks. 4 MB).</p>
                        <div class="history-proof-row">
                            <input type="file" name="proof" accept=".jpg,.jpeg,.png,.pdf" required class="form-control form-control-sm">
                            <button type="submit" class="wo-btn wo-btn-outline wo-btn-sm">Unggah Bukti</button>
                        </div>
                        @error('proof') <small class="text-danger">{{ $message }}</small> @enderror
                    </form>
                </details>
            </section>
        @endif

        <section class="wo-card">
            <div class="wo-section-head">
                <div><span class="wo-eyebrow">Rincian Acara</span><h2>Acara dan Vendor</h2></div>
                <span class="wo-badge wo-badge-neutral">{{ $booking->events->count() }} acara</span>
            </div>
            @forelse($booking->events as $event)
                <div class="history-booking-event history-detail-event">
                    <div class="history-event-head">
                        <div><strong>{{ $event->name }}</strong><span>{{ $event->event_date?->format('d F Y') }}</span></div>
                        <span class="wo-badge {{ $event->status === 'completed' ? 'wo-badge-green' : 'wo-badge-neutral' }}">{{ $event->status === 'completed' ? 'Selesai' : ucfirst($event->status) }}</span>
                    </div>
                    @forelse($event->schedules as $schedule)
                        @if($schedule->vendor)
                            <div class="history-detail-vendor">
                                <div><strong>{{ $schedule->vendor->name }}</strong><span>{{ $schedule->vendor->category?->name ?? 'Vendor' }}</span></div>
                                @if($isPaid)<strong>Rp {{ number_format((float) $schedule->price, 0, ',', '.') }}</strong>@endif
                            </div>
                        @endif
                    @empty
                        <p class="history-muted">Belum ada vendor yang disetujui pada acara ini.</p>
                    @endforelse
                </div>
            @empty
                <p class="history-muted">Booking ini belum memiliki rincian acara.</p>
            @endforelse
            <div class="history-total"><span>Total booking</span><strong>Rp {{ number_format((float) $booking->total_price, 0, ',', '.') }}</strong></div>
        </section>

        <aside class="wo-card history-venue">
            <span class="wo-eyebrow">Informasi Booking</span>
            <h2>{{ $booking->venue_name }}</h2>
            <p><i class="bi bi-geo-alt"></i> {{ $booking->venue_address }}<br>{{ $booking->venue_city }}, {{ $booking->venue_province }}</p>
            <div class="history-venue-meta"><span>Jumlah tamu</span><strong>{{ number_format($booking->guest_count, 0, ',', '.') }} orang</strong></div>
            @if($booking->package)<div class="history-venue-meta"><span>Paket</span><strong>{{ $booking->package->name }}</strong></div>@endif
            <div class="history-venue-meta"><span>Status booking</span><span class="wo-badge {{ $bookingStatusBadge }}">{{ $bookingStatusLabel }}</span></div>
            <div class="history-venue-meta"><span>Status pembayaran</span><span class="wo-badge {{ $paymentBadge }}">{{ $paymentLabel }}</span></div>
            @if($deadline && ! $isPaid)
                <div class="history-venue-meta">
                    <span>{{ ($paymentSummary['deadline_type'] ?? '') === 'dp' ? 'Batas DP' : 'Tenggat pelunasan' }}</span>
                    <strong class="{{ $isOverdue ? 'text-danger' : '' }}">{{ $deadline->locale('id')->translatedFormat('d M Y') }}</strong>
                </div>
            @endif
        </aside>

        <section class="wo-card history-payments">
            <div class="wo-section-head">
                <div><span class="wo-eyebrow">Riwayat Transaksi</span><h2>Pembayaran</h2></div>
                <span class="wo-badge {{ $paymentBadge }}">{{ $paymentLabel }}</span>
            </div>
            <div class="history-payment-totals">
                <div><span>Total booking</span><strong>Rp {{ number_format((float) $booking->total_price, 0, ',', '.') }}</strong></div>
                <div><span>Sudah dibayar</span><strong>Rp {{ number_format($paidAmount, 0, ',', '.') }}</strong></div>
                <div><span>Sisa pembayaran</span><strong>Rp {{ number_format($remainingAmount, 0, ',', '.') }}</strong></div>
                @if($verificationAmount > 0)<div><span>Menunggu verifikasi manual</span><strong>Rp {{ number_format($verificationAmount, 0, ',', '.') }}</strong></div>@endif
                @if($midtransPendingAmount > 0)<div><span>Transaksi Midtrans berjalan</span><strong>Rp {{ number_format($midtransPendingAmount, 0, ',', '.') }}</strong></div>@endif
            </div>
            @forelse($booking->payments as $payment)
                @php
                    $statusClass = $payment->status === 'verified' ? 'wo-badge-green' : (in_array($payment->status, ['pending', 'initiated'], true) ? 'wo-badge-gold' : ($payment->status === 'rejected' ? 'wo-badge-red' : 'wo-badge-neutral'));
                    $methodLabel = $payment->method === 'midtrans' ? 'Midtrans' : ucfirst(str_replace('_', ' ', $payment->method));
                @endphp
                <article class="history-payment-row">
                    <div>
                        <strong>{{ $paymentTypes[$payment->type] ?? ucfirst($payment->type) }}</strong>
                        <span>
                            {{ $methodLabel }} · {{ $payment->created_at?->locale('id')->translatedFormat('d M Y, H:i') }}
                            @if($payment->proof_url) · <a href="{{ $payment->proof_url }}" target="_blank" class="wo-link" style="font-size:0.78rem;">Lihat bukti</a>@endif
                        </span>
                        @if($payment->method === 'midtrans')
                            <small>{{ $midtransLabels[$payment->transaction_status] ?? 'Status transaksi diperbarui' }}@if($payment->midtrans_order_id) · Ref {{ $payment->midtrans_order_id }}@endif</small>
                        @endif
                        @if($payment->verified_at)<small>Terverifikasi {{ $payment->verified_at->locale('id')->translatedFormat('d M Y, H:i') }}</small>@endif
                        @if($payment->admin_note)<small>Catatan: {{ $payment->admin_note }}</small>@endif
                    </div>
                    <div class="history-payment-amount">
                        <strong>Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</strong>
                        <span class="wo-badge {{ $statusClass }}">{{ $paymentStatuses[$payment->status] ?? ucfirst($payment->status) }}</span>
                    </div>
                    @if($payment->events->isNotEmpty())
                        <ol class="history-payment-timeline">
                            @foreach($payment->events as $paymentEvent)
                                <li><span>{{ $paymentEvent->message ?? 'Status transaksi diperbarui.' }}</span><time>{{ $paymentEvent->created_at?->locale('id')->translatedFormat('d M Y, H:i') }}</time></li>
                            @endforeach
                        </ol>
                    @endif
                </article>
            @empty
                <p class="history-muted history-payment-empty">Belum ada transaksi pembayaran untuk booking ini.</p>
            @endforelse
        </section>
    </div>
</main>
@endsection

@if(count($options))
    @push('scripts')
        <script src="{{ $snapScriptUrl }}" data-client-key="{{ $snapClientKey }}" defer></script>
        <script>
            (function () {
                const remaining = {{ (int) round($remainingAmount) }};
                const radios = document.querySelectorAll('input[name="scheme"]');
                const midtransBox = document.getElementById('midtrans-box');
                const manualSchemeInput = document.getElementById('manual-scheme-input');
                const button = document.getElementById('midtrans-pay-button');
                const errorBox = document.getElementById('midtrans-payment-error');
                const rupiah = (n) => 'Rp ' + Math.round(n).toLocaleString('id-ID');

                function updateSummary() {
                    const chosen = document.querySelector('input[name="scheme"]:checked');
                    if (!chosen) return;
                    const amount = Number(chosen.dataset.amount);
                    const schemeEl = document.getElementById('sum-scheme');
                    const payEl = document.getElementById('sum-pay');
                    const leftEl = document.getElementById('sum-left');
                    if (schemeEl) schemeEl.textContent = chosen.dataset.label;
                    if (payEl) payEl.textContent = rupiah(amount);
                    if (leftEl) leftEl.textContent = rupiah(Math.max(0, remaining - amount));
                    if (manualSchemeInput) manualSchemeInput.value = chosen.value;
                }

                radios.forEach((radio) => radio.addEventListener('change', updateSummary));
                updateSummary();

                if (!button || !midtransBox) return;

                const showMsg = (msg) => {
                    errorBox.textContent = msg;
                    errorBox.classList.remove('d-none');
                };

                button.addEventListener('click', async () => {
                    errorBox.classList.add('d-none');
                    button.disabled = true;
                    const originalHtml = button.innerHTML;
                    button.textContent = 'Menyiapkan pembayaran...';

                    try {
                        const selected = document.querySelector('input[name="scheme"]:checked');
                        const response = await fetch(midtransBox.dataset.payUrl, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': midtransBox.dataset.csrf
                            },
                            body: JSON.stringify({
                                scheme: selected ? selected.value : '',
                                payment_channel: 'midtrans'
                            })
                        });
                        const result = await response.json();

                        if (!response.ok) {
                            throw new Error(result.message || 'Tidak dapat menyiapkan pembayaran.');
                        }
                        if (!window.snap) {
                            throw new Error('Layanan Midtrans belum siap. Muat ulang halaman lalu coba lagi.');
                        }

                        window.snap.pay(result.token, {
                            onSuccess: () => {
                                showMsg('Pembayaran berhasil! Menyinkronkan status invoice...');
                                setTimeout(() => window.location.reload(), 800);
                            },
                            onPending: () => {
                                showMsg('Instruksi pembayaran Midtrans telah dibuat. Selesaikan pembayaran lalu muat ulang halaman.');
                                button.disabled = false;
                                button.innerHTML = originalHtml;
                            },
                            onError: () => {
                                showMsg('Pembayaran belum berhasil. Silakan coba lagi atau pilih metode lain.');
                                button.disabled = false;
                                button.innerHTML = originalHtml;
                            },
                            onClose: () => {
                                window.location.reload();
                            }
                        });
                    } catch (err) {
                        showMsg(err.message || 'Terjadi kesalahan saat menyiapkan pembayaran.');
                        button.disabled = false;
                        button.innerHTML = originalHtml;
                    }
                });
            })();
        </script>
    @endpush
@endif
