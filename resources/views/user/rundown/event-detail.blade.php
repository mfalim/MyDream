{{-- resources/views/user/rundown/event-detail.blade.php --}}
@extends('user.layouts.app')

@section('title', 'Detail Acara - ' . $event['date_label'])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/wo-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/rundown-detail.css') }}">
@endpush

@section('content')
<main class="wo-page rundown-detail">

    <div class="rd-topbar">
        <a href="{{ route('user.rundown') }}" class="wo-link"><i class="bi bi-arrow-left"></i> Kembali ke Kalender Tanggal Acara</a>
        <div class="rd-topbar-actions">
            <button type="button" class="wo-btn wo-btn-outline wo-btn-sm"><i class="bi bi-printer"></i> Cetak Lembar Rundown</button>
            <button type="button" class="wo-btn wo-btn-dark wo-btn-sm"><i class="bi bi-broadcast"></i> Hubungi Tim Lapangan</button>
        </div>
    </div>


    {{-- HERO --}}
    <div class="rd-hero" style="background-image: linear-gradient(180deg, rgba(22,48,42,0.15), rgba(22,48,42,0.85)), url('{{ $event['hero_image'] }}');">
        <span class="rd-hero-status">
            <i class="bi bi-record-circle-fill"></i> {{ $event['live'] ? 'Sedang Berlangsung (Live)' : 'Terjadwal' }}
        </span>
        <span class="rd-hero-time">{{ $event['session_time'] }}</span>
        <div class="rd-hero-bottom">
            <span><i class="bi bi-geo-alt-fill"></i> {{ $event['venue'] }}</span>
            <h1>{{ $event['title'] }}</h1>
        </div>
    </div>


    {{-- QUICK STATS --}}
    <div class="rd-quick-grid">
        <div class="rd-quick-card">
            <span class="wo-stat-icon"><i class="bi bi-heart-fill"></i></span>
            <div><span>Pasangan Pengantin</span><strong>{{ $event['couple'] }}</strong></div>
        </div>
        <div class="rd-quick-card">
            <span class="wo-stat-icon"><i class="bi bi-gem"></i></span>
            <div><span>Paket Pilihan</span><strong>{{ $event['package'] }}</strong></div>
        </div>
        <div class="rd-quick-card">
            <span class="wo-stat-icon"><i class="bi bi-people-fill"></i></span>
            <div>
                <span>Tamu Undangan &bull; {{ $event['guests']['percent'] }}% Hadir</span>
                <strong>{{ $event['guests']['confirmed'] }} / {{ $event['guests']['total'] }} Terkonfirmasi</strong>
                <div class="wo-bar"><i style="width: {{ $event['guests']['percent'] }}%"></i></div>
            </div>
        </div>
        <div class="rd-quick-card">
            <span class="wo-stat-icon"><i class="bi bi-person-badge"></i></span>
            <div><span>Lead Wedding Director</span><strong>{{ $event['lead_director'] }}</strong></div>
        </div>
    </div>


    <div class="rd-layout">

        <div class="rd-main">

            {{-- PROGRESS SUMMARY --}}
            <div class="wo-card rd-progress-card">
                <div class="rd-progress-item">
                    <span>Waktu Berjalan (WIB)</span>
                    <strong>{{ $event['progress']['current_time'] }}</strong>
                    <small><span class="wo-badge wo-badge-green">Tepat Waktu</span></small>
                </div>
                <div class="rd-progress-item">
                    <span>Fase Berjalan</span>
                    <strong>{{ $event['progress']['phase_label'] }}</strong>
                    <div class="wo-bar"><i style="width: {{ $event['progress']['phase_percent'] }}%"></i></div>
                </div>
                <div class="rd-progress-item">
                    <span>Personel Lapangan</span>
                    <strong>{{ $event['progress']['crew_count'] }} Tim On-Site</strong>
                    <small>{{ $event['progress']['crew_note'] }}</small>
                </div>
            </div>

            {{-- RUNDOWN FLOW (FASES) --}}
            <div class="wo-card">
                <div class="wo-section-head">
                    <div>
                        <span class="wo-eyebrow"><i class="bi bi-clock-history"></i> Urutan Acara</span>
                        <h2>Rundown Flow &amp; Timeline</h2>
                    </div>
                    <span class="wo-badge wo-badge-neutral">Data diperbarui otomatis oleh Stage Director</span>
                </div>

                @foreach ($phases as $phase)
                    <div class="rd-phase {{ $phase['state'] }}">
                        <div class="rd-phase-head">
                            <span class="wo-badge {{ $phase['state'] === 'done' ? 'wo-badge-green' : ($phase['state'] === 'active' ? 'wo-badge-gold' : 'wo-badge-neutral') }}">
                                {{ $phase['state_label'] }}
                            </span>
                            <strong>{{ $phase['title'] }}</strong>
                            <span class="rd-phase-time">{{ $phase['time_range'] }}</span>
                        </div>

                        <div class="wo-timeline rd-phase-items">
                            @foreach ($phase['items'] as $item)
                                <div class="wo-timeline-item {{ $item['done'] ? 'is-done' : ($item['active'] ?? false ? 'is-active' : '') }}">
                                    <span class="wo-timeline-dot"><i class="bi bi-{{ $item['done'] ? 'check' : 'circle' }}"></i></span>
                                    <div class="rd-item-card">
                                        <span class="rd-item-time">{{ $item['time'] }}</span>
                                        <strong>{{ $item['title'] }}</strong>
                                        @if (!empty($item['note']))
                                            <span class="rd-item-note">{{ $item['note'] }}</span>
                                        @endif
                                        @if (!empty($item['tag']))
                                            <span class="wo-badge wo-badge-neutral rd-item-tag">{{ $item['tag'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- FINANCIAL --}}
            <div class="wo-card">
                <div class="wo-section-head">
                    <div>
                        <span class="wo-eyebrow"><i class="bi bi-shield-check"></i> Finansial &amp; Escrow</span>
                        <h2>Ringkasan Kontrak</h2>
                    </div>
                    <span class="wo-badge wo-badge-green">Escrow Secured</span>
                </div>

                <div class="rd-finance-total">
                    <span>Total Nilai Kontrak</span>
                    <strong>Rp {{ number_format($finance['total'], 0, ',', '.') }}</strong>
                    <span class="wo-badge wo-badge-gold">{{ $finance['status'] }}</span>
                </div>

                <div class="rd-finance-rows">
                    @foreach ($finance['terms'] as $term)
                        <div class="rd-finance-row">
                            <span>{{ $term['label'] }}</span>
                            <strong>Rp {{ number_format($term['amount'], 0, ',', '.') }} <span class="rd-paid">({{ $term['status'] }})</span></strong>
                        </div>
                    @endforeach
                    <div class="rd-finance-row">
                        <span>Status Penyaluran Vendor</span>
                        <strong class="rd-escrow-note">{{ $finance['disbursement_status'] }}</strong>
                    </div>
                </div>
            </div>

        </div>


        <aside class="rd-sidebar">

            {{-- VENDOR REKANAN --}}
            <div class="wo-card">
                <div class="wo-section-head">
                    <div>
                        <span class="wo-eyebrow">Kolaborasi Premium</span>
                        <h2>Vendor Rekanan Terikat</h2>
                    </div>
                    <span class="wo-badge wo-badge-green">{{ $vendorsOnSite['ready'] }}/{{ $vendorsOnSite['total'] }} On-Site</span>
                </div>

                <div class="rd-vendor-list">
                    @foreach ($vendorsOnSite['list'] as $v)
                        <div class="rd-vendor-item">
                            <div class="rd-vendor-top">
                                <strong>{{ $v['name'] }}</strong>
                                <span class="wo-badge wo-badge-green">{{ $v['status'] }}</span>
                            </div>
                            <span class="rd-vendor-desc"><i class="bi bi-{{ $v['icon'] }}"></i> {{ $v['desc'] }}</span>
                            <div class="rd-vendor-footer">
                                <span>PIC: {{ $v['pic'] }}</span>
                                <span>{{ $v['note'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="button" class="wo-btn wo-btn-outline rd-full-btn">
                    <i class="bi bi-telephone"></i> Buka Direktori Telepon Semua PIC Vendor
                </button>
            </div>

            {{-- TIM ON DUTY --}}
            <div class="wo-card">
                <div class="wo-section-head">
                    <strong>Tim On-Duty</strong>
                    <span class="wo-badge wo-badge-green">{{ $crew['total'] }} Terverifikasi</span>
                </div>

                <div class="rd-crew-grid">
                    @foreach ($crew['list'] as $c)
                        <div class="rd-crew-item">
                            <span class="wo-avatar">{{ $c['initials'] }}</span>
                            <div>
                                <strong>{{ $c['name'] }}</strong>
                                <span>{{ $c['role'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="rd-crew-channel">
                    <span>Kanal Komunikasi</span>
                    <span class="wo-badge wo-badge-green"><i class="bi bi-broadcast"></i> Frekuensi Radio Aktif</span>
                </div>
            </div>

        </aside>

    </div>

</main>
@endsection
