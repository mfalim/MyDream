@extends('layouts.frontend')

@section('title', 'Acara Saya — MyDream Organizer')

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Acara Saya']]" />
@endsection

@section('content')

    <section class="section-sm">
        <div class="container">

            <p class="eyebrow">MyDream Wedding</p>

            <div class="flex items-center justify-between gap-2 flex-wrap">
                <div>
                    <h1 class="h1 mt-2">Acara Saya</h1>
                    <p class="lede mt-1">
                        Pantau detail acara, jadwal, vendor, dan persiapan pernikahanmu.
                    </p>
                </div>

                <a href="{{ route('user.rundown') }}" class="btn btn-outline">
                    Lihat Rundown
                </a>
            </div>

            @if ($events->isEmpty())

                <div class="card mt-4" style="padding:40px;text-align:center;">
                    <div style="font-size:2.5rem;">💍</div>

                    <h2 class="h2 mt-2">
                        Belum Ada Acara
                    </h2>

                    <p class="muted mt-1">
                        Kamu belum memiliki acara yang terdaftar di MyDream.
                    </p>

                    <div class="mt-3">
                        <a href="{{ route('catalog.index') }}" class="btn btn-maroon">
                            Jelajahi Paket
                        </a>
                    </div>
                </div>
            @else
                @foreach ($events as $event)
                    @php
                        $booking = $event->booking;

                        $statusLabel = match ($event->status) {
                            'pending' => 'Menunggu Konfirmasi',
                            'confirmed' => 'Dikonfirmasi',
                            'ongoing' => 'Sedang Berlangsung',
                            'completed' => 'Selesai',
                            'cancelled' => 'Dibatalkan',
                            default => ucfirst($event->status ?? 'Pending'),
                        };

                        $statusClass = match ($event->status) {
                            'confirmed', 'ongoing' => 'badge-maroon',
                            'completed' => 'badge-dark',
                            'cancelled' => 'badge-outline',
                            default => 'badge-paper',
                        };

                        $scheduleCount = $event->schedules->count();
                    @endphp

                    <div class="card mt-4">

                        {{-- HEADER --}}
                        <div class="card-body" style="padding:28px;">

                            <div class="flex items-center justify-between gap-2 flex-wrap">

                                <div>
                                    <span class="badge {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>

                                    <h2 class="h2 mt-2">
                                        {{ $event->name }}
                                    </h2>

                                    @if ($event->event_type)
                                        <p class="small muted mt-1">
                                            {{ ucfirst($event->event_type) }}
                                        </p>
                                    @endif
                                </div>

                                <div style="text-align:right;">
                                    <p class="tiny muted">Tanggal Acara</p>
                                    <strong style="font-size:1.1rem;">
                                        {{ $event->event_date?->translatedFormat('d F Y') ?? '-' }}
                                    </strong>
                                </div>

                            </div>

                            <hr style="border:0;border-top:1px solid var(--line);margin:24px 0;">

                            {{-- INFO GRID --}}
                            <div class="grid grid-4">

                                <div>
                                    <p class="tiny muted">Lokasi</p>
                                    <strong>
                                        {{ $booking?->venue_name ?? '-' }}
                                    </strong>

                                    @if ($booking?->venue_city)
                                        <p class="tiny muted mt-1">
                                            {{ $booking->venue_city }},
                                            {{ $booking->venue_province }}
                                        </p>
                                    @endif
                                </div>

                                <div>
                                    <p class="tiny muted">Jumlah Tamu</p>
                                    <strong>
                                        {{ number_format($event->guest_count ?? ($booking?->guest_count ?? 0), 0, ',', '.') }}
                                        orang
                                    </strong>
                                </div>

                                <div>
                                    <p class="tiny muted">Paket</p>
                                    <strong>
                                        {{ $event->package?->name ?? ($booking?->package?->name ?? 'Custom Vendor') }}
                                    </strong>
                                </div>

                                <div>
                                    <p class="tiny muted">Vendor</p>
                                    <strong>
                                        {{ $scheduleCount }} vendor
                                    </strong>
                                </div>

                            </div>

                        </div>

                        {{-- SCHEDULE --}}
                        @if ($event->schedules->isNotEmpty())
                            <div style="border-top:1px solid var(--line);padding:24px 28px;">

                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                    <div>
                                        <p class="eyebrow">Timeline</p>
                                        <h3 class="h2 mt-1" style="font-size:1.35rem;">
                                            Jadwal Vendor
                                        </h3>
                                    </div>

                                    <a href="{{ route('user.rundown') }}" class="btn btn-outline btn-sm">
                                        Lihat Rundown
                                    </a>
                                </div>

                                <div class="mt-3" style="display:flex;flex-direction:column;gap:10px;">

                                    @foreach ($event->schedules->sortBy('start_time') as $schedule)
                                        <div
                                            style="
                                            display:flex;
                                            align-items:center;
                                            gap:16px;
                                            padding:14px 16px;
                                            border:1px solid var(--line);
                                            border-radius:var(--radius-md);
                                            background:var(--paper);
                                        ">

                                            <div style="min-width:80px;">
                                                <strong style="font-size:.85rem;">
                                                    {{ $schedule->start_time?->format('H:i') ?? '--:--' }}
                                                </strong>

                                                @if ($schedule->end_time)
                                                    <p class="tiny muted">
                                                        {{ $schedule->end_time->format('H:i') }}
                                                    </p>
                                                @endif
                                            </div>

                                            <div style="flex:1;min-width:0;">
                                                <strong>
                                                    {{ $schedule->activity ?: $schedule->vendor?->name ?? 'Aktivitas' }}
                                                </strong>

                                                @if ($schedule->vendor)
                                                    <p class="tiny muted mt-1">
                                                        {{ $schedule->vendor->name }}

                                                        @if ($schedule->vendor->category)
                                                            · {{ $schedule->vendor->category->name }}
                                                        @endif
                                                    </p>
                                                @endif

                                                @if ($schedule->location)
                                                    <p class="tiny muted mt-1">
                                                        📍 {{ $schedule->location }}
                                                    </p>
                                                @endif
                                            </div>

                                            <span class="badge badge-paper">
                                                {{ ucfirst($schedule->status ?? 'pending') }}
                                            </span>

                                        </div>
                                    @endforeach

                                </div>

                            </div>
                        @endif

                        {{-- VENUE --}}
                        @if ($booking)
                            <div
                                style="
                                border-top:1px solid var(--line);
                                padding:24px 28px;
                                background:rgba(0,0,0,.015);
                            ">

                                <p class="eyebrow">Venue Acara</p>

                                <div class="flex items-center justify-between gap-2 flex-wrap mt-1">

                                    <div>
                                        <h3 class="h2" style="font-size:1.2rem;">
                                            {{ $booking->venue_name }}
                                        </h3>

                                        <p class="small muted mt-1">
                                            {{ $booking->venue_address }}
                                        </p>

                                        <p class="small muted">
                                            {{ $booking->venue_city }},
                                            {{ $booking->venue_province }}
                                        </p>
                                    </div>

                                    @if ($booking->venue_maps)
                                        <a href="{{ $booking->venue_maps }}" target="_blank" rel="noopener"
                                            class="btn btn-outline btn-sm">
                                            Buka Maps
                                        </a>
                                    @endif

                                </div>

                            </div>
                        @endif

                    </div>
                @endforeach

            @endif

        </div>
    </section>

    @endsections
