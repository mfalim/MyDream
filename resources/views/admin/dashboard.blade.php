@extends('layouts.admin')

@section('title', 'Dashboard Utama')
@section('page-title', 'Dashboard')

@push('styles')
    @vite('resources/css/admin/dashboard.css')
@endpush

@section('content')
<div class="dashboard-container">

    {{-- ==========================================================
       DASHBOARD HEADER & QUICK ACTIONS
       ========================================================== --}}
    <div class="dashboard-header">
        <div>
            <div class="eyebrow">
                <i class="bi bi-stars"></i>
                <span>EXECUTIVE SUMMARY &amp; OPERATIONAL OVERVIEW</span>
            </div>
            <h2>Dashboard Utama Wedding Organizer</h2>
            <p>Pantau performa bisnis, jadwal pernikahan klien, dan koordinasi internal tim dalam satu layar kendali.</p>
        </div>

        <div class="dashboard-actions">
            <a href="{{ route('admin.calendar') }}" class="btn-dash-outline">
                <i class="bi bi-calendar3"></i>
                <span>Master Kalender</span>
            </a>
            <a href="{{ route('admin.tracking.index') }}" class="btn-dash-outline">
                <i class="bi bi-journal-check"></i>
                <span>Tracking Booking</span>
            </a>
            <a href="{{ route('admin.organizer-event.create') }}" class="btn-dash-primary">
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Agenda Internal</span>
            </a>
        </div>
    </div>

    {{-- ==========================================================
       KEY METRICS (4 CARDS GRID)
       ========================================================== --}}
    <div class="stats-grid-4">
        {{-- CARD 1: REVENUE --}}
        <div class="stat-card-luxury">
            <div class="stat-top">
                <div class="stat-label">Total Omzet Booking</div>
                <div class="stat-icon-wrapper stat-icon-emerald">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
            <div class="stat-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <div class="stat-subtext">
                <span>Bulan Ini:</span>
                <span class="highlight">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- CARD 2: WEDDING EVENTS --}}
        <div class="stat-card-luxury">
            <div class="stat-top">
                <div class="stat-label">Pernikahan Klien (Hari H)</div>
                <div class="stat-icon-wrapper stat-icon-gold">
                    <i class="bi bi-heart-fill"></i>
                </div>
            </div>
            <div class="stat-value">{{ $totalEvents }} <span style="font-size: 15px; font-weight: 500;">Acara</span></div>
            <div class="stat-subtext">
                <span class="highlight">{{ $upcomingEventsCount }} Terjadwal</span>
                <span>&bull;</span>
                <span>{{ $completedEventsCount }} Selesai</span>
            </div>
        </div>

        {{-- CARD 3: BOOKINGS --}}
        <div class="stat-card-luxury">
            <div class="stat-top">
                <div class="stat-label">Total Booking Klien</div>
                <div class="stat-icon-wrapper stat-icon-blue">
                    <i class="bi bi-journal-bookmark-fill"></i>
                </div>
            </div>
            <div class="stat-value">{{ $totalBookings }} <span style="font-size: 15px; font-weight: 500;">Booking</span></div>
            <div class="stat-subtext">
                <span class="highlight text-success">{{ $approvedBookings }} Disetujui</span>
                <span>&bull;</span>
                <span class="text-warning">{{ $pendingBookings }} Menunggu</span>
            </div>
        </div>

        {{-- CARD 4: INTERNAL ORGANIZER & KRU --}}
        <div class="stat-card-luxury">
            <div class="stat-top">
                <div class="stat-label">Agenda Internal &amp; Kru</div>
                <div class="stat-icon-wrapper stat-icon-purple">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
            <div class="stat-value">{{ $totalOrganizerEvents }} <span style="font-size: 15px; font-weight: 500;">Agenda</span></div>
            <div class="stat-subtext">
                <span class="highlight">{{ $totalMembers }} Kru Aktif</span>
                <span>&bull;</span>
                <span>{{ $totalVendors }} Mitra Vendor</span>
            </div>
        </div>
    </div>

    {{-- ==========================================================
       INTERACTIVE CHARTS (ROW 2: AREA CHART & DONUT CHART)
       ========================================================== --}}
    <div class="row g-3 mb-4">
        {{-- CHART 1: TREN PENDAPATAN & BOOKING --}}
        <div class="col-lg-7">
            <div class="chart-card">
                <div class="chart-header">
                    <div>
                        <h4 class="chart-title">
                            <i class="bi bi-graph-up-arrow"></i>
                            <span>Tren Pendapatan &amp; Volume Booking</span>
                        </h4>
                        <div class="chart-subtitle">Ringkasan performa 6 bulan terakhir dalam juta rupiah</div>
                    </div>
                </div>
                <div id="revenueBookingChart" style="min-height: 300px;"></div>
            </div>
        </div>

        {{-- CHART 2: DISTRIBUSI STATUS OPERASIONAL --}}
        <div class="col-lg-5">
            <div class="chart-card">
                <div class="chart-header">
                    <div>
                        <h4 class="chart-title">
                            <i class="bi bi-pie-chart-fill"></i>
                            <span>Distribusi Operasional</span>
                        </h4>
                        <div class="chart-subtitle">Komposisi status booking &amp; event pernikahan</div>
                    </div>
                </div>
                <div id="statusDistributionChart" style="min-height: 300px;"></div>
            </div>
        </div>
    </div>

    {{-- ==========================================================
       UPCOMING TIMELINE (HARI H PERNIKAHAN & AGENDA INTERNAL)
       ========================================================== --}}
    <div class="row g-3 mb-4">
        {{-- UPCOMING CLIENT EVENTS --}}
        <div class="col-lg-6">
            <div class="schedule-card">
                <div class="chart-header">
                    <div>
                        <h4 class="chart-title">
                            <i class="bi bi-calendar2-heart"></i>
                            <span>Pernikahan Klien Terdekat</span>
                        </h4>
                        <div class="chart-subtitle">Jadwal hari H pernikahan klien MyDream</div>
                    </div>
                    <a href="{{ route('admin.events.index') }}" class="btn-dash-outline py-1 px-3" style="font-size: 12px;">
                        <span>Semua Event</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <div class="schedule-list">
                    @forelse($upcomingClientEvents as $ev)
                        <div class="schedule-item client-event">
                            <div class="schedule-date-badge">
                                <span class="day">{{ $ev->event_date ? $ev->event_date->format('d') : '--' }}</span>
                                <span class="month">{{ $ev->event_date ? $ev->event_date->format('M') : '' }}</span>
                            </div>
                            <div class="schedule-info">
                                <div class="schedule-title">
                                    {{ $ev->name ?: ($ev->booking?->client ? 'Pernikahan ' . $ev->booking->client->groom_name . ' & ' . $ev->booking->client->bride_name : 'Event Pernikahan') }}
                                </div>
                                <div class="schedule-meta">
                                    <div class="schedule-meta-item">
                                        <i class="bi bi-geo-alt"></i>
                                        <span>{{ $ev->booking?->venue_name ?: 'Venue belum diatur' }}</span>
                                    </div>
                                    @if($ev->package)
                                        <div class="schedule-meta-item">
                                            <i class="bi bi-box-seam"></i>
                                            <span>{{ $ev->package->name }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('admin.events.show', $ev->id) }}" class="btn btn-sm btn-light border" title="Buka Detail">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                            <span>Belum ada jadwal acara pernikahan mendatang</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- UPCOMING INTERNAL ORGANIZER EVENTS --}}
        <div class="col-lg-6">
            <div class="schedule-card">
                <div class="chart-header">
                    <div>
                        <h4 class="chart-title">
                            <i class="bi bi-clock-history"></i>
                            <span>Agenda Internal WO Terdekat</span>
                        </h4>
                        <div class="chart-subtitle">Briefing, technical meeting, dan koordinasi kru</div>
                    </div>
                    <a href="{{ route('admin.organizer-event.index') }}" class="btn-dash-outline py-1 px-3" style="font-size: 12px;">
                        <span>Semua Agenda</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <div class="schedule-list">
                    @forelse($upcomingOrganizerEvents as $orgEv)
                        <div class="schedule-item internal-event">
                            <div class="schedule-date-badge" style="background: var(--wo-emerald-800);">
                                <span class="day">{{ $orgEv->event_date ? $orgEv->event_date->format('d') : '--' }}</span>
                                <span class="month">{{ $orgEv->event_date ? $orgEv->event_date->format('M') : '' }}</span>
                            </div>
                            <div class="schedule-info">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="schedule-title mb-0">{{ $orgEv->title }}</span>
                                    @if($orgEv->priority === 'high')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 10px;">Prioritas Tinggi</span>
                                    @endif
                                </div>
                                <div class="schedule-meta">
                                    <div class="schedule-meta-item">
                                        <i class="bi bi-clock"></i>
                                        <span>{{ $orgEv->start_time ? substr($orgEv->start_time, 0, 5) : 'Waktu menyusul' }} WIB</span>
                                    </div>
                                    <div class="schedule-meta-item">
                                        <i class="bi bi-geo-alt"></i>
                                        <span>{{ $orgEv->location ?: 'Kantor / Lokasi WO' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('admin.organizer-event.show', $orgEv->id) }}" class="btn btn-sm btn-light border" title="Lihat Agenda">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-calendar-check fs-3 d-block mb-2"></i>
                            <span>Tidak ada agenda internal terdekat</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ==========================================================
       RECENT BOOKING TRANSACTIONS TABLE
       ========================================================== --}}
    <div class="table-card-luxury">
        <div class="table-card-header">
            <div>
                <h4 class="chart-title mb-1">
                    <i class="bi bi-receipt-cutoff"></i>
                    <span>Booking &amp; Transaksi Terbaru</span>
                </h4>
                <div class="chart-subtitle">Daftar pemesanan paket pernikahan terkini dari klien</div>
            </div>
            <a href="{{ route('admin.tracking.index') }}" class="btn-dash-outline py-1 px-3" style="font-size: 12px;">
                <span>Buka Tracking Booking</span>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-luxury align-middle">
                <thead>
                    <tr>
                        <th>Invoice / ID</th>
                        <th>Calon Pengantin</th>
                        <th>Paket Wedding</th>
                        <th>Venue &amp; Kota</th>
                        <th>Nilai Transaksi</th>
                        <th>Status Booking</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentBookings as $b)
                        <tr>
                            <td>
                                <strong class="text-dark">#BK-{{ str_pad($b->id, 4, '0', STR_PAD_LEFT) }}</strong>
                                <small class="text-muted d-block" style="font-size: 11px;">{{ $b->created_at ? $b->created_at->diffForHumans() : '' }}</small>
                            </td>
                            <td>
                                @if($b->client)
                                    <strong>{{ $b->client->groom_name }} &amp; {{ $b->client->bride_name }}</strong>
                                    <small class="text-muted d-block" style="font-size: 11px;">{{ $b->client->phone ?? $b->client->groom_phone ?? '-' }}</small>
                                @else
                                    <span class="text-muted">Klien Belum Terdaftar</span>
                                @endif
                            </td>
                            <td>
                                @if($b->package)
                                    <span class="badge bg-light text-dark border">{{ $b->package->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $b->venue_name ?: 'Venue belum diisi' }}</div>
                                @if($b->venue_city)
                                    <small class="text-muted" style="font-size: 11px;">{{ $b->venue_city }}</small>
                                @endif
                            </td>
                            <td>
                                <strong class="text-dark">Rp {{ number_format($b->total_price, 0, ',', '.') }}</strong>
                            </td>
                            <td>
                                @if($b->status === 'approved')
                                    <span class="badge-status badge-approved">
                                        <i class="bi bi-check-circle-fill"></i> Disetujui
                                    </span>
                                @elseif($b->status === 'pending')
                                    <span class="badge-status badge-pending">
                                        <i class="bi bi-hourglass-split"></i> Menunggu
                                    </span>
                                @else
                                    <span class="badge-status badge-rejected">
                                        <i class="bi bi-x-circle-fill"></i> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.tracking.index', ['booking_id' => $b->id]) }}" class="btn btn-sm btn-light border text-muted" title="Buka Detail">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Tracking
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                <span>Belum ada data booking yang masuk</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. CHART TREN PENDAPATAN & BOOKING
    const monthsData = @json($months);
    const revenueData = @json($revenueData);
    const bookingCountData = @json($bookingCountData);

    const revenueOptions = {
        series: [
            {
                name: 'Pendapatan (Juta Rp)',
                type: 'area',
                data: revenueData
            },
            {
                name: 'Volume Booking',
                type: 'column',
                data: bookingCountData
            }
        ],
        chart: {
            height: 310,
            type: 'line',
            toolbar: { show: false },
            fontFamily: 'Plus Jakarta Sans, sans-serif'
        },
        stroke: {
            curve: 'smooth',
            width: [3, 0]
        },
        fill: {
            type: ['gradient', 'solid'],
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [0, 90, 100]
            }
        },
        colors: ['#073d36', '#dfc28b'],
        labels: monthsData,
        xaxis: {
            type: 'category',
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                style: { colors: '#7a948e', fontSize: '11px' }
            }
        },
        yaxis: [
            {
                title: { text: 'Juta Rupiah', style: { color: '#073d36', fontSize: '11px', fontWeight: 600 } },
                labels: {
                    formatter: function (val) { return 'Rp ' + val + 'M'; },
                    style: { colors: '#7a948e', fontSize: '11px' }
                }
            },
            {
                opposite: true,
                title: { text: 'Jumlah Booking', style: { color: '#dfc28b', fontSize: '11px', fontWeight: 600 } },
                labels: {
                    formatter: function (val) { return Math.round(val); },
                    style: { colors: '#7a948e', fontSize: '11px' }
                }
            }
        ],
        grid: {
            borderColor: '#eef3f0',
            strokeDashArray: 4
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            fontSize: '12px',
            markers: { radius: 12 }
        },
        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: function (val, opts) {
                    if (opts.seriesIndex === 0) {
                        return 'Rp ' + (val * 1000000).toLocaleString('id-ID');
                    }
                    return val + ' Pesanan';
                }
            }
        }
    };

    const chartRevenue = new ApexCharts(document.querySelector("#revenueBookingChart"), revenueOptions);
    chartRevenue.render();


    // 2. CHART DISTRIBUSI STATUS OPERASIONAL (DONUT)
    const donutLabels = @json($statusDonutLabels);
    const donutSeries = @json($statusDonutSeries);

    // Filter jika semua bernilai 0, berikan placeholder
    const totalVal = donutSeries.reduce((a, b) => a + b, 0);
    const safeSeries = totalVal === 0 ? [1, 1, 1, 1] : donutSeries;

    const donutOptions = {
        series: safeSeries,
        labels: donutLabels,
        chart: {
            type: 'donut',
            height: 310,
            fontFamily: 'Plus Jakarta Sans, sans-serif'
        },
        colors: ['#073d36', '#dfc28b', '#2b6e62', '#e5dfd5'],
        plotOptions: {
            pie: {
                donut: {
                    size: '68%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total Aktivitas',
                            fontSize: '11px',
                            fontWeight: 600,
                            color: '#7a948e',
                            formatter: function () {
                                return totalVal;
                            }
                        },
                        value: {
                            fontSize: '22px',
                            fontWeight: 700,
                            fontFamily: 'Plus Jakarta Sans, sans-serif',
                            color: '#16302a',
                            formatter: function (val) { return val; }
                        }
                    }
                }
            }
        },
        dataLabels: { enabled: false },
        legend: {
            position: 'bottom',
            fontSize: '11.5px',
            markers: { radius: 10 }
        },
        stroke: { width: 2, colors: ['#ffffff'] },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + ' Data';
                }
            }
        }
    };

    const chartDonut = new ApexCharts(document.querySelector("#statusDistributionChart"), donutOptions);
    chartDonut.render();
});
</script>
@endpush
