@extends('layouts.admin')

@section('title', 'Monitoring & Tracking Acara')
@section('page-title', 'Monitoring & Tracking Acara')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="display-6 fw-bold mb-2">Monitoring & Tracking Acara Klien</h1>
            <p class="text-muted mb-0">Pantau kemajuan persiapan dan pelaksanaan acara pernikahan</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari nama pengantin atau acara..." id="searchInput">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <select class="form-select" id="statusFilter">
                <option value="">Semua Status</option>
                <option value="scheduled">Scheduled</option>
                <option value="ongoing">Ongoing</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="fw-bold mb-0">{{ $events->count() }}</h3>
                    <p class="text-muted mb-0 small">Total Acara</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="fw-bold mb-0 text-warning">{{ $events->where('status', 'scheduled')->count() }}</h3>
                    <p class="text-muted mb-0 small">Scheduled</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="fw-bold mb-0 text-primary">{{ $events->where('status', 'ongoing')->count() }}</h3>
                    <p class="text-muted mb-0 small">Ongoing</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="fw-bold mb-0 text-success">{{ $events->where('status', 'completed')->count() }}</h3>
                    <p class="text-muted mb-0 small">Completed</p>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID Acara</th>
                    <th>Nama Acara</th>
                    <th>Tanggal</th>
                    <th>Lokasi Venue</th>
                    <th>Status Persiapan</th>
                    <th>Kesukaan Checklist</th>
                    <th>PIC Team Kami</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="eventTable">
                @forelse($events as $event)
                @php
                    $client = $event->booking?->client;
                    $groomName = $client?->groom_name ?? 'N/A';
                    $brideName = $client?->bride_name ?? 'N/A';
                    $venueName = $event->booking?->venue_name ?? 'N/A';
                    $venueCity = $event->booking?->venue_city ?? '';
                    $leadMember = $event->eventMembers->first()?->member;
                    
                    $statusClass = match($event->status) {
                        'scheduled' => 'secondary',
                        'ongoing' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'secondary'
                    };
                    
                    $progressClass = match(true) {
                        $event->progress >= 75 => 'success',
                        $event->progress >= 50 => 'info',
                        $event->progress >= 25 => 'warning',
                        default => 'danger'
                    };
                @endphp
                <tr data-search="{{ strtolower($event->name . ' ' . $groomName . ' ' . $brideName) }}" data-status="{{ $event->status }}">
                    <td><strong>#{{ $event->id }}</strong></td>
                    <td>
                        <div>
                            <strong>{{ $event->name }}</strong>
                            <br>
                            <small class="text-muted">{{ $groomName }} & {{ $brideName }}</small>
                        </div>
                    </td>
                    <td>{{ $event->event_date->format('d M Y') }}</td>
                    <td>
                        <div>
                            {{ $venueName }}
                            @if($venueCity)
                            <br><small class="text-muted">{{ $venueCity }}</small>
                            @endif
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-{{ $statusClass }}">{{ ucfirst($event->status) }}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height: 20px; min-width: 100px;">
                                <div class="progress-bar bg-{{ $progressClass }}" role="progressbar" style="width: {{ $event->progress }}%">
                                    {{ round($event->progress) }}%
                                </div>
                            </div>
                            <small class="text-muted">{{ $event->schedules->where('status', 'completed')->count() }}/{{ $event->schedules->count() }}</small>
                        </div>
                    </td>
                    <td>
                        @if($leadMember)
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 12px;">
                                {{ strtoupper(substr($leadMember->name, 0, 2)) }}
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $leadMember->name }}</div>
                                <small class="text-muted">{{ $leadMember->call_sign }}</small>
                            </div>
                        </div>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.tracking.show', $event->id) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <p class="text-muted mb-0">Belum ada data acara</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const rows = document.querySelectorAll('#eventTable tr[data-search]');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedStatus = statusFilter.value;

        rows.forEach(row => {
            const searchText = row.getAttribute('data-search');
            const rowStatus = row.getAttribute('data-status');
            
            const matchesSearch = searchText.includes(searchTerm);
            const matchesStatus = !selectedStatus || rowStatus === selectedStatus;
            
            row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);
});
</script>
@endsection
