@extends('layouts.admin')

@section('title', 'Event Days')
@section('page-title', 'Event Days')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h1 class="display-6 fw-bold mb-2">Event Day (Hari Acara Pernikahan)</h1>
                <p class="text-muted mb-0">Monitoring menyeluruh pelaksanaan acara pada hari H</p>
            </div>
            <div>
                <a class="btn btn-dark px-4" href='{{ route('admin.events.create') }}'>
                    <i class="bi bi-plus-circle me-2"></i> Buat Acara Baru
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control border-start-0 ps-0"
                        placeholder="Cari nama pengantin, venue, atau koordinator..." id="searchInput">
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex gap-4">
                <div>
                    <span class="text-muted">Total Events:</span>
                    <strong>{{ $events->count() }}</strong>
                </div>
            </div>
        </div>

        <div class="row g-4" id="eventGrid">
            @forelse($events as $event)
                @php
                    $booking = $event->booking;
                    $client = $booking ? $booking->client : null;
                    $leadMember = $event->eventMembers->first();
                    $statusBadge = [
                        'draft' => 'secondary',
                        'scheduled' => 'secondary',
                        'upcoming' => 'primary',
                        'ongoing' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    ][$event->status] ?? 'secondary';

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
                        : ($effectiveStart ? date('H:i', strtotime($effectiveStart)) . ' WIB' : 'Belum ada jadwal');
                @endphp
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="position-relative">
                            @if ($eventPhotoUrl)
                                <img src="{{ $eventPhotoUrl }}" class="card-img-top" alt="Event" style="height: 200px; object-fit: cover;">
                            @else
                                <div class="d-flex align-items-center justify-content-center bg-light text-secondary" style="height: 200px;">
                                    <i class="bi bi-calendar-event fs-1" aria-label="Foto acara belum tersedia"></i>
                                </div>
                            @endif
                            <span class="badge bg-{{ $statusBadge }} text-white position-absolute top-0 start-0 m-2">
                                @if ($event->status === 'ongoing')
                                    <i class="bi bi-clock-fill me-1"></i>
                                @endif
                                {{ strtoupper($event->status) }}
                            </span>
                            <span class="badge bg-dark position-absolute top-0 end-0 m-2">
                                {{ $timeLabel }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                @if ($client)
                                    <h5 class="card-title fw-bold mb-1">
                                        {{ $client->groom_name }} & {{ $client->bride_name }}
                                    </h5>
                                @endif

                                <div class="text-muted small">
                                    <i class="bi bi-heart-fill me-1"></i>
                                    {{ $event->name }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <i class="bi bi-calendar-event text-muted me-1"></i>
                                <small class="text-muted">{{ $event->event_date->format('d F Y') }}</small>
                            </div>

                            @if ($booking)
                                <div class="mb-3">
                                    <i class="bi bi-geo-alt-fill text-muted me-1"></i>
                                    <small class="text-muted">
                                        {{ $booking->venue_name }}<br>
                                        {{ $booking->venue_city }} • {{ $booking->guest_count }} Tamu
                                    </small>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-light text-dark">TIM INTERNAL WO</span>
                                <span class="text-muted">{{ $event->eventMembers->count() }} Personel</span>
                            </div>

                            @if ($leadMember)
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <small class="d-block fw-bold">{{ $leadMember->member->name }}</small>
                                        <small class="text-muted">{{ $leadMember->role ?? $leadMember->member->position }}</small>
                                    </div>
                                </div>
                            @endif

                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="badge bg-light text-dark">VENDOR BERTUGAS</span>
                                    <span class="badge bg-info">{{ $event->schedules->count() }} Vendor</span>
                                </div>
                                <div class="d-flex flex-wrap gap-1 mb-2">
                                    @forelse($event->schedules->take(3) as $schedule)
                                        <span class="badge bg-secondary">{{ $schedule->vendor ? $schedule->vendor->name : 'N/A' }}</span>
                                    @empty
                                        <span class="badge bg-light text-dark">Belum ada vendor</span>
                                    @endforelse
                                    @if ($event->schedules->count() > 3)
                                        <span class="badge bg-secondary">+{{ $event->schedules->count() - 3 }} lagi</span>
                                    @endif
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.event.show', $event->id) }}" class="btn btn-dark" data-event-id="{{ $event->id }}">
                                    <i class="bi bi-eye me-2"></i> Rincian Acara
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                        <h5 class="text-muted">Belum ada event</h5>
                        <p class="text-muted">Klik tombol "Buat Acara Baru" untuk menambahkan event pertama</p>
                        <a href="{{ route('admin.events.create') }}" class="btn btn-dark">
                            <i class="bi bi-plus-circle me-2"></i> Buat Acara Baru
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div class="modal fade" id="eventDetailModal" tabindex="-1" aria-labelledby="eventDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventDetailModalLabel">Detail Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalEventDetailContent">
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('#eventGrid .col-md-6');

            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        document.querySelectorAll('a[data-event-id]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const eventId = this.getAttribute('data-event-id');
                showEventDetailModal(eventId);
            });
        });

        function showEventDetailModal(eventId) {
            const modal = new bootstrap.Modal(document.getElementById('eventDetailModal'));
            const modalBody = document.getElementById('modalEventDetailContent');

            modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border" role="status"></div></div>';
            modal.show();

            fetch(`/admin/events/${eventId}/detail`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    modalBody.innerHTML = '<div class="alert alert-danger">' + data.error + '</div>';
                    return;
                }

                const info = data;
                const statusClass = {
                    draft: 'secondary',
                    scheduled: 'secondary',
                    upcoming: 'primary',
                    ongoing: 'warning',
                    completed: 'success',
                    cancelled: 'danger'
                }[info.status] || 'secondary';

                const leadName = info.lead_director || 'Belum ditentukan';
                const venue = info.venue || 'N/A';
                const packageName = info.package || 'Tidak ada paket';
                const vendorList = (info.vendors || []).length
                    ? (info.vendors || []).map(v => `
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                            <div>
                                <div class="fw-semibold">${v.name}</div>
                                <small class="text-muted">${v.category || 'N/A'}</small>
                            </div>
                            <span class="badge bg-${v.status === 'approved' ? 'success' : v.status === 'rejected' ? 'danger' : 'secondary'} text-uppercase">${v.status || 'pending'}</span>
                        </div>
                    `).join('')
                    : '<div class="text-muted">Belum ada vendor yang ditambahkan</div>';

                const teamList = (info.team_members || []).length
                    ? (info.team_members || []).map(member => `
                        <div class="col-md-6 mb-2">
                            <div class="fw-semibold">${member.name}</div>
                            <small class="text-muted">${member.role || 'Belum ditentukan'}</small>
                        </div>
                    `).join('')
                    : '<div class="col-12 text-muted">Belum ada tim yang ditambahkan</div>';

                modalBody.innerHTML = `
                    <div class="container-fluid px-0">
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="fw-bold mb-1">${info.title || 'Event'}</h3>
                                    <p class="text-muted mb-2">${info.couple_name || 'Event Detail'}</p>
                                    <div class="d-flex gap-2 align-items-center flex-wrap">
                                        <span class="badge bg-${statusClass}">${String(info.status || 'upcoming').toUpperCase()}</span>
                                        <span class="text-muted"><i class="bi bi-clock"></i> ${info.schedule || 'Waktu belum ditentukan'}</span>
                                    </div>
                                </div>
                                <a href="/admin/events/${info.id}/edit" class="btn btn-dark btn-sm">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <div class="small text-muted">LOKASI</div>
                                        <div class="fw-bold">${venue}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="small text-muted">TANGGAL</div>
                                        <div class="fw-bold">${info.event_date || 'N/A'}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="small text-muted">TAMU UNDANGAN</div>
                                        <div class="fw-bold">${info.guest_count ?? 'N/A'} Orang</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-8">
                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-body p-4">
                                        <h5 class="fw-bold mb-3"><i class="bi bi-person-badge-fill text-primary"></i> KOORDINATOR UTAMA</h5>
                                        <div class="fw-bold">${leadName}</div>
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-body p-4">
                                        <h5 class="fw-bold mb-3"><i class="bi bi-box-seam-fill text-info"></i> PAKET PILIHAN</h5>
                                        <div class="fw-bold">${packageName}</div>
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-body p-4">
                                        <h5 class="fw-bold mb-3"><i class="bi bi-shop-window text-success"></i> VENDOR REKOMENDASI TERIKAT</h5>
                                        ${vendorList}
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-body p-4">
                                        <h5 class="fw-bold mb-3"><i class="bi bi-people-fill text-warning"></i> TIM ON-DUTY</h5>
                                        <div class="row">${teamList}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-body p-4">
                                        <h5 class="fw-bold mb-3">Info Event</h5>
                                        <div class="mb-3"><div class="small text-muted">Tipe Event</div><div class="fw-semibold">${info.event_type || 'N/A'}</div></div>
                                        <div class="mb-3"><div class="small text-muted">Status</div><div class="fw-semibold">${String(info.status || 'upcoming').charAt(0).toUpperCase() + String(info.status || 'upcoming').slice(1)}</div></div>
                                        <div class="mb-3"><div class="small text-muted">Total Vendor</div><div class="fw-semibold">${info.vendor_count ?? 0} Vendor</div></div>
                                        <div class="mb-3"><div class="small text-muted">Total Tim</div><div class="fw-semibold">${info.team_count ?? 0} Orang</div></div>
                                    </div>
                                </div>
                                ${info.notes ? `
                                    <div class="card border-0 shadow-sm mb-4">
                                        <div class="card-body p-4">
                                            <h5 class="fw-bold mb-3">Catatan</h5>
                                            <p class="text-muted small mb-0">${info.notes}</p>
                                        </div>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
            })
            .catch(error => {
                modalBody.innerHTML = '<div class="alert alert-danger">Gagal memuat detail acara</div>';
                console.error('Error:', error);
            });
        }
    </script>
@endsection
