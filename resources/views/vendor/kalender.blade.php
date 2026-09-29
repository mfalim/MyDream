@extends('vendor.layouts.app')

@section('title', 'Kalender & Jadwal Acara')

@section('content')

@php
$bulan = (int) request('bulan', 10);
$tahun = (int) request('tahun', 2025);

if ($bulan < 1) {
    $bulan = 12;
    $tahun--;
}

if ($bulan > 12) {
    $bulan = 1;
    $tahun++;
}

$namaBulan = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember',
];

$namaHari = [
    'SEN',
    'SEL',
    'RAB',
    'KAM',
    'JUM',
    'SAB',
    'MIN',
];

$events = [
    '2025-10-01' => [
        'title' => 'Plotting Tim Workshop',
        'venue' => 'Tim Workshop',
        'type' => 'loading',
        'status' => 'Draft Loading',
    ],

    '2025-10-02' => [
        'title' => 'Loading In 22:00',
        'venue' => 'Ballroom Mulia',
        'type' => 'loading',
        'status' => 'Draft Loading',
    ],

    '2025-10-03' => [
        'title' => 'Loading 22:00',
        'venue' => 'Ballroom Mulia',
        'type' => 'loading',
        'status' => 'Draft Loading',
    ],

    '2025-10-04' => [
        'title' => 'Kevin & Michelle',
        'venue' => 'The Dharmawangsa',
        'type' => 'closed',
        'status' => 'Selesai',
        'overview' => true,
    ],

    '2025-10-05' => [
        'title' => 'Maintenance Alat',
        'venue' => 'Workshop',
        'type' => 'option',
        'status' => 'Opsi Cadangan',
    ],

    '2025-10-08' => [
        'title' => 'Meeting Vendor',
        'venue' => 'Vendor Meeting',
        'type' => 'option',
        'status' => 'Opsi Cadangan',
    ],

    '2025-10-10' => [
        'title' => 'Loading',
        'venue' => 'Plataran',
        'type' => 'loading',
        'status' => 'Loading',
    ],

    '2025-10-12' => [
        'title' => 'Arya & Anindita',
        'venue' => 'Plataran Cilandak',
        'type' => 'closed',
        'status' => 'Selesai',
        'overview' => true,
    ],

    '2025-10-15' => [
        'title' => 'Technical Meeting',
        'venue' => 'Technical Meeting',
        'type' => 'loading',
        'status' => 'Persiapan',
    ],

    '2025-10-17' => [
        'title' => 'Loading',
        'venue' => 'Ritz 21:00',
        'type' => 'loading',
        'status' => 'Loading',
    ],

    '2025-10-18' => [
        'title' => 'Clarissa & Danis',
        'venue' => 'Ritz-Carlton Mega K',
        'type' => 'closed',
        'status' => 'Selesai',
        'overview' => true,
    ],

    '2025-10-22' => [
        'title' => 'Bunga Segar',
        'venue' => 'Tiba',
        'type' => 'loading',
        'status' => 'Persiapan',
    ],

    '2025-10-24' => [
        'title' => 'Loading In 23:00',
        'venue' => 'Ballroom Mulia',
        'type' => 'loading',
        'status' => 'STAGE 1',
    ],

    '2025-10-25' => [
        'title' => 'Aditya & Sarah',
        'venue' => 'Grand Ballroom Hotel Mulia',
        'type' => 'hari-h',
        'status' => 'HARI UTAMA',
        'overview' => true,
    ],

    '2025-10-26' => [
        'title' => 'Standby & Bongkaran',
        'venue' => 'Daniel & Fiora',
        'type' => 'closed',
        'status' => 'Selesai',
    ],

    '2025-10-31' => [
        'title' => 'Loading',
        'venue' => 'Blokdara',
        'type' => 'loading',
        'status' => 'Loading',
    ],
];

$firstDate = \Carbon\Carbon::create($tahun, $bulan, 1);

$daysInMonth = $firstDate->daysInMonth;

$startOffset = $firstDate->dayOfWeekIso - 1;

$previousMonthDays = $firstDate->copy()->subMonth()->daysInMonth;

$totalCells = (int) ceil(($startOffset + $daysInMonth) / 7) * 7;

$prev = $firstDate->copy()->subMonth();

$next = $firstDate->copy()->addMonth();

$selectedDate = request('tanggal');

$search = strtolower(trim(request('q', '')));

@endphp

<div class="vendor-calendar-page">

