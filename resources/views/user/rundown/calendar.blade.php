{{-- resources/views/user/rundown/calendar.blade.php --}}
@extends('user.layouts.app')

@section('title', 'Rundown & Timeline Hari H')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/wo-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/rundown-calendar.css') }}">
@endpush

@section('content')
<main class="wo-page rundown-calendar">

    <div class="rc-head">
        <div>
            <span class="wo-eyebrow"><i class="bi bi-circle-fill"></i> Master Event Scheduler &bull; Live Execution</span>
            <h1>Kalender &amp; Tanggal Acara Berlangsung</h1>
            <p>Monitoring jadwal resepsi aktif, gladi resik, dan timeline operasional WO PROJECT.</p>
        </div>

        <button type="button" class="wo-btn wo-btn-dark">
            <i class="bi bi-plus-circle"></i> Jadwal Acara Baru
        </button>
    </div>


    <div class="rc-tabs">
        <span class="rc-tab is-active">Semua Acara <b>{{ $tabCounts['all'] }}</b></span>
        <span class="rc-tab">Resepsi Hari Ini <b>{{ $tabCounts['today'] }}</b></span>
        <span class="rc-tab">Gladi Resik <b>{{ $tabCounts['rehearsal'] }}</b></span>
        <span class="rc-tab">Mendatang <b>{{ $tabCounts['upcoming'] }}</b></span>
    </div>


    <div class="rc-highlight">
        <span class="wo-stat-icon"><i class="bi bi-star-fill"></i></span>
        <div>
            <span class="wo-badge wo-badge-gold">Fokus Hari H Terpadat &bull; {{ $highlight['crew_count'] }} Tim Bertugas Lapangan</span>
            <strong>{{ $highlight['title'] }}</strong>
            <p>{{ $highlight['desc'] }}</p>
        </div>
        <a href="{{ route('user.rundown.detail', $highlight['date']) }}" class="wo-btn wo-btn-dark">
            Buka Overview Acara {{ $highlight['date_short'] }} <i class="bi bi-arrow-right"></i>
        </a>
    </div>


    <div class="rc-layout">

        <div class="wo-card rc-calendar-card">

            <div class="rc-cal-top">
                <strong>{{ $monthLabel }}</strong>
                <span class="wo-badge wo-badge-neutral">{{ $tabCounts['all'] }} Resepsi &amp; Gladi</span>
            </div>

            <div class="rc-grid rc-grid-days">
                <span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span><span>Min</span>
            </div>

            @foreach ($weeks as $week)
                <div class="rc-grid rc-grid-week">
                    @foreach ($week as $day)
                        @if (is_null($day))
                            <div class="rc-day is-empty"></div>
                        @else
                            <a href="{{ $day['events'] ? route('user.rundown.detail', $day['date']) : '#' }}"
                               class="rc-day {{ $day['is_peak'] ? 'is-peak' : '' }} {{ empty($day['events']) ? 'is-inactive' : '' }}">
                                <span class="rc-day-num">{{ $day['num'] }}</span>
                                @foreach ($day['events'] as $ev)
                                    <span class="rc-pill rc-pill-{{ $ev['type'] }}">{{ $ev['label'] }}</span>
                                @endforeach
                            </a>
                        @endif
                    @endforeach
                </div>
            @endforeach

            <div class="rc-legend">
                <span><i class="dot dot-green"></i> Resepsi Akbar (Full Pack)</span>
                <span><i class="dot dot-amber"></i> Gladi Resik / GR Final</span>
                <span><i class="dot dot-teal"></i> Intimate Wedding</span>
                <span class="rc-legend-note"><i class="bi bi-info-circle"></i> Klik tanggal untuk membuka overview acara</span>
            </div>

        </div>


        <aside class="rc-sidebar">

            <div class="wo-card">
                <span class="wo-badge wo-badge-gold">Tanggal Terpilih</span>
                <h2 class="rc-selected-date">{{ $selected['date_label'] }}</h2>
                <p class="rc-selected-desc">Terdata {{ $selected['event_count'] }} acara pernikahan berlangsung serentak di berbagai venue rekanan premium.</p>

                <div class="rc-crew-box">
                    <div class="rc-crew-row">
                        <span>Kesiapan Tim Lapangan ({{ $selected['crew_percent'] }}%)</span>
                        <strong>{{ $selected['crew_ready'] }} / {{ $selected['crew_total'] }} Personil</strong>
                    </div>
                    <div class="wo-bar"><i style="width: {{ $selected['crew_percent'] }}%"></i></div>

                    <div class="rc-crew-legend">
                        <span><strong>{{ $selected['leads'] }}</strong> Lead Wedding Planners</span>
                        <span><strong>{{ $selected['mcs'] }}</strong> Master of Ceremonies</span>
                        <span><strong>{{ $selected['floor'] }}</strong> Kru Floor</span>
                    </div>
                </div>

                <a href="{{ route('user.rundown.detail', $selected['date']) }}" class="wo-btn wo-btn-dark rc-full-btn">
                    <i class="bi bi-box-arrow-up-right"></i> Buka Overview Lengkap {{ $selected['date_short'] }}
                </a>
            </div>


            <div class="rc-event-list">
                <div class="wo-section-head">
                    <strong>Daftar Acara Terpasang ({{ count($selected['events']) }})</strong>
                    <span class="wo-badge wo-badge-neutral">Sesi Siang &amp; Malam</span>
                </div>

                @foreach ($selected['events'] as $ev)
                    <div class="rc-event-card">
                        <span class="wo-badge wo-badge-neutral">{{ $ev['session'] }} ({{ $ev['time'] }})</span>
                        <strong>{{ $ev['couple'] }}</strong>
                        <span class="rc-event-venue"><i class="bi bi-building"></i> {{ $ev['venue'] }} &bull; {{ $ev['hall'] }}</span>
                        <span class="rc-event-meta"><i class="bi bi-person-badge"></i> Lead: {{ $ev['lead'] }}</span>
                        <span class="rc-event-meta"><i class="bi bi-shield-check"></i> Kru: {{ $ev['crew'] }}</span>

                        <div class="rc-event-footer">
                            <span>{{ $ev['progress_label'] }}</span>
                            <a href="{{ route('user.rundown.detail', $selected['date']) }}" class="wo-link">Rincian Acara <i class="bi bi-chevron-right"></i></a>
                        </div>
                    </div>
                @endforeach
            </div>

        </aside>

    </div>

</main>
@endsection
