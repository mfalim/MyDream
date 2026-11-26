@extends('vendor.layouts.app')

@section('title', 'Dashboard Vendor')

@section('content')
    <section class="vendor-hero-banner">
        <div class="vendor-hero-copy">
            <span class="wo-eyebrow"><i class="bi bi-circle-fill"></i> Ruang Kerja Vendor</span>
            <h1>Selamat datang, {{ $vendor->name }}</h1>
            <p>Pantau jadwal acara, status tugas, dan kas pencairan dana untuk vendor Anda.</p>
        </div>

        <div class="vendor-hero-actions">
            <a class="wo-btn wo-btn-outline vendor-hero-btn-light" href="{{ route('vendor.kalender') }}">
                <i class="bi bi-calendar3"></i> Kalender
            </a>
            <a class="wo-btn wo-btn-dark vendor-hero-btn-gold" href="{{ route('vendor.kas') }}">
                <i class="bi bi-wallet2"></i> Kas Masuk
            </a>
        </div>
    </section>

    <section class="wo-card mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <span class="wo-eyebrow">Status Pemesanan</span>
            <h2 class="h5 mb-1">
                Sesi {{ $vendor->is_open ? 'sedang dibuka' : 'sedang ditutup' }}
            </h2>
            <p class="mb-0 text-muted">
                {{ $vendor->is_open
                    ? 'Vendor Anda dan paket yang memuat vendor Anda dapat dipesan.'
                    : 'Vendor Anda dan paket yang memuat vendor Anda tidak akan tampil sebagai tersedia untuk dipesan.' }}
            </p>
        </div>
        <form action="{{ route('vendor.availability.update') }}" method="POST">
            @csrf
            @method('PATCH')
            <input type="hidden" name="is_open" value="{{ $vendor->is_open ? 0 : 1 }}">
            <button type="submit" class="wo-btn {{ $vendor->is_open ? 'wo-btn-outline' : 'wo-btn-dark' }}">
                <i class="bi bi-{{ $vendor->is_open ? 'pause-circle' : 'play-circle' }}"></i>
                {{ $vendor->is_open ? 'Tutup Sesi' : 'Buka Sesi' }}
            </button>
        </form>
    </section>

    <div class="stats-grid">
        <div class="wo-card stat-card">
            <span class="vendor-kpi-label">Acara Hari Ini</span>
            <h2>{{ $acaraHariIni }}</h2>
            <p>Acara yang memiliki tugas untuk vendor Anda hari ini.</p>
        </div>

        <div class="wo-card stat-card">
            <span class="vendor-kpi-label">Acara Bulan Ini</span>
            <h2>{{ $acaraBulanIni }}</h2>
            <p>Acara terjadwal pada bulan berjalan.</p>
        </div>

        <div class="wo-card stat-card">
            <span class="vendor-kpi-label">Total Kas Masuk</span>
            <h2 class="stat-money">Rp {{ number_format($totalKasMasuk ?? 0, 0, ',', '.') }}</h2>
            <p>
                Sisa belum cair: <strong>Rp {{ number_format($sisaPiutang ?? 0, 0, ',', '.') }}</strong>
                · <a href="{{ route('vendor.kas') }}" class="wo-link" style="font-size:0.78rem;">Detail</a>
            </p>
        </div>

        <div class="wo-card stat-card">
            <span class="vendor-kpi-label">Status Tugas ({{ $totalTugas }})</span>
            <div class="mini-stats">
                <div>
                    <strong>{{ $disetujui }}</strong>
                    <small>Disetujui</small>
                </div>
                <div>
                    <strong>{{ $menunggu }}</strong>
                    <small>Menunggu</small>
                </div>
                <div>
                    <strong>{{ $ditolak }}</strong>
                    <small>Ditolak</small>
                </div>
            </div>
        </div>
    </div>

    <section class="wo-card pipeline-panel">
        <div class="wo-section-head">
            <div>
                <span class="wo-eyebrow">Jadwal Vendor</span>
                <h2>Acara dan Tugas Mendatang</h2>
            </div>
            <a href="{{ route('vendor.tracking') }}" class="wo-link">Lihat semua tugas <i class="bi bi-arrow-right"></i></a>
        </div>

        <div class="table-wrapper">
            <table class="wo-table vendor-table">
                <thead>
                    <tr>
                        <th>Acara</th>
                        <th>Tanggal</th>
                        <th>Kegiatan</th>
                        <th>Lokasi</th>
                        <th>Waktu</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jadwalMendatang as $jadwal)
                        @php
                            $stClass = match ($jadwal->status) {
                                'approved' => 'wo-badge-green',
                                'pending' => 'wo-badge-gold',
                                'rejected' => 'wo-badge-red',
                                default => 'wo-badge-neutral',
                            };
                            $stLabel = match ($jadwal->status) {
                                'approved' => 'Disetujui',
                                'pending' => 'Menunggu',
                                'rejected' => 'Ditolak',
                                default => 'Belum ditentukan',
                            };
                        @endphp
                        <tr>
                            <td><strong style="color: var(--wo-maroon);">{{ $jadwal->event_name }}</strong></td>
                            <td class="text-nowrap">{{ \Carbon\Carbon::parse($jadwal->event_date)->locale('id')->translatedFormat('d M Y') }}</td>
                            <td>{{ $jadwal->activity }}</td>
                            <td>{{ $jadwal->location ?: '-' }}</td>
                            <td class="text-nowrap">
                                {{ $jadwal->start_time ? substr($jadwal->start_time, 0, 5) : '-' }}
                                @if ($jadwal->end_time)
                                    – {{ substr($jadwal->end_time, 0, 5) }}
                                @endif
                            </td>
                            <td><span class="wo-badge {{ $stClass }}">{{ $stLabel }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Belum ada jadwal tugas untuk vendor Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
