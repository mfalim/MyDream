<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Member - {{ $member->name }}</title>
    @vite(['resources/css/member/shared.css', 'resources/css/member/dashboard.css'])
    <style>
        .coord-panel {
            margin-top: 18px;
            padding: 16px;
            border: 1px solid #d5e3df;
            border-radius: 12px;
            background: #f4f9f7;
        }
        .coord-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 12px;
            color: var(--wo-primary);
            font-weight: 700;
        }
        .coord-team-list {
            display: grid;
            gap: 10px;
        }
        .coord-team-row {
            display: grid;
            grid-template-columns: 1.5fr 1fr 0.8fr 1fr auto;
            gap: 10px;
            align-items: center;
            padding: 12px;
            background: #fff;
            border: 1px solid var(--wo-border);
            border-radius: 10px;
        }
        .team-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }
        .team-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            background: #fff;
            border: 1px solid var(--wo-border);
            border-radius: 999px;
            font-size: 0.82rem;
            color: var(--wo-ink);
        }
        .coord-input, .coord-select {
            width: 100%;
            padding: 7px 10px;
            border: 1px solid var(--wo-border-strong);
            border-radius: 8px;
            font-size: 0.85rem;
            background: #fff;
            color: var(--wo-ink);
        }
        .coord-save-btn {
            padding: 8px 14px;
            border: 0;
            border-radius: 8px;
            background: var(--wo-primary);
            color: #fff;
            font-size: 0.82rem;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
        }
        .coord-save-btn:disabled {
            opacity: 0.55;
            cursor: not-allowed;
        }
        .my-fee-strip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 14px;
            padding: 12px 14px;
            border-radius: 10px;
            background: #f8faf9;
            border: 1px solid var(--wo-border);
            font-size: 0.88rem;
        }
        .pill-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.74rem;
            font-weight: 700;
        }
        .pill-badge.coord { background: var(--wo-gold-soft); color: #7d5b2b; border: 1px solid #ead2a6; }
        .pill-badge.pic { background: #e7f1ea; color: var(--wo-primary); border: 1px solid #cfe0d6; }
        .pill-badge.pay-pending { background: var(--wo-warning-soft); color: #92400e; }
        .pill-badge.pay-approved { background: #e7f1ea; color: var(--wo-primary); }
        .pill-badge.pay-paid { background: var(--wo-success-soft); color: #065f46; }
        .pill-badge.status-rejected { background: var(--wo-danger-soft); color: #991b1b; }
        .task-pic-select {
            padding: 5px 8px;
            border: 1px solid var(--wo-border-strong);
            border-radius: 8px;
            font-size: 0.8rem;
            background: #fff;
            color: var(--wo-ink);
            max-width: 220px;
        }
        .task-status-btns {
            display: inline-flex;
            gap: 6px;
            align-items: center;
        }
        .mini-status-btn {
            padding: 4px 9px;
            border-radius: 6px;
            border: 1px solid var(--wo-border-strong);
            background: #fff;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            color: var(--wo-ink-soft);
        }
        .mini-status-btn.active-approved {
            background: var(--wo-success);
            border-color: var(--wo-success);
            color: #fff;
        }
        .mini-status-btn.active-rejected {
            background: var(--wo-danger);
            border-color: var(--wo-danger);
            color: #fff;
        }
        @media (max-width: 767px) {
            .coord-team-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="member-shell">
        <header class="member-topbar">
            <div class="member-brand">
                <div class="member-brand-mark">◉</div>
                <div>
                    <span>Member Area</span>
                    <strong>WO PROJECT</strong>
                </div>
            </div>

            <div class="member-userchip">
                <span class="avatar-dot">{{ strtoupper(substr($member->call_sign ?? $member->name, 0, 1)) }}</span>
                <span>{{ $member->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-link btn btn-link p-0 border-0">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </header>

        <section class="member-hero">
            <div class="member-hero-content">
                <div class="member-avatar-large">
                    {{ strtoupper(substr($member->call_sign ?? $member->name, 0, 2)) }}
                </div>
                <div class="member-hero-copy">
                    <h1>{{ $member->name }}</h1>
                    <p>{{ $member->call_sign }} • {{ $member->position }} • {{ $member->division }}</p>
                </div>
            </div>

            <div class="member-hero-meta">
                <span class="hero-pill"><i class="bi bi-people"></i> {{ $stats['total_events'] }} Event</span>
                @if(($stats['coordinator_events'] ?? 0) > 0)
                    <span class="hero-pill"><i class="bi bi-shield-check"></i> {{ $stats['coordinator_events'] }} Coordinator</span>
                @endif
                <span class="hero-pill"><i class="bi bi-list-check"></i> {{ $stats['total_schedules'] }} Tugas Saya</span>
            </div>
        </section>

        <div class="member-stats">
            <div class="member-stat-card">
                <div class="stat-icon" style="background:#e7f1ea; color:var(--wo-primary);">
                    <i class="bi bi-calendar-event"></i>
                </div>
                <div class="stat-label">Event Diikuti</div>
                <p class="stat-value">{{ $stats['total_events'] }}</p>
            </div>

            <div class="member-stat-card">
                <div class="stat-icon" style="background:var(--wo-gold-soft); color:#8a6a3b;">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-label">Tugas Saya</div>
                <p class="stat-value">{{ $stats['total_schedules'] }}</p>
            </div>

            <div class="member-stat-card">
                <div class="stat-icon" style="background:var(--wo-success-soft); color:var(--wo-success);">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-label">Tugas Selesai</div>
                <p class="stat-value">{{ $stats['approved'] }}</p>
            </div>

            <div class="member-stat-card">
                <div class="stat-icon" style="background:var(--wo-warning-soft); color:#b77b0d;">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="stat-label">Daily Fee / ACC</div>
                <p class="stat-value">Rp {{ number_format((float) ($member->daily_fee ?? 0), 0, ',', '.') }}</p>
                <small style="color: var(--wo-text-muted); font-size: 0.78rem;">
                    Tervalidasi Admin: <strong>Rp {{ number_format((float) ($stats['approved_fee'] ?? 0), 0, ',', '.') }}</strong>
                </small>
            </div>
        </div>

        @php
            $todaySchedules = $schedules->filter(function ($schedule) {
                $eventDate = $schedule->event?->event_date;
                return $eventDate && \Carbon\Carbon::parse($eventDate)->isToday();
            });
            $upcomingEvents = $eventMembers->filter(function ($em) {
                $eventDate = $em->event?->event_date;
                return $eventDate && (\Carbon\Carbon::parse($eventDate)->isToday() || \Carbon\Carbon::parse($eventDate)->isFuture());
            });
        @endphp

        <section class="member-summary-grid">
            <div class="member-summary-panel">
                <div class="summary-panel-header">
                    <span><i class="bi bi-person-badge"></i> Profil Team</span>
                </div>
                <ul class="summary-list">
                    <li><span>Divisi</span><strong>{{ $member->division ?? '-' }}</strong></li>
                    <li><span>Jabatan</span><strong>{{ $member->position ?? '-' }}</strong></li>
                    <li><span>Spesialisasi</span><strong>{{ $member->specialization ?? '-' }}</strong></li>
                    <li><span>Status</span><strong>{{ ucfirst($member->status ?? 'aktif') }}</strong></li>
                </ul>
            </div>

            <div class="member-summary-panel">
                <div class="summary-panel-header">
                    <span><i class="bi bi-clock-fill"></i> Tugas Hari Ini</span>
                </div>
                @if($todaySchedules->isNotEmpty())
                    <ul class="summary-list compact-list">
                        @foreach($todaySchedules as $todaySchedule)
                            <li>
                                <span>{{ $todaySchedule->activity }} ({{ $todaySchedule->event?->name }})</span>
                                <strong>
                                    {{ $todaySchedule->start_time ? \Carbon\Carbon::parse($todaySchedule->start_time)->format('H:i') : '-' }}
                                    -
                                    {{ $todaySchedule->end_time ? \Carbon\Carbon::parse($todaySchedule->end_time)->format('H:i') : '-' }}
                                </strong>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="mini-empty-state">Tidak ada tugas hari ini.</div>
                @endif
            </div>

            <div class="member-summary-panel">
                <div class="summary-panel-header">
                    <span><i class="bi bi-flag-fill"></i> Agenda Event Terdekat</span>
                </div>
                @if($upcomingEvents->isNotEmpty())
                    <ul class="summary-list compact-list">
                        @foreach($upcomingEvents->take(3) as $upcomingEm)
                            <li>
                                <span>{{ $upcomingEm->event?->name }} ({{ $upcomingEm->role }})</span>
                                <strong>{{ $upcomingEm->event?->event_date ? \Carbon\Carbon::parse($upcomingEm->event->event_date)->format('d M Y') : '-' }}</strong>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="mini-empty-state">Belum ada agenda mendatang.</div>
                @endif
            </div>
        </section>

        <section>
            <h2 class="section-heading"><i class="bi bi-calendar-check"></i>Event & Checklist Tugas Saya</h2>

            @forelse($eventMembers as $eventMember)
                @php
                    $isCoordinator = $eventMember->isCoordinator();
                    $allEventSchedules = $eventMember->event->schedules;

                    // Coordinator melihat & mengelola seluruh tugas event;
                    // Member biasa hanya fokus pada tugas yang ditugaskan kepadanya (atau tugas umum yang belum ada PIC).
                    $visibleSchedules = $isCoordinator
                        ? $allEventSchedules
                        : $allEventSchedules->filter(fn ($s) => $s->member_id === null || (int) $s->member_id === (int) $member->id);

                    $totalTasks = $visibleSchedules->count();
                    $completedTasks = $visibleSchedules->where('status', 'approved')->count();
                    $progressPercent = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

                    $myPayStatus = $eventMember->payment_status ?? 'pending';
                    $myPayClass = match($myPayStatus) {
                        'paid' => 'pay-paid',
                        'approved' => 'pay-approved',
                        default => 'pay-pending',
                    };
                    $myPayLabel = match($myPayStatus) {
                        'paid' => 'Lunas Dibayar Admin',
                        'approved' => 'Bayaran Disetujui (ACC Admin)',
                        default => 'Menunggu ACC Bayaran Admin',
                    };
                @endphp

                <div class="member-event-list" style="margin-bottom: 22px;">
                    <article class="member-event-card">
                        <div class="event-head">
                            <div>
                                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                    <h3 class="event-title">{{ $eventMember->event->name }}</h3>
                                    @if($isCoordinator)
                                        <span class="pill-badge coord"><i class="bi bi-shield-check"></i> Coordinator Utama Event</span>
                                    @endif
                                </div>
                                <div class="event-subtitle">
                                    <i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($eventMember->event->event_date)->format('d M Y') }}
                                    @if($eventMember->event->booking && $eventMember->event->booking->client)
                                        • {{ $eventMember->event->booking->client->groom_name }} & {{ $eventMember->event->booking->client->bride_name }}
                                    @endif
                                    @if($eventMember->event->booking?->venue_name)
                                        • <i class="bi bi-geo-alt"></i> {{ $eventMember->event->booking->venue_name }}
                                    @endif
                                </div>
                            </div>
                            <span class="role-badge approved">{{ $eventMember->role }}</span>
                        </div>

                        <div class="my-fee-strip">
                            <div>
                                <strong>Kehadiran Saya:</strong>
                                {{ ucfirst($eventMember->attendance_status ?? 'present') }}
                                ({{ number_format((float) ($eventMember->actual_hours ?? 0), 1) }} jam)
                                @if((float) ($eventMember->allowance ?? 0) > 0)
                                    • Tunjangan: Rp {{ number_format((float) $eventMember->allowance, 0, ',', '.') }}
                                @endif
                            </div>
                            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                                <span>Total Fee: <strong>Rp {{ number_format((float) ($eventMember->total_fee ?? 0), 0, ',', '.') }}</strong></span>
                                <span class="pill-badge {{ $myPayClass }}">{{ $myPayLabel }}</span>
                            </div>
                        </div>

                        @if($eventMember->notes)
                            <div class="schedule-notes mb-3">
                                <small><i class="bi bi-chat-left-text"></i> Catatan Penugasan</small>
                                <div>{{ $eventMember->notes }}</div>
                            </div>
                        @endif

                        {{-- Bagian 1: Checklist Tugas --}}
                        @if($totalTasks > 0)
                            <div class="task-checklist">
                                <div class="task-header">
                                    <span>
                                        <i class="bi bi-list-check"></i>
                                        {{ $isCoordinator ? 'Checklist Seluruh Jadwal & Distribusi PIC' : 'Checklist Tugas Saya di Event Ini' }}
                                        ({{ $completedTasks }}/{{ $totalTasks }})
                                    </span>
                                    <span>{{ $progressPercent }}%</span>
                                </div>

                                <div class="progress-track">
                                    <div class="progress-fill" style="width: {{ $progressPercent }}%"></div>
                                </div>

                                <div class="task-list">
                                    @foreach($visibleSchedules as $task)
                                        <div class="task-item" onclick="toggleTask({{ $task->id }}, '{{ $task->status }}')">
                                            <div class="task-checkbox {{ $task->status === 'approved' ? 'checked' : '' }}" id="checkbox-{{ $task->id }}">
                                                @if($task->status === 'approved')
                                                    <i class="bi bi-check-lg"></i>
                                                @endif
                                            </div>
                                            <div class="task-text {{ $task->status === 'approved' ? 'completed' : '' }}" id="text-{{ $task->id }}">
                                                <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; flex-wrap:wrap;">
                                                    <div>
                                                        <span>{{ $task->activity }}</span>
                                                        @if($task->status === 'rejected')
                                                            <span class="pill-badge status-rejected">Kendala / Ditolak</span>
                                                        @endif
                                                    </div>

                                                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;" onclick="event.stopPropagation();">
                                                        @if($isCoordinator)
                                                            <select class="task-pic-select" onchange="assignSchedulePic({{ $task->id }}, this.value)">
                                                                <option value="">-- Pilih PIC Tim --</option>
                                                                @foreach($eventMember->event->eventMembers as $teamEm)
                                                                    @if($teamEm->member)
                                                                        <option value="{{ $teamEm->member_id }}" @selected((int) $task->member_id === (int) $teamEm->member_id)>
                                                                            PIC: {{ $teamEm->member->call_sign ?: $teamEm->member->name }} ({{ $teamEm->role }})
                                                                        </option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        @else
                                                            <span class="pill-badge {{ (int) $task->member_id === (int) $member->id ? 'pic' : '' }}">
                                                                <i class="bi bi-person-check"></i>
                                                                {{ (int) $task->member_id === (int) $member->id ? 'PIC: Anda' : 'Tugas Tim' }}
                                                            </span>
                                                        @endif

                                                        <div class="task-status-btns">
                                                            <button type="button"
                                                                class="mini-status-btn {{ $task->status === 'approved' ? 'active-approved' : '' }}"
                                                                onclick="updateStatus({{ $task->id }}, '{{ $task->status === 'approved' ? 'pending' : 'approved' }}')">
                                                                <i class="bi bi-check2"></i> Selesai
                                                            </button>
                                                            <button type="button"
                                                                class="mini-status-btn {{ $task->status === 'rejected' ? 'active-rejected' : '' }}"
                                                                onclick="updateStatus({{ $task->id }}, '{{ $task->status === 'rejected' ? 'pending' : 'rejected' }}')">
                                                                <i class="bi bi-exclamation-circle"></i> Kendala
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if($task->vendor)
                                                    <small><i class="bi bi-shop"></i> Vendor: {{ $task->vendor->name }}</small>
                                                @endif
                                                <small>
                                                    <i class="bi bi-clock"></i>
                                                    {{ $task->start_time ? \Carbon\Carbon::parse($task->start_time)->format('H:i') : '-' }}
                                                    @if($task->end_time)
                                                        - {{ \Carbon\Carbon::parse($task->end_time)->format('H:i') }}
                                                    @endif
                                                    @if($task->location)
                                                        • <i class="bi bi-geo-alt"></i> {{ $task->location }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="text-muted mt-3 small">
                                <i class="bi bi-info-circle"></i> Belum ada tugas yang ditugaskan kepada Anda pada event ini.
                            </div>
                        @endif

                        {{-- Bagian 2: Tim Event (Coordinator: Kelola Presensi Tim | Member Biasa: Info Rekan Tim) --}}
                        @if($isCoordinator)
                            <div class="coord-panel">
                                <div class="coord-panel-header">
                                    <span><i class="bi bi-people-fill"></i> Tim Event & Input Kehadiran Lapangan</span>
                                    <small style="font-weight: 500; color: var(--wo-text-muted);">
                                        Catat kehadiran & jam kerja aktual tim di lapangan untuk diverifikasi & di-ACC bayarannya oleh Admin.
                                    </small>
                                </div>

                                <div class="coord-team-list">
                                    @foreach($eventMember->event->eventMembers as $teamEm)
                                        @php
                                            $isLocked = ($teamEm->payment_status ?? 'pending') === 'paid';
                                            $teamPayStatus = $teamEm->payment_status ?? 'pending';
                                            $teamPayClass = match($teamPayStatus) {
                                                'paid' => 'pay-paid',
                                                'approved' => 'pay-approved',
                                                default => 'pay-pending',
                                            };
                                            $teamPayLabel = match($teamPayStatus) {
                                                'paid' => 'Lunas',
                                                'approved' => 'ACC Admin',
                                                default => 'Menunggu ACC',
                                            };
                                        @endphp
                                        <div class="coord-team-row">
                                            <div>
                                                <strong>{{ $teamEm->member?->name ?? 'Member' }}</strong>
                                                <div style="font-size: 0.8rem; color: var(--wo-text-muted);">
                                                    {{ $teamEm->member?->call_sign ?? '-' }} • {{ $teamEm->role }}
                                                </div>
                                            </div>

                                            <div>
                                                <label style="display:block; font-size:0.75rem; color:var(--wo-text-muted); margin-bottom:3px;">Kehadiran</label>
                                                <select class="coord-select team-att-status" {{ $isLocked ? 'disabled' : '' }}>
                                                    <option value="present" @selected(($teamEm->attendance_status ?? 'present') === 'present')>Present (Hadir)</option>
                                                    <option value="late" @selected($teamEm->attendance_status === 'late')>Late (Terlambat)</option>
                                                    <option value="absent" @selected($teamEm->attendance_status === 'absent')>Absent (Tidak Hadir)</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label style="display:block; font-size:0.75rem; color:var(--wo-text-muted); margin-bottom:3px;">Jam Kerja</label>
                                                <input type="number" step="0.25" min="0" max="24" class="coord-input team-actual-hours"
                                                    value="{{ number_format((float) ($teamEm->actual_hours ?? 0), 2, '.', '') }}"
                                                    {{ $isLocked ? 'disabled' : '' }}>
                                            </div>

                                            <div>
                                                <div style="font-size: 0.78rem; color: var(--wo-text-muted);">Estimasi Fee:</div>
                                                <strong style="font-size: 0.9rem; color: var(--wo-primary);">
                                                    Rp {{ number_format((float) ($teamEm->total_fee ?? 0), 0, ',', '.') }}
                                                </strong>
                                                <div>
                                                    <span class="pill-badge {{ $teamPayClass }}" style="margin-top:3px;">{{ $teamPayLabel }}</span>
                                                </div>
                                            </div>

                                            <div>
                                                <button type="button" class="coord-save-btn"
                                                    onclick="saveTeamAttendance(this, {{ $eventMember->event_id }}, {{ $teamEm->member_id }})"
                                                    {{ $isLocked ? 'disabled' : '' }}>
                                                    <i class="bi bi-check2-circle"></i> Simpan
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="coord-panel" style="background: #fafcfb;">
                                <div class="coord-panel-header" style="margin-bottom: 6px;">
                                    <span><i class="bi bi-people"></i> Rekan Tim di Event Ini</span>
                                </div>
                                <div class="team-chips">
                                    @foreach($eventMember->event->eventMembers as $teamEm)
                                        <span class="team-chip">
                                            <strong>{{ $teamEm->member?->call_sign ?: $teamEm->member?->name }}</strong>
                                            <span style="color: var(--wo-text-muted);">({{ $teamEm->role }})</span>
                                            @if($teamEm->isCoordinator())
                                                <span class="pill-badge coord" style="padding: 2px 7px; font-size: 0.68rem;">Coordinator</span>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </article>
                </div>
            @empty
                <div class="empty-state">
                    <i class="bi bi-calendar-x"></i>
                    <p>Belum terdaftar di event manapun</p>
                </div>
            @endforelse
        </section>
    </div>

    @vite('resources/js/app.js')
    <script>
        function toggleTask(scheduleId, currentStatus) {
            const newStatus = currentStatus === 'approved' ? 'pending' : 'approved';
            updateStatus(scheduleId, newStatus);
        }

        function updateStatus(scheduleId, status) {
            fetch(`/member/dashboard/schedule/${scheduleId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status: status })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Gagal update status: ' + (data.message || 'Tidak diizinkan'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat update status');
                });
        }

        function assignSchedulePic(scheduleId, memberId) {
            fetch(`/member/dashboard/schedule/${scheduleId}/assign`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ member_id: memberId || null })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Gagal mengatur PIC tugas.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengatur PIC tugas.');
                });
        }

        function saveTeamAttendance(button, eventId, memberId) {
            const row = button.closest('.coord-team-row');
            if (!row) return;

            const attendanceStatus = row.querySelector('.team-att-status').value;
            const actualHours = parseFloat(row.querySelector('.team-actual-hours').value) || 0;

            if (actualHours < 0 || actualHours > 24) {
                alert('Jam kerja aktual harus antara 0 hingga 24 jam.');
                return;
            }

            button.disabled = true;

            fetch(`/member/dashboard/event/${eventId}/member/${memberId}/attendance`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    attendance_status: attendanceStatus,
                    actual_hours: actualHours
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message || 'Gagal menyimpan kehadiran tim.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan kehadiran tim.');
                })
                .finally(() => {
                    button.disabled = false;
                });
        }
    </script>
</body>

</html>
