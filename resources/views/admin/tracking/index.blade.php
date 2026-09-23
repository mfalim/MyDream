@extends('layouts.admin')

@section('title', 'Monitoring & Tracking Booking')
@section('page-title', 'Monitoring & Tracking Booking')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="display-6 fw-bold mb-2">Monitoring & Tracking Booking</h1>
            <p class="text-muted mb-0">Kelola booking client dan approve dengan assign coordinator</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari nama pengantin atau venue..." id="searchInput">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <select class="form-select" id="statusFilter">
                <option value="">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="fw-bold mb-0">{{ $bookings->count() }}</h3>
                    <p class="text-muted mb-0 small">Total Booking</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="fw-bold mb-0 text-warning">{{ $bookings->where('status', 'pending')->count() }}</h3>
                    <p class="text-muted mb-0 small">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="fw-bold mb-0 text-success">{{ $bookings->where('status', 'approved')->count() }}</h3>
                    <p class="text-muted mb-0 small">Approved</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="fw-bold mb-0 text-danger">{{ $bookings->where('status', 'rejected')->count() }}</h3>
                    <p class="text-muted mb-0 small">Rejected</p>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Paket</th>
                    <th>Venue</th>
                    <th>Total Harga</th>
                    <th>Event</th>
                    <th>Progress Vendor</th>
                    <th>Status</th>
                    <th>Coordinator</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="bookingTable">
                @forelse($bookings as $booking)
                @php
                    $statusClass = match($booking->status) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'secondary'
                    };
                    
                    $eventData = $eventsByBooking->get($booking->id);
                    $events = $eventData ? $eventData['events'] : collect();
                    $progress = $eventData ? $eventData['progress'] : 0;
                    $approvedCount = $eventData ? $eventData['approved_count'] : 0;
                    $totalCount = $eventData ? $eventData['total_count'] : 0;
                    
                    $progressClass = match(true) {
                        $progress >= 75 => 'success',
                        $progress >= 50 => 'info',
                        $progress >= 25 => 'warning',
                        default => 'danger'
                    };
                    
                    $leadCoordinator = null;
                    if ($events->isNotEmpty()) {
                        $firstEvent = $events->first();
                        $leadMember = $firstEvent->eventMembers->firstWhere('role', 'Lead Coordinator');
                        $leadCoordinator = $leadMember?->member;
                    }
                    
                    $searchText = strtolower(($booking->client->groom_name ?? '') . ' ' . ($booking->client->bride_name ?? '') . ' ' . $booking->venue_name);
                @endphp
                <tr data-search="{{ $searchText }}" data-status="{{ $booking->status }}">
                    <td><strong>#{{ $booking->id }}</strong></td>
                    <td>
                        <div>
                            <strong>{{ $booking->client->groom_name ?? 'N/A' }} & {{ $booking->client->bride_name ?? 'N/A' }}</strong>
                            <br>
                            <small class="text-muted">{{ $booking->client->email ?? '-' }}</small>
                        </div>
                    </td>
                    <td>{{ $booking->package->name ?? 'Custom' }}</td>
                    <td>
                        <div>
                            {{ $booking->venue_name }}
                            @if($booking->venue_city)
                            <br><small class="text-muted">{{ $booking->venue_city }}</small>
                            @endif
                        </div>
                    </td>
                    <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                    <td>
                        @if($events->isNotEmpty())
                            <span class="badge bg-info">{{ $events->count() }} Event</span>
                        @else
                            <span class="text-muted small">Belum ada</span>
                        @endif
                    </td>
                    <td>
                        @if($totalCount > 0)
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height: 20px; min-width: 80px;">
                                <div class="progress-bar bg-{{ $progressClass }}" role="progressbar" style="width: {{ $progress }}%">
                                    {{ round($progress) }}%
                                </div>
                            </div>
                            <small class="text-muted">{{ $approvedCount }}/{{ $totalCount }}</small>
                        </div>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-{{ $statusClass }}">{{ ucfirst($booking->status) }}</span>
                    </td>
                    <td>
                        @if($leadCoordinator)
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 11px;">
                                {{ strtoupper(substr($leadCoordinator->name, 0, 2)) }}
                            </div>
                            <small class="fw-semibold">{{ $leadCoordinator->call_sign }}</small>
                        </div>
                        @else
                        <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary approve-btn" data-booking-id="{{ $booking->id }}" data-bs-toggle="modal" data-bs-target="#approveModal{{ $booking->id }}">
                            <i class="bi bi-check-circle"></i> Kelola
                        </button>
                        @if($events->isNotEmpty())
                        <a href="{{ route('admin.tracking.show', $events->first()->id) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                        @endif
                    </td>
                </tr>

                <!-- Modal Approve -->
                <div class="modal fade" id="approveModal{{ $booking->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Kelola Booking #{{ $booking->id }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Client</label>
                                    <p class="mb-0">{{ $booking->client->groom_name ?? 'N/A' }} & {{ $booking->client->bride_name ?? 'N/A' }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Venue</label>
                                    <p class="mb-0">{{ $booking->venue_name }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Total Harga</label>
                                    <p class="mb-0">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                                </div>
                                <hr>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Status Booking <span class="text-danger">*</span></label>
                                    <select class="form-select" id="status{{ $booking->id }}">
                                        <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ $booking->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ $booking->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                                <div class="mb-3" id="coordinatorSection{{ $booking->id }}" style="display: {{ $booking->status === 'approved' ? 'block' : 'none' }};">
                                    <label class="form-label fw-semibold">Assign Lead Coordinator</label>
                                    <select class="form-select" id="coordinator{{ $booking->id }}">
                                        <option value="">Pilih Coordinator (Optional)</option>
                                        @foreach($members as $member)
                                            <option value="{{ $member->id }}" {{ $leadCoordinator && $leadCoordinator->id == $member->id ? 'selected' : '' }}>
                                                {{ $member->name }} ({{ $member->call_sign }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Coordinator akan otomatis ditambahkan ke event pertama</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="button" class="btn btn-primary save-booking-btn" data-booking-id="{{ $booking->id }}">Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-4">
                        <p class="text-muted mb-0">Belum ada booking</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const rows = document.querySelectorAll('#bookingTable tr[data-search]');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedStatus = statusFilter.value;

        rows.forEach(row => {
            const searchText = row.getAttribute('data-search');
            const rowStatus = row.getAttribute('data-status');
            
            const matchesSearch = searchText.includes(searchTerm);
            const matchesStatus = !selectedStatus || rowStatus === selectedStatus;
            
            row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);

    document.querySelectorAll('[id^="status"]').forEach(select => {
        select.addEventListener('change', function() {
            const bookingId = this.id.replace('status', '');
            const coordinatorSection = document.getElementById(`coordinatorSection${bookingId}`);
            
            if (this.value === 'approved') {
                coordinatorSection.style.display = 'block';
            } else {
                coordinatorSection.style.display = 'none';
            }
        });
    });

    document.querySelectorAll('.save-booking-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const bookingId = this.dataset.bookingId;
            const status = document.getElementById(`status${bookingId}`).value;
            const coordinatorId = document.getElementById(`coordinator${bookingId}`).value;
            
            fetch(`/admin/tracking/booking/${bookingId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ 
                    status: status,
                    coordinator_id: coordinatorId || null
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Status booking berhasil diupdate');
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>
@endsection
