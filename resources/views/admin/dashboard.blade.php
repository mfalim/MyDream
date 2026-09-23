@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')

    <div class="mb-4">
        <h3 class="mb-1">Dashboard</h3>
        <p class="text-muted">
            Selamat datang di halaman administrasi Wedding Organizer.
        </p>
    </div>

    <!-- Statistik Utama -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="bi bi-box-seam fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small">Paket Wedding</p>
                            <h3 class="mb-0">{{ $totalPackages }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="bi bi-shop fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small">Vendor</p>
                            <h3 class="mb-0">{{ $totalVendors }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="bi bi-calendar-check fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small">Total Booking</p>
                            <h3 class="mb-0">{{ $totalBookings }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="bi bi-heart-fill fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small">Total Event</p>
                            <h3 class="mb-0">{{ $totalEvents }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Booking & Event -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">Status Booking</h5>
                    <div class="row g-3">
                        <div class="col-4 text-center">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-warning mb-1">{{ $pendingBookings }}</h4>
                                <small class="text-muted">Pending</small>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-success mb-1">{{ $approvedBookings }}</h4>
                                <small class="text-muted">Approved</small>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-danger mb-1">{{ $rejectedBookings }}</h4>
                                <small class="text-muted">Rejected</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">Status Event</h5>
                    <div class="row g-3">
                        <div class="col-4 text-center">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-primary mb-1">{{ $scheduledEvents }}</h4>
                                <small class="text-muted">Scheduled</small>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-info mb-1">{{ $ongoingEvents }}</h4>
                                <small class="text-muted">Ongoing</small>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-success mb-1">{{ $completedEvents }}</h4>
                                <small class="text-muted">Completed</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pendapatan -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-cash-stack text-success fs-4 me-2"></i>
                        <h5 class="card-title mb-0">Total Pendapatan</h5>
                    </div>
                    <h2 class="text-success mb-0">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h2>
                    <small class="text-muted">Dari booking yang approved</small>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-calendar-month text-info fs-4 me-2"></i>
                        <h5 class="card-title mb-0">Pendapatan Bulan Ini</h5>
                    </div>
                    <h2 class="text-info mb-0">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</h2>
                    <small class="text-muted">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tim & Client -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="bi bi-people-fill fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small">Tim Organizer Aktif</p>
                            <h3 class="mb-0">{{ $totalMembers }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="bi bi-person-hearts fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small">Total Client</p>
                            <h3 class="mb-0">{{ $totalClients }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Mendatang & Booking Terbaru -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Event Mendatang (7 Hari)</h5>
                        <a href="{{ route('admin.event_day.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                    @forelse($upcomingEvents as $event)
                        <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                            <div class="flex-shrink-0 me-3">
                                <div class="bg-primary text-white rounded text-center" style="width: 50px; padding: 8px;">
                                    <div class="fw-bold">{{ $event->event_date->format('d') }}</div>
                                    <small style="font-size: 0.7rem;">{{ $event->event_date->format('M') }}</small>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $event->name }}</h6>
                                <small class="text-muted d-block">{{ $event->booking->client->groom_name ?? 'N/A' }} & {{ $event->booking->client->bride_name ?? 'N/A' }}</small>
                                <small class="text-muted">{{ $event->schedules->count() }} vendor</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">Tidak ada event mendatang</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Booking Terbaru</h5>
                        <a href="{{ route('admin.event_day.index') }}" class="btn btn-sm btn-outline-success">Lihat Semua</a>
                    </div>
                    @forelse($recentBookings as $booking)
                        <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                            <div class="flex-shrink-0 me-3">
                                @if($booking->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($booking->status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $booking->client->groom_name ?? 'N/A' }} & {{ $booking->client->bride_name ?? 'N/A' }}</h6>
                                <small class="text-muted d-block">{{ $booking->venue_name }}</small>
                                <small class="text-muted">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</small>
                            </div>
                            <div class="flex-shrink-0 text-end">
                                <small class="text-muted">{{ $booking->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">Belum ada booking</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

@endsection
