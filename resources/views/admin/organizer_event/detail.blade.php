@extends('layouts.admin')

@section('title', 'Detail Organizer Event')
@section('page-title', 'Detail Organizer Event')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <a href="javascript:history.back()" class="btn btn-sm btn-outline-secondary mb-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h2 class="fw-bold mb-1">{{ $organizerEvent->title }}</h2>
                <p class="text-muted mb-2">Organizer Internal Event</p>
                <div class="d-flex gap-2 align-items-center">
                    @php
                        $statusBadge = [
                            'scheduled' => 'secondary',
                            'completed' => 'success',
                            'cancelled' => 'danger'
                        ][$organizerEvent->status] ?? 'secondary';
                        
                        $priorityBadge = [
                            'low' => 'info',
                            'medium' => 'warning',
                            'high' => 'danger'
                        ][$organizerEvent->priority] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $statusBadge }}">{{ strtoupper($organizerEvent->status) }}</span>
                    <span class="badge bg-{{ $priorityBadge }}">{{ strtoupper($organizerEvent->priority) }} PRIORITY</span>
                    <span class="text-muted">
                        <i class="bi bi-clock"></i> 
                        @if($organizerEvent->start_time && $organizerEvent->end_time)
                            {{ date('H:i', strtotime($organizerEvent->start_time)) }} - {{ date('H:i', strtotime($organizerEvent->end_time)) }} WIB
                        @else
                            Waktu belum ditentukan
                        @endif
                    </span>
                </div>
            </div>
            <div>
                <a href="{{ route('admin.organizer-event.edit', $organizerEvent->id) }}" class="btn btn-dark">
                    <i class="bi bi-pencil"></i> Edit Detail Event
                </a>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-calendar-event text-primary fs-4"></i>
                        </div>
                        <div>
                            <div class="small text-muted">TANGGAL</div>
                            <div class="fw-bold">{{ $organizerEvent->event_date->format('d F Y') }}</div>
                            <div class="small text-muted">{{ $organizerEvent->event_date->format('l') }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-geo-alt-fill text-warning fs-4"></i>
                        </div>
                        <div>
                            <div class="small text-muted">LOKASI</div>
                            <div class="fw-bold">{{ $organizerEvent->location ?? 'Internal' }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-bookmark-fill text-info fs-4"></i>
                        </div>
                        <div>
                            <div class="small text-muted">TIPE EVENT</div>
                            <div class="fw-bold">{{ ucfirst(str_replace('_', ' ', $organizerEvent->event_type)) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            @if($organizerEvent->description)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-file-text text-primary"></i> Deskripsi Event
                    </h5>
                    <p class="mb-0">{{ $organizerEvent->description }}</p>
                </div>
            </div>
            @endif

            @if($organizerEvent->attendees)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-people-fill text-success"></i> Peserta / Attendees
                    </h5>
                    <p class="mb-0">{{ $organizerEvent->attendees }}</p>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Info Event</h5>
                    <div class="mb-3">
                        <div class="small text-muted">Tipe Event</div>
                        <div class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $organizerEvent->event_type)) }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">Status</div>
                        <div class="fw-semibold">{{ ucfirst($organizerEvent->status) }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">Priority</div>
                        <div class="fw-semibold">{{ ucfirst($organizerEvent->priority) }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">Waktu</div>
                        <div class="fw-semibold">
                            @if($organizerEvent->start_time && $organizerEvent->end_time)
                                {{ date('H:i', strtotime($organizerEvent->start_time)) }} - {{ date('H:i', strtotime($organizerEvent->end_time)) }}
                            @else
                                Belum ditentukan
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($organizerEvent->notes)
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Catatan</h5>
                    <p class="text-muted small mb-0">{{ $organizerEvent->notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
