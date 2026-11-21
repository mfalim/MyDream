@extends('user.layouts.app')

@section('title', 'Dashboard Pernikahan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/wo-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard-home.css') }}">
@endpush

@section('content')
<main class="wo-page dashboard-home">
    <section class="dh-hero">
        <div class="dh-hero-text">
            <span class="wo-eyebrow"><i class="bi bi-circle-fill"></i> Dashboard Client</span>
            <h1>Selamat datang, {{ $userName }}!</h1>

            @if($event && $booking)
                <p>
                    {{ $client->groom_name ?: $userName }} &amp; {{ $client->bride_name ?: 'Pasangan Anda' }}
                    <span>&bull;</span> {{ $event->name }}
                    <span>&bull;</span> <i class="bi bi-geo-alt"></i> {{ $booking->venue_name }}
                </p>
            @else
                <p>Ringkasan booking dan persiapan acara Anda.</p>
            @endif
        </div>

        @if($event)
            <div class="dh-countdown">
                <i class="bi bi-hourglass-split"></i>
                <div>
                    <span>{{ $event->event_date?->isToday() ? 'Hari acara' : ($daysToEvent !== null ? 'Hitung mundur acara' : 'Acara terakhir') }}</span>
                    <strong>{{ $daysToEvent === 0 ? 'Hari ini' : ($daysToEvent !== null ? $daysToEvent . ' hari lagi' : $eventDateLabel) }}</strong>
                    <small>{{ $eventDateLabel }} · {{ ucfirst($event->status) }}</small>
                </div>
            </div>
        @endif
    </section>

    @if($paymentBooking && $paymentSummary && $paymentSummary['total'] > 0 && ($paymentSummary['remaining'] > 0 || $paymentSummary['status'] === 'dp_paid'))
        @php
            $pStatus = $paymentSummary['status'];
            $pDeadline = $paymentSummary['deadline'];
            $pDaysLeft = $paymentSummary['days_to_deadline'];
            $pIsOverdue = $paymentSummary['is_overdue'];
            $pDeadlineText = null;
            if ($pDeadline) {
                $dateStr = $paymentSummary['deadline_type'] === 'dp'
                    ? $pDeadline->locale('id')->translatedFormat('d M Y, H:i')
                    : $pDeadline->locale('id')->translatedFormat('d M Y');
                if ($pIsOverdue) {
                    $pDeadlineText = 'Melewati tenggat (' . $dateStr . ') — segera selesaikan pembayaran';
                } elseif ($pDaysLeft === 0) {
                    $pDeadlineText = 'Jatuh tempo hari ini (' . $dateStr . ')';
                } else {
                    $suffix = $paymentSummary['deadline_type'] === 'settlement'
                        ? ' · H-' . $paymentSummary['deadline_days_before'] . ' sebelum acara'
                        : '';
                    $pDeadlineText = 'Batas waktu: ' . $dateStr . ' (' . $pDaysLeft . ' hari lagi' . $suffix . ')';
                }
            }
        @endphp
        <section class="dh-payment-banner {{ $pIsOverdue ? 'is-overdue' : ($pStatus === 'dp_paid' ? 'is-dp' : '') }}" aria-label="Pengingat Pembayaran">
            <div class="dh-payment-banner-main">
                <div class="dh-payment-banner-top">
                    @if($pStatus === 'dp_paid')
                        <span class="wo-badge wo-badge-green"><i class="bi bi-check-circle-fill"></i> Sedang Masa DP ({{ $paymentSummary['percent_paid'] }}% Terbayar)</span>
                    @elseif($pStatus === 'awaiting_verification')
                        <span class="wo-badge wo-badge-gold"><i class="bi bi-hourglass-split"></i> Menunggu Verifikasi Pembayaran</span>
                    @else
                        <span class="wo-badge wo-badge-gold"><i class="bi bi-exclamation-circle-fill"></i> Menunggu Pembayaran DP ({{ $paymentSummary['dp_percent'] }}%)</span>
                    @endif
                    @if($paymentBooking->invoice_number)
                        <span class="wo-badge wo-badge-neutral">{{ $paymentBooking->invoice_number }}</span>
                    @endif
                </div>

                @if($pStatus === 'dp_paid')
                    <h2>Pelunasan Sisa Tagihan: Rp {{ number_format($paymentSummary['remaining'], 0, ',', '.') }}</h2>
                    <p>
                        DP sebesar <strong>Rp {{ number_format($paymentSummary['paid'], 0, ',', '.') }}</strong> sudah terverifikasi.
                        @if($paymentSummary['pending'] > 0)
                            Ada pembayaran <strong>Rp {{ number_format($paymentSummary['pending'], 0, ',', '.') }}</strong> yang sedang diverifikasi.
                        @else
                            Tanggal acara Anda telah terkunci. Segera lunasi sisa tagihan sebelum tenggat waktu.
                        @endif
                    </p>
                @elseif($pStatus === 'awaiting_verification')
                    <h2>Pembayaran Sedang Diverifikasi (Rp {{ number_format($paymentSummary['pending'], 0, ',', '.') }})</h2>
                    <p>Tim kami sedang memeriksa bukti transfer Anda. Sisa tagihan saat ini: <strong>Rp {{ number_format($paymentSummary['remaining'], 0, ',', '.') }}</strong>.</p>
                @else
                    <h2>Segera Bayar DP Minimal Rp {{ number_format($paymentSummary['dp_amount'], 0, ',', '.') }}</h2>
                    <p>Lakukan pembayaran DP ({{ $paymentSummary['dp_percent'] }}%) atau bayar lunas untuk mengunci jadwal dan vendor pernikahan Anda.</p>
                @endif

                @if($pDeadlineText)
                    <span class="dh-payment-deadline {{ $pIsOverdue ? 'overdue' : '' }}">
                        <i class="bi {{ $pIsOverdue ? 'bi-exclamation-triangle-fill' : 'bi-calendar-event' }}"></i>
                        {{ $pDeadlineText }}
                    </span>
                @endif
            </div>

            <div class="dh-payment-banner-actions">
                <div class="dh-payment-progress-wrap">
                    <div class="dh-payment-progress-meta">
                        <span>Progres Pembayaran</span>
                        <strong>{{ $paymentSummary['percent_paid'] }}%</strong>
                    </div>
                    <progress class="dh-progress" max="100" value="{{ $paymentSummary['percent_paid'] }}"></progress>
                </div>
                <a href="{{ route('user.history.show', $paymentBooking->id) }}#bayar" class="wo-btn wo-btn-dark">
                    <i class="bi bi-credit-card"></i>
                    {{ $pStatus === 'dp_paid' ? 'Lunasi Sekarang' : 'Bayar DP / Lunas' }}
                </a>
            </div>
        </section>
    @endif

    <section class="dh-stat-grid">
            <article class="wo-card dh-stat-card">
                <span class="wo-badge wo-badge-gold">Ringkasan Booking</span>
                <h2 class="dh-card-title">Status Booking: {{ match($bookingSummary?->status) {
                    'approved' => 'Disetujui',
                    'pending' => 'Menunggu Persetujuan',
                    'rejected' => 'Ditolak',
                    default => 'Belum tersedia',
                } }}</h2>
                <p class="dh-budget-amount">{{ $bookingSummary?->total_price !== null ? 'Rp ' . number_format((float) $bookingSummary->total_price, 0, ',', '.') : 'Belum ditentukan' }}</p>
                @if($paymentSummary)
                    <div class="dh-stat-foot">
                        <span>Terbayar ({{ $paymentSummary['percent_paid'] }}%)</span>
                        <strong class="text-success">Rp {{ number_format($paymentSummary['paid'], 0, ',', '.') }}</strong>
                    </div>
                    <div class="dh-stat-foot">
                        <span>Sisa tagihan</span>
                        <strong>Rp {{ number_format($paymentSummary['remaining'], 0, ',', '.') }}</strong>
                    </div>
                @else
                    <div class="dh-stat-foot"><span>Status sistem</span><strong class="dh-status dh-status-{{ $bookingSummary?->status ?? 'unavailable' }}">{{ ucfirst($bookingSummary?->status ?? 'Belum tersedia') }}</strong></div>
                    <div class="dh-stat-foot"><span>Jumlah tamu</span><strong>{{ number_format((int) ($bookingSummary?->guest_count ?? 0), 0, ',', '.') }} orang</strong></div>
                @endif
                <a href="{{ route('user.history.index') }}" class="wo-link">Buka Riwayat Pembelian <i class="bi bi-arrow-right"></i></a>
            </article>

            <article class="wo-card dh-stat-card">
                <span class="wo-badge wo-badge-green">Kesiapan Vendor</span>
                @if($bookingSummary?->status === 'approved')
                    <h2 class="dh-card-title">{{ $vendorReadiness['percent'] }}% terjadwal</h2>
                    <p class="dh-card-sub">{{ $vendorReadiness['confirmed'] }} dari {{ $vendorReadiness['total'] }} vendor pada acara ini telah dijadwalkan oleh Tim WO.</p>
                    <progress class="dh-progress" max="100" value="{{ $vendorReadiness['percent'] }}" aria-label="Kesiapan vendor {{ $vendorReadiness['percent'] }} persen"></progress>
                    <div class="dh-stat-foot"><span>{{ $vendorReadiness['waiting'] }} menunggu</span><span>{{ $vendorReadiness['review'] }} terkendala</span></div>
                @else
                    <p class="dh-card-sub">Data vendor akan tampil setelah booking disetujui Admin.</p>
                @endif
                <a href="{{ route('user.vendor-confirmation') }}" class="wo-link">Lihat status &amp; jadwal vendor <i class="bi bi-arrow-right"></i></a>
            </article>

            <article class="wo-card dh-stat-card">
                <span class="wo-badge wo-badge-neutral">Informasi Acara</span>
                @if($bookingSummary?->status === 'approved')
                    <h2 class="dh-card-title">{{ $event?->name ?? 'Belum ada acara' }}</h2>
                    <p class="dh-card-sub">{{ $event?->event_date?->format('d/m/Y') ?? 'Tanggal belum ditentukan' }} · {{ ucfirst($event?->status ?? 'Belum tersedia') }}</p>
                    <div class="dh-event-summary"><i class="bi bi-geo-alt"></i><span>{{ $booking?->venue_name ?? 'Lokasi belum ditentukan' }}<small>{{ $booking?->venue_city ?? '-' }}, {{ $booking?->venue_province ?? '-' }}</small></span></div>
                    <div class="dh-event-summary"><i class="bi bi-calendar-event"></i><span>{{ $bookingEvents->count() }} acara dalam booking</span></div>
                @else
                    <p class="dh-card-sub">Informasi acara akan tampil setelah booking disetujui.</p>
                @endif
                <a href="{{ route('user.rundown') }}" class="wo-link">Lihat Rundown <i class="bi bi-arrow-right"></i></a>
            </article>
        </section>

        <div class="dh-layout">
            <section class="dh-main">
                <article class="wo-card dh-content-card">
                    <div class="wo-section-head">
                        <div><span class="wo-eyebrow">Jadwal Anda</span><h2>Acara Mendatang</h2></div>
                        <a href="{{ route('user.rundown') }}" class="wo-link">Semua acara <i class="bi bi-arrow-right"></i></a>
                    </div>

                    @if($bookingSummary?->status !== 'approved')
                        <div class="dh-inline-empty"><i class="bi bi-hourglass-split"></i><p>Jadwal akan tampil setelah booking disetujui.</p></div>
                    @elseif($upcomingEvents->isEmpty())
                        <div class="dh-inline-empty"><i class="bi bi-calendar-check"></i><p>Tidak ada acara mendatang pada booking aktif Anda.</p></div>
                    @else
                        <div class="dh-event-list">
                            @foreach($upcomingEvents as $upcomingEvent)
                                <div class="dh-event-row">
                                    <span class="dh-date-box"><strong>{{ $upcomingEvent->event_date?->format('d') }}</strong><small>{{ $upcomingEvent->event_date?->format('M') }}</small></span>
                                    <div class="dh-event-info">
                                        <strong>{{ $upcomingEvent->name }}</strong>
                                        <span>{{ $upcomingEvent->event_type ? ucfirst($upcomingEvent->event_type) . ' · ' : '' }}{{ $upcomingEvent->event_date?->format('d/m/Y') }}</span>
                                        @if($upcomingEvent->notes)
                                            <span class="dh-event-notes"><i class="bi bi-sticky"></i> {{ $upcomingEvent->notes }}</span>
                                        @endif
                                    </div>
                                    <span class="dh-status dh-status-{{ $upcomingEvent->status }}">{{ ucfirst($upcomingEvent->status) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </article>

                <article class="wo-card dh-content-card">
                    <div class="wo-section-head">
                        <div><span class="wo-eyebrow">Penanggung Jawab Acara</span><h2>Vendor pada Acara Ini</h2></div>
                        <span class="wo-badge wo-badge-neutral">{{ $vendorCount }} vendor</span>
                    </div>

                    @if($bookingSummary?->status !== 'approved')
                        <div class="dh-inline-empty"><i class="bi bi-hourglass-split"></i><p>Penanggung jawab akan tampil setelah booking disetujui.</p></div>
                    @elseif($vendorSchedules->isEmpty())
                        <div class="dh-inline-empty"><i class="bi bi-shop"></i><p>Belum ada vendor yang tercatat pada acara ini.</p></div>
                    @else
                        <div class="dh-vendor-list">
                            @foreach($vendorSchedules as $schedule)
                                @if($schedule->vendor)
                                    <div class="dh-vendor-row">
                                        <span class="dh-vendor-icon"><i class="bi bi-shop"></i></span>
                                        <div class="dh-vendor-info"><strong>{{ $schedule->vendor->name }}</strong><span>{{ $schedule->vendor->category?->name ?? 'Vendor' }}</span></div>
                                        <span class="dh-status dh-status-{{ $schedule->status }}">{{ ucfirst($schedule->status) }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                    <a href="{{ route('user.vendor-confirmation') }}" class="wo-link">Lihat status &amp; jadwal vendor <i class="bi bi-arrow-right"></i></a>
                </article>
            </section>

            <aside class="dh-sidebar">
                <article class="wo-card dh-content-card">
                    <div class="wo-section-head"><div><span class="wo-eyebrow">Navigasi Cepat</span><h2>Kelola Acara</h2></div></div>
                    <nav class="dh-shortcuts">
                        <a href="{{ route('user.rundown') }}"><i class="bi bi-calendar3"></i><span>Rundown Timeline</span><i class="bi bi-chevron-right"></i></a>
                        <a href="{{ route('user.vendor-confirmation') }}"><i class="bi bi-patch-check"></i><span>Status &amp; Jadwal Vendor</span><i class="bi bi-chevron-right"></i></a>
                        <a href="{{ route('user.history.index') }}"><i class="bi bi-clock-history"></i><span>Riwayat Pembelian</span><i class="bi bi-chevron-right"></i></a>
                        <a href="{{ route('user.reviews.index') }}"><i class="bi bi-star"></i><span>Ulasan</span><i class="bi bi-chevron-right"></i></a>
                    </nav>
                </article>

                <article class="dh-booking-note">
                    <span class="wo-badge wo-badge-gold">Ketentuan Pembayaran</span>
                    @if($paymentSummary && $paymentSummary['status'] === 'paid')
                        <strong>Pembayaran Lunas 100%</strong>
                        <p>Seluruh tagihan booking Anda sudah terverifikasi lunas. Tim MyDream siap mendampingi persiapan hingga hari acara.</p>
                    @elseif($paymentSummary && $paymentSummary['deadline'])
                        <strong>{{ $paymentSummary['deadline_type'] === 'dp' ? 'Batas DP ' . $paymentSummary['dp_percent'] . '%' : 'Tenggat Pelunasan H-' . $paymentSummary['deadline_days_before'] }}</strong>
                        <p>Pelunasan penuh paling lambat {{ $paymentSummary['deadline']->locale('id')->translatedFormat('d M Y') }} agar seluruh jadwal vendor tetap aman hingga hari pernikahan.</p>
                    @else
                        <strong>Ringkasan dari data Anda</strong>
                        <p>Anggaran menunjukkan total nilai booking. Anda dapat melakukan pembayaran DP minimal {{ config('mydream.dp_percent', 10) }}% atau pelunasan penuh melalui menu Riwayat Pembelian.</p>
                    @endif
                </article>
            </aside>
        </div>
</main>
@endsection
