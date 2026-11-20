@extends('vendor.layouts.app')

@section('title', 'Dashboard Vendor')

@section('content')
    <div class="page-header">
        <div>
            <span class="eyebrow">RUANG KERJA VENDOR</span>
            <h1>Selamat datang, {{ $vendor->name }}</h1>
            <p>Pantau acara dan tugas yang ditugaskan kepada vendor Anda.</p>
        </div>

        <div class="header-actions">
            <a class="btn-light" href="{{ route('vendor.kalender') }}">Lihat Kalender</a>
            <a class="btn-primary" href="{{ route('vendor.profil') }}">Kelola Profil</a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <span>ACARA HARI INI</span>
            <h2>{{ $acaraHariIni }}</h2>
            <p>Acara yang memiliki tugas untuk vendor Anda.</p>
        </div>

        <div class="stat-card">
            <span>ACARA BULAN INI</span>
            <h2>{{ $acaraBulanIni }}</h2>
            <p>Acara terjadwal pada bulan berjalan.</p>
        </div>

        <div class="stat-card">
            <span>TOTAL TUGAS</span>
            <h2>{{ $totalTugas }}</h2>
            <p>Seluruh tugas yang tercatat untuk vendor Anda.</p>
        </div>

        <div class="stat-card">
            <span>STATUS TUGAS</span>
            <div class="mini-stats">
                <div>
                    <strong>{{ $disetujui }}</strong>
                    <small>DISETUJUI</small>
                </div>
                <div>
                    <strong>{{ $menunggu }}</strong>
                    <small>MENUNGGU</small>
                </div>
                <div>
                    <strong>{{ $ditolak }}</strong>
                    <small>DITOLAK</small>
                </div>
            </div>
        </div>
    </div>

    <section class="panel pipeline-panel">
        <div class="panel-header">
            <div>
                <span class="eyebrow">JADWAL VENDOR</span>
                <h2>Acara dan Tugas Mendatang</h2>
            </div>
            <a href="{{ route('vendor.tracking') }}">Lihat semua tugas</a>
        </div>

        <div class="table-wrapper">
            <table class="vendor-table">
                <thead>
                    <tr>
                        <th>ACARA</th>
                        <th>TANGGAL</th>
                        <th>KEGIATAN</th>
                        <th>LOKASI</th>
                        <th>WAKTU</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jadwalMendatang as $jadwal)
                        <tr>
                            <td><strong>{{ $jadwal->event_name }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($jadwal->event_date)->translatedFormat('d M Y') }}</td>
                            <td>{{ $jadwal->activity }}</td>
                            <td>{{ $jadwal->location ?: '-' }}</td>
                            <td>
                                {{ $jadwal->start_time ? substr($jadwal->start_time, 0, 5) : '-' }}
                                @if ($jadwal->end_time)
                                    - {{ substr($jadwal->end_time, 0, 5) }}
                                @endif
                            </td>
                            <td>
                                {{ match ($jadwal->status) {
                                    'approved' => 'Disetujui',
                                    'pending' => 'Menunggu',
                                    'rejected' => 'Ditolak',
                                    default => 'Belum ditentukan',
                                } }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                Belum ada jadwal tugas untuk vendor Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
