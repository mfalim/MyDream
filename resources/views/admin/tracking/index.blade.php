@extends('layouts.admin')

@section('title', 'Monitoring & Tracking Booking')
@section('page-title', 'Monitoring & Tracking Booking')

@push('styles')
    @vite('resources/css/admin/tracking.css')
@endpush

@section('content')
<div class="tracking-page">
    {{-- Header --}}
    <div class="tracking-header">
        <div>
            <div class="tracking-eyebrow">Operasional & Monitoring Acara</div>
            <h2>Monitoring & Tracking Booking Client</h2>
            <p>Klik nama acara untuk melihat checklist progress, atau klik <strong>Kelola</strong> untuk mengatur status booking & tim per event.</p>
        </div>
        <a href="{{ route('admin.events.create') }}" class="btn-wo-primary">
            <i class="bi bi-plus-lg"></i> Buat Acara Baru
        </a>
    </div>

    {{-- Stat Cards --}}
    <div class="tracking-stats-grid">
        <div class="tracking-stat-card">
            <div class="tracking-stat-info">
                <small>Total Booking</small>
                <strong>{{ $bookings->count() }}</strong>
            </div>
            <div class="tracking-stat-icon" style="background: #edf4f1; color: #073d36;">
                <i class="bi bi-journal-bookmark-fill"></i>
            </div>
        </div>

        <div class="tracking-stat-card">
            <div class="tracking-stat-info">
                <small>Menunggu Konfirmasi</small>
                <strong style="color: #b7791f;">{{ $bookings->where('status', 'pending')->count() }}</strong>
            </div>
            <div class="tracking-stat-icon" style="background: #fff3d6; color: #b7791f;">
                <i class="bi bi-hourglass-split"></i>
            </div>
        </div>

        <div class="tracking-stat-card">
            <div class="tracking-stat-info">
                <small>Booking Disetujui</small>
                <strong style="color: #0d6e4c;">{{ $bookings->where('status', 'approved')->count() }}</strong>
            </div>
            <div class="tracking-stat-icon" style="background: #dff7ee; color: #0d6e4c;">
                <i class="bi bi-check-circle-fill"></i>
            </div>
        </div>

        <div class="tracking-stat-card">
            <div class="tracking-stat-info">
                <small>Booking Ditolak</small>
                <strong style="color: #b91c1c;">{{ $bookings->where('status', 'rejected')->count() }}</strong>
            </div>
            <div class="tracking-stat-icon" style="background: #fee2e2; color: #b91c1c;">
                <i class="bi bi-x-circle-fill"></i>
            </div>
        </div>
    </div>

    {{-- Search & Filter Toolbar --}}
    <div class="tracking-toolbar">
        <div class="tracking-search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="searchInput" placeholder="Cari pasangan pengantin, venue, atau nama acara...">
        </div>

        <div class="tracking-filter-tabs" id="statusTabs">
            <button type="button" class="tracking-filter-tab active" data-status="">
                Semua ({{ $bookings->count() }})
            </button>
            <button type="button" class="tracking-filter-tab" data-status="pending">
                Pending ({{ $bookings->where('status', 'pending')->count() }})
            </button>
            <button type="button" class="tracking-filter-tab" data-status="approved">
                Approved ({{ $bookings->where('status', 'approved')->count() }})
            </button>
            <button type="button" class="tracking-filter-tab" data-status="rejected">
                Rejected ({{ $bookings->where('status', 'rejected')->count() }})
            </button>
        </div>
    </div>

    {{-- Tabel Utama Booking & Rangkaian Event --}}
    <div class="tracking-table-card">
        <div class="table-responsive">
            <table class="tracking-table">
                <thead>
                    <tr>
                        <th style="width: 22%;">Booking & Pasangan</th>
                        <th style="width: 20%;">Paket & Venue</th>
                        <th style="width: 34%;">Rangkaian Acara (Klik Acara untuk Detail)</th>
                        <th style="width: 14%;">Total Progress</th>
                        <th style="width: 10%; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="bookingTable">
                    @forelse($bookings as $booking)
                        @php
                            $eventData = $eventsByBooking->get($booking->id);
                            $events = $eventData ? $eventData['events'] : collect();
                            $progress = $eventData ? $eventData['progress'] : 0;
                            $approvedCount = $eventData ? $eventData['approved_count'] : 0;
                            $totalCount = $eventData ? $eventData['total_count'] : 0;

                            $eventNamesText = $events->pluck('name')->implode(' ');
                            $searchText = strtolower(
                                ($booking->client->groom_name ?? '') . ' ' .
                                ($booking->client->bride_name ?? '') . ' ' .
                                ($booking->client->email ?? '') . ' ' .
                                ($booking->venue_name ?? '') . ' ' .
                                $eventNamesText
                            );
                        @endphp
                        <tr data-search="{{ $searchText }}" data-status="{{ $booking->status }}">
                            {{-- 1. Booking & Pasangan --}}
                            <td>
                                <div class="booking-top-row">
                                    <span class="booking-id-pill">#{{ $booking->id }}</span>
                                    <span class="status-pill {{ $booking->status }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </div>
                                <div class="client-couple-name">
                                    {{ $booking->client->groom_name ?? 'N/A' }} & {{ $booking->client->bride_name ?? 'N/A' }}
                                </div>
                                <div class="client-meta">
                                    {{ $booking->client->email ?? '-' }}
                                </div>
                            </td>

                            {{-- 2. Paket & Venue --}}
                            <td>
                                @php
                                    $bookingPackages = $booking->all_packages;
                                @endphp
                                <div class="d-flex flex-wrap gap-1 mb-1">
                                    @forelse($bookingPackages as $pkg)
                                        <div class="package-tag">
                                            <i class="bi bi-box-seam me-1"></i>{{ $pkg->name }}
                                        </div>
                                    @empty
                                        <div class="package-tag">
                                            Custom Vendor
                                        </div>
                                    @endforelse
                                </div>
                                <div class="venue-title">{{ $booking->venue_name }}</div>
                                @if($booking->full_venue_address)
                                    <div class="client-meta mb-1" style="font-size: 0.8rem; color: #555;">
                                        <i class="bi bi-geo-alt me-1"></i>{{ $booking->full_venue_address }}
                                    </div>
                                @elseif($booking->venue_city)
                                    <div class="client-meta mb-1"><i class="bi bi-geo-alt me-1"></i>{{ $booking->venue_city }}</div>
                                @endif
                                <div class="price-text mt-1">
                                    Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                    @if($bookingPackages->count() > 1)
                                        <small class="text-muted" style="font-size: 0.75rem; font-weight: normal;">({{ $bookingPackages->count() }} Paket Acara)</small>
                                    @endif
                                </div>
                            </td>

                            {{-- 3. Rangkaian Acara (Simple, Clean & Clickable) --}}
                            <td>
                                @if($events->isNotEmpty())
                                    <div class="event-clean-list">
                                        @foreach($events as $ev)
                                            @php
                                                $evTotal = $ev->schedules->count();
                                                $evApproved = $ev->schedules->where('status', 'approved')->count();
                                                $evCoord = $ev->eventMembers->first(fn ($em) => $em->isCoordinator());
                                                $evCrewCount = $ev->eventMembers->filter(fn ($em) => !$em->isCoordinator())->count();
                                            @endphp
                                            <a href="{{ route('admin.tracking.show', $ev->id) }}" class="event-clean-item" title="Buka Detail Progress {{ $ev->name }}">
                                                <div class="event-clean-left">
                                                    <div class="event-date-box">
                                                        <span class="day">{{ $ev->event_date ? $ev->event_date->format('d') : '-' }}</span>
                                                        <span class="mon">{{ $ev->event_date ? $ev->event_date->format('M') : '' }}</span>
                                                    </div>
                                                    <div style="min-width: 0;">
                                                        <div class="event-clean-name">{{ $ev->name }}</div>
                                                        <div class="event-clean-sub">
                                                            @if($evCoord?->member)
                                                                Coord: <strong>{{ $evCoord->member->call_sign ?: $evCoord->member->name }}</strong>
                                                                @if($evCrewCount > 0)
                                                                    <span>• +{{ $evCrewCount }} kru</span>
                                                                @endif
                                                            @elseif($evCrewCount > 0)
                                                                <span>{{ $evCrewCount }} kru bertugas</span>
                                                            @else
                                                                <span class="text-warning">Tim belum diatur</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="event-clean-right">
                                                    <span>{{ $evApproved }}/{{ $evTotal }}</span>
                                                    <i class="bi bi-chevron-right" style="font-size: 11px;"></i>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted small">Belum ada acara</span>
                                @endif
                            </td>

                            {{-- 4. Progress Keseluruhan --}}
                            <td>
                                @if($totalCount > 0)
                                    <div class="wo-progress-wrap">
                                        <div class="wo-progress-top">
                                            <strong style="color: #173b32;">{{ round($progress) }}%</strong>
                                            <span class="text-muted">{{ $approvedCount }}/{{ $totalCount }}</span>
                                        </div>
                                        <div class="wo-progress-bar">
                                            <div class="wo-progress-fill {{ $progress < 50 ? 'warn' : '' }}" style="width: {{ $progress }}%;"></div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                            {{-- 5. Aksi (Hanya 1 Tombol Bersih) --}}
                            <td style="text-align: right;">
                                <button type="button" class="btn-wo-outline approve-btn" data-booking-id="{{ $booking->id }}" data-bs-toggle="modal" data-bs-target="#approveModal{{ $booking->id }}">
                                    <i class="bi bi-sliders"></i> Kelola
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">Belum ada data booking client.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Daftar Modal Kelola Booking & Tim Per-Event --}}
@foreach($bookings as $booking)
    @php
        $eventData = $eventsByBooking->get($booking->id);
        $events = $eventData ? $eventData['events'] : collect();
    @endphp
    <div class="modal fade tracking-modal" id="approveModal{{ $booking->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-dark">#{{ $booking->id }}</span>
                            <h5 class="modal-title fw-bold mb-0">Kelola Booking & Tim Acara</h5>
                        </div>
                        <small class="text-white-50">
                            Atur persetujuan booking serta tentukan Coordinator & Kru untuk masing-masing acara.
                        </small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    {{-- Ringkasan Info Booking --}}
                    <div class="booking-summary-grid">
                        <div class="booking-summary-item">
                            <div class="booking-summary-icon" style="background:#e7f1ea; color:#073d36;">
                                <i class="bi bi-heart-fill"></i>
                            </div>
                            <div>
                                <div class="booking-summary-label">Pasangan Mempelai</div>
                                <div class="booking-summary-value">
                                    {{ $booking->client->groom_name ?? 'N/A' }} & {{ $booking->client->bride_name ?? 'N/A' }}
                                </div>
                                <small class="text-muted">{{ $booking->client->email ?? '-' }}</small>
                            </div>
                        </div>

                        <div class="booking-summary-item">
                            <div class="booking-summary-icon" style="background:#f6ebd7; color:#8a6a3b;">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <div>
                                <div class="booking-summary-label">Lokasi Venue</div>
                                <div class="booking-summary-value">{{ $booking->venue_name }}</div>
                                <small class="text-muted">{{ $booking->full_venue_address ?: ($booking->venue_city ?? '-') }}</small>
                            </div>
                        </div>

                        <div class="booking-summary-item">
                            <div class="booking-summary-icon" style="background:#e7f1ea; color:#073d36;">
                                <i class="bi bi-box-seam-fill"></i>
                            </div>
                            <div>
                                <div class="booking-summary-label">Paket Wedding ({{ $booking->all_packages->count() ?: 1 }} Paket)</div>
                                <div class="booking-summary-value" style="font-size: 0.95rem;">
                                    @forelse($booking->all_packages as $pkg)
                                        <div><i class="bi bi-check2-circle text-success me-1"></i>{{ $pkg->name }} <span class="text-muted" style="font-size: 0.8rem;">(Rp {{ number_format($pkg->price, 0, ',', '.') }})</span></div>
                                    @empty
                                        <div>{{ $booking->package->name ?? 'Custom Vendor' }}</div>
                                    @endforelse
                                </div>
                                <small class="text-muted">{{ $events->count() }} Rangkaian Acara Terjadwal</small>
                            </div>
                        </div>

                        <div class="booking-summary-item">
                            <div class="booking-summary-icon" style="background:#dff7ee; color:#1b9e6a;">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                            <div>
                                <div class="booking-summary-label">Total Nilai Booking</div>
                                <div class="booking-summary-value text-success">
                                    Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                </div>
                                <small class="text-muted">{{ $booking->guest_count ?? '-' }} Tamu Undangan</small>
                            </div>
                        </div>
                    </div>

                    {{-- Status Persetujuan Booking --}}
                    <div class="modal-section-card">
                        <div class="row align-items-center g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold mb-1" style="color: #173b32;">
                                    Status Persetujuan Booking <span class="text-danger">*</span>
                                </label>
                                <small class="text-muted d-block">
                                    Pilih <strong>Approved</strong> untuk menugaskan tim pada setiap acara.
                                </small>
                            </div>
                            <div class="col-md-6">
                                <select class="form-select fw-semibold booking-status-modal-select" id="status{{ $booking->id }}" data-booking-id="{{ $booking->id }}">
                                    <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>⏳ Pending (Menunggu Konfirmasi)</option>
                                    <option value="approved" {{ $booking->status === 'approved' ? 'selected' : '' }}>✅ Approved (Disetujui & Aktif)</option>
                                    <option value="rejected" {{ $booking->status === 'rejected' ? 'selected' : '' }}>❌ Rejected (Ditolak)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Pengaturan Tim Per Event (Muncul ketika Approved) --}}
                    <div id="eventsTeamWrapper{{ $booking->id }}" style="display: {{ $booking->status === 'approved' ? 'block' : 'none' }};">
                        @if($events->isNotEmpty())
                            {{-- Baris Tab Pilih Acara --}}
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                <div class="modal-event-tabs mb-0" id="modalEventTabs{{ $booking->id }}">
                                    @foreach($events as $idx => $ev)
                                        <button type="button"
                                            class="modal-event-tab-btn {{ $idx === 0 ? 'active' : '' }}"
                                            data-booking-id="{{ $booking->id }}"
                                            data-target-pane="eventPane{{ $booking->id }}_{{ $ev->id }}">
                                            <span>{{ $ev->name }}</span>
                                            <span class="tab-date">({{ $ev->event_date ? $ev->event_date->format('d M') : '-' }})</span>
                                        </button>
                                    @endforeach
                                </div>

                                <a href="{{ route('admin.events.create', ['booking_id' => $booking->id]) }}" class="btn-wo-gold">
                                    <i class="bi bi-plus-lg"></i> Tambah Acara
                                </a>
                            </div>

                            {{-- Isi Form Masing-Masing Tab Event --}}
                            @foreach($events as $idx => $ev)
                                @php
                                    $leadMember = $ev->eventMembers->first(fn ($em) => $em->isCoordinator());
                                    $leadCoordinator = $leadMember?->member;
                                    $otherTeamMembers = $ev->eventMembers->filter(fn ($em) => !$em->isCoordinator())->values();
                                @endphp
                                <div class="event-tab-pane {{ $idx === 0 ? 'active' : '' }}"
                                     id="eventPane{{ $booking->id }}_{{ $ev->id }}"
                                     data-event-id="{{ $ev->id }}"
                                     data-event-name="{{ $ev->name }}">

                                    {{-- Coordinator Utama untuk Event Ini --}}
                                    <div class="modal-section-card">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #073d36; color: #fff;">
                                                    <i class="bi bi-shield-check"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold" style="color: #173b32;">Coordinator Utama — {{ $ev->name }}</div>
                                                    <small class="text-muted">Penanggung jawab lapangan untuk acara {{ $ev->name }}</small>
                                                </div>
                                            </div>

                                            @if($events->count() > 1)
                                                <button type="button"
                                                    class="btn-wo-soft copy-team-to-all-btn"
                                                    data-booking-id="{{ $booking->id }}"
                                                    data-source-event-id="{{ $ev->id }}"
                                                    title="Gunakan Coordinator & Kru yang sama untuk acara lain di booking ini">
                                                    <i class="bi bi-files"></i> Samakan ke Acara Lain
                                                </button>
                                            @endif
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-md-7">
                                                <label class="form-label small fw-semibold mb-1">Pilih Member Coordinator</label>
                                                <select class="form-select event-coord-id">
                                                    <option value="">-- Pilih Coordinator Utama --</option>
                                                    @foreach($members as $member)
                                                        <option value="{{ $member->id }}" {{ $leadCoordinator && $leadCoordinator->id == $member->id ? 'selected' : '' }}>
                                                            {{ $member->name }} ({{ $member->call_sign ?: '-' }}) — {{ $member->position ?? 'Member' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label small fw-semibold mb-1">Jabatan Koordinator</label>
                                                <select class="form-select event-coord-role">
                                                    <option value="Lead Coordinator" {{ ($leadMember?->role ?? 'Lead Coordinator') === 'Lead Coordinator' ? 'selected' : '' }}>
                                                        Lead Coordinator
                                                    </option>
                                                    <option value="Coordinator" {{ ($leadMember?->role ?? '') === 'Coordinator' ? 'selected' : '' }}>
                                                        Coordinator
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small fw-semibold mb-1">Catatan / Instruksi Coordinator</label>
                                                <textarea class="form-control form-control-sm event-coord-notes" rows="2" placeholder="Tulis arahan khusus untuk coordinator pada acara {{ $ev->name }}...">{{ $leadMember?->notes }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Anggota Tim Pelaksana (Crew) untuk Event Ini --}}
                                    <div class="modal-section-card mb-0">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #f6ebd7; color: #8a6a3b;">
                                                    <i class="bi bi-people-fill"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold" style="color: #173b32;">Anggota Tim Pelaksana (Crew) — {{ $ev->name }}</div>
                                                    <small class="text-muted">Kru yang bertugas pada acara {{ $ev->name }}</small>
                                                </div>
                                            </div>
                                            <button type="button" class="btn-wo-gold add-team-member-btn">
                                                <i class="bi bi-plus-lg"></i> Tambah Kru
                                            </button>
                                        </div>

                                        <div class="team-list">
                                            @forelse($otherTeamMembers as $teamEm)
                                                <div class="team-row-card team-item">
                                                    <div class="row g-2 align-items-end">
                                                        <div class="col-md-5">
                                                            <label class="form-label small fw-semibold mb-1">Pilih Member</label>
                                                            <select class="form-select form-select-sm team-member-select">
                                                                <option value="">-- Pilih Member --</option>
                                                                @foreach($members as $member)
                                                                    <option value="{{ $member->id }}"
                                                                        data-default-role="{{ $member->position ?? 'Team Member' }}"
                                                                        {{ $teamEm->member_id == $member->id ? 'selected' : '' }}>
                                                                        {{ $member->name }} ({{ $member->call_sign ?: '-' }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label small fw-semibold mb-1">Role / Tugas</label>
                                                            <input type="text" class="form-control form-control-sm team-member-role"
                                                                placeholder="Contoh: Crew"
                                                                value="{{ $teamEm->role ?: ($teamEm->member?->position ?? 'Team Member') }}">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label small fw-semibold mb-1">Catatan</label>
                                                            <input type="text" class="form-control form-control-sm team-member-notes"
                                                                placeholder="Catatan tugas..."
                                                                value="{{ $teamEm->notes }}">
                                                        </div>
                                                        <div class="col-md-1 d-flex align-items-end">
                                                            <button type="button" class="btn btn-outline-danger btn-sm remove-team-btn w-100" title="Hapus baris">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="team-row-card team-item">
                                                    <div class="row g-2 align-items-end">
                                                        <div class="col-md-5">
                                                            <label class="form-label small fw-semibold mb-1">Pilih Member</label>
                                                            <select class="form-select form-select-sm team-member-select">
                                                                <option value="">-- Pilih Member --</option>
                                                                @foreach($members as $member)
                                                                    <option value="{{ $member->id }}" data-default-role="{{ $member->position ?? 'Team Member' }}">
                                                                        {{ $member->name }} ({{ $member->call_sign ?: '-' }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label small fw-semibold mb-1">Role / Tugas</label>
                                                            <input type="text" class="form-control form-control-sm team-member-role" placeholder="Contoh: Crew" value="Team Member">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label small fw-semibold mb-1">Catatan</label>
                                                            <input type="text" class="form-control form-control-sm team-member-notes" placeholder="Catatan tugas...">
                                                        </div>
                                                        <div class="col-md-1 d-flex align-items-end">
                                                            <button type="button" class="btn btn-outline-danger btn-sm remove-team-btn w-100" title="Hapus baris">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="modal-section-card text-center py-4 mb-0">
                                <p class="text-muted mb-2">Belum ada acara yang dibuat untuk booking ini.</p>
                                <a href="{{ route('admin.events.create', ['booking_id' => $booking->id]) }}" class="btn-wo-primary">
                                    <i class="bi bi-plus-lg"></i> Buat Acara Pertama
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn-wo-primary px-4 save-booking-btn" data-booking-id="{{ $booking->id }}">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endforeach

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusTabs = document.querySelectorAll('#statusTabs .tracking-filter-tab');
    const rows = document.querySelectorAll('#bookingTable tr[data-search]');
    let selectedStatus = '';

    function filterTable() {
        const searchTerm = (searchInput?.value || '').toLowerCase();

        rows.forEach(row => {
            const searchText = row.getAttribute('data-search') || '';
            const rowStatus = row.getAttribute('data-status') || '';

            const matchesSearch = searchText.includes(searchTerm);
            const matchesStatus = !selectedStatus || rowStatus === selectedStatus;

            row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }

    statusTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            statusTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            selectedStatus = this.getAttribute('data-status') || '';
            filterTable();
        });
    });

    // Toggle tampil/sembunyi pengaturan tim saat status booking diganti
    document.querySelectorAll('.booking-status-modal-select').forEach(select => {
        select.addEventListener('change', function() {
            const bookingId = this.dataset.bookingId;
            const wrapper = document.getElementById(`eventsTeamWrapper${bookingId}`);
            if (!wrapper) return;
            wrapper.style.display = this.value === 'approved' ? 'block' : 'none';
        });
    });

    // Switching Tab Event di dalam Modal
    function activateEventTab(bookingId, targetPaneId) {
        const modalEl = document.getElementById(`approveModal${bookingId}`);
        if (!modalEl) return;

        modalEl.querySelectorAll('.modal-event-tab-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.targetPane === targetPaneId);
        });

        modalEl.querySelectorAll('.event-tab-pane').forEach(pane => {
            pane.classList.toggle('active', pane.id === targetPaneId);
        });
    }

    document.querySelectorAll('.modal-event-tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            activateEventTab(this.dataset.bookingId, this.dataset.targetPane);
        });
    });

    // Fitur "Samakan ke Acara Lain" di dalam Modal
    document.querySelectorAll('.copy-team-to-all-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const bookingId = this.dataset.bookingId;
            const sourceEventId = this.dataset.sourceEventId;
            const modalEl = document.getElementById(`approveModal${bookingId}`);
            const sourcePane = document.getElementById(`eventPane${bookingId}_${sourceEventId}`);
            if (!modalEl || !sourcePane) return;

            const coordId = sourcePane.querySelector('.event-coord-id')?.value || '';
            const coordRole = sourcePane.querySelector('.event-coord-role')?.value || 'Lead Coordinator';
            const coordNotes = sourcePane.querySelector('.event-coord-notes')?.value || '';

            const sourceCrewRows = sourcePane.querySelectorAll('.team-item');
            const crewData = [];
            sourceCrewRows.forEach(row => {
                crewData.push({
                    member_id: row.querySelector('.team-member-select')?.value || '',
                    role: row.querySelector('.team-member-role')?.value || 'Team Member',
                    notes: row.querySelector('.team-member-notes')?.value || ''
                });
            });

            modalEl.querySelectorAll('.event-tab-pane').forEach(pane => {
                if (pane.id === sourcePane.id) return;

                pane.querySelector('.event-coord-id').value = coordId;
                pane.querySelector('.event-coord-role').value = coordRole;
                pane.querySelector('.event-coord-notes').value = coordNotes;

                const teamList = pane.querySelector('.team-list');
                const templateRow = teamList.querySelector('.team-item').cloneNode(true);
                teamList.innerHTML = '';

                crewData.forEach(c => {
                    const newRow = templateRow.cloneNode(true);
                    newRow.querySelector('.team-member-select').value = c.member_id;
                    newRow.querySelector('.team-member-role').value = c.role;
                    newRow.querySelector('.team-member-notes').value = c.notes;
                    teamList.appendChild(newRow);
                    attachRemoveTeamEvent(newRow.querySelector('.remove-team-btn'));
                    attachTeamMemberSelectionHandlers(newRow);
                });
            });

            alert('Susunan Coordinator & Kru berhasil disamakan ke acara lainnya pada booking ini. Klik "Simpan Perubahan" untuk menyimpan.');
        });
    });

    // Simpan perubahan Status Booking + Tim untuk setiap Event
    document.querySelectorAll('.save-booking-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const bookingId = this.dataset.bookingId;
            const modalEl = document.getElementById(`approveModal${bookingId}`);
            const status = document.getElementById(`status${bookingId}`).value;

            const eventsTeamPayload = {};
            let duplicateErrorEventName = null;
            let hasAnyTeamAssigned = false;

            const eventPanes = modalEl.querySelectorAll('.event-tab-pane');
            eventPanes.forEach(pane => {
                const eventId = pane.dataset.eventId;
                const eventName = pane.dataset.eventName || `Event #${eventId}`;

                const coordinatorId = pane.querySelector('.event-coord-id')?.value || '';
                const coordinatorRole = pane.querySelector('.event-coord-role')?.value || 'Lead Coordinator';
                const coordinatorNotes = pane.querySelector('.event-coord-notes')?.value || '';

                const teamMemberSelects = pane.querySelectorAll('.team-member-select');
                const teamMemberRoles = pane.querySelectorAll('.team-member-role');
                const teamMemberNotes = pane.querySelectorAll('.team-member-notes');

                const teamMemberIds = [];
                const teamMemberRoleList = [];
                const teamMemberNotesList = [];
                const selectedIds = [];

                if (coordinatorId) {
                    selectedIds.push(String(coordinatorId));
                    hasAnyTeamAssigned = true;
                }

                teamMemberSelects.forEach((select, index) => {
                    const memberId = select.value;
                    if (!memberId) return;

                    const normalizedId = String(memberId);
                    if (selectedIds.includes(normalizedId)) {
                        duplicateErrorEventName = eventName;
                        return;
                    }

                    selectedIds.push(normalizedId);
                    hasAnyTeamAssigned = true;
                    teamMemberIds.push(memberId);
                    teamMemberRoleList.push(teamMemberRoles[index]?.value || 'Team Member');
                    teamMemberNotesList.push(teamMemberNotes[index]?.value || '');
                });

                eventsTeamPayload[eventId] = {
                    coordinator_id: coordinatorId || null,
                    coordinator_role: coordinatorRole,
                    coordinator_notes: coordinatorNotes,
                    team_member_id: teamMemberIds,
                    team_member_role: teamMemberRoleList,
                    team_member_notes: teamMemberNotesList
                };
            });

            if (duplicateErrorEventName) {
                alert(`Pada "${duplicateErrorEventName}", satu member tidak boleh dipilih lebih dari satu kali.`);
                return;
            }

            if (status === 'approved' && eventPanes.length > 0 && !hasAnyTeamAssigned) {
                alert('Pilih minimal satu Coordinator atau Anggota Tim pada salah satu acara.');
                return;
            }

            this.disabled = true;

            fetch(`/admin/tracking/booking/${bookingId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    status: status,
                    events_team: eventsTeamPayload
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Data booking & susunan tim per event berhasil disimpan.');
                    location.reload();
                } else {
                    alert(data.message || 'Gagal menyimpan perubahan');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan data.');
            })
            .finally(() => {
                this.disabled = false;
            });
        });
    });

    // Tambah baris crew di dalam tab event yang aktif
    document.querySelectorAll('.add-team-member-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const sectionCard = this.closest('.modal-section-card');
            const teamList = sectionCard.querySelector('.team-list');
            const firstItem = teamList.querySelector('.team-item');
            const newItem = firstItem.cloneNode(true);

            newItem.querySelector('.team-member-select').value = '';
            newItem.querySelector('.team-member-role').value = 'Team Member';
            newItem.querySelector('.team-member-notes').value = '';

            teamList.appendChild(newItem);
            attachRemoveTeamEvent(newItem.querySelector('.remove-team-btn'));
            attachTeamMemberSelectionHandlers(newItem);
        });
    });

    function attachRemoveTeamEvent(btn) {
        btn.addEventListener('click', function() {
            const teamList = this.closest('.team-list');
            const items = teamList.querySelectorAll('.team-item');
            if (items.length > 1) {
                this.closest('.team-item').remove();
            } else {
                const item = this.closest('.team-item');
                item.querySelector('.team-member-select').value = '';
                item.querySelector('.team-member-role').value = 'Team Member';
                item.querySelector('.team-member-notes').value = '';
            }
        });
    }

    document.querySelectorAll('.remove-team-btn').forEach(btn => {
        attachRemoveTeamEvent(btn);
    });

    function attachTeamMemberSelectionHandlers(root) {
        const select = root.querySelector('.team-member-select');
        const roleInput = root.querySelector('.team-member-role');

        if (!select || !roleInput) return;

        select.addEventListener('change', function() {
            const selected = this.selectedOptions[0];
            const defaultRole = selected?.dataset?.defaultRole || 'Team Member';

            if (!roleInput.value.trim() || roleInput.value.trim() === 'Team Member') {
                roleInput.value = defaultRole;
            }
        });
    }

    document.querySelectorAll('.team-item').forEach(item => attachTeamMemberSelectionHandlers(item));
});
</script>
@endsection
