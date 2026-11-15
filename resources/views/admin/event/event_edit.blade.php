@extends('layouts.admin')

@section('title', 'Edit Event')
@section('page-title', 'Form Edit Event')

@push('styles')
    @vite('resources/css/admin/events.css')
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="wo-event-page">
        <div class="wo-page-header">
            <div>
                <div class="wo-breadcrumb">
                    <a href="{{ route('admin.events.index') }}">Event Day</a>
                    <span>/</span>
                    <span>Edit Event</span>
                </div>
                <h2>Edit Event</h2>
                <p>Perbarui detail pelaksanaan acara, penugasan tim organizer, serta jadwal & PIC vendor</p>
            </div>
            <div class="d-flex gap-2">
                <a href="javascript:void(0)" onclick="history.back()" class="wo-btn-outline">Batal</a>
                <button type="submit" form="eventForm" class="wo-btn-primary">
                    <i class="bi bi-check2-circle"></i> Update Event
                </button>
            </div>
        </div>

        <form id="eventForm" method="POST" action="{{ route('admin.events.update', $event->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Section 1: Info Booking (Read Only) -->
            <div class="wo-section-card">
                <div class="wo-section-head">
                    <div class="wo-section-title">
                        <div class="wo-section-icon">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div>
                            <h5>1. Info Booking</h5>
                            <small>Booking client yang terkait dengan event ini</small>
                        </div>
                    </div>
                </div>
                <div class="wo-section-body">
                    <div class="wo-booking-summary">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Pasangan Mempelai</small>
                                <span class="fw-bold" style="color:#173b32;">
                                    {{ $event->booking?->client?->groom_name ?? 'N/A' }} & {{ $event->booking?->client?->bride_name ?? 'N/A' }}
                                </span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Lokasi Venue</small>
                                <span class="fw-bold" style="color:#173b32;">{{ $event->booking?->venue_name ?? 'N/A' }}</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Paket Booking</small>
                                <span class="fw-bold" style="color:#8a6a3b;">{{ $event->booking?->package?->name ?? 'Custom Vendor' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Detail Event -->
            <div class="wo-section-card">
                <div class="wo-section-head">
                    <div class="wo-section-title">
                        <div class="wo-section-icon">
                            <i class="bi bi-heart-fill"></i>
                        </div>
                        <div>
                            <h5>2. Detail Acara</h5>
                            <small>Informasi tentang acara (Akad, Resepsi, Lamaran, dll)</small>
                        </div>
                    </div>
                    <span class="wo-badge-required">Wajib</span>
                </div>
                <div class="wo-section-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="wo-label">Nama Event <span class="req">*</span></label>
                            <input type="text" class="wo-control" name="event_name" value="{{ $event->name }}" placeholder="Contoh: Akad Nikah" required>
                        </div>
                        <div class="col-md-6">
                            <label class="wo-label">Foto Cover Event</label>
                            <input type="file" class="wo-control" name="event_photo" accept="image/*">
                            @if ($event->photo)
                                @php
                                    $storagePath = storage_path('app/public/' . $event->photo);
                                    $photoUrl = asset('storage/' . $event->photo);
                                    if (file_exists($storagePath)) {
                                        $photoUrl .= '?v=' . filemtime($storagePath);
                                    }
                                @endphp
                                <small class="text-muted">Foto saat ini: <a href="{{ $photoUrl }}" target="_blank" style="color:#176257; font-weight:600;">Lihat foto</a></small>
                            @else
                                <small class="text-muted">Belum ada foto</small>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="wo-label">Tipe Event</label>
                            <select class="wo-select" name="event_type">
                                <option value="">-- Pilih Tipe --</option>
                                <option value="akad_nikah" {{ $event->event_type == 'akad_nikah' ? 'selected' : '' }}>Akad Nikah</option>
                                <option value="resepsi" {{ $event->event_type == 'resepsi' ? 'selected' : '' }}>Resepsi</option>
                                <option value="lamaran" {{ $event->event_type == 'lamaran' ? 'selected' : '' }}>Lamaran</option>
                                <option value="siraman" {{ $event->event_type == 'siraman' ? 'selected' : '' }}>Siraman</option>
                                <option value="midodareni" {{ $event->event_type == 'midodareni' ? 'selected' : '' }}>Midodareni</option>
                                <option value="pemberkatan" {{ $event->event_type == 'pemberkatan' ? 'selected' : '' }}>Pemberkatan</option>
                                <option value="tea_ceremony" {{ $event->event_type == 'tea_ceremony' ? 'selected' : '' }}>Tea Ceremony</option>
                                <option value="lainnya" {{ $event->event_type == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="wo-label">Tanggal Acara <span class="req">*</span></label>
                            <input type="date" class="wo-control" name="event_date" value="{{ $event->event_date?->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="wo-label">Jumlah Tamu Undangan</label>
                            <input type="number" class="wo-control" name="guest_count" value="{{ $event->guest_count }}" placeholder="Contoh: 300">
                        </div>
                        <div class="col-md-6">
                            <label class="wo-label">Status Event</label>
                            <select class="wo-select" name="event_status">
                                <option value="draft" {{ $event->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="upcoming" {{ $event->status == 'upcoming' || $event->status == 'scheduled' ? 'selected' : '' }}>Upcoming</option>
                                <option value="ongoing" {{ $event->status == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="completed" {{ $event->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $event->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="wo-label">Catatan Event</label>
                            <textarea class="wo-control" name="event_notes" rows="2" placeholder="Catatan khusus untuk event ini">{{ $event->notes }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Team Members -->
            <div class="wo-section-card">
                <div class="wo-section-head">
                    <div class="wo-section-title">
                        <div class="wo-section-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div>
                            <h5>3. Tim Organizer</h5>
                            <small>Gunakan role <strong>Lead Coordinator</strong> / <strong>Coordinator</strong> untuk Penanggung Jawab Utama Event</small>
                        </div>
                    </div>
                    <button type="button" class="wo-btn-add" id="addTeamMemberBtn">
                        <i class="bi bi-plus-lg"></i> Tambah Anggota Tim
                    </button>
                </div>

                <div class="wo-section-body">
                    <datalist id="teamRoleOptions">
                        <option value="Lead Coordinator"></option>
                        <option value="Coordinator"></option>
                        <option value="Team Member"></option>
                        <option value="Crew"></option>
                    </datalist>

                    <div class="team-list" id="teamList">
                        @forelse($event->eventMembers as $eventMember)
                            <div class="wo-item-row team-item">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-6">
                                        <label class="wo-label">Pilih Member</label>
                                        <select class="wo-select team-member-select" name="team_member_id[]">
                                            <option value="">-- Pilih Member --</option>
                                            @foreach ($members as $member)
                                                <option value="{{ $member->id }}" data-default-role="{{ $member->position ?? 'Team Member' }}"
                                                    {{ $eventMember->member_id == $member->id ? 'selected' : '' }}>
                                                    {{ $member->name }} ({{ $member->call_sign ?: '-' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="wo-label">Role di Event</label>
                                        <input type="text" list="teamRoleOptions" class="wo-control team-member-role" name="team_member_role[]"
                                            value="{{ $eventMember->role ?: ($eventMember->member?->position ?? 'Team Member') }}"
                                            placeholder="Contoh: Lead Coordinator, Coordinator, Crew">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="wo-btn-remove remove-team-btn" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="wo-item-row team-item">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-6">
                                        <label class="wo-label">Pilih Member</label>
                                        <select class="wo-select team-member-select" name="team_member_id[]">
                                            <option value="">-- Pilih Member --</option>
                                            @foreach ($members as $member)
                                                <option value="{{ $member->id }}" data-default-role="{{ $member->position ?? 'Team Member' }}">
                                                    {{ $member->name }} ({{ $member->call_sign ?: '-' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="wo-label">Role di Event</label>
                                        <input type="text" list="teamRoleOptions" class="wo-control team-member-role" name="team_member_role[]"
                                            placeholder="Contoh: Lead Coordinator, Coordinator, Crew">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="wo-btn-remove remove-team-btn" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Section 4: Vendor & Paket -->
            <div class="wo-section-card">
                <div class="wo-section-head">
                    <div class="wo-section-title">
                        <div class="wo-section-icon">
                            <i class="bi bi-shop"></i>
                        </div>
                        <div>
                            <h5>4. Jadwal Vendor & Penanggung Jawab (PIC)</h5>
                            <small>Atur paket atau vendor kustom beserta member PIC masing-masing</small>
                        </div>
                    </div>
                </div>

                <div class="wo-section-body">
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="wo-label">Pilih Paket Wedding</label>
                            <select class="wo-select" name="package_id" id="packageSelect">
                                <option value="">Tidak Menggunakan Paket (Pilih Vendor Kustom)</option>
                                @foreach ($packages as $package)
                                    <option value="{{ $package->id }}"
                                        {{ $event->package_id == $package->id ? 'selected' : '' }}
                                        data-vendors="{{ json_encode($package->vendors->map(function ($v) { return ['id' => $v->id, 'name' => $v->name, 'category' => $v->category->name ?? 'N/A']; })) }}">
                                        {{ $package->name }} - Rp {{ number_format($package->price, 0, ',', '.') }}
                                        ({{ $package->vendors->count() }} vendor)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="packageVendorsDisplay" class="mb-3" style="display: {{ $event->package_id ? 'block' : 'none' }};">
                        <label class="wo-label mb-2">Daftar Vendor dalam Paket & Penugasan PIC:</label>
                        <div id="packageVendorsList"></div>
                    </div>

                    <div id="customVendorSection" style="display: {{ $event->package_id ? 'none' : 'block' }};">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <label class="wo-label mb-0">Daftar Vendor Kustom</label>
                                <small class="text-muted">Pilih vendor kustom dan tentukan member penanggung jawabnya</small>
                            </div>
                            <button type="button" class="wo-btn-add" id="addVendorBtn">
                                <i class="bi bi-plus-lg"></i> Tambah Vendor
                            </button>
                        </div>

                        <div class="vendor-list" id="vendorList">
                            @forelse($event->schedules as $schedule)
                                <div class="wo-item-row vendor-item">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-3">
                                            <label class="wo-label">Pilih Vendor</label>
                                            <select class="wo-select" name="vendor_id[]">
                                                <option value="">-- Pilih Vendor --</option>
                                                @foreach ($vendors as $vendor)
                                                    <option value="{{ $vendor->id }}" {{ $schedule->vendor_id == $vendor->id ? 'selected' : '' }}>
                                                        {{ $vendor->name }} - {{ $vendor->category ? $vendor->category->name : 'N/A' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="wo-label">Penanggung Jawab (PIC)</label>
                                            <select class="wo-select" name="vendor_member_id[]">
                                                <option value="">Tidak ditugaskan</option>
                                                @foreach ($members as $member)
                                                    <option value="{{ $member->id }}" {{ $schedule->member_id == $member->id ? 'selected' : '' }}>
                                                        {{ $member->name }} ({{ $member->call_sign ?: '-' }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="wo-label">Waktu Mulai</label>
                                            <input type="time" class="wo-control" name="vendor_start_time[]"
                                                value="{{ $schedule->start_time ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i') : '09:00' }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="wo-label">Waktu Selesai</label>
                                            <input type="time" class="wo-control" name="vendor_end_time[]"
                                                value="{{ $schedule->end_time ? \Carbon\Carbon::parse($schedule->end_time)->format('H:i') : '17:00' }}">
                                        </div>
                                        <div class="col-md-1">
                                            <label class="wo-label">Status</label>
                                            <select class="wo-select" name="vendor_status[]">
                                                <option value="pending" {{ $schedule->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="approved" {{ $schedule->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                                <option value="rejected" {{ $schedule->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="wo-btn-remove remove-vendor-btn" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="wo-item-row vendor-item">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-3">
                                            <label class="wo-label">Pilih Vendor</label>
                                            <select class="wo-select" name="vendor_id[]">
                                                <option value="">-- Pilih Vendor --</option>
                                                @foreach ($vendors as $vendor)
                                                    <option value="{{ $vendor->id }}">
                                                        {{ $vendor->name }} - {{ $vendor->category ? $vendor->category->name : 'N/A' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="wo-label">Penanggung Jawab (PIC)</label>
                                            <select class="wo-select" name="vendor_member_id[]">
                                                <option value="">Tidak ditugaskan</option>
                                                @foreach ($members as $member)
                                                    <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->call_sign ?: '-' }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="wo-label">Waktu Mulai</label>
                                            <input type="time" class="wo-control" name="vendor_start_time[]" value="09:00">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="wo-label">Waktu Selesai</label>
                                            <input type="time" class="wo-control" name="vendor_end_time[]" value="17:00">
                                        </div>
                                        <div class="col-md-1">
                                            <label class="wo-label">Status</label>
                                            <select class="wo-select" name="vendor_status[]">
                                                <option value="pending">Pending</option>
                                                <option value="approved">Approved</option>
                                                <option value="rejected">Rejected</option>
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="wo-btn-remove remove-vendor-btn" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-4">
                <a href="{{ route('admin.events.index') }}" class="wo-btn-outline">Batal</a>
                <button type="submit" class="wo-btn-primary">
                    <i class="bi bi-check2-circle"></i> Update Event
                </button>
            </div>
        </form>
    </div>
</div>

@php
    $existingVendorsJson = $event->schedules->keyBy('vendor_id')->map(function ($s) {
        return [
            'member_id' => $s->member_id,
            'start_time' => $s->start_time ? \Carbon\Carbon::parse($s->start_time)->format('H:i') : '09:00',
            'end_time' => $s->end_time ? \Carbon\Carbon::parse($s->end_time)->format('H:i') : '17:00',
            'status' => $s->status ?? 'pending',
        ];
    });

    $availableMembersJson = $members->map(function ($m) {
        return [
            'id' => $m->id,
            'label' => $m->name . ' (' . ($m->call_sign ?: '-') . ')',
        ];
    })->values();
@endphp

<script>
document.addEventListener('DOMContentLoaded', function() {
    const packageSelect = document.getElementById('packageSelect');
    const customVendorSection = document.getElementById('customVendorSection');
    const packageVendorsDisplay = document.getElementById('packageVendorsDisplay');
    const packageVendorsList = document.getElementById('packageVendorsList');
    const addVendorBtn = document.getElementById('addVendorBtn');
    const vendorList = document.getElementById('vendorList');
    const addTeamMemberBtn = document.getElementById('addTeamMemberBtn');
    const teamList = document.getElementById('teamList');

    const existingVendors = @json($existingVendorsJson);
    const availableMembers = @json($availableMembersJson);

    if (packageSelect && packageSelect.value) {
        updatePackageVendors(true);
    }

    function updatePackageVendors() {
        const selectedOption = packageSelect.options[packageSelect.selectedIndex];

        if (packageSelect.value) {
            customVendorSection.style.display = 'none';
            packageVendorsDisplay.style.display = 'block';

            const vendors = JSON.parse(selectedOption.getAttribute('data-vendors') || '[]');

            let vendorHTML = '';

            if (vendors.length > 0) {
                vendors.forEach(vendor => {
                    const existingVendor = existingVendors[vendor.id];
                    const startTime = existingVendor && existingVendor.start_time ? existingVendor.start_time : '09:00';
                    const endTime = existingVendor && existingVendor.end_time ? existingVendor.end_time : '17:00';
                    const selectedMemberId = existingVendor && existingVendor.member_id ? Number(existingVendor.member_id) : null;
                    const statusValue = existingVendor && existingVendor.status ? existingVendor.status : 'pending';

                    let memberOptionsHTML = '<option value="">Tidak ditugaskan</option>';
                    availableMembers.forEach(m => {
                        const isSelected = selectedMemberId === Number(m.id) ? 'selected' : '';
                        memberOptionsHTML += `<option value="${m.id}" ${isSelected}>${m.label}</option>`;
                    });

                    vendorHTML += `
                        <div class="wo-item-row">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label class="wo-label">Vendor Paket</label>
                                    <input type="text" class="wo-control bg-light" value="${vendor.name} - ${vendor.category}" readonly>
                                    <input type="hidden" name="package_vendor_id[]" value="${vendor.id}">
                                </div>
                                <div class="col-md-3">
                                    <label class="wo-label">Penanggung Jawab (PIC)</label>
                                    <select class="wo-select" name="package_vendor_member_id[]">
                                        ${memberOptionsHTML}
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="wo-label">Waktu Mulai</label>
                                    <input type="time" class="wo-control" name="package_vendor_start_time[]" value="${startTime}">
                                </div>
                                <div class="col-md-2">
                                    <label class="wo-label">Waktu Selesai</label>
                                    <input type="time" class="wo-control" name="package_vendor_end_time[]" value="${endTime}">
                                </div>
                                <div class="col-md-2">
                                    <label class="wo-label">Status</label>
                                    <select class="wo-select" name="package_vendor_status[]">
                                        <option value="pending" ${statusValue === 'pending' ? 'selected' : ''}>Pending</option>
                                        <option value="approved" ${statusValue === 'approved' ? 'selected' : ''}>Approved</option>
                                        <option value="rejected" ${statusValue === 'rejected' ? 'selected' : ''}>Rejected</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                vendorHTML = '<div class="text-muted small">Tidak ada vendor dalam paket ini.</div>';
            }

            packageVendorsList.innerHTML = vendorHTML;
        } else {
            customVendorSection.style.display = 'block';
            packageVendorsDisplay.style.display = 'none';
        }
    }

    if (packageSelect) {
        packageSelect.addEventListener('change', updatePackageVendors);
    }

    addVendorBtn.addEventListener('click', function() {
        const firstVendorItem = vendorList.querySelector('.vendor-item');
        const newVendorItem = firstVendorItem.cloneNode(true);

        newVendorItem.querySelectorAll('select').forEach(select => {
            if (select.name === 'vendor_status[]') select.value = 'pending';
            else select.value = '';
        });

        newVendorItem.querySelectorAll('input').forEach(input => {
            if (input.name === 'vendor_start_time[]') input.value = '09:00';
            else if (input.name === 'vendor_end_time[]') input.value = '17:00';
            else input.value = '';
        });

        vendorList.appendChild(newVendorItem);
        attachRemoveEvent(newVendorItem.querySelector('.remove-vendor-btn'));
    });

    function attachRemoveEvent(btn) {
        btn.addEventListener('click', function() {
            const vendorItems = vendorList.querySelectorAll('.vendor-item');
            if (vendorItems.length > 1) {
                this.closest('.vendor-item').remove();
            }
        });
    }

    document.querySelectorAll('.remove-vendor-btn').forEach(btn => attachRemoveEvent(btn));

    addTeamMemberBtn.addEventListener('click', function() {
        const firstTeamItem = teamList.querySelector('.team-item');
        const newTeamItem = firstTeamItem.cloneNode(true);

        newTeamItem.querySelectorAll('select').forEach(select => select.value = '');
        newTeamItem.querySelectorAll('input').forEach(input => {
            input.value = '';
            input.dataset.autofilled = 'false';
        });

        teamList.appendChild(newTeamItem);
        attachRemoveTeamEvent(newTeamItem.querySelector('.remove-team-btn'));
        attachTeamMemberSelectionHandlers(newTeamItem);
    });

    function attachRemoveTeamEvent(btn) {
        btn.addEventListener('click', function() {
            const teamItems = teamList.querySelectorAll('.team-item');
            if (teamItems.length > 1) {
                this.closest('.team-item').remove();
            }
        });
    }

    document.querySelectorAll('.remove-team-btn').forEach(btn => attachRemoveTeamEvent(btn));

    function attachTeamMemberSelectionHandlers(root) {
        const selects = root.querySelectorAll('.team-member-select');
        selects.forEach(select => {
            const item = select.closest('.team-item');
            const roleInput = item.querySelector('.team-member-role');
            const defaultRole = select.selectedOptions[0]?.dataset?.defaultRole || 'Team Member';

            if (!roleInput.value.trim()) {
                roleInput.value = defaultRole;
                roleInput.dataset.autofilled = 'true';
            }

            select.addEventListener('change', function() {
                const selectedOption = this.selectedOptions[0];
                const fallbackRole = selectedOption?.dataset?.defaultRole || 'Team Member';

                if (!roleInput.value.trim() || roleInput.dataset.autofilled === 'true') {
                    roleInput.value = fallbackRole;
                    roleInput.dataset.autofilled = 'true';
                }
            });

            roleInput.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    this.dataset.autofilled = 'false';
                }
            });
        });
    }

    attachTeamMemberSelectionHandlers(document);
});
</script>
@endsection
