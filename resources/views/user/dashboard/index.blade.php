{{-- resources/views/user/dashboard/index.blade.php --}}
@extends('user.layouts.app')

@section('title', 'Dashboard Pernikahan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/wo-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard-home.css') }}">
@endpush

@section('content')
<main class="wo-page dashboard-home">

    {{-- HEADER --}}
    <section class="dh-hero">

        <div class="dh-hero-text">
            <span class="wo-eyebrow"><i class="bi bi-circle-fill"></i> Wedding Concierge Atelier Portal</span>
            <h1>Selamat Datang, {{ $couple['names'] }}!</h1>
            <p>
                {{ $couple['package_name'] }} <span>&bull;</span> {{ $couple['event_date_label'] }} <span>&bull;</span>
                <i class="bi bi-geo-alt"></i> {{ $couple['venue'] }}
            </p>
        </div>

        <div class="dh-hero-actions">

            <div class="dh-countdown">
                <i class="bi bi-hourglass-split"></i>
                <div>
                    <span>Hitung Mundur Hari H</span>
                    <strong>{{ $countdown['days'] }} Hari &nbsp; {{ $countdown['hours'] }} Jam &nbsp; {{ $countdown['minutes'] }} Mnt</strong>
                </div>
            </div>

            <div class="dh-quick-links">
                <a href="{{ route('user.rundown') }}" class="wo-btn wo-btn-dark wo-btn-sm">
                    <i class="bi bi-journal-bookmark"></i> Buka Rundown Acara
                </a>
                <a href="https://wa.me/6280000000000" target="_blank" rel="noopener" class="wo-btn wo-btn-outline wo-btn-sm">
                    <i class="bi bi-telephone"></i> Hotline Wedding Lead
                </a>
            </div>

        </div>

    </section>


    {{-- TOP STAT CARDS --}}
    <section class="dh-stat-grid">

        {{-- Vendor Readiness --}}
        <div class="wo-card">
            <span class="wo-badge wo-badge-green">Vendor Readiness</span>
            <h3 class="dh-card-title">Kesiapan Vendor</h3>
            <p class="dh-card-sub">{{ $vendorReadiness['confirmed'] }} dari {{ $vendorReadiness['total'] }} Vendor Utama Telah Memberi Konfirmasi Final</p>

            <div class="dh-ring-row">
                <div class="wo-ring" style="--pct: {{ $vendorReadiness['percent'] }}">
                    <span>{{ $vendorReadiness['percent'] }}%</span>
                </div>

                <ul class="dh-legend">
                    <li><i class="dot dot-green"></i> {{ $vendorReadiness['confirmed'] }} Siap &amp; Fixed</li>
                    <li><i class="dot dot-amber"></i> {{ $vendorReadiness['waiting'] }} Menunggu Respon</li>
                    <li><i class="dot dot-red"></i> {{ $vendorReadiness['review'] }} Perlu Review Adat</li>
                </ul>
            </div>

            <a href="{{ route('user.vendor-confirmation') }}" class="wo-link">Kelola Semua Vendor Rekanan <i class="bi bi-arrow-right"></i></a>
        </div>

        {{-- Budget --}}
        <div class="wo-card">
            <span class="wo-badge wo-badge-gold">Escrow Protection</span>
            <h3 class="dh-card-title">Anggaran &amp; Rekening Dana</h3>
            <p class="dh-card-sub">Sistem Dana Aman WO PROJECT Terverifikasi</p>

            <div class="dh-budget-box">
                <span>TOTAL ANGGARAN DEAL</span>
                <strong>Rp {{ number_format($budget['total'], 0, ',', '.') }}</strong>

                <div class="wo-bar"><i style="width: {{ $budget['paid_percent'] }}%"></i></div>

                <div class="dh-budget-row">
                    <span>Telah Dibayar (Termin 1 &amp; 2)</span>
                    <strong>Rp {{ number_format($budget['paid'], 0, ',', '.') }} ({{ $budget['paid_percent'] }}%)</strong>
                </div>
                <div class="dh-budget-row">
                    <span>Sisa Pelunasan (H-7)</span>
                    <strong>Rp {{ number_format($budget['remaining'], 0, ',', '.') }}</strong>
                </div>
            </div>

            <a href="#" class="wo-link">Lihat Riwayat &amp; Invoice Escrow <i class="bi bi-arrow-right"></i></a>
        </div>

        {{-- Checklist --}}
        <div class="wo-card">
            <span class="wo-badge wo-badge-neutral">Checklist Pengantin</span>
            <h3 class="dh-card-title">Tugas &amp; Agenda Pribadi</h3>
            <p class="dh-card-sub">{{ $checklist['done'] }} dari {{ $checklist['total'] }} persiapan telah terselesaikan dengan baik</p>

            <ul class="dh-task-list">
                @foreach ($checklist['items'] as $item)
                    <li>
                        <i class="bi bi-{{ $item['icon'] }}"></i>
                        <div>
                            <strong>{{ $item['title'] }}</strong>
                            <span>{{ $item['when'] }} &bull; {{ $item['where'] }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>

            <a href="#" class="wo-link">Buka Semua Checklist ({{ $checklist['total'] }} Item) <i class="bi bi-arrow-right"></i></a>
        </div>

    </section>


    {{-- MAIN + SIDEBAR --}}
    <div class="dh-layout">

        <div class="dh-main">

            {{-- MILESTONE TIMELINE --}}
            <div class="wo-card">
                <div class="wo-section-head">
                    <div>
                        <span class="wo-eyebrow">Critical Path Timeline</span>
                        <h2>Milestone Menjelang Resepsi</h2>
                    </div>
                    <span class="wo-badge wo-badge-neutral">Sinkronisasi Otomatis Vendor</span>
                </div>

                <div class="wo-timeline">
                    @foreach ($milestones as $m)
                        <div class="wo-timeline-item {{ $m['state'] === 'done' ? 'is-done' : ($m['state'] === 'active' ? 'is-active' : '') }}">
                            <span class="wo-timeline-dot">
                                <i class="bi bi-{{ $m['icon'] }}"></i>
                            </span>

                            <div class="dh-milestone-card">
                                <div class="dh-milestone-top">
                                    <span class="dh-milestone-label">{{ $m['status_label'] }} <span>&bull;</span> {{ $m['date'] }}</span>
                                    @if (!empty($m['tag']))
                                        <span class="wo-badge wo-badge-neutral">{{ $m['tag'] }}</span>
                                    @endif
                                </div>
                                <strong>{{ $m['title'] }}</strong>
                                <p>{{ $m['desc'] }}</p>
                                @if (!empty($m['action']))
                                    <button type="button" class="wo-btn wo-btn-outline wo-btn-sm">{{ $m['action'] }}</button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- MOODBOARD --}}
            <div class="wo-card">
                <div class="wo-section-head">
                    <div>
                        <span class="wo-eyebrow">Visual Identity &amp; Creative</span>
                        <h2>Moodboard &amp; Desain Pelaminan</h2>
                    </div>
                    <a href="#" class="wo-link">Buka Moodboard HD <i class="bi bi-box-arrow-up-right"></i></a>
                </div>

                <div class="dh-moodboard">
                    @foreach ($moodboard as $item)
                        <figure>
                            <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}">
                            <figcaption>
                                <strong>{{ $item['title'] }}</strong>
                                <span>{{ $item['subtitle'] }}</span>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>

        </div>


        <aside class="dh-sidebar">

            {{-- TEAM --}}
            <div class="wo-card">
                <div class="dh-team-head">
                    <span class="wo-stat-icon"><i class="bi bi-people-fill"></i></span>
                    <div>
                        <strong>Dedicated Wedding Team</strong>
                        <span>Tim Khusus Pemegang Acara Anda</span>
                    </div>
                </div>

                <ul class="dh-team-list">
                    @foreach ($team as $person)
                        <li>
                            <span class="wo-avatar">{{ $person['initials'] }}</span>
                            <div>
                                <strong>{{ $person['name'] }}</strong>
                                <span>{{ $person['role'] }}</span>
                                <small>{{ $person['note'] }}</small>
                            </div>
                            <i class="bi bi-chat-dots"></i>
                        </li>
                    @endforeach
                </ul>

                <button type="button" class="wo-btn wo-btn-dark dh-full-btn">
                    <i class="bi bi-headset"></i> Hubungi Hotline Darurat H-24
                </button>
                <small class="dh-response-time">Waktu respon rata-rata &lt; 5 menit</small>
            </div>

            {{-- DOCUMENTS --}}
            <div class="wo-card">
                <div class="wo-section-head">
                    <strong>Dokumen Terverifikasi</strong>
                    <i class="bi bi-folder2-open"></i>
                </div>

                <ul class="dh-doc-list">
                    @foreach ($documents as $doc)
                        <li>
                            <i class="bi bi-file-earmark-text"></i>
                            <div>
                                <strong>{{ $doc['title'] }}</strong>
                                <span>{{ $doc['note'] }}</span>
                            </div>
                            <a href="#"><i class="bi bi-download"></i></a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- GUARANTEE --}}
            <div class="dh-guarantee">
                <span class="wo-badge wo-badge-gold"><i class="bi bi-star-fill"></i> Garansi Kepuasan Klien</span>
                <strong>100% On-Time Execution</strong>
                <p>Seluruh rundown dikoordinasikan lewat silent intercom &amp; live monitor backstage untuk memastikan momen sakral Anda berjalan tanpa cela.</p>
            </div>

        </aside>

    </div>

</main>
@endsection
