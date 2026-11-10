@extends('layouts.admin')

@section('title', 'Event Day (Hari H Acara)')
@section('page-title', 'Event Day — Kontrol Hari H')

@push('styles')
    @vite('resources/css/admin/events.css')
@endpush

@section('content')
<div class="eventday-page">
    {{-- Header --}}
    <div class="eventday-header">
        <div>
            <div class="eventday-eyebrow">Live Execution & Kontrol Hari H</div>
            <h2>Event Day (Hari Pelaksanaan Acara)</h2>
            <p>Pusat kendali operasional live hari H: pantau status pelaksanaan acara, timeline jam acara, koordinator on-duty, kru pelaksana, dan vendor bertugas.</p>
        </div>
        <a href="{{ route('admin.events.create') }}" class="btn-wo-primary">
            <i class="bi bi-plus-lg"></i> Buat Acara Baru
        </a>
    </div>

    {{-- Stat Cards --}}
    @php
        $totalEvents = $events->count();
        $todayEvents = $events->filter(function($e) {
            return ($e->event_date && $e->event_date->isToday()) || $e->status === 'ongoing';
        })->count();
        $upcomingEvents = $events->where('status', 'upcoming')->count();
        $completedEvents = $events->where('status', 'completed')->count();
    @endphp
    <div class="eventday-stats-grid">
        <div class="eventday-stat-card">
            <div class="eventday-stat-info">
                <small>Total Seluruh Acara</small>
                <strong>{{ $totalEvents }}</strong>
            </div>
            <div class="eventday-stat-icon" style="background: #edf4f1; color: #073d36;">
                <i class="bi bi-calendar-event"></i>
            </div>
        </div>

        <div class="eventday-stat-card">
            <div class="eventday-stat-info">
                <small>Live / Hari Ini</small>
                <strong style="color: #15803d;">{{ $todayEvents }}</strong>
            </div>
            <div class="eventday-stat-icon" style="background: #dff7ee; color: #15803d;">
                <i class="bi bi-broadcast"></i>
            </div>
        </div>

        <div class="eventday-stat-card">
            <div class="eventday-stat-info">
                <small>Acara Mendatang</small>
                <strong style="color: #073d36;">{{ $upcomingEvents }}</strong>
            </div>
            <div class="eventday-stat-icon" style="background: #e7f1ea; color: #073d36;">
                <i class="bi bi-clock-history"></i>
            </div>
        </div>

        <div class="eventday-stat-card">
            <div class="eventday-stat-info">
                <small>Acara Selesai</small>
                <strong style="color: #6b7c75;">{{ $completedEvents }}</strong>
            </div>
            <div class="eventday-stat-icon" style="background: #f1f6f3; color: #6b7c75;">
                <i class="bi bi-check2-all"></i>
            </div>
        </div>
    </div>

    {{-- Toolbar Search & Filter Tabs --}}
    <div class="eventday-toolbar">
        <div class="eventday-search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="searchInput" placeholder="Cari nama pasangan pengantin, acara, venue, atau koordinator...">
        </div>

        <div class="eventday-filter-tabs" id="statusTabs">
            <button type="button" class="eventday-filter-tab active" data-status="">
                Semua Acara ({{ $totalEvents }})
            </button>
            <button type="button" class="eventday-filter-tab" data-status="ongoing">
                🔴 Live / Hari Ini ({{ $todayEvents }})
            </button>
            <button type="button" class="eventday-filter-tab" data-status="upcoming">
                📅 Mendatang ({{ $upcomingEvents }})
            </button>
            <button type="button" class="eventday-filter-tab" data-status="completed">
                ✅ Selesai ({{ $completedEvents }})
            </button>
        </div>
    </div>

    {{-- Event Grid Cards --}}
    <div class="event-grid" id="eventGrid">
        @forelse($events as $event)
            @php
                $booking = $event->booking;
                $client = $booking ? $booking->client : null;
                $leadCoordinatorEm = $event->eventMembers->first(fn($em) => $em->isCoordinator()) ?? $event->eventMembers->first();
                $leadCoordinator = $leadCoordinatorEm?->member;
                $crewCount = $event->eventMembers->filter(fn($em) => !$em->isCoordinator())->count();

                $eventPhotoUrl = null;
                if (!empty($event->photo)) {
                    $eventPhotoFile = storage_path('app/public/' . $event->photo);
                    $eventPhotoUrl = asset('storage/' . $event->photo);
                    if (file_exists($eventPhotoFile)) {
                        $eventPhotoUrl .= '?v=' . filemtime($eventPhotoFile);
                    }
                }

                $effectiveStart = $event->effective_start_time;
                $effectiveEnd = $event->effective_end_time;
                $timeLabel = $effectiveStart && $effectiveEnd
                    ? date('H:i', strtotime($effectiveStart)) . ' - ' . date('H:i', strtotime($effectiveEnd)) . ' WIB'
                    : ($effectiveStart ? date('H:i', strtotime($effectiveStart)) . ' WIB' : 'Waktu belum diatur');

                $isLiveToday = ($event->event_date && $event->event_date->isToday()) || $event->status === 'ongoing';
                $cardStatusKey = $isLiveToday ? 'ongoing' : $event->status;

                $searchText = strtolower(
                    ($client ? ($client->groom_name . ' ' . $client->bride_name) : '') . ' ' .
                    $event->name . ' ' .
                    ($event->event_type ?? '') . ' ' .
                    ($booking ? ($booking->venue_name . ' ' . $booking->venue_city) : '') . ' ' .
                    ($leadCoordinator ? ($leadCoordinator->name . ' ' . $leadCoordinator->call_sign) : '')
                );
            @endphp
            <div class="wo-event-card" data-search="{{ $searchText }}" data-status="{{ $cardStatusKey }}">
                {{-- Media Cover --}}
                <div class="event-card-media">
                    @if ($eventPhotoUrl)
                        <img src="{{ $eventPhotoUrl }}" class="event-card-img" alt="{{ $event->name }}">
                    @else
                        <div class="event-card-no-img">
                            <i class="bi bi-calendar2-heart"></i>
                            <span style="font-size: 12px; font-weight: 700; color: #748d85; letter-spacing: 0.04em;">WO MYDREAM</span>
                        </div>
                    @endif

                    {{-- Status Badge --}}
                    @if ($isLiveToday)
                        <span class="event-status-badge ongoing">
                            <span class="pulsing-dot"></span> LIVE HARI H
                        </span>
                    @elseif ($event->status === 'upcoming')
                        <span class="event-status-badge upcoming">Mendatang</span>
                    @elseif ($event->status === 'completed')
                        <span class="event-status-badge completed">Selesai</span>
                    @elseif ($event->status === 'draft')
                        <span class="event-status-badge draft">Draft</span>
                    @elseif ($event->status === 'cancelled')
                        <span class="event-status-badge cancelled">Dibatalkan</span>
                    @endif

                    {{-- Time Badge --}}
                    <span class="event-time-badge">
                        <i class="bi bi-clock"></i> {{ $timeLabel }}
                    </span>
                </div>

                {{-- Card Body --}}
                <div class="event-card-body">
                    <div class="event-card-meta-top">
                        <div class="event-card-date">
                            <i class="bi bi-calendar3"></i>
                            {{ $event->event_date ? $event->event_date->format('d M Y') : 'Tanggal belum diatur' }}
                        </div>
                        @if ($event->event_type)
                            <span class="event-type-pill">
                                {{ ucfirst(str_replace('_', ' ', $event->event_type)) }}
                            </span>
                        @endif
                    </div>

                    {{-- Couple Name --}}
                    @if ($client)
                        <h4 class="event-couple-title" title="{{ $client->groom_name }} & {{ $client->bride_name }}">
                            {{ $client->groom_name }} & {{ $client->bride_name }}
                        </h4>
                    @else
                        <h4 class="event-couple-title">Klien Belum Diatur</h4>
                    @endif

                    <div class="event-name-sub">
                        {{ $event->name }}
                    </div>

                    {{-- Venue --}}
                    <div class="event-venue-block">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span class="text-truncate">
                            {{ $booking ? $booking->venue_name : 'Venue belum ditentukan' }}
                            @if ($booking && $booking->venue_city)
                                • {{ $booking->venue_city }}
                            @endif
                        </span>
                    </div>

                    {{-- Koordinator Lapangan On-Duty --}}
                    <div class="event-coord-strip">
                        @if ($leadCoordinator)
                            <div class="coord-avatar-circle">
                                {{ strtoupper(substr($leadCoordinator->call_sign ?: $leadCoordinator->name, 0, 2)) }}
                            </div>
                            <div class="coord-info-text min-w-0">
                                <small>Koordinator On-Duty</small>
                                <strong class="d-block text-truncate">
                                    {{ $leadCoordinator->name }} ({{ $leadCoordinator->call_sign ?: '-' }})
                                </strong>
                            </div>
                        @else
                            <div class="coord-avatar-circle" style="background:#e4ebe8; color:#74847c;">
                                <i class="bi bi-person"></i>
                            </div>
                            <div class="coord-info-text">
                                <small>Koordinator On-Duty</small>
                                <strong class="text-muted">Belum Ditugaskan</strong>
                            </div>
                        @endif
                    </div>

                    {{-- Stat Tim & Vendor --}}
                    <div class="event-stats-row">
                        <span class="event-stat-tag">
                            <i class="bi bi-people-fill"></i> {{ $event->eventMembers->count() }} Kru Internal
                        </span>
                        <span class="event-stat-tag" style="background: #f6ebd7; color: #7d5b2b;">
                            <i class="bi bi-shop"></i> {{ $event->schedules->count() }} Vendor
                        </span>
                    </div>

                    {{-- Vendor Mini Chips --}}
                    <div class="vendor-tags-flow">
                        @forelse($event->schedules->take(2) as $schedule)
                            <span class="vendor-mini-chip">
                                {{ $schedule->vendor ? $schedule->vendor->name : ($schedule->activity ?: 'Vendor') }}
                            </span>
                        @empty
                            <span class="text-muted" style="font-size: 12px;">Belum ada vendor terdaftar</span>
                        @endforelse

                        @if ($event->schedules->count() > 2)
                            <span class="vendor-mini-chip" style="background: #e7f1ea; color: #073d36; font-weight: 700;">
                                +{{ $event->schedules->count() - 2 }} vendor lagi
                            </span>
                        @endif
                    </div>

                    {{-- Card Actions --}}
                    <div class="event-card-actions">
                        <button type="button" class="btn-wo-primary open-detail-modal-btn" data-event-id="{{ $event->id }}">
                            <i class="bi bi-eye"></i> Rincian Acara
                        </button>
                        <a href="{{ route('admin.events.edit', $event->id) }}" class="btn-icon-soft" title="Edit Acara">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="{{ route('admin.tracking.show', $event->id) }}" class="btn-icon-soft" title="Tracking Checklist & Absensi Kru">
                            <i class="bi bi-check2-square"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 py-5 text-center">
                <i class="bi bi-calendar-x fs-1 text-muted d-block mb-2"></i>
                <h5 class="text-muted">Belum ada acara terdaftar</h5>
                <p class="text-muted small">Klik tombol "Buat Acara Baru" untuk memulai penjadwalan hari H.</p>
                <a href="{{ route('admin.events.create') }}" class="btn-wo-primary">
                    <i class="bi bi-plus-lg"></i> Buat Acara Baru
                </a>
            </div>
        @endforelse
    </div>
