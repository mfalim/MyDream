@extends('layouts.admin')

@section('title', 'Detail Tracking - ' . $event->name)
@section('page-title', 'Detail Tracking Acara')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.tracking.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <h1 class="display-6 fw-bold mb-2">{{ $event->name }}</h1>
            <p class="text-muted mb-0">Detail progress dan checklist acara</p>
        </div>
        <div>
            <a href="{{ route('admin.event.edit', $event->id) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Edit Acara
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">Informasi Acara</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small mb-1">Nama Pengantin</label>
                            <div class="fw-semibold">
                                {{ $event->booking?->client?->groom_name ?? 'N/A' }} & {{ $event->booking?->client?->bride_name ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small mb-1">Tanggal Acara</label>
                            <div class="fw-semibold">{{ $event->event_date->format('d F Y') }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small mb-1">Waktu</label>
                            <div class="fw-semibold">
                                @if($event->start_time && $event->end_time)
                                    {{ $event->start_time }} - {{ $event->end_time }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small mb-1">Jumlah Tamu</label>
                            <div class="fw-semibold">{{ $event->guest_count ?? '-' }} orang</div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="text-muted small mb-1">Venue</label>
                            <div class="fw-semibold">
                                {{ $event->booking?->venue_name ?? 'N/A' }}
                                @if($event->booking?->venue_address)
                                    <br><small class="text-muted">{{ $event->booking->venue_address }}</small>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Status Acara</label>
                            <div>
                                @php
                                    $statusClass = match($event->status) {
                                        'scheduled' => 'secondary',
                                        'ongoing' => 'warning',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">{{ ucfirst($event->status) }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Paket</label>
                            <div class="fw-semibold">{{ $event->package?->name ?? 'Custom' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Progress Keseluruhan</h6>
                    <div class="text-center mb-3">
                        <h2 class="display-4 fw-bold mb-0">{{ round($progress) }}%</h2>
                        <p class="text-muted small">Checklist Selesai</p>
                    </div>
                    <div class="progress mb-3" style="height: 25px;">
                        @php
                            $progressClass = match(true) {
                                $progress >= 75 => 'success',
                                $progress >= 50 => 'info',
                                $progress >= 25 => 'warning',
                                default => 'danger'
                            };
                        @endphp
                        <div class="progress-bar bg-{{ $progressClass }}" role="progressbar" style="width: {{ $progress }}%">
                            {{ round($progress) }}%
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Selesai</span>
                        <span class="fw-semibold text-success">{{ $completedSchedules }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Dalam Progress</span>
                        <span class="fw-semibold text-warning">{{ $inProgressSchedules }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Pending</span>
                        <span class="fw-semibold text-secondary">{{ $pendingSchedules }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Total</span>
                        <span class="fw-bold">{{ $totalSchedules }}</span>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Team Kami</h6>
                    @forelse($event->eventMembers as $eventMember)
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            {{ strtoupper(substr($eventMember->member->name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $eventMember->member->name }}</div>
                            <small class="text-muted">{{ $eventMember->role ?? 'Member' }}</small>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted small mb-0">Belum ada team member</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title fw-bold mb-4">Checklist Schedule & Vendor</h5>
            
            @if($event->schedules->isEmpty())
            <div class="text-center py-5">
                <p class="text-muted">Belum ada schedule untuk acara ini</p>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;">
                                <input type="checkbox" class="form-check-input" disabled>
                            </th>
                            <th>Vendor / Activity</th>
                            <th>Kategori</th>
                            <th>Waktu</th>
                            <th>PIC Team Kami</th>
                            <th>Status</th>
                            <th style="width: 10%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($event->schedules as $schedule)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input schedule-checkbox" 
                                    data-schedule-id="{{ $schedule->id }}"
                                    {{ $schedule->status === 'completed' ? 'checked' : '' }}>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $schedule->vendor?->name ?? $schedule->activity }}</div>
                                @if($schedule->location)
                                <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $schedule->location }}</small>
                                @endif
                            </td>
                            <td>
                                @if($schedule->vendor?->category)
                                <span class="badge bg-light text-dark">{{ $schedule->vendor->category->name }}</span>
                                @else
                                -
                                @endif
                            </td>
                            <td>
                                <small>
                                    {{ $schedule->start_time ? date('H:i', strtotime($schedule->start_time)) : '-' }}
                                    @if($schedule->end_time)
                                    - {{ date('H:i', strtotime($schedule->end_time)) }}
                                    @endif
                                </small>
                            </td>
                            <td>
                                @if($schedule->member)
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 11px;">
                                        {{ strtoupper(substr($schedule->member->name, 0, 2)) }}
                                    </div>
                                    <small class="fw-semibold">{{ $schedule->member->call_sign }}</small>
                                </div>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <select class="form-select form-select-sm status-select" data-schedule-id="{{ $schedule->id }}">
                                    <option value="pending" {{ $schedule->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ $schedule->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ $schedule->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </td>
                            <td>
                                @if($schedule->notes)
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="{{ $schedule->notes }}">
                                    <i class="bi bi-sticky"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelects = document.querySelectorAll('.status-select');
    const checkboxes = document.querySelectorAll('.schedule-checkbox');

    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const scheduleId = this.dataset.scheduleId;
            const status = this.value;
            
            updateScheduleStatus(scheduleId, status);
        });
    });

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const scheduleId = this.dataset.scheduleId;
            const status = this.checked ? 'completed' : 'pending';
            const select = document.querySelector(`.status-select[data-schedule-id="${scheduleId}"]`);
            
            if (select) {
                select.value = status;
                updateScheduleStatus(scheduleId, status);
            }
        });
    });

    function updateScheduleStatus(scheduleId, status) {
        fetch(`/admin/tracking/schedule/${scheduleId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }

    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
