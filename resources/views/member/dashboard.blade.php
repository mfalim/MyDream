<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Member - {{ $member->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/member/shared.css', 'resources/css/member/dashboard.css'])
</head>

@php
    $currentStatus = in_array($member->status, ['aktif', 'standby', 'inaktif'], true) ? $member->status : 'standby';
    $statusMeta = [
        'aktif' => [
            'label' => 'Aktif',
            'hint' => 'Anda tampil sebagai kru siap tugas di Tracking Admin.',
        ],
        'standby' => [
            'label' => 'Standby',
            'hint' => 'Anda tampil sebagai kru siaga (standby) di Tracking Admin.',
        ],
        'inaktif' => [
            'label' => 'Tidak Aktif',
            'hint' => 'Anda sedang off dan tidak muncul di penugasan event baru.',
        ],
    ];
    $divisionLabel = $member->division ? ucwords(str_replace('-', ' ', $member->division)) : '-';
@endphp

<body class="member-portal">
    <x-login-curtain subtitle="Crew Workspace" />

    {{-- STICKY TOP NAVBAR (Seragam dengan gaya Landing Page MyDream, tanpa sidebar kosong) --}}
    <header class="member-navbar">
        <div class="member-navbar-inner">
            <a href="{{ route('member.dashboard') }}" class="member-navbar-brand">
                <span class="member-navbar-mark"><i class="bi bi-stars"></i></span>
                <span class="member-navbar-title">
                    <strong>MyDream</strong>
                    <small>Crew Workspace</small>
                </span>
            </a>

            <div class="member-navbar-actions">
                <span class="status-dot-badge status-{{ $currentStatus }}" id="topbarStatusBadge">
                    <span class="dot"></span>
                    <span class="status-text">{{ $statusMeta[$currentStatus]['label'] }}</span>
                </span>

                <a href="{{ route('home') }}" target="_blank" rel="noopener" class="member-nav-link" title="Kunjungi Website">
                    <i class="bi bi-house"></i>
                    <span>Website</span>
                </a>

                <div class="member-nav-user">
                    <span class="member-nav-avatar">{{ strtoupper(substr($member->call_sign ?? $member->name, 0, 1)) }}</span>
                    <span class="member-nav-name">{{ $member->name }}</span>
                </div>

                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="member-nav-logout" title="Keluar Sistem">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="member-workspace">

        <section class="member-hero">
            <div class="member-hero-left">
                <div class="member-hero-content">
                    <div class="member-avatar-large">
                        {{ strtoupper(substr($member->call_sign ?? $member->name, 0, 2)) }}
                    </div>
                    <div class="member-hero-copy">
                        <div class="member-hero-eyebrow">
                            <i class="bi bi-circle-fill" style="font-size: 0.42rem;"></i>
                            Tim Operasional Wedding Organizer
                        </div>
                        <h1>{{ $member->name }}</h1>
                        <p>
                            {{ $member->call_sign }}
                            <span class="sep">•</span>
                            {{ $member->position }}
                            <span class="sep">•</span>
                            {{ $divisionLabel }}
                        </p>
                    </div>
                </div>

                <div class="member-hero-meta">
                    <span class="hero-pill"><i class="bi bi-calendar-heart"></i> {{ $stats['total_events'] }} Event</span>
                    @if(($stats['coordinator_events'] ?? 0) > 0)
                        <span class="hero-pill"><i class="bi bi-shield-check"></i> {{ $stats['coordinator_events'] }} Lead Coordinator</span>
                    @endif
                    <span class="hero-pill"><i class="bi bi-list-check"></i> {{ $stats['total_schedules'] }} Tugas Saya</span>
                </div>
            </div>

            <div class="availability-card">
                <div class="availability-card-head">
                    <span class="availability-label">Status Ketersediaan</span>
                    <span class="status-dot-badge status-{{ $currentStatus }}" id="heroStatusBadge">
                        <span class="dot"></span>
                        <span class="status-text">{{ $statusMeta[$currentStatus]['label'] }}</span>
                    </span>
                </div>

                <div class="availability-switcher" role="group" aria-label="Ubah status ketersediaan member">
                    <button type="button"
                        class="availability-btn {{ $currentStatus === 'aktif' ? 'is-active' : '' }}"
                        data-status="aktif"
                        onclick="updateMemberStatus('aktif')">
                        <i class="bi bi-check-circle-fill"></i> Aktif
                    </button>
                    <button type="button"
                        class="availability-btn {{ $currentStatus === 'standby' ? 'is-active' : '' }}"
                        data-status="standby"
                        onclick="updateMemberStatus('standby')">
                        <i class="bi bi-clock-fill"></i> Standby
                    </button>
                    <button type="button"
                        class="availability-btn {{ $currentStatus === 'inaktif' ? 'is-active' : '' }}"
                        data-status="inaktif"
                        onclick="updateMemberStatus('inaktif')">
                        <i class="bi bi-moon-fill"></i> Off
                    </button>
                </div>

                <p class="availability-hint" id="availabilityHint">
                    {{ $statusMeta[$currentStatus]['hint'] }}
                </p>
            </div>
        </section>

        <div class="member-stats">
            <div class="member-stat-card">
                <div class="stat-icon" style="background: var(--wo-primary-soft); color: var(--wo-primary-mid);">
                    <i class="bi bi-calendar-event"></i>
                </div>
                <div class="stat-label">Event Diikuti</div>
                <p class="stat-value">{{ $stats['total_events'] }}</p>
            </div>

            <div class="member-stat-card">
                <div class="stat-icon" style="background: var(--wo-gold-soft); color: var(--wo-gold-bg);">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-label">Tugas Saya</div>
                <p class="stat-value">{{ $stats['total_schedules'] }}</p>
            </div>

            <div class="member-stat-card">
                <div class="stat-icon" style="background: var(--wo-success-soft); color: var(--wo-success);">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-label">Tugas Selesai</div>
                <p class="stat-value">{{ $stats['approved'] }}</p>
            </div>

            <div class="member-stat-card">
                <div class="stat-icon" style="background: var(--wo-warning-soft); color: var(--wo-warning);">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="stat-label">Daily Fee / ACC</div>
                <p class="stat-value">Rp {{ number_format((float) ($member->daily_fee ?? 0), 0, ',', '.') }}</p>
                <small class="stat-sub">
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

        <div class="member-content-grid">
            <section class="member-primary-col" id="penugasan-event">
                <div class="wo-eyebrow">
                    <i class="bi bi-circle-fill" style="font-size: 0.42rem;"></i>
                    Penugasan &amp; Checklist Lapangan
                </div>
                <h2 class="section-heading"><i class="bi bi-calendar-check"></i>Event &amp; Checklist Tugas Saya</h2>

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

                <div class="member-event-list">
                    <article class="member-event-card">
                        <div class="event-head">
                            <div>
                                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
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
                                @if($eventMember->payout_proof_url)
                                    <a href="{{ $eventMember->payout_proof_url }}" target="_blank" class="pill-badge pay-approved">
                                        <i class="bi bi-receipt"></i> Lihat Bukti Transfer
                                    </a>
                                @endif
                            </div>
                        </div>

                        @if($eventMember->notes)
                            <div class="schedule-notes">
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

                        {{-- Bagian 2: Tim Event --}}
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
                            <div class="coord-panel" style="background: var(--wo-surface-soft);">
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
                    <i class="bi bi-calendar-heart"></i>
                    <h3>Belum Ada Penugasan Event</h3>
                    <p>Pastikan status ketersediaan Anda <strong>Aktif</strong> atau <strong>Standby</strong> agar Admin dapat menugaskan Anda ke event mendatang.</p>
                </div>
            @endforelse
            </section>

            {{-- KOLOM KANAN: Panel Info Sticky (Tugas Hari Ini, Agenda Terdekat, Profil & Honor) --}}
            <aside class="member-side-col" id="profil-agenda">
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
                        <div class="mini-empty-state">
                            <i class="bi bi-sun"></i>
                            <span>Tidak ada tugas terjadwal hari ini.</span>
                        </div>
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
                        <div class="mini-empty-state">
                            <i class="bi bi-calendar2-week"></i>
                            <span>Belum ada agenda event mendatang.</span>
                        </div>
                    @endif
                </div>

                <div class="member-summary-panel">
                    <div class="summary-panel-header">
                        <span><i class="bi bi-person-badge"></i> Profil &amp; Rekening Honor</span>
                    </div>
                    <ul class="summary-list">
                        <li><span>Divisi</span><strong>{{ $divisionLabel }}</strong></li>
                        <li><span>Jabatan</span><strong>{{ $member->position ?? '-' }}</strong></li>
                        <li><span>Spesialisasi</span><strong>{{ $member->specialization ?? '-' }}</strong></li>
                        <li><span>Rekening Honor</span><strong>{{ $member->formatted_bank_account ?? 'Belum Diisi' }}</strong></li>
                        <li>
                            <span>Status</span>
                            <span class="status-dot-badge status-{{ $currentStatus }}" id="profileStatusBadge">
                                <span class="dot"></span>
                                <span class="status-text">{{ $statusMeta[$currentStatus]['label'] }}</span>
                            </span>
                        </li>
                    </ul>
                </div>
            </aside>
        </div>
    </main>

    <x-mydream-toast />

    @vite('resources/js/app.js')
    <script>
        function updateMemberStatus(status) {
            const buttons = document.querySelectorAll('.availability-btn');
            buttons.forEach(btn => btn.disabled = true);

            fetch('{{ route('member.status.update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status })
            })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        showToast(data.message || 'Gagal memperbarui status.', true);
                        return;
                    }

                    buttons.forEach(btn => {
                        btn.classList.toggle('is-active', btn.dataset.status === data.status);
                    });

                    ['topbarStatusBadge', 'heroStatusBadge', 'profileStatusBadge'].forEach(id => {
                        const badge = document.getElementById(id);
                        if (!badge) return;
                        badge.classList.remove('status-aktif', 'status-standby', 'status-inaktif');
                        badge.classList.add(`status-${data.status}`);
                        const textEl = badge.querySelector('.status-text');
                        if (textEl) textEl.textContent = data.label;
                    });

                    const hint = document.getElementById('availabilityHint');
                    if (hint && data.hint) {
                        hint.textContent = data.hint;
                    }

                    showToast(data.message || 'Status ketersediaan diperbarui.');
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Terjadi kesalahan saat memperbarui status.', true);
                })
                .finally(() => {
                    buttons.forEach(btn => btn.disabled = false);
                });
        }

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
                        showToast(data.message || 'Gagal update status tugas.', true);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Terjadi kesalahan saat update status.', true);
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
                        showToast(data.message || 'Gagal mengatur PIC tugas.', true);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Terjadi kesalahan saat mengatur PIC tugas.', true);
                });
        }

        function saveTeamAttendance(button, eventId, memberId) {
            const row = button.closest('.coord-team-row');
            if (!row) return;

            const attendanceStatus = row.querySelector('.team-att-status').value;
            const actualHours = parseFloat(row.querySelector('.team-actual-hours').value) || 0;

            if (actualHours < 0 || actualHours > 24) {
                showToast('Jam kerja aktual harus antara 0 hingga 24 jam.', true);
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
                        showToast(data.message || 'Kehadiran tim berhasil disimpan.');
                        setTimeout(() => location.reload(), 600);
                    } else {
                        showToast(data.message || 'Gagal menyimpan kehadiran tim.', true);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Terjadi kesalahan saat menyimpan kehadiran tim.', true);
                })
                .finally(() => {
                    button.disabled = false;
                });
        }
    </script>
</body>

</html>
