@extends('user.layouts.app')

@section('title', 'Overview Acara - ' . $dateLabel)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/wo-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/rundown-detail.css') }}">
@endpush

@section('content')
<main class="wo-page rundown-detail rd-overview">
    <div class="rd-topbar">
        <div>
            <div class="rd-breadcrumb">Jadwal Acara <i class="bi bi-chevron-right"></i> {{ $dateLabel }}</div>
            <h1 class="rd-date-heading">Overview Acara</h1>
        </div>
        <a href="{{ route('user.rundown') }}" class="wo-link"><i class="bi bi-arrow-left"></i> Kembali ke Kalender Tanggal Acara</a>
    </div>

    @foreach ($events as $event)
        @php
            $booking = $event->booking;
            $client = $booking?->client;
            $members = $event->eventMembers;
            $schedules = $event->schedules;
            $vendors = $schedules->filter(fn ($schedule) => $schedule->vendor);
            $lead = $members->first()?->member?->name ?? 'Belum ditentukan';
            $couple = trim(($client?->groom_name ?? '') . ' & ' . ($client?->bride_name ?? ''), ' &') ?: 'Pasangan pengantin';
            $venue = collect([$booking?->venue_name, $booking?->venue_city])->filter()->implode(' • ') ?: 'Lokasi belum ditentukan';
            $package = $event->package?->name ?? $booking?->package?->name ?? 'Paket belum ditentukan';
            $timeRange = $event->start_time && $event->end_time ? \Illuminate\Support\Carbon::parse($event->start_time)->format('H:i') . ' – ' . \Illuminate\Support\Carbon::parse($event->end_time)->format('H:i') . ' WIB' : 'Waktu belum ditentukan';
            $total = $booking?->total_price ?? 0;
        @endphp

        <section class="rd-event-overview">
            <div class="rd-hero" style="background-image: linear-gradient(180deg, rgba(22,48,42,0.12), rgba(22,48,42,0.88)), url('{{ $event->photo ? asset('storage/' . $event->photo) : 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=1600&q=85' }}');">
                <span class="rd-hero-status {{ \Illuminate\Support\Carbon::parse($event->event_date)->isToday() ? '' : 'rd-status-scheduled' }}">
                    <i class="bi bi-record-circle-fill"></i> {{ \Illuminate\Support\Carbon::parse($event->event_date)->isToday() ? 'Sedang Berlangsung (Live)' : ucfirst($event->status ?? 'Terjadwal') }}
                </span>
                <span class="rd-hero-time"><i class="bi bi-clock"></i> {{ $timeRange }}</span>
                <div class="rd-hero-bottom">
                    <span><i class="bi bi-geo-alt-fill"></i> {{ $venue }}</span>
                    <h2>{{ $event->name }}</h2>
                    <p class="rd-hero-subtitle">{{ $dateLabel }}</p>
                </div>
            </div>

            <div class="rd-quick-grid">
                <div class="rd-quick-card"><span class="wo-stat-icon"><i class="bi bi-heart-fill"></i></span><div><span>Pasangan Pengantin</span><strong>{{ $couple }}</strong></div></div>
                <div class="rd-quick-card"><span class="wo-stat-icon"><i class="bi bi-gem"></i></span><div><span>Paket Pilihan</span><strong>{{ $package }}</strong></div></div>
                <div class="rd-quick-card"><span class="wo-stat-icon"><i class="bi bi-people-fill"></i></span><div><span>Tamu Undangan</span><strong>{{ number_format($event->guest_count ?? $booking?->guest_count ?? 0, 0, ',', '.') }} Orang</strong></div></div>
                <div class="rd-quick-card"><span class="wo-stat-icon"><i class="bi bi-person-badge"></i></span><div><span>Lead Wedding Director</span><strong>{{ $lead }}</strong></div></div>
            </div>

            <div class="rd-layout">
                <div class="rd-main">
                    <section class="wo-card rd-overview-card">
                        <div class="wo-section-head"><div><span class="wo-eyebrow"><i class="bi bi-clock-history"></i> Urutan Acara</span><h2>Rundown Flow &amp; Timeline</h2></div><span class="wo-badge wo-badge-neutral">{{ $event->rundownPhases->count() }} Tahap</span></div>
                        @forelse ($event->rundownPhases as $phase)
                            <div class="rd-phase {{ $phase['state'] }}">
                                <div class="rd-phase-head"><span class="wo-badge {{ $phase['state'] === 'done' ? 'wo-badge-green' : ($phase['state'] === 'active' ? 'wo-badge-gold' : 'wo-badge-neutral') }}">{{ $phase['state_label'] }}</span><strong>{{ $phase['title'] }}</strong><span class="rd-phase-time">{{ $phase['time_range'] }}</span></div>
                                <div class="wo-timeline rd-phase-items">
                                    @foreach ($phase['items'] as $item)
                                        <div class="wo-timeline-item {{ $item['done'] ? 'is-done' : ($item['active'] ? 'is-active' : '') }}"><span class="wo-timeline-dot"><i class="bi bi-{{ $item['done'] ? 'check' : 'circle' }}"></i></span><div class="rd-item-card"><span class="rd-item-time">{{ $item['time'] }} WIB</span><strong>{{ $item['title'] }}</strong>@if($item['note'])<span class="rd-item-note">{{ $item['note'] }}</span>@endif @if($item['tag'])<span class="wo-badge wo-badge-neutral rd-item-tag">{{ $item['tag'] }}</span>@endif</div></div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <p class="rd-empty-note">Rundown acara belum tersedia.</p>
                        @endforelse
                    </section>
                    <section class="wo-card rd-overview-card">
                        <div class="wo-section-head"><div><span class="wo-eyebrow"><i class="bi bi-shield-check"></i> Informasi Booking</span><h2>Ringkasan Kontrak</h2></div><span class="wo-badge wo-badge-neutral">{{ ucfirst($booking?->status ?? 'Belum tersedia') }}</span></div>
                        <div class="rd-finance-total"><span>Total Nilai Kontrak</span><strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></div>
                        <div class="rd-finance-row"><span>Status Booking</span><strong>{{ ucfirst($booking?->status ?? 'Belum tersedia') }}</strong></div>
                        <div class="rd-finance-row"><span>Alamat Venue</span><strong>{{ $booking?->venue_address ?? 'Belum tersedia' }}</strong></div>
                    </section>
                </div>

                <aside class="rd-sidebar">
                    <section class="wo-card rd-overview-card">
                        <div class="wo-section-head"><div><span class="wo-eyebrow">Kolaborasi Acara</span><h2>Vendor Terpasang</h2></div><span class="wo-badge wo-badge-green">{{ $vendors->count() }} Vendor</span></div>
                        <div class="rd-vendor-list">
                            @forelse ($vendors as $schedule)
                                <div class="rd-vendor-item"><div class="rd-vendor-top"><strong>{{ $schedule->vendor->name }}</strong><span class="wo-badge {{ $schedule->status === 'approved' ? 'wo-badge-green' : 'wo-badge-gold' }}">{{ ucfirst($schedule->status ?? 'pending') }}</span></div><span class="rd-vendor-desc"><i class="bi bi-shop"></i> {{ $schedule->activity }}@if($schedule->vendor->category) • {{ $schedule->vendor->category->name }}@endif</span><div class="rd-vendor-footer"><span>{{ $schedule->start_time ? \Illuminate\Support\Carbon::parse($schedule->start_time)->format('H:i') : '--:--' }}–{{ $schedule->end_time ? \Illuminate\Support\Carbon::parse($schedule->end_time)->format('H:i') : '--:--' }}</span><span>{{ $schedule->location ?? 'Lokasi acara' }}</span></div></div>
                            @empty
                                <p class="rd-empty-note">Belum ada vendor yang terpasang.</p>
                            @endforelse
                        </div>
                    </section>
                    <section class="wo-card rd-overview-card">
                        <div class="wo-section-head"><div><span class="wo-eyebrow">Koordinasi Acara</span><h2>Tim On-Duty</h2></div><span class="wo-badge wo-badge-green">{{ $members->count() }} Personel</span></div>
                        <div class="rd-crew-grid">
                            @forelse ($members as $member)
                                @php $memberName = $member->member?->name ?? 'Personel'; $initials = collect(explode(' ', $memberName))->map(fn ($word) => strtoupper(substr($word, 0, 1)))->take(2)->implode(''); @endphp
                                <div class="rd-crew-item"><span class="wo-avatar">{{ $initials }}</span><div><strong>{{ $memberName }}</strong><span>{{ $member->role ?? 'Tim Lapangan' }}</span></div></div>
                            @empty
                                <p class="rd-empty-note">Belum ada personel yang ditugaskan.</p>
                            @endforelse
                        </div>
                    </section>
                </aside>
            </div>
        </section>
    @endforeach
</main>
@endsection
