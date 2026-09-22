@extends('layouts.admin')

@section('title', 'Tambah Event Baru')
@section('page-title', 'Form Tambah Event')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Tambah Event Baru</h2>
                    <p class="text-muted mb-0">Tambahkan event seperti Akad Nikah, Resepsi, dll ke booking</p>
                </div>
                <div>
                    <a href="{{ route('admin.event_day.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" form="eventForm" class="btn btn-dark">Simpan</button>
                </div>
            </div>

            <form id="eventForm" method="POST" action="{{ route('admin.event_day.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Section 1: Pilih Booking -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">Pilih Booking</h5>
                                <small class="text-muted">Pilih booking client yang sudah approved</small>
                            </div>
                            <span class="badge bg-danger ms-auto">WAJIB</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Pilih Booking <span class="text-danger">*</span></label>
                                <select class="form-select" name="booking_id" id="bookingSelect" required>
                                    <option value="">Pilih Booking</option>
                                    @foreach($bookings as $booking)
                                        <option value="{{ $booking->id }}" 
                                                data-client="{{ $booking->client->groom_name }} & {{ $booking->client->bride_name }}"
                                                data-venue="{{ $booking->venue_name }}"
                                                data-package="{{ $booking->package->name ?? 'N/A' }}">
                                            {{ $booking->client->groom_name }} & {{ $booking->client->bride_name }} - {{ $booking->venue_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div id="bookingInfo" class="col-12" style="display: none;">
                                <div class="alert alert-info">
                                    <h6 class="fw-bold mb-2">Info Booking:</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Client:</small>
                                            <span id="bookingClient" class="fw-semibold">-</span>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Venue:</small>
                                            <span id="bookingVenue" class="fw-semibold">-</span>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Package:</small>
                                            <span id="bookingPackage" class="fw-semibold">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Detail Event -->
                <div class="card border-0 shadow-sm mb-4" id="eventDetailSection" style="display: none;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="bi bi-heart-fill"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">Detail Event</h5>
                                <small class="text-muted">Informasi tentang event (Akad, Resepsi, dll)</small>
                            </div>
                            <span class="badge bg-danger ms-auto">WAJIB</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Event <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="event_name" placeholder="Contoh: Akad Nikah" required>
                                <small class="text-muted">Contoh: Akad Nikah, Resepsi, Lamaran, Siraman</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Foto Event</label>
                                <input type="file" class="form-control" name="event_photo" accept="image/*">
                                <small class="text-muted">Upload foto event (optional)</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tipe Event</label>
                                <select class="form-select" name="event_type">
                                    <option value="">Pilih Tipe</option>
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
                                <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="event_date" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jumlah Tamu Undangan</label>
                                <input type="number" class="form-control" name="guest_count" placeholder="Contoh: 300">
                                <small class="text-muted">Jumlah tamu yang diundang</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select class="form-select" name="event_status">
                                    <option value="scheduled">Scheduled</option>
                                    <option value="ongoing">Ongoing</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan Event</label>
                                <textarea class="form-control" name="event_notes" rows="2" placeholder="Catatan khusus untuk event ini"></textarea>
                                <small class="text-muted">Waktu event akan otomatis sesuai jadwal vendor (dari paling awal sampai paling akhir)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Team Members -->
                <div class="card border-0 shadow-sm mb-4" id="teamSection" style="display: none;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fw-bold mb-0">Tim Organizer</h5>
                                <small class="text-muted">Assign anggota tim yang bertanggung jawab</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <label class="form-label fw-semibold mb-0">Anggota Tim</label>
                                <small class="text-muted d-block">Lead Director, Coordinator, dll</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-warning" id="addTeamMemberBtn">+ Tambah Tim</button>
                        </div>

                        <div class="team-list" id="teamList">
                            <div class="row g-3 mb-3 team-item">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Pilih Member</label>
                                    <select class="form-select" name="team_member_id[]">
                                        <option value="">Pilih Member</option>
                                        @foreach($members as $member)
                                            <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->call_sign }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold">Role</label>
                                    <input type="text" class="form-control" name="team_member_role[]" placeholder="Contoh: Lead Director, Coordinator">
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-danger w-100 remove-team-btn">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Vendor & Paket -->
                <div class="card border-0 shadow-sm mb-4" id="vendorSection" style="display: none;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="bi bi-shop"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fw-bold mb-0">Vendor & Paket</h5>
                                <small class="text-muted">Pilih paket tambahan atau vendor custom</small>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Pilih Paket Tambahan</label>
                                <select class="form-select" name="package_id" id="packageSelect">
                                    <option value="">Tidak Menggunakan Paket (Pilih Vendor Custom)</option>
                                    @foreach($packages as $package)
                                        <option value="{{ $package->id }}" 
                                                data-vendors="{{ json_encode($package->vendors->map(function($v) { return ['id' => $v->id, 'name' => $v->name, 'category' => $v->category->name ?? 'N/A']; })) }}">
                                            {{ $package->name }} - Rp {{ number_format($package->price, 0, ',', '.') }}
                                            ({{ $package->vendors->count() }} vendor)
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Package tambahan untuk event ini</small>
                            </div>
                        </div>

                        <div id="packageVendorsDisplay" class="mb-4" style="display: none;">
                            <h6 class="fw-bold mb-3">Vendor dalam paket:</h6>
                            <div id="packageVendorsList" class="border rounded p-3 bg-light"></div>
                        </div>

                        <div id="customVendorSection">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <label class="form-label fw-semibold mb-0">Vendor Kustom</label>
                                    <small class="text-muted d-block">Pilih vendor custom</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addVendorBtn">+ Tambah Vendor</button>
                            </div>

                            <div class="vendor-list" id="vendorList">
                                <div class="row g-3 mb-3 vendor-item">
                                    <div class="col-md-5">
                                        <label class="form-label fw-semibold">Pilih Vendor</label>
                                        <select class="form-select" name="vendor_id[]">
                                            <option value="">Pilih Vendor</option>
                                            @foreach($vendors as $vendor)
                                                <option value="{{ $vendor->id }}">
                                                    {{ $vendor->name }} - {{ $vendor->category ? $vendor->category->name : 'N/A' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold">Waktu Mulai</label>
                                        <input type="time" class="form-control" name="vendor_start_time[]">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold">Waktu Selesai</label>
                                        <input type="time" class="form-control" name="vendor_end_time[]">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold">Status</label>
                                        <select class="form-select" name="vendor_status[]">
                                            <option value="pending">Pending</option>
                                            <option value="confirmed">Confirmed</option>
                                            <option value="on_site">On-Site</option>
                                            <option value="loading">Loading</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-outline-danger w-100 remove-vendor-btn">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mb-4">
                    <a href="{{ route('admin.event_day.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                    <button type="submit" class="btn btn-dark px-5">
                        <i class="bi bi-check-circle me-2"></i> Simpan Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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

    bookingSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            document.getElementById('bookingClient').textContent = selectedOption.getAttribute('data-client');
            document.getElementById('bookingVenue').textContent = selectedOption.getAttribute('data-venue');
            document.getElementById('bookingPackage').textContent = selectedOption.getAttribute('data-package');
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

    packageSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            customVendorSection.style.display = 'none';
            packageVendorsDisplay.style.display = 'block';
            
            const vendors = JSON.parse(selectedOption.getAttribute('data-vendors') || '[]');
            let vendorHTML = '';
            
            if (vendors.length > 0) {
                vendors.forEach((vendor, index) => {
                    vendorHTML += `
                        <div class="row g-3 mb-3 p-3 border-bottom">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Vendor</label>
                                <input type="text" class="form-control bg-white" value="${vendor.name} - ${vendor.category}" readonly>
                                <input type="hidden" name="package_vendor_id[]" value="${vendor.id}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Waktu Mulai</label>
                                <input type="time" class="form-control" name="package_vendor_start_time[]">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Waktu Selesai</label>
                                <input type="time" class="form-control" name="package_vendor_end_time[]">
                            </div>
                            <div class="col-md-1 d-flex align-items-center justify-content-center">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    `;
                });
            } else {
                vendorHTML = '<div class="text-muted">Tidak ada vendor dalam paket ini.</div>';
            }
            
            packageVendorsList.innerHTML = vendorHTML;
        } else {
            customVendorSection.style.display = 'block';
            packageVendorsDisplay.style.display = 'none';
        }
    });

    addVendorBtn.addEventListener('click', function() {
        const firstVendorItem = vendorList.querySelector('.vendor-item');
        const newVendorItem = firstVendorItem.cloneNode(true);
        
        const selects = newVendorItem.querySelectorAll('select');
        selects.forEach(select => select.value = '');
        
        const inputs = newVendorItem.querySelectorAll('input');
        inputs.forEach(input => input.value = '');
        
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

    document.querySelectorAll('.remove-vendor-btn').forEach(btn => {
        attachRemoveEvent(btn);
    });

    addTeamMemberBtn.addEventListener('click', function() {
        const firstTeamItem = teamList.querySelector('.team-item');
        const newTeamItem = firstTeamItem.cloneNode(true);
        
        const selects = newTeamItem.querySelectorAll('select');
        selects.forEach(select => select.value = '');
        
        teamList.appendChild(newTeamItem);
        attachRemoveTeamEvent(newTeamItem.querySelector('.remove-team-btn'));
    });

    function attachRemoveTeamEvent(btn) {
        btn.addEventListener('click', function() {
            const teamItems = teamList.querySelectorAll('.team-item');
            if (teamItems.length > 1) {
                this.closest('.team-item').remove();
            }
        });
    }

    document.querySelectorAll('.remove-team-btn').forEach(btn => {
        attachRemoveTeamEvent(btn);
    });
});
</script>
@endsection