{{-- MODULE BAR --}}
<div class="calendar-module-bar">

    <div class="module-left">

        <div class="module-icon">
            ▣
        </div>

        <div>
            <div class="module-title">
                VENDOR WORKSPACE CALENDAR MODULE
            </div>

            <div class="module-subtitle">
                Sinkronisasi Jadwal Acara & Kolaborasi Lintas Vendor Resmi WO PROJECT
            </div>
        </div>

    </div>

    <div class="module-right">

        <span class="module-badge">
            ◉ Lihat Blueprint Desain Asli
        </span>

        <span class="module-live">
            Live Operational View
        </span>

    </div>

</div>


{{-- HEADER --}}
<section class="calendar-intro">

    <div>

        <div class="calendar-eyebrow">
            ● OPERASIONAL & PENJADWALAN MITRA
        </div>

        <h1>
            Kalender & Jadwal Pelaksanaan
            <br>
            Acara Vendor
        </h1>

        <p>
            Kelola jadwal booking, plotting kru instalasi dekorasi,
            dan detail kolaborasi lintas mitra rekanan WO PROJECT.
        </p>

    </div>


    <div class="calendar-controls">

        {{-- PILIH BULAN --}}
        <div class="month-picker">

            <a
                class="month-arrow"
                href="{{ route('vendor.kalender', [
                    'bulan' => $prev->month,
                    'tahun' => $prev->year
                ]) }}"
            >
                ‹
            </a>

            <form
                method="GET"
                action="{{ route('vendor.kalender') }}"
                class="month-form"
            >

                <span class="calendar-small-icon">
                    ▦
                </span>

                <select
                    name="bulan"
                    aria-label="Pilih bulan"
                >

                    @foreach ($namaBulan as $nomor => $nama)

                        <option
                            value="{{ $nomor }}"
                            @selected($nomor == $bulan)
                        >
                            {{ $nama }}
                        </option>

                    @endforeach

                </select>


                <select
                    name="tahun"
                    aria-label="Pilih tahun"
                >

                    @for ($y = 2024; $y <= 2035; $y++)

                        <option
                            value="{{ $y }}"
                            @selected($y == $tahun)
                        >
                            {{ $y }}
                        </option>

                    @endfor

                </select>


                <button
                    type="submit"
                    class="month-apply"
                >
                    Tampilkan
                </button>

            </form>


            <a
                class="month-arrow"
                href="{{ route('vendor.kalender', [
                    'bulan' => $next->month,
                    'tahun' => $next->year
                ]) }}"
            >
                ›
            </a>

        </div>


        {{-- VIEW BUTTON --}}
        <div class="calendar-view-buttons">

            <span class="view-active">
                ▦ Tampilan Kalender
            </span>

            <span>
                ☷ Agenda (List)
            </span>

        </div>


        {{-- GOOGLE CALENDAR --}}
        <a
            class="sync-button"
            href="#"
        >
            ↻ &nbsp; Sinkron ke Google Calendar
        </a>

    </div>

</section>


{{-- FILTER --}}
<section class="calendar-filter-card">

    <div class="filter-pills">

        <span class="filter-active">
            Semua Status {{ count($events) }}
        </span>

        <span>
            <i class="dot dot-hari-h"></i>
            Hari H Terkunci (5)
        </span>

        <span>
            <i class="dot dot-loading"></i>
            Draft Loading (2)
        </span>

        <span>
            <i class="dot dot-option"></i>
            Opsi Cadangan (1)
        </span>

    </div>


    {{-- SEARCH --}}
    <form
        method="GET"
        action="{{ route('vendor.kalender') }}"
        class="calendar-search"
    >

        <input
            type="hidden"
            name="bulan"
            value="{{ $bulan }}"
        >

        <input
            type="hidden"
            name="tahun"
            value="{{ $tahun }}"
        >

        <span>
            ⌕
        </span>

        <input
            type="text"
            name="q"
            value="{{ request('q') }}"
            placeholder="Cari nama klien pengantin, venue, atau rekanan..."
        >

    </form>

</section>


