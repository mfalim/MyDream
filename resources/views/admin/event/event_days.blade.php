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
            <a class="btn btn-dark px-4" href='{{ route('admin.event_day.create') }}'>
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
                <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari nama pengantin, venue, atau koordinator..." id="searchInput">
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
                'scheduled' => 'secondary',
                'ongoing' => 'warning',
                'completed' => 'success',
                'cancelled' => 'danger'
            ][$event->status] ?? 'secondary';
            
            $vendorPhotos = $event->schedules ? $event->schedules->map(function($schedule) {
                return $schedule->vendor && $schedule->vendor->photos && $schedule->vendor->photos->count() > 0 ? 
                    ($schedule->vendor->photos->where('is_cover', true)->first() ?? $schedule->vendor->photos->first()) : null;
            })->filter()->take(3) : collect();
        @endphp
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="position-relative">
                    @if($vendorPhotos->count() > 0)
                        <img src="{{ asset('storage/' . $vendorPhotos->first()->photo) }}" class="card-img-top" alt="Event" style="height: 200px; object-fit: cover;">
                    @elseif($event->photo)
                        <img src="{{ asset('storage/' . $event->photo) }}" class="card-img-top" alt="Event" style="height: 200px; object-fit: cover;">
                    @else
                        <img src="https://images.unsplash.com/photo-1519167758481-83f29da8fd4e?w=400" class="card-img-top" alt="Event" style="height: 200px; object-fit: cover;">
                    @endif
                    <span class="badge bg-{{ $statusBadge }} text-white position-absolute top-0 start-0 m-2">
                        @if($event->status === 'ongoing')
                            <i class="bi bi-clock-fill me-1"></i>
                        @endif
                        {{ strtoupper($event->status) }}
                    </span>
                    <span class="badge bg-dark position-absolute top-0 end-0 m-2">
                        @if($event->start_time)
                            {{ date('H:i', strtotime($event->start_time)) }} WIB
                        @else
                            Belum ada jadwal
                        @endif
                    </span>
                </div>
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">
                        @if($client)
                            {{ $client->groom_name }} & {{ $client->bride_name }}
                        @else
                            {{ $event->name }}
                        @endif
                    </h5>
                    
                    <div class="mb-3">
                        <i class="bi bi-calendar-event text-muted me-1"></i>
                        <small class="text-muted">{{ $event->event_date->format('d F Y') }}</small>
                    </div>

                    @if($booking)
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
                    
                    @if($leadMember)
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
                            @if($event->schedules->count() > 3)
                                <span class="badge bg-secondary">+{{ $event->schedules->count() - 3 }} lagi</span>
                            @endif
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.event.show', $event->id) }}" class="btn btn-dark">
                            <i class="bi bi-box-arrow-up-right me-2"></i> Lihat Detail
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
                <a href="{{ route('admin.event_day.create') }}" class="btn btn-dark">
                    <i class="bi bi-plus-circle me-2"></i> Buat Acara Baru
                </a>
            </div>
        </div>
        @endforelse
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
</script>
@endsection
