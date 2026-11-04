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
            <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-primary">
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
                                    $eventStatusLabel = match($event->status) {
                                        'draft' => 'Draft',
                                        'scheduled', 'upcoming' => 'Upcoming',
                                        'ongoing' => 'Ongoing',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled',
                                        default => ucfirst($event->status),
                                    };

                                    $statusClass = match($event->status) {
                                        'draft' => 'secondary',
                                        'scheduled', 'upcoming' => 'primary',
                                        'ongoing' => 'warning',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">{{ $eventStatusLabel }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Status Booking</label>
                            <div>
                                <select class="form-select form-select-sm booking-status-select" data-booking-id="{{ $event->booking->id }}" style="max-width: 150px;">
                                    <option value="pending" {{ $event->booking->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ $event->booking->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ $event->booking->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
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
                        <span class="text-muted small">Approved</span>
                        <span class="fw-semibold text-success">{{ $approvedSchedules }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Rejected</span>
                        <span class="fw-semibold text-danger">{{ $rejectedSchedules }}</span>
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
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $eventMember->member->name }}</div>
                            <small class="text-muted">{{ $eventMember->role ?? 'Member' }}</small>
                        </div>
                        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#memberAttendance{{ $eventMember->id }}">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                    </div>

                    <div class="collapse mb-3" id="memberAttendance{{ $eventMember->id }}">
                        <div class="card card-body border-0 bg-light member-fee-row"
                            data-daily-fee="{{ (float) ($eventMember->member?->daily_fee ?? 0) }}">
                            
                            <div class="row g-2 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Attendance</label>
                                    <select class="form-select attendance-status">
                                        <option value="present"
                                            @selected(($eventMember->attendance_status ?? 'present') === 'present')>
                                            Present
                                        </option>

                                        <option value="late"
                                            @selected($eventMember->attendance_status === 'late')>
                                            Late
                                        </option>

                                        <option value="absent"
                                            @selected($eventMember->attendance_status === 'absent')>
                                            Absent
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold">Actual Hours</label>
                                    <input type="number" step="0.25" min="0" max="24" class="form-control form-control-sm actual-hours" value="{{ number_format((float) ($eventMember->actual_hours ?? 0), 2, '.', '') }}" data-event-id="{{ $event->id }}" data-member-id="{{ $eventMember->member_id }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold">Allowance</label>
                                    <input type="number" step="1000" min="0" class="form-control form-control-sm allowance" value="{{ number_format((float) ($eventMember->allowance ?? 0), 0, '.', '') }}" data-event-id="{{ $event->id }}" data-member-id="{{ $eventMember->member_id }}">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-sm btn-primary w-100 save-member-fee" data-event-id="{{ $event->id }}" data-member-id="{{ $eventMember->member_id }}">
                                        Simpan
                                    </button>
                                </div>
                            </div>
                            <div class="mt-2 small text-muted">
                                Total Fee: <strong class="member-total-fee">Rp {{ number_format((float) ($eventMember->total_fee ?? 0), 0, ',', '.') }}</strong>
                            </div>
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
                            <th>Jam Hari H</th>
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
                                    {{ $schedule->status === 'approved' ? 'checked' : '' }}>
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
                                @if($schedule->status === 'approved' && $schedule->vendor)
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <input type="time" class="form-control form-control-sm schedule-time-start"
                                        aria-label="Jam mulai {{ $schedule->vendor->name }}"
                                        value="{{ $schedule->start_time ? date('H:i', strtotime($schedule->start_time)) : '' }}"
                                        style="width: 125px;" required>
                                    <span>-</span>
                                    <input type="time" class="form-control form-control-sm schedule-time-end"
                                        aria-label="Jam selesai {{ $schedule->vendor->name }}"
                                        value="{{ $schedule->end_time ? date('H:i', strtotime($schedule->end_time)) : '' }}"
                                        style="width: 125px;">
                                    <button type="button" class="btn btn-sm btn-outline-primary save-schedule-time"
                                        data-schedule-id="{{ $schedule->id }}" title="Simpan jam vendor">
                                        <i class="bi bi-save"></i>
                                    </button>
                                </div>
                                @else
                                <small>
                                    {{ $schedule->start_time ? date('H:i', strtotime($schedule->start_time)) : '-' }}
                                    @if($schedule->end_time)
                                    - {{ date('H:i', strtotime($schedule->end_time)) }}
                                    @endif
                                </small>
                                @endif
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
                                    <option value="approved" {{ $schedule->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ $schedule->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
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
    const bookingStatusSelect = document.querySelector('.booking-status-select');

    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const scheduleId = this.dataset.scheduleId;
            const status = this.value;

            updateScheduleStatus(scheduleId, status);
        });
    });

    document.querySelectorAll('.save-schedule-time').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const startTime = row.querySelector('.schedule-time-start').value;
            const endTime = row.querySelector('.schedule-time-end').value;

            if (!startTime || (endTime && endTime <= startTime)) {
                alert('Isi jam mulai dan pastikan jam selesai setelah jam mulai.');
                return;
            }

            fetch(`/admin/tracking/schedule/${this.dataset.scheduleId}/time`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ start_time: startTime, end_time: endTime || null })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                } else {
                    alert(data.message || 'Jam vendor gagal disimpan.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Jam vendor gagal disimpan.');
            });
        });
    });

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const scheduleId = this.dataset.scheduleId;
            const status = this.checked ? 'approved' : 'pending';
            const select = document.querySelector(`.status-select[data-schedule-id="${scheduleId}"]`);

            if (select) {
                select.value = status;
                updateScheduleStatus(scheduleId, status);
            }
        });
    });

    if (bookingStatusSelect) {
        bookingStatusSelect.addEventListener('change', function() {
            const bookingId = this.dataset.bookingId;
            const status = this.value;

            updateBookingStatus(bookingId, status);
        });
    }

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

    function updateBookingStatus(bookingId, status) {
        fetch(`/admin/tracking/booking/${bookingId}/status`, {
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
                alert('Status booking berhasil diupdate');
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // ==========================================
// MEMBER ATTENDANCE & FEE
// ==========================================

    function calculateMemberFee(row) {
        const attendanceStatus =
            row.querySelector('.attendance-status')?.value || 'present';

        const actualHours = Math.max(
            0,
            parseFloat(row.querySelector('.actual-hours')?.value) || 0
        );

        const allowance = Math.max(
            0,
            parseFloat(row.querySelector('.allowance')?.value) || 0
        );

        const dailyFee = Math.max(
            0,
            parseFloat(row.dataset.dailyFee) || 0
        );

        if (attendanceStatus === 'absent') {
            return 0;
        }

        const hourlyRate = dailyFee / 8;
        const workFee = actualHours * hourlyRate;

        return Math.round(workFee + allowance);
    }

function formatRupiah(value) {
    return 'Rp ' + value.toLocaleString('id-ID');
}

// Preview perhitungan fee masing-masing member.
document.querySelectorAll('.member-fee-row').forEach(row => {
    const inputs = row.querySelectorAll(
        '.attendance-status, .actual-hours, .allowance'
    );

    function updateFeePreview() {
        const totalFeeEl = row.querySelector('.member-total-fee');

        if (totalFeeEl) {
            totalFeeEl.textContent = formatRupiah(calculateMemberFee(row));
        }
    }

    inputs.forEach(input => {
        input.addEventListener('input', updateFeePreview);
        input.addEventListener('change', updateFeePreview);
    });

    // Tampilkan perhitungan awal.
    updateFeePreview();
});

// Simpan attendance dan fee member.
document.querySelectorAll('.save-member-fee').forEach(button => {
    button.addEventListener('click', async function() {
        const eventId = this.dataset.eventId;
        const memberId = this.dataset.memberId;
        const row = this.closest('.member-fee-row');

        if (!row) return;

        const attendanceStatus = row.querySelector('.attendance-status').value;
        const actualHours = parseFloat(row.querySelector('.actual-hours').value) || 0;
        const allowance = parseFloat(row.querySelector('.allowance').value) || 0;

        if (actualHours < 0 || actualHours > 24 || allowance < 0) {
            alert('Jam kerja harus antara 0–24 jam dan allowance tidak boleh negatif.');
            return;
        }

        this.disabled = true;

        try {
            const response = await fetch(
                `/admin/tracking/event/${eventId}/member/${memberId}/attendance`,
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        attendance_status: attendanceStatus,
                        actual_hours: actualHours,
                        allowance: allowance
                    })
                }
            );

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Gagal menyimpan tracking member.');
            }

            const totalFeeEl = row.querySelector('.member-total-fee');

            if (totalFeeEl) {
                totalFeeEl.textContent = data.total_fee ?? formatRupiah(calculateMemberFee(row));
            }

            alert(data.message || 'Tracking member berhasil disimpan.');

        } catch (error) {
            console.error('Error:', error);
            alert(error.message || 'Gagal menyimpan tracking member.');

        } finally {
            this.disabled = false;
        }
    });
});

    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
