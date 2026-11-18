@extends('vendor.layouts.app')

@section('title', 'Kalender & Jadwal Acara')

@section('content')

<style>
.calendar-page {
    padding: 0;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 25px;
}

.calendar-header h1 {
    margin: 5px 0;
}

.calendar-filter {
    display: flex;
    gap: 8px;
    align-items: center;
}

.calendar-filter select,
.calendar-filter button {
    padding: 9px 12px;
    border: 1px solid #ddd;
    border-radius: 7px;
}

.calendar-filter button {
    background: #344b78;
    color: white;
    cursor: pointer;
}

.calendar-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

.calendar-table th,
.calendar-table td {
    border: 1px solid #eee;
    padding: 10px;
    vertical-align: top;
    width: 14.28%;
}

.calendar-table th {
    background: #f5f6fa;
    text-align: center;
}

.calendar-day {
    min-height: 90px;
}

.calendar-day a {
    text-decoration: none;
    color: #344b78;
    font-weight: bold;
}

.calendar-day.outside {
    background: #fafafa;
    color: #aaa;
}

.calendar-event {
    display: block;
    background: #e8edf8;
    color: #344b78;
    padding: 5px;
    margin-top: 6px;
    border-radius: 5px;
    font-size: 12px;
    text-decoration: none;
}

.event-detail {
    background: white;
    padding: 20px;
    margin-top: 25px;
    border-radius: 10px;
}

@media(max-width: 700px) {
    .calendar-table th,
    .calendar-table td {
        padding: 4px;
        font-size: 11px;
    }

    .calendar-day {
        min-height: 65px;
    }

    .calendar-event {
        font-size: 9px;
        padding: 3px;
    }
}
</style>

<div class="calendar-page">

    <div class="calendar-header">
        <div>
            <span>VENDOR WORKSPACE</span>
            <h1>Kalender & Jadwal Acara</h1>
            <p>Jadwal kegiatan acara yang tersimpan di database.</p>
        </div>

        <form action="{{ route('vendor.kalender') }}"
              method="GET"
              class="calendar-filter">

            <select name="bulan">
                @foreach($namaBulan as $nomor => $nama)
                    <option value="{{ $nomor }}"
                        {{ $bulan == $nomor ? 'selected' : '' }}>
                        {{ $nama }}
                    </option>
                @endforeach
            </select>

            <select name="tahun">
                @for($y = now()->year - 5; $y <= now()->year + 5; $y++)
                    <option value="{{ $y }}"
                        {{ $tahun == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>

            <button type="submit">Tampilkan</button>
        </form>
    </div>

    <div style="overflow-x:auto;">
        <table class="calendar-table">
            <thead>
                <tr>
                    @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $hari)
                        <th>{{ $hari }}</th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @foreach($grid as $minggu)
                    <tr>
                        @foreach($minggu as $tanggal)
                            <td>
                                <div class="calendar-day
                                    {{ !$tanggal['in_month'] ? 'outside' : '' }}">

                                    <a href="{{ route('vendor.kalender', [
                                        'bulan' => $bulan,
                                        'tahun' => $tahun,
                                        'tanggal' => $tanggal['date']
                                    ]) }}">
                                        {{ $tanggal['day'] }}
                                    </a>

                                    @foreach(($events[$tanggal['date']] ?? []) as $event)
                                        <a class="calendar-event"
                                           href="{{ route('vendor.kalender', [
                                               'bulan' => $bulan,
                                               'tahun' => $tahun,
                                               'tanggal' => $tanggal['date']
                                           ]) }}">

                                            {{ $event->name }}

                                            @if($event->start_time)
                                                <br>
                                                {{ substr($event->start_time, 0, 5) }}
                                            @endif
                                        </a>
                                    @endforeach

                                </div>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="event-detail">
        <h2>Detail Acara</h2>
        <p>
            Tanggal:
            {{ $tanggalObj->translatedFormat('d F Y') }}
        </p>

        @forelse($detailAcara as $event)
            <div style="border-bottom:1px solid #eee;padding:12px 0;">

                <h3>{{ $event->name }}</h3>

                <p>
                    Jenis Acara: {{ $event->event_type ?? '-' }}
                </p>

                <p>
                    Jumlah Tamu: {{ $event->guest_count ?? '-' }}
                </p>

                <p>
                    Waktu:
                    {{ $event->start_time ?? '-' }}
                    -
                    {{ $event->end_time ?? '-' }}
                </p>

                <p>
                    Status: {{ $event->status ?? '-' }}
                </p>

                <p>
                    Catatan: {{ $event->notes ?? '-' }}
                </p>

            </div>
        @empty
            <p>Belum ada acara pada tanggal ini.</p>
        @endforelse
    </div>

</div>

@endsection
