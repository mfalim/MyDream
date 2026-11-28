@extends('layouts.admin')

@section('title', 'Monitoring & Tracking Booking')
@section('page-title', 'Monitoring & Tracking Booking')

@push('styles')
    @vite('resources/css/admin/tracking.css')
@endpush

@section('content')
<div class="tracking-page">
    <div class="tracking-header">
        <div>
            <div class="tracking-eyebrow">OPERASIONAL &amp; PEMESANAN KLIEN</div>
            <h1>Monitoring &amp; Tracking Booking</h1>
            <p>Kelola kesiapan operasional booking, status pembayaran klien, dan penugasan koordinator acara</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.payments.index') }}" class="btn btn-wo-primary">
                <i class="bi bi-wallet2"></i>
                <span>Pusat Keuangan &amp; Kas</span>
            </a>
        </div>
    </div>

    {{-- 4 KPI Stat Cards (Selaras dengan tema MyDream) --}}
    <div class="tracking-stats-grid">
        <div class="tracking-stat-card">
            <div class="tracking-stat-info">
                <small>Total Booking</small>
                <strong>{{ $bookings->count() }}</strong>
            </div>
            <div class="tracking-stat-icon" style="background: #F4E4E6; color: #7A1F2B;">
                <i class="bi bi-journal-check"></i>
            </div>
        </div>

        <div class="tracking-stat-card">
            <div class="tracking-stat-info">
                <small>Menunggu Persetujuan</small>
                <strong style="color: #9a6507;">{{ $bookings->where('status', 'pending')->count() }}</strong>
            </div>
            <div class="tracking-stat-icon" style="background: #fff3d6; color: #9a6507;">
                <i class="bi bi-hourglass-split"></i>
            </div>
        </div>

        <div class="tracking-stat-card">
            <div class="tracking-stat-info">
                <small>Disetujui (Approved)</small>
                <strong style="color: #0d6e4c;">{{ $bookings->where('status', 'approved')->count() }}</strong>
            </div>
            <div class="tracking-stat-icon" style="background: #dff7ee; color: #0d6e4c;">
                <i class="bi bi-check-circle-fill"></i>
            </div>
        </div>

        <div class="tracking-stat-card">
            <div class="tracking-stat-info">
                <small>Ditolak (Rejected)</small>
                <strong style="color: #b91c1c;">{{ $bookings->where('status', 'rejected')->count() }}</strong>
            </div>
            <div class="tracking-stat-icon" style="background: #fee2e2; color: #b91c1c;">
                <i class="bi bi-x-circle-fill"></i>
            </div>
        </div>
    </div>

    {{-- Search & Status Filter Toolbar --}}
    <div class="tracking-toolbar">
        <div class="tracking-search-box">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Cari nama pengantin atau venue..." id="searchInput">
        </div>
        <div class="tracking-filter-tabs" role="tablist">
            <button type="button" class="tracking-filter-tab active" data-status-filter="">Semua</button>
            <button type="button" class="tracking-filter-tab" data-status-filter="pending">Pending</button>
            <button type="button" class="tracking-filter-tab" data-status-filter="approved">Approved</button>
            <button type="button" class="tracking-filter-tab" data-status-filter="rejected">Rejected</button>
        </div>
    </div>

    <div class="tracking-table-card">
        <div class="table-responsive">
            <table class="tracking-table align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Paket</th>
                        <th>Venue</th>
                        <th>Kontrak &amp; Kas Klien</th>
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

                    $payStatus = $booking->payment_status ?: 'unpaid';
                    $payBadgeClass = match($payStatus) {
                        'paid' => 'success',
                        'dp_paid' => 'info',
                        'awaiting_verification' => 'warning',
                        default => 'danger'
                    };
                    $payLabel = match($payStatus) {
                        'paid' => 'Lunas',
                        'dp_paid' => 'DP Terbayar',
                        'awaiting_verification' => 'Cek Bukti Transfer',
                        default => 'Belum Bayar'
                    };
                    $paidAmt = (float) ($booking->paid_amount ?? 0);
                    $totalAmt = (float) ($booking->total_price ?? 0);
                    $remAmt = max(0, $totalAmt - $paidAmt);
                    
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
                        $leadMember = $firstEvent->eventMembers->firstWhere('role', 'Lead Coordinator')
                            ?? $firstEvent->eventMembers->firstWhere('role', 'Coordinator');
                        $leadCoordinator = $leadMember?->member;
                    }
                    
                    $searchText = strtolower(($booking->client->groom_name ?? '') . ' ' . ($booking->client->bride_name ?? '') . ' ' . $booking->venue_name);
                @endphp
                <tr data-search="{{ $searchText }}" data-status="{{ $booking->status }}">
                    <td><span class="booking-id-pill">#{{ $booking->id }}</span></td>
                    <td>
                        <div class="client-couple-name">{{ $booking->client->groom_name ?? 'N/A' }} &amp; {{ $booking->client->bride_name ?? 'N/A' }}</div>
                        <div class="client-meta">{{ $booking->client->email ?? '-' }}</div>
                    </td>
                    <td><span class="fw-semibold">{{ $booking->package->name ?? 'Custom' }}</span></td>
                    <td>
                        <div class="fw-semibold">{{ $booking->venue_name }}</div>
                        @if($booking->venue_city)
                            <div class="client-meta">{{ $booking->venue_city }}</div>
                        @endif
                    </td>
                    <td>
                        <div class="fw-bold" style="font-variant-numeric: tabular-nums;">Rp {{ number_format($totalAmt, 0, ',', '.') }}</div>
                        <div class="mt-1">
                            <span class="badge bg-{{ $payBadgeClass }}-subtle text-{{ $payBadgeClass }} border border-{{ $payBadgeClass }}-subtle">
                                {{ $payLabel }}
                            </span>
                        </div>
                        <div class="client-meta mt-1" style="font-variant-numeric: tabular-nums;">
                            Masuk: Rp {{ number_format($paidAmt, 0, ',', '.') }}
                        </div>
                    </td>
                    <td>
                        @if($events->isNotEmpty())
                            <span class="badge rounded-pill" style="background: #F4E4E6; color: #7A1F2B; border: 1px solid #e4c2c7;">{{ $events->count() }} Event</span>
                        @else
                            <span class="client-meta">Belum ada</span>
                        @endif
                    </td>
                    <td>
                        @if($totalCount > 0)
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height: 18px; min-width: 80px; border-radius: 999px; background: #f2e8e0;">
                                <div class="progress-bar bg-{{ $progressClass }}" role="progressbar" style="width: {{ $progress }}%; font-size: 11px; font-weight: 700;">
                                    {{ round($progress) }}%
                                </div>
                            </div>
                            <span class="client-meta" style="font-variant-numeric: tabular-nums;">{{ $approvedCount }}/{{ $totalCount }}</span>
                        </div>
                        @else
                        <span class="client-meta">-</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-{{ $statusClass }}-subtle text-{{ $statusClass }} border border-{{ $statusClass }}-subtle px-2 py-1">{{ ucfirst($booking->status) }}</span>
                    </td>
                    <td>
                        @if($leadCoordinator)
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-3 d-flex align-items-center justify-content-center fw-bold" style="width: 30px; height: 30px; font-size: 11.5px; background: #7A1F2B; color: #E2BE82;">
                                {{ strtoupper(substr($leadCoordinator->name, 0, 2)) }}
                            </div>
                            <span class="fw-semibold" style="font-size: 13px;">{{ $leadCoordinator->call_sign }}</span>
                        </div>
                        @else
                        <span class="client-meta">-</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-1">
                            <button class="btn btn-sm btn-wo-primary approve-btn" style="padding: 6px 12px; font-size: 12.5px;" data-booking-id="{{ $booking->id }}" data-bs-toggle="modal" data-bs-target="#approveModal{{ $booking->id }}">
                                <i class="bi bi-check-circle"></i> Kelola
                            </button>
                            <a href="{{ route('admin.payments.index', ['tab' => 'vendor_payouts', 'booking_id' => $booking->id]) }}" class="btn btn-sm btn-outline-success" title="Kelola Kas & Vendor Booking Ini">
                                <i class="bi bi-wallet2"></i>
                            </a>
                            @if($events->isNotEmpty())
                            <a href="{{ route('admin.tracking.show', $events->first()->id) }}" class="btn btn-sm btn-outline-secondary" title="Detail Tracking">
                                <i class="bi bi-eye"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-5">
                        <p class="client-meta mb-0">Belum ada booking</p>
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Approve / Kelola Booking (di luar <table> agar HTML valid) --}}
    @foreach($bookings as $booking)
        @php
            $payStatus = $booking->payment_status ?: 'unpaid';
            $payBadgeClass = match($payStatus) {
                'paid' => 'success',
                'dp_paid' => 'info',
                'awaiting_verification' => 'warning',
                default => 'danger'
            };
            $payLabel = match($payStatus) {
                'paid' => 'Lunas',
                'dp_paid' => 'DP Terbayar',
                'awaiting_verification' => 'Cek Bukti Transfer',
                default => 'Belum Bayar'
            };
            $paidAmt = (float) ($booking->paid_amount ?? 0);
            $totalAmt = (float) ($booking->total_price ?? 0);
            $remAmt = max(0, $totalAmt - $paidAmt);

            $eventData = $eventsByBooking->get($booking->id);
            $events = $eventData ? $eventData['events'] : collect();
            $firstEvent = $events->first();
            $leadMember = null;
            $leadCoordinator = null;
            $existingTeamMembers = collect();
            if ($firstEvent) {
                $leadMember = $firstEvent->eventMembers->firstWhere('role', 'Lead Coordinator')
                    ?? $firstEvent->eventMembers->firstWhere('role', 'Coordinator');
                $leadCoordinator = $leadMember?->member;
                $existingTeamMembers = $firstEvent->eventMembers
                    ->reject(fn ($em) => $leadMember && $em->id === $leadMember->id)
                    ->values();
            }
        @endphp
        <div class="modal fade tracking-modal" id="approveModal{{ $booking->id }}" data-booking-id="{{ $booking->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title mb-0">Kelola Booking #{{ $booking->id }}</h5>
                            <div class="modal-subtitle">
                                {{ $booking->invoice_number ?: ('Reservasi #' . $booking->id) }} · {{ $booking->package->name ?? 'Paket Custom' }}
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="booking-summary-grid">
                            <div class="booking-summary-item">
                                <div class="booking-summary-icon" style="background: #F4E4E6; color: #7A1F2B;">
                                    <i class="bi bi-hearts"></i>
                                </div>
                                <div>
                                    <div class="booking-summary-label">Mempelai / Client</div>
                                    <div class="booking-summary-value">{{ $booking->client->groom_name ?? 'N/A' }} &amp; {{ $booking->client->bride_name ?? 'N/A' }}</div>
                                    <div class="client-meta">{{ $booking->client->email ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="booking-summary-item">
                                <div class="booking-summary-icon" style="background: #F7ECDA; color: #8A6122;">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </div>
                                <div>
                                    <div class="booking-summary-label">Venue Acara</div>
                                    <div class="booking-summary-value">{{ $booking->venue_name }}</div>
                                    <div class="client-meta">{{ $booking->venue_city ?: 'Lokasi belum dilengkapi' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-kas-box">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-bold text-uppercase" style="color: #7A6760; letter-spacing: 0.05em;">Status Kas Klien</span>
                                <span class="badge bg-{{ $payBadgeClass }}-subtle text-{{ $payBadgeClass }} border border-{{ $payBadgeClass }}-subtle">{{ $payLabel }}</span>
                            </div>
                            <div class="fw-bold" style="color: #2B211C; font-variant-numeric: tabular-nums;">Kontrak: Rp {{ number_format($totalAmt, 0, ',', '.') }}</div>
                            <div class="small mt-1" style="color: #7A6760; font-variant-numeric: tabular-nums;">
                                Dana Masuk: <strong style="color: #0d6e4c;">Rp {{ number_format($paidAmt, 0, ',', '.') }}</strong>
                                &nbsp;·&nbsp; Sisa Piutang: <strong style="color: #7A1F2B;">Rp {{ number_format($remAmt, 0, ',', '.') }}</strong>
                            </div>
                        </div>

                        <div class="modal-section-card">
                            <label class="form-label fw-semibold mb-1" for="status{{ $booking->id }}">Status Booking <span class="text-danger">*</span></label>
                            <select class="form-select" id="status{{ $booking->id }}">
                                <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>Pending — Menunggu Persetujuan</option>
                                <option value="approved" {{ $booking->status === 'approved' ? 'selected' : '' }}>Approved — Disetujui &amp; Siapkan Tim</option>
                                <option value="rejected" {{ $booking->status === 'rejected' ? 'selected' : '' }}>Rejected — Ditolak</option>
                            </select>
                        </div>

                        <div id="coordinatorSection{{ $booking->id }}" style="display: {{ $booking->status === 'approved' ? 'block' : 'none' }};">
                            <div class="modal-section-card">
                                <div class="modal-section-head">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="modal-section-icon coord">
                                            <i class="bi bi-person-badge-fill"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold" style="color: #2B211C;">Coordinator Utama</div>
                                            <small class="text-muted">Penanggung jawab utama jalannya event</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-2">
                                    <div class="col-md-7">
                                        <label class="form-label small fw-semibold mb-1">Pilih Member</label>
                                        <select class="form-select coordinator-select" id="coordinator{{ $booking->id }}" data-booking-id="{{ $booking->id }}">
                                            <option value="">Pilih coordinator utama</option>
                                            @foreach($members as $member)
                                                <option value="{{ $member->id }}" data-base-label="{{ $member->name }} ({{ $member->call_sign }})" {{ $leadCoordinator && $leadCoordinator->id == $member->id ? 'selected' : '' }}>
                                                    {{ $member->name }} ({{ $member->call_sign }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label small fw-semibold mb-1">Role</label>
                                        <select class="form-select" id="coordinatorRole{{ $booking->id }}">
                                            <option value="Lead Coordinator" {{ ($leadMember?->role ?? 'Lead Coordinator') === 'Lead Coordinator' ? 'selected' : '' }}>
                                                Lead Coordinator
                                            </option>
                                            <option value="Coordinator" {{ $leadMember?->role === 'Coordinator' ? 'selected' : '' }}>
                                                Coordinator
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <label class="form-label small fw-semibold mb-1">Catatan Coordinator</label>
                                    <textarea class="form-control" id="coordinatorNotes{{ $booking->id }}" rows="2" placeholder="Catatan khusus untuk coordinator...">{{ $leadMember?->notes ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div id="teamSection{{ $booking->id }}" style="display: {{ $booking->status === 'approved' ? 'block' : 'none' }};">
                            <div class="modal-section-card mb-0">
                                <div class="modal-section-head">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="modal-section-icon team">
                                            <i class="bi bi-people-fill"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold" style="color: #2B211C;">Anggota Tim Event</div>
                                            <small class="text-muted">Personel lapangan pendamping (selain Coordinator Utama)</small>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-wo-gold add-team-member-btn" data-booking-id="{{ $booking->id }}">
                                        <i class="bi bi-plus-lg"></i> Tambah Tim
                                    </button>
                                </div>

                                <div class="modal-rule-hint">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Satu personel hanya dapat memegang <strong>satu peran</strong> pada event yang sama. Member yang terpilih sebagai Coordinator Utama otomatis terkunci di daftar ini.
                                </div>

                                <div class="team-list" id="teamList{{ $booking->id }}">
                                    @if($existingTeamMembers->isNotEmpty())
                                        @foreach($existingTeamMembers as $existingTeam)
                                            <div class="team-row-card team-item">
                                                <div class="row g-2 align-items-end">
                                                    <div class="col-md-5">
                                                        <label class="form-label small fw-semibold mb-1">Pilih Member</label>
                                                        <select class="form-select form-select-sm team-member-select" data-booking-id="{{ $booking->id }}">
                                                            <option value="">Pilih Member</option>
                                                            @foreach($members as $member)
                                                                <option value="{{ $member->id }}" data-base-label="{{ $member->name }} ({{ $member->call_sign }})" data-default-role="{{ $member->position ?? 'Team Member' }}" {{ (int) $existingTeam->member_id === (int) $member->id ? 'selected' : '' }}>
                                                                    {{ $member->name }} ({{ $member->call_sign }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label small fw-semibold mb-1">Role</label>
                                                        <input type="text" class="form-control form-control-sm team-member-role" placeholder="Contoh: Team Member" value="{{ $existingTeam->role ?: 'Team Member' }}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label small fw-semibold mb-1">Catatan</label>
                                                        <input type="text" class="form-control form-control-sm team-member-notes" placeholder="Catatan" value="{{ $existingTeam->notes ?? '' }}">
                                                    </div>
                                                    <div class="col-md-1 d-flex align-items-end">
                                                        <button type="button" class="btn btn-outline-danger btn-sm remove-team-btn w-100" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="team-row-card team-item">
                                            <div class="row g-2 align-items-end">
                                                <div class="col-md-5">
                                                    <label class="form-label small fw-semibold mb-1">Pilih Member</label>
                                                    <select class="form-select form-select-sm team-member-select" data-booking-id="{{ $booking->id }}">
                                                        <option value="">Pilih Member</option>
                                                        @foreach($members as $member)
                                                            <option value="{{ $member->id }}" data-base-label="{{ $member->name }} ({{ $member->call_sign }})" data-default-role="{{ $member->position ?? 'Team Member' }}">
                                                                {{ $member->name }} ({{ $member->call_sign }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-semibold mb-1">Role</label>
                                                    <input type="text" class="form-control form-control-sm team-member-role" placeholder="Contoh: Team Member" value="Team Member">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-semibold mb-1">Catatan</label>
                                                    <input type="text" class="form-control form-control-sm team-member-notes" placeholder="Catatan">
                                                </div>
                                                <div class="col-md-1 d-flex align-items-end">
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-team-btn w-100" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-wo-outline" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn-wo-primary save-booking-btn" data-booking-id="{{ $booking->id }}">
                            <i class="bi bi-check2-circle"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const filterTabs = document.querySelectorAll('[data-status-filter]');
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

    searchInput?.addEventListener('input', filterTable);
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            selectedStatus = this.getAttribute('data-status-filter') || '';
            filterTable();
        });
    });

    // Kunci otomatis member yang sudah terpilih agar Coordinator Utama tidak bisa merangkap Anggota Tim (dan sebaliknya)
    function syncMemberAvailability(bookingId, notifyOnClear = false) {
        const coordSelect = document.getElementById(`coordinator${bookingId}`);
        const teamSelects = Array.from(document.querySelectorAll(`#teamList${bookingId} .team-member-select`));
        if (!coordSelect) return;

        const coordVal = String(coordSelect.value || '');
        let clearedConflict = false;

        // Jika Coordinator baru dipilih dan ternyata sebelumnya ada di Anggota Tim, bersihkan dari Anggota Tim
        if (coordVal) {
            teamSelects.forEach(sel => {
                if (String(sel.value) === coordVal) {
                    sel.value = '';
                    clearedConflict = true;
                }
            });
        }

        const chosenTeamIds = new Set(
            teamSelects.map(sel => String(sel.value || '')).filter(Boolean)
        );

        // Update opsi pada dropdown Coordinator Utama
        Array.from(coordSelect.options).forEach(opt => {
            if (!opt.value) return;
            const baseLabel = opt.dataset.baseLabel || opt.textContent.trim();
            const usedInTeam = chosenTeamIds.has(String(opt.value));
            opt.disabled = usedInTeam;
            opt.textContent = usedInTeam ? `${baseLabel} — Sudah di Tim` : baseLabel;
        });

        // Update opsi pada setiap dropdown Anggota Tim Event
        teamSelects.forEach(sel => {
            const selfVal = String(sel.value || '');
            Array.from(sel.options).forEach(opt => {
                if (!opt.value) return;
                const val = String(opt.value);
                const baseLabel = opt.dataset.baseLabel || opt.textContent.trim();
                const isCoord = coordVal !== '' && val === coordVal;
                const isOtherTeam = val !== selfVal && chosenTeamIds.has(val);
                opt.disabled = isCoord || isOtherTeam;
                if (isCoord) {
                    opt.textContent = `${baseLabel} — Coordinator Utama`;
                } else if (isOtherTeam) {
                    opt.textContent = `${baseLabel} — Sudah dipilih`;
                } else {
                    opt.textContent = baseLabel;
                }
            });
        });

        if (clearedConflict && notifyOnClear && typeof window.showAdminToast === 'function') {
            window.showAdminToast(
                'Member yang dipilih sebagai Coordinator Utama otomatis dikeluarkan dari daftar Anggota Tim.',
                'info',
                'Penyesuaian Penugasan'
            );
        }
    }

    document.querySelectorAll('.tracking-modal').forEach(modal => {
        const bookingId = modal.dataset.bookingId;
        if (bookingId) {
            syncMemberAvailability(bookingId, false);
            modal.addEventListener('show.bs.modal', () => syncMemberAvailability(bookingId, false));
        }
    });

    document.querySelectorAll('.coordinator-select').forEach(select => {
        select.addEventListener('change', function() {
            syncMemberAvailability(this.dataset.bookingId, true);
        });
    });

    document.querySelectorAll('[id^="status"]').forEach(select => {
        select.addEventListener('change', function() {
            const bookingId = this.id.replace('status', '');
            const coordinatorSection = document.getElementById(`coordinatorSection${bookingId}`);
            const teamSection = document.getElementById(`teamSection${bookingId}`);

            if (this.value === 'approved') {
                coordinatorSection.style.display = 'block';
                teamSection.style.display = 'block';
                syncMemberAvailability(bookingId, false);
            } else {
                coordinatorSection.style.display = 'none';
                teamSection.style.display = 'none';
            }
        });
    });

    document.querySelectorAll('.save-booking-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const bookingId = this.dataset.bookingId;
            const status = document.getElementById(`status${bookingId}`).value;
            const coordinatorId = document.getElementById(`coordinator${bookingId}`).value;
            const coordinatorRole = document.getElementById(`coordinatorRole${bookingId}`).value;
            const coordinatorNotes = document.getElementById(`coordinatorNotes${bookingId}`)?.value || '';

            const teamMemberSelects = document.querySelectorAll(`#teamList${bookingId} .team-member-select`);
            const teamMemberRoles = document.querySelectorAll(`#teamList${bookingId} .team-member-role`);
            const teamMemberNotes = document.querySelectorAll(`#teamList${bookingId} .team-member-notes`);

            const teamMemberIds = [];
            const teamMemberRoleList = [];
            const teamMemberNotesList = [];
            const selectedIds = [];
            let duplicateFound = false;

            if (status === 'approved') {
                if (coordinatorId) {
                    selectedIds.push(String(coordinatorId));
                }

                teamMemberSelects.forEach((select, index) => {
                    const memberId = select.value;
                    if (!memberId) return;

                    const normalizedId = String(memberId);
                    if (selectedIds.includes(normalizedId)) {
                        duplicateFound = true;
                        return;
                    }

                    selectedIds.push(normalizedId);
                    teamMemberIds.push(memberId);
                    teamMemberRoleList.push(teamMemberRoles[index]?.value || 'Team Member');
                    teamMemberNotesList.push(teamMemberNotes[index]?.value || '');
                });

                if (duplicateFound) {
                    window.showAdminToast('Satu member tidak boleh dipilih lebih dari satu kali dalam satu event.', 'warning', 'Duplikasi Personel');
                    return;
                }

                if (teamMemberIds.length === 0 && !coordinatorId) {
                    window.showAdminToast('Pilih minimal satu Coordinator Utama atau Anggota Tim untuk menyetujui booking.', 'warning', 'Tim Belum Dipilih');
                    return;
                }
            }

            const originalHtml = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...';

            fetch(`/admin/tracking/booking/${bookingId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    status: status,
                    coordinator_id: status === 'approved' ? (coordinatorId || null) : null,
                    coordinator_role: coordinatorRole || 'Coordinator',
                    coordinator_notes: coordinatorNotes,
                    team_member_id: status === 'approved' ? teamMemberIds : [],
                    team_member_role: status === 'approved' ? teamMemberRoleList : [],
                    team_member_notes: status === 'approved' ? teamMemberNotesList : []
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    try {
                        sessionStorage.setItem('mydream_pending_toast', JSON.stringify({
                            type: 'success',
                            title: 'Booking Diperbarui',
                            message: data.message || 'Status booking & penugasan tim berhasil disimpan.'
                        }));
                    } catch (_) {}
                    const modalEl = document.getElementById(`approveModal${bookingId}`);
                    bootstrap.Modal.getInstance(modalEl)?.hide();
                    location.reload();
                } else {
                    this.disabled = false;
                    this.innerHTML = originalHtml;
                    window.showAdminToast(data.message || 'Gagal memperbarui status booking.', 'error', 'Gagal Menyimpan');
                }
            })
            .catch(() => {
                this.disabled = false;
                this.innerHTML = originalHtml;
                window.showAdminToast('Terjadi kendala koneksi ke server. Silakan coba lagi.', 'error', 'Kesalahan Jaringan');
            });
        });
    });

    document.querySelectorAll('.add-team-member-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const bookingId = this.dataset.bookingId;
            const teamList = document.getElementById(`teamList${bookingId}`);
            const firstItem = teamList.querySelector('.team-item');
            const newItem = firstItem.cloneNode(true);

            newItem.querySelector('.team-member-select').value = '';
            newItem.querySelector('.team-member-role').value = 'Team Member';
            newItem.querySelector('.team-member-notes').value = '';

            teamList.appendChild(newItem);
            attachRemoveTeamEvent(newItem.querySelector('.remove-team-btn'), bookingId);
            attachTeamMemberSelectionHandlers(newItem, bookingId);
            syncMemberAvailability(bookingId, false);
        });
    });

    function attachRemoveTeamEvent(btn, bookingId) {
        btn.addEventListener('click', function() {
            const teamList = this.closest('.team-list');
            const items = teamList.querySelectorAll('.team-item');
            if (items.length > 1) {
                this.closest('.team-item').remove();
            } else {
                const row = this.closest('.team-item');
                row.querySelector('.team-member-select').value = '';
                row.querySelector('.team-member-role').value = 'Team Member';
                row.querySelector('.team-member-notes').value = '';
            }
            syncMemberAvailability(bookingId, false);
        });
    }

    document.querySelectorAll('.team-item').forEach(item => {
        const bookingId = item.querySelector('.team-member-select')?.dataset?.bookingId;
        attachRemoveTeamEvent(item.querySelector('.remove-team-btn'), bookingId);
        attachTeamMemberSelectionHandlers(item, bookingId);
    });

    function attachTeamMemberSelectionHandlers(root, bookingId) {
        const select = root.querySelector('.team-member-select');
        const roleInput = root.querySelector('.team-member-role');
        if (!select || !roleInput) return;

        select.addEventListener('change', function() {
            const selected = this.selectedOptions[0];
            const defaultRole = selected?.dataset?.defaultRole || 'Team Member';
            if (!roleInput.value.trim() || roleInput.value.trim() === 'Team Member') {
                roleInput.value = defaultRole;
            }
            syncMemberAvailability(bookingId, false);
        });
    }
});
</script>
@endsection
