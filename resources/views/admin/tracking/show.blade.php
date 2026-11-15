@extends('layouts.admin')

@section('title', 'Detail Tracking - ' . $event->name)
@section('page-title', 'Detail Tracking Acara')

@push('styles')
    @vite('resources/css/admin/tracking.css')
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <a href="{{ route('admin.tracking.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Booking
            </a>
            <h1 class="tracking-show-title mb-1">{{ $event->name }}</h1>
            <p class="text-muted mb-0">
                Detail progress, penugasan PIC jadwal, dan verifikasi kehadiran & bayaran tim
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.events.create') }}" class="btn btn-wo-outline">
                <i class="bi bi-plus-lg"></i> Tambah Event Baru
            </a>
            <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-wo-primary">
                <i class="bi bi-pencil"></i> Edit Acara Ini
            </a>
        </div>
    </div>

    {{-- Panel Rangkaian Event pada Booking Ini --}}
    @if(isset($bookingEvents) && $bookingEvents->isNotEmpty())
        <div class="card wo-card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h6 class="wo-card-title mb-0">
                            <i class="bi bi-calendar2-week me-1"></i>
                            Rangkaian Event pada Booking #{{ $event->booking_id }} ({{ $bookingEvents->count() }} Event)
                        </h6>
                        <small class="text-muted">
                            Pasangan: <strong>{{ $event->booking?->client?->groom_name ?? 'N/A' }} & {{ $event->booking?->client?->bride_name ?? 'N/A' }}</strong>
                            — Klik salah satu event di bawah untuk melihat detail progress masing-masing acara.
                        </small>
                    </div>
                </div>

                <div class="event-switcher-grid">
                    @foreach($bookingEvents as $idx => $bEvent)
                        @php
                            $isCurrent = (int) $bEvent->id === (int) $event->id;
                            $bTotal = $bEvent->schedules->count();
                            $bApproved = $bEvent->schedules->where('status', 'approved')->count();
                            $bPct = $bTotal > 0 ? round(($bApproved / $bTotal) * 100) : 0;
                            $bCoord = $bEvent->eventMembers->first(fn ($em) => $em->isCoordinator());
                        @endphp
                        <a href="{{ route('admin.tracking.show', $bEvent->id) }}" class="event-switcher-card {{ $isCurrent ? 'active' : '' }}">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                <span class="badge {{ $isCurrent ? 'bg-light text-dark' : 'bg-white text-dark border' }}" style="font-size: 0.7rem;">
                                    Event #{{ $idx + 1 }} {{ $isCurrent ? '• Sedang Dibuka' : '' }}
                                </span>
                                <span class="badge {{ $isCurrent ? 'bg-warning text-dark' : 'bg-success text-white' }}" style="font-size: 0.72rem;">
                                    {{ $bPct }}% ({{ $bApproved }}/{{ $bTotal }})
                                </span>
                            </div>
                            <div class="fw-bold" style="font-size: 1rem;">{{ $bEvent->name }}</div>
                            <div class="small {{ $isCurrent ? 'text-white-50' : 'text-muted' }} mt-1">
                                <i class="bi bi-calendar3"></i> {{ $bEvent->event_date ? $bEvent->event_date->format('d M Y') : '-' }}
                                @if($bEvent->event_type)
                                    • {{ ucfirst(str_replace('_', ' ', $bEvent->event_type)) }}
                                @endif
                            </div>
                            <div class="small {{ $isCurrent ? 'text-white-50' : 'text-muted' }} mt-1">
                                <i class="bi bi-people"></i> Tim: {{ $bEvent->eventMembers->count() }} orang
                                @if($bCoord?->member)
                                    • Coord: {{ $bCoord->member->call_sign ?: $bCoord->member->name }}
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card wo-card mb-4">
                <div class="card-body">
                    <h5 class="wo-card-title mb-3">Informasi Acara</h5>
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
                            <label class="text-muted small mb-1">Lokasi & Alamat Venue</label>
                            <div class="fw-semibold">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $event->booking?->venue_name ?? 'N/A' }}
                                @if($event->booking?->full_venue_address)
                                    <br><small class="text-muted">{{ $event->booking->full_venue_address }}</small>
                                @endif
                                @if($event->booking?->venue_maps)
                                    <div class="mt-1">
                                        <a href="{{ $event->booking->venue_maps }}" target="_blank" class="small text-primary text-decoration-none">
                                            <i class="bi bi-box-arrow-up-right me-1"></i>Buka Google Maps
                                        </a>
                                    </div>
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
                    <div class="row mt-2">
                        <div class="col-md-6 mb-2">
                            <label class="text-muted small mb-1">Paket Acara Ini</label>
                            <div class="fw-semibold text-success">
                                <i class="bi bi-box-seam me-1"></i>{{ $event->package?->name ?? 'Custom Vendor' }}
                                @if($event->package)
                                    <span class="text-muted small">(Rp {{ number_format($event->package->price, 0, ',', '.') }})</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="text-muted small mb-1">Total Nilai Seluruh Booking</label>
                            <div class="fw-bold" style="color: #073d36; font-size: 1.05rem;">
                                Rp {{ number_format($event->booking?->total_price ?? 0, 0, ',', '.') }}
                                @if(($event->booking?->all_packages?->count() ?? 0) > 1)
                                    <span class="badge bg-light text-dark border small" style="font-size: 0.75rem;">{{ $event->booking->all_packages->count() }} Paket Acara</span>
                                @endif
                            </div>
                        </div>
                        @if(($event->booking?->all_packages?->count() ?? 0) > 1)
                            <div class="col-12 mt-2">
                                <div class="p-2 rounded bg-light border">
                                    <small class="text-muted fw-bold d-block mb-1">Seluruh Paket pada Booking Ini:</small>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($event->booking->all_packages as $bPkg)
                                            <span class="badge bg-white text-dark border">
                                                <i class="bi bi-check-circle-fill text-success me-1"></i>{{ $bPkg->name }} (Rp {{ number_format($bPkg->price, 0, ',', '.') }})
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card wo-card mb-3">
                <div class="card-body">
                    <h6 class="wo-card-title mb-3">Progress Keseluruhan</h6>
                    <div class="text-center mb-3">
                        <h2 class="display-4 fw-bold mb-0" style="color: #073d36;">{{ round($progress) }}%</h2>
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

            <div class="card wo-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-3 flex-wrap">
                        <div>
                            <h6 class="wo-card-title mb-1">Team Kami & Verifikasi Honor</h6>
                            <small class="text-muted d-block">Presensi & jam diajukan oleh Koordinator, diverifikasi & dicairkan oleh Admin.</small>
                        </div>
                        @if($event->eventMembers->isNotEmpty())
                            <div class="d-flex gap-1 flex-wrap">
                                <button type="button" class="btn btn-sm btn-outline-warning text-dark fw-semibold" onclick="batchEventFee({{ $event->id }}, 'approve_all')" title="Setujui seluruh honor kru di event ini">
                                    <i class="bi bi-check-all"></i> ACC Semua
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-success fw-semibold" onclick="batchEventFee({{ $event->id }}, 'pay_all')" title="Tandai lunas seluruh honor kru di event ini">
                                    <i class="bi bi-cash"></i> Cairkan Semua
                                </button>
                                <a href="{{ route('admin.payroll.index', ['event_id' => $event->id]) }}" class="btn btn-sm btn-light border" title="Lihat Rekapitulasi Lengkap">
                                    <i class="bi bi-wallet2"></i>
                                </a>
                            </div>
                        @endif
                    </div>

                    @php
                        $eventTotalFee = (float) $event->eventMembers->sum('total_fee');
                    @endphp
                    @if($event->eventMembers->isNotEmpty())
                        <div class="p-2 mb-3 rounded d-flex justify-content-between align-items-center" style="background: #edf4f1; border: 1px solid #cce1d8;">
                            <span class="small fw-semibold" style="color: #073d36;">
                                <i class="bi bi-people-fill me-1"></i> {{ $event->eventMembers->count() }} Kru Bertugas
                            </span>
                            <span class="small fw-bold" style="color: #073d36;">
                                Total Honor: Rp {{ number_format($eventTotalFee, 0, ',', '.') }}
                            </span>
                        </div>
                    @endif

                    @forelse($event->eventMembers as $eventMember)
                    @php
                        $isCoord = $eventMember->isCoordinator();
                        $payStatus = $eventMember->payment_status ?? 'pending';
                        $dailyFeeValue = (float) ($eventMember->member?->daily_fee ?? 0);
                        if ($dailyFeeValue <= 0) {
                            $dailyFeeValue = $isCoord ? 150000 : 100000;
                        }
                        $payBadgeClass = match($payStatus) {
                            'paid' => 'success',
                            'approved' => 'info',
                            default => 'warning text-dark',
                        };
                        $payBadgeLabel = match($payStatus) {
                            'paid' => 'Lunas Dibayar',
                            'approved' => 'ACC Disetujui',
                            default => 'Menunggu ACC',
                        };
                    @endphp
                    <div class="d-flex align-items-center gap-2 mb-2 p-2 rounded border" style="background: #f7faf8; border-color: #e4ebe8 !important;">
                        <div class="member-avatar-circle {{ $isCoord ? 'coord' : 'crew' }}">
                            {{ strtoupper(substr($eventMember->member?->name ?? 'M', 0, 2)) }}
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                <span class="fw-semibold" style="color: #173b32;">{{ $eventMember->member?->name ?? 'Member #' . $eventMember->member_id }}</span>
                                @if($isCoord)
                                    <span class="badge badge-wo-coord">Coordinator Utama</span>
                                @endif
                            </div>
                            <small class="text-muted d-block">{{ $eventMember->role ?? 'Member' }} ({{ $eventMember->member?->call_sign ?? '-' }})</small>
                            <div class="d-flex align-items-center gap-2 flex-wrap mt-1">
                                <span class="badge bg-white text-dark border" style="font-size: 11px;">
                                    {{ ucfirst($eventMember->attendance_status ?? 'present') }} • {{ number_format((float) ($eventMember->actual_hours ?? 0), 1) }} jam
                                </span>
                                <span class="badge bg-{{ $payBadgeClass }} member-pay-badge-{{ $eventMember->member_id }}" style="font-size: 11px;">
                                    {{ $payBadgeLabel }}
                                </span>
                                <strong class="small ms-auto" style="color: #073d36;">
                                    Rp {{ number_format((float) ($eventMember->total_fee ?? 0), 0, ',', '.') }}
                                </strong>
                            </div>
                        </div>

                        {{-- Quick Action Buttons --}}
                        <div class="d-flex align-items-center gap-1">
                            @if($payStatus === 'pending')
                                <button type="button" class="btn btn-sm btn-warning text-dark py-1 px-2" title="ACC & Setujui Fee" onclick="quickFeeStatus({{ $event->id }}, {{ $eventMember->member_id }}, 'approved')">
                                    <i class="bi bi-check-lg"></i> ACC
                                </button>
                                <button type="button" class="btn btn-sm btn-success py-1 px-2" title="Langsung Tandai Lunas" onclick="quickFeeStatus({{ $event->id }}, {{ $eventMember->member_id }}, 'paid')">
                                    <i class="bi bi-cash"></i> Cairkan
                                </button>
                            @elseif($payStatus === 'approved')
                                <button type="button" class="btn btn-sm btn-success py-1 px-2" title="Tandai Lunas Dicairkan" onclick="quickFeeStatus({{ $event->id }}, {{ $eventMember->member_id }}, 'paid')">
                                    <i class="bi bi-cash-stack"></i> Cairkan
                                </button>
                            @endif

                            <button class="btn btn-sm btn-wo-outline" type="button" data-bs-toggle="collapse" data-bs-target="#memberAttendance{{ $eventMember->id }}" title="Detail & Edit Perhitungan Fee">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                        </div>
                    </div>

                    <div class="collapse mb-3" id="memberAttendance{{ $eventMember->id }}">
                        <div class="card card-body border member-fee-row" style="background: #fff; border-color: #e4ebe8 !important; border-radius: 12px;"
                            data-daily-fee="{{ $dailyFeeValue }}"
                            data-is-coord="{{ $isCoord ? '1' : '0' }}">
                            
                            <div class="row g-2 align-items-end">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold mb-1">Kehadiran (Dari Coordinator)</label>
                                    <select class="form-select form-select-sm attendance-status">
                                        <option value="present"
                                            @selected(($eventMember->attendance_status ?? 'present') === 'present')>
                                            Present (Hadir)
                                        </option>

                                        <option value="late"
                                            @selected($eventMember->attendance_status === 'late')>
                                            Late (Terlambat)
                                        </option>

                                        <option value="absent"
                                            @selected($eventMember->attendance_status === 'absent')>
                                            Absent (Tidak Hadir)
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold mb-1">Jam Kerja Aktual</label>
                                    <input type="number" step="0.25" min="0" max="24" class="form-control form-control-sm actual-hours" value="{{ number_format((float) ($eventMember->actual_hours ?? 0), 2, '.', '') }}" data-event-id="{{ $event->id }}" data-member-id="{{ $eventMember->member_id }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold mb-1">Tunjangan / Allowance (Rp)</label>
                                    <input type="number" step="1000" min="0" class="form-control form-control-sm allowance" value="{{ number_format((float) ($eventMember->allowance ?? 0), 0, '.', '') }}" data-event-id="{{ $event->id }}" data-member-id="{{ $eventMember->member_id }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold mb-1">Status Bayaran (ACC Admin)</label>
                                    <select class="form-select form-select-sm payment-status">
                                        <option value="pending" @selected(($eventMember->payment_status ?? 'pending') === 'pending')>⏳ Menunggu ACC</option>
                                        <option value="approved" @selected($eventMember->payment_status === 'approved')>✨ Disetujui (ACC Fee)</option>
                                        <option value="paid" @selected($eventMember->payment_status === 'paid')>✅ Lunas Dibayar</option>
                                    </select>
                                </div>
                                <div class="col-12 mt-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div class="small text-muted">
                                        Perhitungan: <span class="badge bg-light text-dark border">Tarif: Rp {{ number_format($dailyFeeValue, 0, ',', '.') }}/hari</span> &bull; 
                                        Total Fee: <strong class="member-total-fee" style="color: #073d36;">Rp {{ number_format((float) ($eventMember->total_fee ?? 0), 0, ',', '.') }}</strong>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-wo-primary save-member-fee" data-event-id="{{ $event->id }}" data-member-id="{{ $eventMember->member_id }}">
                                        <i class="bi bi-check2-circle"></i> Simpan & Validasi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted small mb-0">Belum ada team member yang ditugaskan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="card wo-card">
        <div class="card-body">
            <h5 class="wo-card-title mb-4">Checklist Schedule & Vendor</h5>

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
                            <td style="min-width: 190px;">
                                <select class="form-select form-select-sm schedule-member-select" data-schedule-id="{{ $schedule->id }}">
                                    <option value="">-- Pilih PIC Tim --</option>
                                    @foreach($event->eventMembers as $em)
                                        @if($em->member)
                                            <option value="{{ $em->member_id }}" @selected($schedule->member_id == $em->member_id)>
                                                {{ $em->member->call_sign ?: $em->member->name }} ({{ $em->role }})
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
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
    const memberSelects = document.querySelectorAll('.schedule-member-select');
    const checkboxes = document.querySelectorAll('.schedule-checkbox');
    const bookingStatusSelect = document.querySelector('.booking-status-select');

    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const scheduleId = this.dataset.scheduleId;
            const status = this.value;

            updateScheduleStatus(scheduleId, status);
        });
    });

    memberSelects.forEach(select => {
        select.addEventListener('change', function() {
            const scheduleId = this.dataset.scheduleId;
            const memberId = this.value || null;

            fetch(`/admin/tracking/schedule/${scheduleId}/member`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ member_id: memberId })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    alert(data.message || 'Gagal memperbarui PIC tugas.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal memperbarui PIC tugas.');
            });
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

        let dailyFee = Math.max(
            0,
            parseFloat(row.dataset.dailyFee) || 0
        );

        if (dailyFee <= 0) {
            dailyFee = row.dataset.isCoord === '1' ? 150000 : 100000;
        }

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
        const paymentStatus = row.querySelector('.payment-status')?.value || 'pending';

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
                        allowance: allowance,
                        payment_status: paymentStatus
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
            location.reload();

        } catch (error) {
            console.error('Error:', error);
            alert(error.message || 'Gagal menyimpan tracking member.');

        } finally {
            this.disabled = false;
        }
    });
});

window.quickFeeStatus = async function(eventId, memberId, status) {
    const label = status === 'paid' ? 'mencairkan & menandai lunas' : 'menyetujui (ACC)';
    if (!confirm(`Apakah Anda yakin ingin ${label} honor kru ini?`)) {
        return;
    }

    try {
        const response = await fetch(`/admin/tracking/event/${eventId}/member/${memberId}/quick-fee`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                payment_status: status
            })
        });

        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Gagal mengubah status bayaran.');
        }

        location.reload();
    } catch (err) {
        alert(err.message || 'Terjadi kesalahan sistem.');
    }
};

window.batchEventFee = async function(eventId, action) {
    const label = action === 'pay_all' ? 'mencairkan seluruh honor' : 'menyetujui (ACC) seluruh honor';
    if (!confirm(`Konfirmasi: Anda akan ${label} tim pada acara ini?`)) {
        return;
    }

    try {
        const response = await fetch(`/admin/tracking/event/${eventId}/batch-fee`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                action: action
            })
        });

        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Gagal memproses aksi massal.');
        }

        location.reload();
    } catch (err) {
        alert(err.message || 'Terjadi kesalahan sistem.');
    }
};

    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
