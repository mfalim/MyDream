<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Member - {{ $member->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-900: #111827;
        }

        body {
            background: var(--gray-50);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--gray-900);
        }

        .navbar {
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary);
        }

        .member-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .member-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .member-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--gray-600);
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-900);
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .schedule-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.2s;
        }

        .schedule-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .schedule-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .schedule-title {
            font-weight: 600;
            font-size: 1.125rem;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
        }

        .schedule-event {
            color: var(--gray-600);
            font-size: 0.875rem;
        }

        .schedule-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            padding: 1rem;
            background: var(--gray-50);
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }

        .meta-item i {
            color: var(--gray-600);
        }

        .badge {
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-approved {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-actions {
            display: flex;
            gap: 0.5rem;
        }

        .status-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .status-btn-approve {
            background: var(--success);
            color: white;
        }

        .status-btn-reject {
            background: var(--danger);
            color: white;
        }

        .status-btn-pending {
            background: var(--warning);
            color: white;
        }

        .status-btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--gray-900);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--gray-600);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        .event-member-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .event-title {
            font-weight: 600;
            font-size: 1.125rem;
            margin-bottom: 0.5rem;
        }

        .event-meta {
            color: var(--gray-600);
            font-size: 0.875rem;
        }

        .role-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: var(--primary);
            color: white;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }

        .task-checklist {
            margin-top: 1rem;
            padding: 1rem;
            background: var(--gray-50);
            border-radius: 8px;
        }

        .task-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: white;
            border-radius: 6px;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .task-item:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .task-checkbox {
            width: 20px;
            height: 20px;
            border: 2px solid var(--gray-300);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .task-checkbox.checked {
            background: var(--success);
            border-color: var(--success);
            color: white;
        }

        .task-text {
            flex: 1;
            font-size: 0.875rem;
        }

        .task-text.completed {
            text-decoration: line-through;
            color: var(--gray-600);
        }

        .progress-bar-container {
            width: 100%;
            height: 8px;
            background: var(--gray-200);
            border-radius: 4px;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--success) 0%, #059669 100%);
            transition: width 0.3s ease;
        }

        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .task-progress-text {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-700);
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center w-100">
                <span class="navbar-brand">WO PROJECT - Member Area</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="member-header">
        <div class="container">
            <div class="member-info">
                <div class="member-avatar">
                    {{ strtoupper(substr($member->call_sign ?? $member->name, 0, 2)) }}
                </div>
                <div>
                    <h1 class="mb-1">{{ $member->name }}</h1>
                    <p class="mb-0 opacity-75">{{ $member->call_sign }} • {{ $member->position }}</p>
                    <p class="mb-0 opacity-75">{{ $member->division }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: #dbeafe; color: var(--primary);">
                    <i class="bi bi-list-check"></i>
                </div>
                <div class="stat-label">Total Jadwal</div>
                <div class="stat-value">{{ $stats['total_schedules'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #fef3c7; color: var(--warning);">
                    <i class="bi bi-clock"></i>
                </div>
                <div class="stat-label">Pending</div>
                <div class="stat-value">{{ $stats['pending'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #d1fae5; color: var(--success);">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-label">Approved</div>
                <div class="stat-value">{{ $stats['approved'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #fee2e2; color: var(--danger);">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div class="stat-label">Rejected</div>
                <div class="stat-value">{{ $stats['rejected'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #e9d5ff; color: #7c3aed;">
                    <i class="bi bi-calendar-event"></i>
                </div>
                <div class="stat-label">Event Terlibat</div>
                <div class="stat-value">{{ $stats['total_events'] }}</div>
            </div>
        </div>

        <h2 class="section-title">
            <i class="bi bi-list-task"></i> Jadwal Tugas Saya
        </h2>

        @forelse($schedules as $schedule)
            <div class="schedule-card" data-schedule-id="{{ $schedule->id }}">
                <div class="schedule-header">
                    <div>
                        <div class="schedule-title">{{ $schedule->activity }}</div>
                        <div class="schedule-event">
                            Event: <strong>{{ $schedule->event->name }}</strong>
                            @if($schedule->event->booking && $schedule->event->booking->client)
                                - {{ $schedule->event->booking->client->groom_name }} & {{ $schedule->event->booking->client->bride_name }}
                            @endif
                        </div>
                    </div>
                    <span class="badge badge-{{ $schedule->status }}">{{ $schedule->status }}</span>
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
                    <div class="mb-3">
                        <small class="text-muted"><i class="bi bi-sticky"></i> Catatan:</small>
                        <p class="mb-0">{{ $schedule->notes }}</p>
                    </div>
                @endif

                <div class="status-actions">
                    <button class="status-btn status-btn-approve" onclick="updateStatus({{ $schedule->id }}, 'approved')">
                        <i class="bi bi-check-circle"></i> Setuju
                    </button>
                    <button class="status-btn status-btn-pending" onclick="updateStatus({{ $schedule->id }}, 'pending')">
                        <i class="bi bi-clock"></i> Pending
                    </button>
                    <button class="status-btn status-btn-reject" onclick="updateStatus({{ $schedule->id }}, 'rejected')">
                        <i class="bi bi-x-circle"></i> Tolak
                    </button>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="bi bi-calendar-x"></i>
                <p>Belum ada jadwal yang ditugaskan</p>
            </div>
        @endforelse

        <h2 class="section-title mt-5">
            <i class="bi bi-calendar-event"></i> Event yang Saya Ikuti
        </h2>

        @forelse($eventMembers as $eventMember)
            <div class="event-member-card">
                <div class="event-title">{{ $eventMember->event->name }}</div>
                <div class="event-meta">
                    <i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($eventMember->event->event_date)->format('d M Y') }}
                    @if($eventMember->event->booking && $eventMember->event->booking->client)
                        | {{ $eventMember->event->booking->client->groom_name }} & {{ $eventMember->event->booking->client->bride_name }}
                    @endif
                </div>
                <span class="role-badge">{{ $eventMember->role }}</span>
                @if($eventMember->notes)
                    <div class="mt-2">
                        <small class="text-muted">Catatan: {{ $eventMember->notes }}</small>
                    </div>
                @endif

                @php
                    $eventSchedules = $eventMember->event->schedules->where('member_id', $member->id);
                    $totalTasks = $eventSchedules->count();
                    $completedTasks = $eventSchedules->where('status', 'approved')->count();
                    $progressPercent = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                @endphp

                @if($totalTasks > 0)
                    <div class="task-checklist">
                        <div class="task-header">
                            <span class="task-progress-text">
                                <i class="bi bi-list-check"></i> Checklist Tugas ({{ $completedTasks }}/{{ $totalTasks }})
                            </span>
                            <span class="task-progress-text">{{ $progressPercent }}%</span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" style="width: {{ $progressPercent }}%"></div>
                        </div>

                        <div style="margin-top: 1rem;">
                            @foreach($eventSchedules as $task)
                                <div class="task-item" onclick="toggleTask({{ $task->id }}, '{{ $task->status }}')">
                                    <div class="task-checkbox {{ $task->status === 'approved' ? 'checked' : '' }}" id="checkbox-{{ $task->id }}">
                                        @if($task->status === 'approved')
                                            <i class="bi bi-check-lg"></i>
                                        @endif
                                    </div>
                                    <div class="task-text {{ $task->status === 'approved' ? 'completed' : '' }}" id="text-{{ $task->id }}">
                                        {{ $task->activity }}
                                        <small class="d-block text-muted mt-1">
                                            <i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($task->start_time)->format('d M Y, H:i') }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="empty-state">
                <i class="bi bi-calendar-x"></i>
                <p>Belum terdaftar di event manapun</p>
            </div>
        @endforelse
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