</div>

{{-- Modal Detail Acara Hari H (Tema Botanical Emerald Green & Warm Gold) --}}
<div class="modal fade eventday-modal" id="eventDetailModal" tabindex="-1" aria-labelledby="eventDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="eventDetailModalLabel">Rincian Operasional Acara Hari H</h5>
                    <small class="text-white-50">Detail pelaksanaan lapangan, timeline jam, koordinator, tim kru, dan vendor rekanan.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalEventDetailContent">
                {{-- Konten diisi dinamis via JavaScript --}}
            </div>
            <div class="modal-footer" id="modalEventDetailFooter">
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusTabs = document.querySelectorAll('#statusTabs .eventday-filter-tab');
    const cards = document.querySelectorAll('#eventGrid .wo-event-card');
    let selectedStatus = '';

    function filterCards() {
        const searchTerm = (searchInput?.value || '').toLowerCase().trim();

        cards.forEach(card => {
            const searchText = card.getAttribute('data-search') || '';
            const cardStatus = card.getAttribute('data-status') || '';

            const matchesSearch = !searchTerm || searchText.includes(searchTerm);
            const matchesStatus = !selectedStatus || cardStatus === selectedStatus;

            card.style.display = (matchesSearch && matchesStatus) ? 'flex' : 'none';
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterCards);
    }

    statusTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            statusTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            selectedStatus = this.getAttribute('data-status') || '';
            filterCards();
        });
    });

    // Handler Buka Modal Detail Acara
    document.querySelectorAll('.open-detail-modal-btn').forEach(button => {
        button.addEventListener('click', function() {
            const eventId = this.getAttribute('data-event-id');
            showEventDetailModal(eventId);
        });
    });

    function showEventDetailModal(eventId) {
        const modalEl = document.getElementById('eventDetailModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        const modalBody = document.getElementById('modalEventDetailContent');
        const modalFooter = document.getElementById('modalEventDetailFooter');

        modalBody.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-success" role="status"></div>
                <div class="small text-muted mt-2">Memuat rincian acara...</div>
            </div>
        `;
        modal.show();

        fetch(`/admin/events/${eventId}/detail`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                modalBody.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                return;
            }

            const info = data;
            const statusLabel = String(info.status || 'upcoming').toUpperCase();
            const statusPillStyle = info.status === 'ongoing' 
                ? 'background:#1b9e6a; color:#fff;'
                : (info.status === 'completed' ? 'background:#52665e; color:#fff;' : 'background:#073d36; color:#fff;');

            // Render Vendor List
            let vendorRows = '';
            if (info.vendors && info.vendors.length > 0) {
                info.vendors.forEach(v => {
                    const timeRange = (v.start_time && v.end_time) ? `${v.start_time} - ${v.end_time}` : (v.start_time || 'Hari H');
                    const statusBadgeClass = v.status === 'approved' 
                        ? 'background:#dff7ee; color:#0d6e4c;' 
                        : (v.status === 'rejected' ? 'background:#fee2e2; color:#991b1b;' : 'background:#fff3d6; color:#966209;');

                    vendorRows += `
                        <div class="modal-item-row">
                            <div>
                                <strong style="color: #173b32; font-size: 14.5px;">${v.name}</strong>
                                <small class="text-muted d-block" style="font-size: 12.5px;">${v.category} • Waktu: ${timeRange}</small>
                            </div>
                            <span class="badge" style="${statusBadgeClass}; font-size: 12px; font-weight: 700; padding: 4px 10px;">
                                ${String(v.status || 'pending').toUpperCase()}
                            </span>
                        </div>
                    `;
                });
            } else {
                vendorRows = `<div class="text-muted small py-2" style="font-size: 13px;">Belum ada vendor rekanan yang bertugas di acara ini.</div>`;
            }

            // Render Tim Internal WO (Kru)
            let crewRows = '';
            if (info.team_members && info.team_members.length > 0) {
                info.team_members.forEach(member => {
                    crewRows += `
                        <div class="modal-item-row">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: #edf4f1; color: #073d36; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;">
                                    ${member.name.substring(0, 2).toUpperCase()}
                                </div>
                                <div>
                                    <strong style="color: #173b32; font-size: 14px;">${member.name}</strong>
                                    ${member.call_sign ? `<small class="text-muted" style="font-size: 12px;">(${member.call_sign})</small>` : ''}
                                </div>
                            </div>
                            <span class="badge" style="background: #f6ebd7; color: #7d5b2b; font-size: 12px; font-weight: 700; padding: 4px 10px;">
                                ${member.role}
                            </span>
                        </div>
                    `;
                });
            } else {
                crewRows = `<div class="text-muted small py-2 col-12" style="font-size: 13px;">Belum ada kru internal yang ditugaskan.</div>`;
            }

            // Pasang ke Modal Body
            modalBody.innerHTML = `
                {{-- Hero Banner Acara --}}
                <div class="modal-hero-box">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                        <div>
                            <span class="badge mb-2" style="${statusPillStyle}; font-size: 12px; font-weight: 700; padding: 5px 12px;">
                                ${statusLabel}
                            </span>
                            <div class="modal-hero-couple">${info.couple_name}</div>
                            <div class="modal-hero-event-name">
                                <i class="bi bi-heart-fill text-danger me-1"></i> ${info.title} (${info.event_type})
                            </div>
                        </div>
                    </div>

                    <div class="modal-hero-grid">
                        <div class="modal-hero-item">
                            <small><i class="bi bi-calendar3"></i> Tanggal Pelaksanaan</small>
                            <strong>${info.event_date}</strong>
                        </div>
                        <div class="modal-hero-item">
                            <small><i class="bi bi-clock"></i> Timeline Acara</small>
                            <strong>${info.schedule}</strong>
                        </div>
                        <div class="modal-hero-item">
                            <small><i class="bi bi-people"></i> Jumlah Tamu</small>
                            <strong>${info.guest_count ? info.guest_count + ' Undangan' : 'Tidak ditentukan'}</strong>
                        </div>
                        <div class="modal-hero-item" style="grid-column: 1 / -1;">
                            <small><i class="bi bi-geo-alt"></i> Lokasi Venue</small>
                            <strong>${info.venue}${info.venue_city ? ' (' + info.venue_city + ')' : ''}</strong>
                            ${info.venue_address ? `<small class="text-muted d-block mt-1" style="font-size: 12.5px;">${info.venue_address}</small>` : ''}
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    {{-- Koordinator Utama On-Duty --}}
                    <div class="col-12">
                        <div class="modal-section-card">
                            <div class="modal-section-title">
                                <i class="bi bi-shield-check"></i> Koordinator Utama Lapangan (On-Duty)
                            </div>
                            <div class="d-flex align-items-center gap-3 p-3 rounded" style="background:#f7faf8; border:1px solid #e4ebe8;">
                                <div style="width: 48px; height: 48px; border-radius: 50%; background:#073d36; color:#dfc28b; display:flex; align-items:center; justify-content:center; font-size:17px; font-weight:700;">
                                    ${(info.lead_director_full || info.lead_director || 'KO').substring(0, 2).toUpperCase()}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold" style="color: #173b32; font-size: 16px;">
                                        ${info.lead_director_full || info.lead_director}
                                    </div>
                                    <small class="text-muted" style="font-size: 13px;">
                                        Jabatan: <strong>${info.lead_director_role || 'Coordinator'}</strong>
                                        ${info.lead_director_phone ? ` • Telp: ${info.lead_director_phone}` : ''}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tim Pelaksana (Crew) --}}
                    <div class="col-12">
                        <div class="modal-section-card">
                            <div class="modal-section-title">
                                <i class="bi bi-people"></i> Tim Internal WO yang Bertugas (${info.team_count} Kru)
                            </div>
                            <div class="team-crew-grid">
                                ${crewRows}
                            </div>
                        </div>
                    </div>

                    {{-- Vendor Rekanan Hari H --}}
                    <div class="col-12">
                        <div class="modal-section-card mb-0">
                            <div class="modal-section-title">
                                <i class="bi bi-shop"></i> Vendor Rekanan Bertugas (${info.vendor_count} Vendor)
                            </div>
                            <div>
                                ${vendorRows}
                            </div>
                        </div>
                    </div>

                    {{-- Catatan Khusus --}}
                    ${info.notes ? `
                        <div class="col-12 mt-3">
                            <div class="modal-section-card mb-0" style="background:#fffaf0; border-color:#faecd8;">
                                <div class="modal-section-title" style="color:#8a6a3b; border-color:#faecd8;">
                                    <i class="bi bi-sticky"></i> Catatan & Instruksi Khusus Hari H
                                </div>
                                <div style="color: #5c4d37; font-size: 14px; line-height: 1.6; white-space: pre-line;">${info.notes}</div>
                            </div>
                        </div>
                    ` : ''}
                </div>
            `;

            // Pasang Tombol Aksi di Modal Footer
            modalFooter.innerHTML = `
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Tutup</button>
                <a href="/admin/events/${info.id}/edit" class="btn-wo-outline">
                    <i class="bi bi-pencil"></i> Edit Acara
                </a>
                <a href="/admin/tracking/${info.id}" class="btn-wo-primary">
                    <i class="bi bi-check2-square"></i> Buka Checklist & Absensi Kru
                </a>
            `;
        })
        .catch(error => {
            modalBody.innerHTML = `<div class="alert alert-danger">Gagal memuat rincian acara. Pastikan koneksi server aktif.</div>`;
            console.error('Error:', error);
        });
    }
});
</script>
@endsection
