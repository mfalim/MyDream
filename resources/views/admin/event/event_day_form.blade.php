@extends('layouts.admin')

@section('title', 'Tambah Event Baru')
@section('page-title', 'Form Tambah Event')

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
                    <span>Tambah Event Baru</span>
                </div>
                <h2>Tambah Event Baru</h2>
                <p>Inisialisasi jadwal acara pernikahan client, tim organizer, serta distribusi PIC vendor</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.events.index') }}" class="wo-btn-outline">Batal</a>
                <button type="submit" form="eventForm" class="wo-btn-primary">
                    <i class="bi bi-check2-circle"></i> Simpan Event
                </button>
            </div>
        </div>

        <form id="eventForm" method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Section 1: Pilih Booking -->
            <div class="wo-section-card">
                <div class="wo-section-head">
                    <div class="wo-section-title">
                        <div class="wo-section-icon">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div>
                            <h5>1. Pilih Booking Client</h5>
                            <small>Pilih booking client yang sudah berstatus Approved</small>
                        </div>
                    </div>
                    <span class="wo-badge-required">Wajib</span>
                </div>

                <div class="wo-section-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="wo-label">Pilih Booking <span class="req">*</span></label>
                            <select class="wo-select" name="booking_id" id="bookingSelect" required>
                                <option value="">-- Pilih Booking Client --</option>
                                @foreach ($bookings as $booking)
                                    <option value="{{ $booking->id }}"
                                        {{ (string) request('booking_id') === (string) $booking->id ? 'selected' : '' }}
                                        data-client="{{ $booking->client->groom_name ?? 'N/A' }} & {{ $booking->client->bride_name ?? 'N/A' }}"
                                        data-venue="{{ $booking->venue_name }}"
                                        data-package="{{ $booking->package->name ?? 'Custom Vendor' }}"
                                        data-package-id="{{ $booking->package_id ?? '' }}"
                                        data-guest="{{ $booking->guest_count ?? '' }}">
                                        #{{ $booking->id }} — {{ $booking->client->groom_name ?? 'N/A' }} & {{ $booking->client->bride_name ?? 'N/A' }} ({{ $booking->venue_name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="bookingInfo" class="col-12" style="display: none;">
                            <div class="wo-booking-summary">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Pasangan Mempelai</small>
                                        <span id="bookingClient" class="fw-bold" style="color:#173b32;">-</span>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Lokasi Venue</small>
                                        <span id="bookingVenue" class="fw-bold" style="color:#173b32;">-</span>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Paket Booking</small>
                                        <span id="bookingPackage" class="fw-bold" style="color:#8a6a3b;">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Detail Event -->
            <div class="wo-section-card" id="eventDetailSection" style="display: none;">
                <div class="wo-section-head">
                    <div class="wo-section-title">
                        <div class="wo-section-icon">
                            <i class="bi bi-heart-fill"></i>
                        </div>
                        <div>
                            <h5>2. Detail Acara</h5>
                            <small>Informasi pelaksanaan acara seperti Akad Nikah, Resepsi, Lamaran, dll</small>
                        </div>
                    </div>
                    <span class="wo-badge-required">Wajib</span>
                </div>

                <div class="wo-section-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="wo-label">Nama Event <span class="req">*</span></label>
                            <input type="text" class="wo-control" name="event_name" placeholder="Contoh: Akad Nikah & Resepsi" required>
                        </div>
                        <div class="col-md-6">
                            <label class="wo-label">Foto Cover Event</label>
                            <input type="file" class="wo-control" name="event_photo" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="wo-label">Tipe Event</label>
                            <select class="wo-select" name="event_type">
                                <option value="">-- Pilih Tipe Event --</option>
                                <option value="akad_nikah">Akad Nikah</option>
                                <option value="resepsi">Resepsi</option>
                                <option value="lamaran">Lamaran</option>
                                <option value="siraman">Siraman</option>
                                <option value="midodareni">Midodareni</option>
                                <option value="pemberkatan">Pemberkatan</option>
                                <option value="tea_ceremony">Tea Ceremony</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="wo-label">Tanggal Acara <span class="req">*</span></label>
                            <input type="date" class="wo-control" name="event_date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="wo-label">Jumlah Tamu Undangan</label>
                            <input type="number" class="wo-control" name="guest_count" id="guestCountInput" placeholder="Contoh: 300">
                        </div>
                        <div class="col-md-6">
                            <label class="wo-label">Status Event</label>
                            <select class="wo-select" name="event_status">
                                <option value="draft">Draft</option>
                                <option value="upcoming" selected>Upcoming</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="wo-label">Catatan Event</label>
                            <textarea class="wo-control" name="event_notes" rows="2" placeholder="Catatan khusus untuk pelaksanaan event ini..."></textarea>
                            <small class="text-muted d-block mt-1">Waktu mulai & selesai event akan otomatis disinkronkan dari jadwal vendor.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Team Members -->
            <div class="wo-section-card" id="teamSection" style="display: none;">
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
                        <div class="wo-item-row team-item">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-6">
                                    <label class="wo-label">Pilih Member</label>
                                    <select class="wo-select team-member-select" name="team_member_id[]">
                                        <option value="">-- Pilih Member --</option>
                                        @foreach ($members as $member)
                                            <option value="{{ $member->id }}" data-default-role="{{ $member->position ?? 'Team Member' }}">
                                                {{ $member->name }} ({{ $member->call_sign ?: '-' }}) — {{ $member->position ?? 'Member' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="wo-label">Role di Event</label>
                                    <input type="text" list="teamRoleOptions" class="wo-control team-member-role" name="team_member_role[]" placeholder="Contoh: Lead Coordinator, Coordinator, Crew">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="wo-btn-remove remove-team-btn" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Vendor & Paket -->
            <div class="wo-section-card" id="vendorSection" style="display: none;">
                <div class="wo-section-head">
                    <div class="wo-section-title">
                        <div class="wo-section-icon">
                            <i class="bi bi-shop"></i>
                        </div>
                        <div>
                            <h5>4. Jadwal Vendor & Penanggung Jawab (PIC)</h5>
                            <small>Pilih paket vendor atau vendor kustom beserta member yang bertanggung jawab</small>
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
                                        data-vendors="{{ json_encode($package->vendors->map(function ($v) { return ['id' => $v->id, 'name' => $v->name, 'category' => $v->category->name ?? 'N/A']; })) }}">
                                        {{ $package->name }} ({{ $package->vendors->count() }} vendor)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="packageVendorsDisplay" class="mb-3" style="display: none;">
                        <label class="wo-label mb-2">Daftar Vendor dalam Paket & Penugasan PIC:</label>
                        <div id="packageVendorsList"></div>
                    </div>

                    <div id="customVendorSection" style="display: block;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <label class="wo-label mb-0">Daftar Vendor Kustom</label>
                                <small class="text-muted">Tambahkan vendor dan tentukan member penanggung jawabnya</small>
                            </div>
                            <button type="button" class="wo-btn-add" id="addVendorBtn">
                                <i class="bi bi-plus-lg"></i> Tambah Vendor
                            </button>
                        </div>

                        <div class="vendor-list" id="vendorList">
                            <div class="wo-item-row vendor-item">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-3">
                                        <label class="wo-label">Pilih Vendor</label>
                                        <select class="wo-select" name="vendor_id[]">
                                            <option value="">-- Pilih Vendor --</option>
                                            @foreach ($vendors as $vendor)
                                                <option value="{{ $vendor->id }}">{{ $vendor->name }} - {{ $vendor->category ? $vendor->category->name : 'N/A' }}</option>
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
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-4">
                <a href="{{ route('admin.events.index') }}" class="wo-btn-outline">Batal</a>
                <button type="submit" class="wo-btn-primary">
                    <i class="bi bi-check2-circle"></i> Simpan Event
                </button>
            </div>
        </form>
    </div>
</div>

@php
    $availableMembersJson = $members->map(function ($m) {
        return [
            'id' => $m->id,
            'label' => $m->name . ' (' . ($m->call_sign ?: '-') . ')',
        ];
    })->values();
@endphp

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bookingSelect = document.getElementById('bookingSelect');
    const bookingInfo = document.getElementById('bookingInfo');
    const eventDetailSection = document.getElementById('eventDetailSection');
    const teamSection = document.getElementById('teamSection');
    const vendorSection = document.getElementById('vendorSection');
    const packageSelect = document.getElementById('packageSelect');
    const customVendorSection = document.getElementById('customVendorSection');
    const packageVendorsDisplay = document.getElementById('packageVendorsDisplay');
    const packageVendorsList = document.getElementById('packageVendorsList');
    const addVendorBtn = document.getElementById('addVendorBtn');
    const vendorList = document.getElementById('vendorList');
    const addTeamMemberBtn = document.getElementById('addTeamMemberBtn');
    const teamList = document.getElementById('teamList');
    const guestCountInput = document.getElementById('guestCountInput');

    const availableMembers = @json($availableMembersJson);

    function renderPackageVendors() {
        const selectedOption = packageSelect.options[packageSelect.selectedIndex];

        if (packageSelect.value && selectedOption) {
            customVendorSection.style.display = 'none';
            packageVendorsDisplay.style.display = 'block';

            const vendors = JSON.parse(selectedOption.getAttribute('data-vendors') || '[]');
            let memberOptionsHTML = '<option value="">Tidak ditugaskan</option>';
            availableMembers.forEach(m => {
                memberOptionsHTML += `<option value="${m.id}">${m.label}</option>`;
            });

            let vendorHTML = '';

            if (vendors.length > 0) {
                vendors.forEach(vendor => {
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
                                    <input type="time" class="wo-control" name="package_vendor_start_time[]" value="09:00">
                                </div>
                                <div class="col-md-2">
                                    <label class="wo-label">Waktu Selesai</label>
                                    <input type="time" class="wo-control" name="package_vendor_end_time[]" value="17:00">
                                </div>
                                <div class="col-md-2">
                                    <label class="wo-label">Status</label>
                                    <select class="wo-select" name="package_vendor_status[]">
                                        <option value="pending" selected>Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
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

    bookingSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];

        if (this.value) {
            document.getElementById('bookingClient').textContent = selectedOption.getAttribute('data-client');
            document.getElementById('bookingVenue').textContent = selectedOption.getAttribute('data-venue');
            document.getElementById('bookingPackage').textContent = selectedOption.getAttribute('data-package');

            const guestVal = selectedOption.getAttribute('data-guest');
            if (guestCountInput && !guestCountInput.value && guestVal) {
                guestCountInput.value = guestVal;
            }

            const pkgId = selectedOption.getAttribute('data-package-id');
            if (pkgId && packageSelect) {
                packageSelect.value = pkgId;
                renderPackageVendors();
            }

            bookingInfo.style.display = 'block';
            eventDetailSection.style.display = 'block';
            teamSection.style.display = 'block';
            vendorSection.style.display = 'block';
        } else {
            bookingInfo.style.display = 'none';
            eventDetailSection.style.display = 'none';
            teamSection.style.display = 'none';
            vendorSection.style.display = 'none';
        }
    });

    packageSelect.addEventListener('change', renderPackageVendors);

    if (bookingSelect.value) {
        bookingSelect.dispatchEvent(new Event('change'));
    }

    addVendorBtn.addEventListener('click', function() {
        const firstVendorItem = vendorList.querySelector('.vendor-item');
        const newVendorItem = firstVendorItem.cloneNode(true);

        newVendorItem.querySelectorAll('select').forEach(select => {
            if (select.name === 'vendor_status[]') {
                select.value = 'pending';
            } else {
                select.value = '';
            }
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
