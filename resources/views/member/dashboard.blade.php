<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Member - {{ $member->name }}</title>
    @vite(['resources/css/member/shared.css', 'resources/css/member/dashboard.css'])
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
                <span class="hero-pill"><i class="bi bi-list-check"></i> {{ $stats['total_schedules'] }} Jadwal</span>
            </div>
        </section>

        <div class="member-stats">
            <div class="member-stat-card">
                <div class="stat-icon" style="background:#dfeafc; color:#2454d3;">
                    <i class="bi bi-list-check"></i>
                </div>
                <div class="stat-label">Total Jadwal</div>
                <p class="stat-value">{{ $stats['total_schedules'] }}</p>
            </div>

            <div class="member-stat-card">
                <div class="stat-icon" style="background:#fff2d9; color:#b77b0d;">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-label">Pending</div>
                <p class="stat-value">{{ $stats['pending'] }}</p>
            </div>

            <div class="member-stat-card">
                <div class="stat-icon" style="background:#dff7ee; color:#0e7d57;">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-label">Approved</div>
                <p class="stat-value">{{ $stats['approved'] }}</p>
            </div>

            <div class="member-stat-card">
                <div class="stat-icon" style="background:#fee7e5; color:#c1504e;">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div class="stat-label">Rejected</div>
                <p class="stat-value">{{ $stats['rejected'] }}</p>
            </div>

            <div class="member-stat-card">
                <div class="stat-icon" style="background:#eeebff; color:#5d4ac6;">
                    <i class="bi bi-calendar-event"></i>
                </div>
                <div class="stat-label">Event Terlibat</div>
                <p class="stat-value">{{ $stats['total_events'] }}</p>
            </div>
        </div>

        <section>
            <h2 class="section-heading"><i class="bi bi-list-task"></i>Jadwal Tugas Saya</h2>

            @forelse($schedules as $schedule)
                <div class="member-schedule-list">
                    <article class="member-schedule-card" data-schedule-id="{{ $schedule->id }}">
                        <div class="schedule-head">
                            <div>
                                <h3 class="schedule-title">{{ $schedule->activity }}</h3>
                                <div class="schedule-subtitle">
                                    Event: <strong>{{ $schedule->event->name }}</strong>
                                    @if($schedule->event->booking && $schedule->event->booking->client)
                                        • {{ $schedule->event->booking->client->groom_name }} & {{ $schedule->event->booking->client->bride_name }}
                                    @endif
                                </div>
                            </div>
                            <span class="schedule-badge {{ $schedule->status }}">{{ $schedule->status }}</span>
                        </div>

                        <div class="schedule-meta">
                            <div class="meta-item">
                                <i class="bi bi-calendar3"></i>
                                <span>{{ \Carbon\Carbon::parse($schedule->start_time)->format('d M Y') }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="bi bi-clock"></i>
                                <span>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="bi bi-geo-alt"></i>
                                <span>{{ $schedule->location ?? '-' }}</span>
                            </div>
                            @if($schedule->vendor)
                                <div class="meta-item">
                                    <i class="bi bi-shop"></i>
                                    <span>Vendor: {{ $schedule->vendor->name }}</span>
                                </div>
                            @endif
                        </div>

                        @if($schedule->notes)
                            <div class="schedule-notes">
                                <small><i class="bi bi-sticky"></i> Catatan</small>
                                <div>{{ $schedule->notes }}</div>
                            </div>
                        @endif

                        <div class="schedule-actions">
                            <button class="status-action approve" type="button" onclick="updateStatus({{ $schedule->id }}, 'approved')">
                                <i class="bi bi-check-circle"></i>
                                Setuju
                            </button>
                            <button class="status-action pending" type="button" onclick="updateStatus({{ $schedule->id }}, 'pending')">
                                <i class="bi bi-clock"></i>
                                Pending
                            </button>
                            <button class="status-action reject" type="button" onclick="updateStatus({{ $schedule->id }}, 'rejected')">
                                <i class="bi bi-x-circle"></i>
                                Tolak
                            </button>
                        </div>
                    </article>
                </div>
            @empty
                <div class="empty-state">
                    <i class="bi bi-calendar-x"></i>
                    <p>Belum ada jadwal yang ditugaskan</p>
                </div>
            @endforelse
        </section>

        <section class="mt-5">
            <h2 class="section-heading"><i class="bi bi-calendar-event"></i>Event yang Saya Ikuti</h2>

            @forelse($eventMembers as $eventMember)
                <div class="member-event-list">
                    <article class="member-event-card">
                        <div class="event-head">
                            <div>
                                <h3 class="event-title">{{ $eventMember->event->name }}</h3>
                                <div class="event-subtitle">
                                    <i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($eventMember->event->event_date)->format('d M Y') }}
                                    @if($eventMember->event->booking && $eventMember->event->booking->client)
                                        • {{ $eventMember->event->booking->client->groom_name }} & {{ $eventMember->event->booking->client->bride_name }}
                                    @endif
                                </div>
                            </div>
                            <span class="role-badge approved">{{ $eventMember->role }}</span>
                        </div>

                        @if($eventMember->notes)
                            <div class="schedule-notes mb-3">
                                <small><i class="bi bi-chat-left-text"></i> Catatan</small>
                                <div>{{ $eventMember->notes }}</div>
                            </div>
                        @endif

                        @php
                            $eventSchedules = $eventMember->event->schedules
                                ->filter(fn ($task) => $task->vendor_id !== null);
                            $totalTasks = $eventSchedules->count();
                            $completedTasks = $eventSchedules->where('status', 'approved')->count();
                            $progressPercent = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                        @endphp

                        @if($totalTasks > 0)
                            <div class="task-checklist">
                                <div class="task-header">
                                    <span><i class="bi bi-list-check"></i> Checklist Vendor ({{ $completedTasks }}/{{ $totalTasks }})</span>
                                    <span>{{ $progressPercent }}%</span>
                                </div>

                                <div class="progress-track">
                                    <div class="progress-fill" style="width: {{ $progressPercent }}%"></div>
                                </div>

                                <div class="task-list">
                                    @foreach($eventSchedules as $task)
                                        <div class="task-item" onclick="toggleTask({{ $task->id }}, '{{ $task->status }}')">
                                            <div class="task-checkbox {{ $task->status === 'approved' ? 'checked' : '' }}" id="checkbox-{{ $task->id }}">
                                                @if($task->status === 'approved')
                                                    <i class="bi bi-check-lg"></i>
                                                @endif
                                            </div>
                                            <div class="task-text {{ $task->status === 'approved' ? 'completed' : '' }}" id="text-{{ $task->id }}">
                                                {{ $task->activity }}
                                                @if($task->vendor)
                                                    <small><i class="bi bi-shop"></i> {{ $task->vendor->name }}</small>
                                                @endif
                                                <small><i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($task->start_time)->format('d M Y, H:i') }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="text-muted mt-3 small">
                                <i class="bi bi-info-circle"></i> Belum ada vendor schedule untuk event ini.
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

            fetch(`/member/dashboard/schedule/${scheduleId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status: newStatus })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Gagal update status: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat update status');
                });
        }

        function updateStatus(scheduleId, status) {
            if (!confirm('Yakin update status menjadi ' + status.toUpperCase() + '?')) {
                return;
            }

            fetch(`/member/dashboard/schedule/${scheduleId}/status`, {
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
                    } else {
                        alert('Gagal update status: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat update status');
                });
        }
    </script>
</body>

</html>
