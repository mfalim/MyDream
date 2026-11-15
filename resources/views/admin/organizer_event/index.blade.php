@extends('layouts.admin')

@section('title', 'Event Organizer Internal')
@section('page-title', 'Event Organizer Internal')

@push('styles')
    @vite('resources/css/admin/organizer-events/index.css')
@endpush

@section('content')
<div class="org-event-page">
    {{-- Header --}}
    <div class="org-event-header">
        <div>
            <div class="eyebrow">AGENDA INTERNAL WO</div>
            <h2>Event Organizer Internal</h2>
            <p>Kelola jadwal technical meeting, briefing vendor, pelatihan kru, survey venue, dan koordinasi internal tim.</p>
        </div>
        <a href="{{ route('admin.organizer-event.create') }}" class="btn-emerald">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah Event Organizer</span>
        </a>
    </div>

    {{-- Stats Grid --}}
    <div class="org-stats-grid">
        <div class="org-stat-card">
            <div class="org-stat-info">
                <small>TOTAL AGENDA</small>
                <strong>{{ $stats['total'] ?? 0 }}</strong>
            </div>
            <div class="org-stat-icon" style="background: rgba(7, 61, 54, 0.08); color: #073d36;">
                <i class="bi bi-calendar-event-fill"></i>
            </div>
        </div>

        <div class="org-stat-card">
            <div class="org-stat-info">
                <small>TERJADWAL (SCHEDULED)</small>
                <strong style="color: #1d4ed8;">{{ $stats['scheduled'] ?? 0 }}</strong>
            </div>
            <div class="org-stat-icon" style="background: #eff6ff; color: #1d4ed8;">
                <i class="bi bi-clock-history"></i>
            </div>
        </div>

        <div class="org-stat-card">
            <div class="org-stat-info">
                <small>SELESAI (COMPLETED)</small>
                <strong style="color: #047857;">{{ $stats['completed'] ?? 0 }}</strong>
            </div>
            <div class="org-stat-icon" style="background: #ecfdf5; color: #047857;">
                <i class="bi bi-check-circle-fill"></i>
            </div>
        </div>

        <div class="org-stat-card">
            <div class="org-stat-info">
                <small>PRIORITAS TINGGI</small>
                <strong style="color: #b91c1c;">{{ $stats['high_priority'] ?? 0 }}</strong>
            </div>
            <div class="org-stat-icon" style="background: #fef2f2; color: #b91c1c;">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
        <form action="{{ route('admin.organizer-event.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-lg-4 col-md-12">
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-search"></i></span>
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        class="form-control border-start-0 ps-0"
                        placeholder="Cari judul, lokasi, atau peserta...">
                </div>
            </div>

            <div class="col-lg-3 col-md-4">
                <select name="type" class="form-select">
                    <option value="all" @selected(request('type', 'all') === 'all')>Semua Tipe Event</option>
                    <option value="technical_meeting" @selected(request('type') === 'technical_meeting')>Technical Meeting</option>
                    <option value="briefing" @selected(request('type') === 'briefing')>Briefing</option>
                    <option value="training" @selected(request('type') === 'training')>Training</option>
                    <option value="site_visit" @selected(request('type') === 'site_visit')>Site Visit</option>
                    <option value="vendor_meeting" @selected(request('type') === 'vendor_meeting')>Vendor Meeting</option>
                    <option value="consultation" @selected(request('type') === 'consultation')>Consultation</option>
                    <option value="evaluation" @selected(request('type') === 'evaluation')>Evaluation</option>
                    <option value="other" @selected(request('type') === 'other')>Lainnya</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-4">
                <select name="status" class="form-select">
                    <option value="all" @selected(request('status', 'all') === 'all')>Semua Status</option>
                    <option value="scheduled" @selected(request('status') === 'scheduled')>Scheduled</option>
                    <option value="completed" @selected(request('status') === 'completed')>Completed</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-4">
                <select name="priority" class="form-select">
                    <option value="all" @selected(request('priority', 'all') === 'all')>Semua Prioritas</option>
                    <option value="high" @selected(request('priority') === 'high')>High Priority</option>
                    <option value="medium" @selected(request('priority') === 'medium')>Medium</option>
                    <option value="low" @selected(request('priority') === 'low')>Low</option>
                </select>
            </div>

            <div class="col-lg-1 col-md-12 d-flex gap-1">
                <button type="submit" class="btn btn-outline-secondary w-100" title="Terapkan filter">
                    <i class="bi bi-funnel"></i>
                </button>
                @if(request()->filled('q') || (request('type') && request('type') !== 'all') || (request('status') && request('status') !== 'all') || (request('priority') && request('priority') !== 'all'))
                    <a href="{{ route('admin.organizer-event.index') }}" class="btn btn-light border text-muted" title="Reset filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Main Table Card --}}
    <div class="org-table-card">
        <div class="table-responsive">
            <table class="org-table">
                <thead>
                    <tr>
                        <th>Judul Event</th>
                        <th style="width: 170px;">Tipe</th>
                        <th style="width: 190px;">Jadwal &amp; Waktu</th>
                        <th>Lokasi</th>
                        <th style="width: 110px;">Prioritas</th>
                        <th style="width: 120px;">Status</th>
                        <th style="width: 120px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($organizerEvents as $event)
                        <tr>
                            <td>
                                <a href="{{ route('admin.organizer-event.show', $event->id) }}" class="fw-bold text-decoration-none" style="color: #173f39; font-size: 14.5px;">
                                    {{ $event->title }}
                                </a>
                                @if($event->description)
                                    <div class="text-muted small mt-1 text-truncate" style="max-width: 320px;">
                                        {{ $event->description }}
                                    </div>
                                @endif
                                @if($event->attendees)
                                    <div class="small mt-1" style="color: #55756d; font-size: 11.5px;">
                                        <i class="bi bi-people me-1"></i> {{ Str::limit($event->attendees, 40) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge-type-tag">
                                    {{ ucfirst(str_replace('_', ' ', $event->event_type)) }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold" style="color: #173f39;">
                                    <i class="bi bi-calendar3 me-1 text-muted"></i>
                                    {{ $event->event_date ? $event->event_date->translatedFormat('d M Y') : '-' }}
                                </div>
                                <div class="text-muted small">
                                    <i class="bi bi-clock me-1"></i>
                                    @if($event->start_time)
                                        {{ is_string($event->start_time) ? substr($event->start_time, 0, 5) : $event->start_time->format('H:i') }}
                                        @if($event->end_time)
                                            - {{ is_string($event->end_time) ? substr($event->end_time, 0, 5) : $event->end_time->format('H:i') }}
                                        @endif
                                        WIB
                                    @else
                                        Waktu fleksibel
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1 text-muted small">
                                    <i class="bi bi-geo-alt"></i>
                                    <span>{{ $event->location ?: 'Internal Office' }}</span>
                                </div>
                            </td>
                            <td>
                                @if($event->priority === 'high')
                                    <span class="badge-priority-high"><i class="bi bi-fire"></i> High</span>
                                @elseif($event->priority === 'medium')
                                    <span class="badge-priority-medium">Medium</span>
                                @else
                                    <span class="badge-priority-low">Low</span>
                                @endif
                            </td>
                            <td>
                                @if($event->status === 'completed')
                                    <span class="badge-status-completed"><i class="bi bi-check2"></i> Selesai</span>
                                @elseif($event->status === 'cancelled')
                                    <span class="badge-status-cancelled"><i class="bi bi-x"></i> Dibatalkan</span>
                                @else
                                    <span class="badge-status-scheduled"><i class="bi bi-clock"></i> Terjadwal</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.organizer-event.show', $event->id) }}" class="btn-action-icon" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.organizer-event.edit', $event->id) }}" class="btn-action-icon" title="Edit Agenda">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.organizer-event.destroy', $event->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus agenda event ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-icon delete" title="Hapus Agenda">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-calendar-x text-muted fs-1 d-block mb-2"></i>
                                    <p class="text-muted mb-0">Belum ada agenda event organizer yang sesuai dengan filter atau pencarian.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($organizerEvents->hasPages())
            <div class="d-flex flex-wrap justify-content-between align-items-center px-4 py-3 border-top bg-light">
                <div class="text-muted small">
                    Menampilkan <strong>{{ $organizerEvents->firstItem() ?? 0 }}-{{ $organizerEvents->lastItem() ?? 0 }}</strong> dari <strong>{{ $organizerEvents->total() }}</strong> event
                </div>
                <div>
                    {{ $organizerEvents->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