{{-- CALENDAR --}}
<section class="calendar-card">

    <div class="calendar-scroll">

        {{-- NAMA HARI --}}
        <div class="calendar-weekdays">

            @foreach ($namaHari as $hari)

                <div>
                    {{ $hari }}
                </div>

            @endforeach

        </div>


        {{-- GRID --}}
        <div class="calendar-grid">

            @for ($cell = 0; $cell < $totalCells; $cell++)

                @php

                    if ($cell < $startOffset) {

                        $dayNumber =
                            $previousMonthDays -
                            $startOffset +
                            $cell +
                            1;

                        $cellDate =
                            $prev->copy()->day($dayNumber);

                        $inMonth = false;

                    } elseif (
                        $cell >= $startOffset + $daysInMonth
                    ) {

                        $dayNumber =
                            $cell -
                            ($startOffset + $daysInMonth) +
                            1;

                        $cellDate =
                            $next->copy()->day($dayNumber);

                        $inMonth = false;

                    } else {

                        $dayNumber =
                            $cell -
                            $startOffset +
                            1;

                        $cellDate =
                            $firstDate->copy()->day($dayNumber);

                        $inMonth = true;
                    }


                    $dateKey = $cellDate->format('Y-m-d');

                    $event = $events[$dateKey] ?? null;

                    $isSelected =
                        $selectedDate === $dateKey;

                    $isToday =
                        $dateKey === now()->format('Y-m-d');

                    $eventText = '';

                    if ($event) {

                        $eventText = strtolower(
                            $event['title'] .
                            ' ' .
                            ($event['venue'] ?? '') .
                            ' ' .
                            ($event['status'] ?? '')
                        );
                    }

                    $showEvent =
                        $event &&
                        (
                            $search === '' ||
                            str_contains($eventText, $search)
                        );

                @endphp


                <div
                    class="
                        calendar-day
                        {{ !$inMonth ? 'outside' : '' }}
                        {{ $showEvent ? 'has-event' : '' }}
                        {{ $event && $event['type'] === 'hari-h' ? 'hari-h' : '' }}
                        {{ $isSelected ? 'selected-day' : '' }}
                    "
                >

                    @if (
                        $showEvent &&
                        !empty($event['overview']) &&
                        Route::has('vendor.overview')
                    )

                        <a
                            class="day-link"
                            href="{{ route('vendor.overview', [
                                'tanggal' => $dateKey
                            ]) }}"
                        >

                    @else

                        <a
                            class="day-link"
                            href="{{ route('vendor.kalender', [
                                'bulan' => $bulan,
                                'tahun' => $tahun,
                                'tanggal' => $dateKey
                            ]) }}"
                        >

                    @endif


                        {{-- NOMOR TANGGAL --}}
                        <div class="day-number">

                            <span>
                                {{ str_pad($dayNumber, 2, '0', STR_PAD_LEFT) }}
                            </span>


                            @if ($isToday)

                                <b class="today-dot"></b>

                            @elseif ($showEvent)

                                <b
                                    class="event-dot {{ $event['type'] }}"
                                ></b>

                            @endif

                        </div>


                        {{-- EVENT --}}
                        @if ($showEvent)

                            @if ($event['status'] === 'HARI UTAMA')

                                <span class="main-event-badge">
                                    HARI UTAMA
                                </span>

                            @endif


                            @if (
                                $event['type'] === 'loading' &&
                                $dateKey === '2025-10-24'
                            )

                                <span class="stage-badge">
                                    STAGE
                                    <br>
                                    1
                                </span>

                            @endif


                            <div class="calendar-event-title">
                                {{ $event['title'] }}
                            </div>


                            <div class="calendar-event-venue">
                                {{ $event['venue'] }}
                            </div>


                            @if ($event['status'] === 'Selesai')

                                <span class="done-badge">
                                    Selesai
                                </span>

                            @elseif ($event['type'] === 'hari-h')

                                <span class="done-badge dark">
                                    Aktif
                                </span>

                            @endif

                        @endif

                    </a>

                </div>

            @endfor

        </div>

    </div>


    {{-- LEGEND --}}
    <div class="calendar-legend">

        <span>
            <i class="legend-dot hari-h"></i>
            Hari H Berlangsung
        </span>

        <span>
            <i class="legend-dot loading"></i>
            Jadwal Loading In / Out
        </span>

        <span>
            <i class="legend-dot closed"></i>
            Selesai & Closed
        </span>

        <span>
            ⌁ Klik tanggal acara untuk membuka Overview
        </span>

    </div>

</section>


{{-- SELECTED DATE --}}
@if ($selectedDate)

    <div class="selected-date-banner">

        <div>

            <span class="selected-label">
                TANGGAL TERPILIH
            </span>

            <strong>
                {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}
            </strong>

        </div>


        <a
            href="{{ route('vendor.kalender', [
                'bulan' => $bulan,
                'tahun' => $tahun
            ]) }}"
        >
            Kembali ke Kalender
        </a>

    </div>

@else

    <div class="selected-date-banner">

        <div>

            <span class="selected-label">
                TANGGAL PILIHAN UTAMA
            </span>

            <strong>
                Sabtu, 25 Oktober 2025 • Acara Hari H
            </strong>

        </div>


        @if (Route::has('vendor.overview'))

            <a
                href="{{ route('vendor.overview', [
                    'tanggal' => '2025-10-25'
                ]) }}"
            >
                Buka Overview Acara →
            </a>

        @endif

    </div>

@endif
```

</div>

@endsection
