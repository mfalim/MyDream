@extends('layouts.admin')

@section('title', 'Edit Event')
@section('page-title', 'Form Edit Event')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Edit Event</h2>
                    <p class="text-muted mb-0">Edit detail event seperti Akad Nikah, Resepsi, dll</p>
                </div>
                <div>
                    <a href="{{ route('admin.event_day.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" form="eventForm" class="btn btn-dark">Update Event</button>
                </div>
            </div>

            <form id="eventForm" method="POST" action="{{ route('admin.event.update', $event->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Section 1: Info Booking (Read Only) -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">Info Booking</h5>
                                <small class="text-muted">Booking yang terkait dengan event ini</small>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <div class="row">
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Client:</small>
                                    <span class="fw-semibold">{{ $event->booking->client->groom_name }} & {{ $event->booking->client->bride_name }}</span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Venue:</small>
                                    <span class="fw-semibold">{{ $event->booking->venue_name }}</span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Package:</small>
                                    <span class="fw-semibold">{{ $event->booking->package->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Detail Event -->
                <div class="card border-0 shadow-sm mb-4">
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
                                <input type="text" class="form-control" name="event_name" value="{{ $event->name }}" placeholder="Contoh: Akad Nikah" required>
                                <small class="text-muted">Contoh: Akad Nikah, Resepsi, Lamaran, Siraman</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Foto Event</label>
                                <input type="file" class="form-control" name="event_photo" accept="image/*">
                                @if($event->photo)
                                    <small class="text-muted">Foto saat ini: <a href="{{ asset('storage/' . $event->photo) }}" target="_blank">Lihat foto</a></small>
                                @else
                                    <small class="text-muted">Belum ada foto</small>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tipe Event</label>
                                <select class="form-select" name="event_type">
                                    <option value="">Pilih Tipe</option>
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
                                <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="event_date" value="{{ $event->event_date->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jumlah Tamu Undangan</label>
                                <input type="number" class="form-control" name="guest_count" value="{{ $event->guest_count }}" placeholder="Contoh: 300">
                                <small class="text-muted">Jumlah tamu yang diundang</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select class="form-select" name="event_status">
                                    <option value="scheduled" {{ $event->status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                    <option value="ongoing" {{ $event->status == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                    <option value="completed" {{ $event->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $event->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan Event</label>
                                <textarea class="form-control" name="event_notes" rows="2" placeholder="Catatan khusus untuk event ini">{{ $event->notes }}</textarea>
                                <small class="text-muted">Waktu event akan otomatis sesuai jadwal vendor (dari paling awal sampai paling akhir)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Team Members -->
                <div class="card border-0 shadow-sm mb-4">
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
                            @forelse($event->teamMembers as $member)
                            <div class="row g-3 mb-3 team-item">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Pilih User</label>
                                    <select class="form-select" name="team_member_id[]">
                                        <option value="">Pilih User</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $member->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold">Role</label>
                                    <select class="form-select" name="team_member_role[]">
                                        <option value="lead_director" {{ $member->role == 'lead_director' ? 'selected' : '' }}>Lead Director</option>
                                        <option value="co_director" {{ $member->role == 'co_director' ? 'selected' : '' }}>Co Director</option>
                                        <option value="coordinator" {{ $member->role == 'coordinator' ? 'selected' : '' }}>Coordinator</option>
                                        <option value="technical" {{ $member->role == 'technical' ? 'selected' : '' }}>Technical</option>
                                        <option value="documentation" {{ $member->role == 'documentation' ? 'selected' : '' }}>Documentation</option>
                                        <option value="other" {{ $member->role == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-danger w-100 remove-team-btn">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            @empty
                            <div class="row g-3 mb-3 team-item">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Pilih User</label>
                                    <select class="form-select" name="team_member_id[]">
                                        <option value="">Pilih User</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold">Role</label>
                                    <select class="form-select" name="team_member_role[]">
                                        <option value="lead_director">Lead Director</option>
                                        <option value="co_director">Co Director</option>
                                        <option value="coordinator">Coordinator</option>
                                        <option value="technical">Technical</option>
                                        <option value="documentation">Documentation</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-danger w-100 remove-team-btn">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Section 4: Vendor & Paket -->
                <div class="card border-0 shadow-sm mb-4">
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
                                                {{ $event->package_id == $package->id ? 'selected' : '' }}
                                                data-vendors="{{ json_encode($package->vendors->map(function($v) { return ['id' => $v->id, 'name' => $v->name, 'category' => $v->category->name ?? 'N/A']; })) }}">
                                            {{ $package->name }} - Rp {{ number_format($package->price, 0, ',', '.') }}
                                            ({{ $package->vendors->count() }} vendor)
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Package tambahan untuk event ini</small>
                            </div>
                        </div>

                        <div id="packageVendorsDisplay" class="mb-4" style="display: {{ $event->package_id ? 'block' : 'none' }};">
                            <h6 class="fw-bold mb-3">Vendor dalam paket:</h6>
                            <div id="packageVendorsList" class="border rounded p-3 bg-light"></div>
                        </div>

                        <div id="customVendorSection" style="display: {{ $event->package_id ? 'none' : 'block' }};">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <label class="form-label fw-semibold mb-0">Vendor Kustom</label>
                                    <small class="text-muted d-block">Pilih vendor custom</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addVendorBtn">+ Tambah Vendor</button>
                            </div>

                            <div class="vendor-list" id="vendorList">
                                @forelse($event->eventVendors as $eventVendor)
                                <div class="row g-3 mb-3 vendor-item">
                                    <div class="col-md-5">
                                        <label class="form-label fw-semibold">Pilih Vendor</label>
                                        <select class="form-select" name="vendor_id[]">
                                            <option value="">Pilih Vendor</option>
                                            @foreach($vendors as $vendor)
                                                <option value="{{ $vendor->id }}" {{ $eventVendor->vendor_id == $vendor->id ? 'selected' : '' }}>
                                                    {{ $vendor->name }} - {{ $vendor->category ? $vendor->category->name : 'N/A' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold">Waktu Mulai</label>
                                        <input type="time" class="form-control" name="vendor_start_time[]" value="{{ $eventVendor->start_time ? \Carbon\Carbon::parse($eventVendor->start_time)->format('H:i') : '' }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold">Waktu Selesai</label>
                                        <input type="time" class="form-control" name="vendor_end_time[]" value="{{ $eventVendor->end_time ? \Carbon\Carbon::parse($eventVendor->end_time)->format('H:i') : '' }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold">Status</label>
                                        <select class="form-select" name="vendor_status[]">
                                            <option value="pending" {{ $eventVendor->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="confirmed" {{ $eventVendor->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                            <option value="on_site" {{ $eventVendor->status == 'on_site' ? 'selected' : '' }}>On-Site</option>
                                            <option value="loading" {{ $eventVendor->status == 'loading' ? 'selected' : '' }}>Loading</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-outline-danger w-100 remove-vendor-btn">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                @empty
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
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mb-4">
                    <a href="{{ route('admin.event_day.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                    <button type="submit" class="btn btn-dark px-5">
                        <i class="bi bi-check-circle me-2"></i> Update Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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

    // Initialize package vendors if package is selected
    if (packageSelect.value) {
        updatePackageVendors(true);
    }

    function updatePackageVendors(isInitialLoad = false) {
        const selectedOption = packageSelect.options[packageSelect.selectedIndex];
        
        if (packageSelect.value) {
            customVendorSection.style.display = 'none';
            packageVendorsDisplay.style.display = 'block';
            
            const vendors = JSON.parse(selectedOption.getAttribute('data-vendors') || '[]');
            
            // Get existing event vendors for this event
            const existingVendors = @json($event->eventVendors->keyBy('vendor_id'));
            
            let vendorHTML = '';
            
            if (vendors.length > 0) {
                vendors.forEach((vendor, index) => {
                    const existingVendor = existingVendors[vendor.id];
                    const startTime = existingVendor && existingVendor.start_time ? existingVendor.start_time.substring(0, 5) : '';
                    const endTime = existingVendor && existingVendor.end_time ? existingVendor.end_time.substring(0, 5) : '';
                    
                    vendorHTML += `
                        <div class="row g-3 mb-3 p-3 border-bottom">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Vendor</label>
                                <input type="text" class="form-control bg-white" value="${vendor.name} - ${vendor.category}" readonly>
                                <input type="hidden" name="package_vendor_id[]" value="${vendor.id}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Waktu Mulai</label>
                                <input type="time" class="form-control" name="package_vendor_start_time[]" value="${startTime}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Waktu Selesai</label>
                                <input type="time" class="form-control" name="package_vendor_end_time[]" value="${endTime}">
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
    }

    packageSelect.addEventListener('change', updatePackageVendors);

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
