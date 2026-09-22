@extends('layouts.admin')

@section('title', 'Detail Event')
@section('page-title', 'Detail Event')

@section('content')
<div class="container-fluid px-4 py-4">
    @php
        $booking = $event->booking;
        $client = $booking ? $booking->client : null;
        $leadMember = $event->eventMembers->first();
    @endphp

    <div class="mb-4">
        <a href="javascript:history.back()" class="btn btn-sm btn-outline-secondary mb-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h2 class="fw-bold mb-1">{{ $event->name }}</h2>
                <p class="text-muted mb-2">
                    @if($client)
                        The Wedding of {{ $client->groom_name }} & {{ $client->bride_name }}
                    @else
                        Event Detail
                    @endif
                </p>
                <div class="d-flex gap-2 align-items-center">
                    @if($event->status === 'ongoing')
                        <span class="badge bg-danger">
                            <i class="bi bi-broadcast"></i> SEDANG BERLANGSUNG (LIVE!)
                        </span>
                    @else
                        <span class="badge bg-secondary">{{ strtoupper($event->status) }}</span>
                    @endif
                    <span class="text-muted">
                        <i class="bi bi-clock"></i> 
                        @if($event->start_time && $event->end_time)
                            {{ date('H:i', strtotime($event->start_time)) }} - {{ date('H:i', strtotime($event->end_time)) }} WIB
                        @else
                            Waktu belum ditentukan
                        @endif
                    </span>
                </div>
            </div>
            <div>
                <a href="{{ route('admin.event.edit', $event->id) }}" class="btn btn-dark">
                    <i class="bi bi-pencil"></i> Edit Detail Acara
                </a>
            </div>
        </div>
    </div>

    @if($booking)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-geo-alt-fill text-warning fs-4"></i>
                        </div>
                        <div>
                            <div class="small text-muted">LOKASI</div>
                            <div class="fw-bold">{{ $booking->venue_name }}</div>
                            <div class="small text-muted">{{ $booking->venue_city }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-calendar-event text-primary fs-4"></i>
                        </div>
                        <div>
                            <div class="small text-muted">TANGGAL</div>
                            <div class="fw-bold">{{ $event->event_date->format('d F Y') }}</div>
                            <div class="small text-muted">{{ $event->event_date->format('l') }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-people-fill text-success fs-4"></i>
                        </div>
                        <div>
                            <div class="small text-muted">TAMU UNDANGAN</div>
                            <div class="fw-bold">{{ $event->guest_count ?? $booking->guest_count ?? 'N/A' }} Orang</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Lead Member -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-person-badge-fill text-primary"></i> KOORDINATOR UTAMA
                    </h5>
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="bi bi-person-fill fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-bold">{{ $leadMember ? $leadMember->member->name : 'Belum ditentukan' }}</div>
                            <div class="small text-muted">{{ $leadMember ? ($leadMember->role ?? $leadMember->member->position) : '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Package -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-box-seam-fill text-info"></i> PAKET PILIHAN
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="small text-muted">Paket Utama (Booking)</div>
                            <div class="fw-bold">{{ $booking && $booking->package ? $booking->package->name : 'Tidak ada paket' }}</div>
                        </div>
                        @if($event->package)
                        <div class="col-md-6">
                            <div class="small text-muted">Paket Tambahan (Event)</div>
                            <div class="fw-bold">{{ $event->package->name }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Vendors -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-shop-window text-success"></i> VENDOR REKOMENDASI TERIKAT
                    </h5>
                    
                    @forelse($event->schedules as $schedule)
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                        <div class="d-flex align-items-center flex-grow-1">
                            <div class="bg-light rounded-3 p-3 me-3">
                                <i class="bi bi-briefcase-fill text-muted fs-5"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $schedule->vendor ? $schedule->vendor->name : 'N/A' }}</div>
                                <div class="small text-muted">
                                    <i class="bi bi-tag-fill"></i> {{ $schedule->vendor && $schedule->vendor->category ? $schedule->vendor->category->name : 'N/A' }}
                                    @if($schedule->start_time && $schedule->end_time)
                                        | <i class="bi bi-clock"></i> {{ date('H:i', strtotime($schedule->start_time)) }} - {{ date('H:i', strtotime($schedule->end_time)) }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div>
                            @php
                                $statusClass = [
                                    'approved' => 'success',
                                    'pending' => 'secondary',
                                    'rejected' => 'danger'
                                ][$schedule->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $statusClass }}">{{ strtoupper($schedule->status) }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        <p>Belum ada vendor yang ditambahkan</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Team Members -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-people-fill text-warning"></i> TIM ON-DUTY
                    </h5>
                    
                    <div class="row">
                        @forelse($event->eventMembers as $eventMember)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="bi bi-person-fill text-warning"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $eventMember->member->name }}</div>
                                    <div class="small text-muted">{{ $eventMember->role ?? $eventMember->member->position }}</div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center text-muted py-3">
                            <p>Belum ada tim yang ditambahkan</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Event Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Info Event</h5>
                    <div class="mb-3">
                        <div class="small text-muted">Tipe Event</div>
                        <div class="fw-semibold">{{ $event->event_type ? ucfirst(str_replace('_', ' ', $event->event_type)) : 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">Status</div>
                        <div class="fw-semibold">{{ ucfirst($event->status) }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">Total Vendor</div>
                        <div class="fw-semibold">{{ $event->schedules->count() }} Vendor</div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">Total Tim</div>
                        <div class="fw-semibold">{{ $event->eventMembers->count() }} Orang</div>
                    </div>
                </div>
            </div>

            @if($event->notes)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Catatan</h5>
                    <p class="text-muted small mb-0">{{ $event->notes }}</p>
                </div>
            </div>
            @endif

            @if($booking)
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Detail Venue</h5>
                    <div class="mb-2">
                        <div class="small text-muted">Alamat Lengkap</div>
                        <div class="small">{{ $booking->venue_address }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="small text-muted">Kota/Provinsi</div>
                        <div class="small">{{ $booking->venue_city }}, {{ $booking->venue_province }}</div>
                    </div>
                    @if($booking->venue_maps)
                    <a href="{{ $booking->venue_maps }}" target="_blank" class="btn btn-sm btn-outline-primary w-100 mt-2">
                        <i class="bi bi-map"></i> Lihat di Maps
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
