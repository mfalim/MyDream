@extends('user.layouts.app')

@section('title', 'Konfirmasi Vendor')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/wo-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard-home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vendor-confirmation.css') }}">
@endpush

@section('content')
<main class="wo-page dashboard-home vendor-confirmation">
    <section class="dh-hero">
        <div class="dh-hero-text">
            <span class="wo-eyebrow"><i class="bi bi-circle-fill"></i> Vendor untuk acara Anda</span>
            <h1>Status Konfirmasi Ketersediaan Vendor</h1>
            <p>Informasi acara dan konfirmasi vendor dari booking Anda.</p>
        </div>
    </section>

    <section class="vc-stats" aria-label="Ringkasan status vendor">
        <article class="vc-stat">
            <span class="vc-stat-label">Total Vendor</span>
            <strong id="vcStatTotal">0</strong>
            <i class="bi bi-people-fill" aria-hidden="true"></i>
        </article>
        <article class="vc-stat vc-stat-ready">
            <span class="vc-stat-label">Terkonfirmasi</span>
            <strong id="vcStatApproved">0</strong>
            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
        </article>
        <article class="vc-stat vc-stat-pending">
            <span class="vc-stat-label">Menunggu</span>
            <strong id="vcStatPending">0</strong>
            <i class="bi bi-hourglass-split" aria-hidden="true"></i>
        </article>
    </section>

    <div class="vc-toolbar">
        <label class="vc-search">
            <i class="bi bi-search" aria-hidden="true"></i>
            <input type="search" id="vcSearch" placeholder="Cari nama acara..." aria-label="Cari nama acara">
        </label>
        <div class="vc-filters" role="group" aria-label="Filter status vendor">
            <button type="button" class="vc-filter is-active" data-status-filter="all">Semua</button>
            <button type="button" class="vc-filter" data-status-filter="approved">Terkonfirmasi</button>
            <button type="button" class="vc-filter" data-status-filter="pending">Menunggu</button>
        </div>
    </div>

    <div class="vc-events" id="vcEvents">
        @forelse ($bookings as $booking)
            @forelse ($booking->events as $event)
                <article class="vc-event-card" data-event-name="{{ $event->name }}">
                    <div class="vc-event-heading">
                        <div>
                            <span class="vc-event-kicker">Informasi Acara</span>
                            <h2>{{ $event->name }}</h2>
                        </div>
                        <button type="button" class="vc-toggle" aria-expanded="false">
                            <span>Detail vendor</span>
                            <i class="bi bi-chevron-down" aria-hidden="true"></i>
                        </button>
                    </div>

                    <div class="vc-event-table-wrap">
                        <table class="vc-table vc-event-table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Event</th>
                                    <th>Tanggal</th>
                                    <th>Tamu</th>
                                    <th>Mulai</th>
                                    <th>Selesai</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $event->name }}</td>
                                    <td>{{ $event->event_type ? ucfirst(str_replace('_', ' ', $event->event_type)) : '-' }}</td>
                                    <td>{{ $event->event_date?->format('d/m/Y') ?? '-' }}</td>
                                    <td>{{ $event->guest_count ?? '-' }}</td>
                                    <td>{{ $event->start_time ? \Illuminate\Support\Carbon::parse($event->start_time)->format('H:i') : '-' }}</td>
                                    <td>{{ $event->end_time ? \Illuminate\Support\Carbon::parse($event->end_time)->format('H:i') : '-' }}</td>
                                    <td>{{ ucfirst($event->status ?? '-') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="vc-vendors" hidden>
                        <div class="vc-vendors-heading">
                            <div>
                                <span class="vc-event-kicker">Rincian layanan</span>
                                <h3>Konfirmasi Vendor</h3>
                            </div>
                        </div>
                        <div class="vc-event-table-wrap">
                            <table class="vc-table vc-vendor-table">
                                <thead>
                                    <tr>
                                        <th>Vendor</th>
                                        <th>Kategori</th>
                                        <th>Waktu</th>
                                        <th>Status</th>
                                        <th>Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $hasVendor = false; @endphp
                                    @foreach ($event->schedules as $schedule)
                                        @if ($schedule->vendor)
                                            @php
                                                $hasVendor = true;
                                                $vendor = $schedule->vendor;
                                                $photo = $vendor->photos->firstWhere('is_cover', true) ?? $vendor->photos->first();
                                                $description = $vendor->description ?: 'Deskripsi vendor belum tersedia.';
                                                $scheduleStatus = $schedule->status ?? 'pending';
                                            @endphp
                                            <tr data-vendor-status="{{ $scheduleStatus }}">
                                                <td>
                                                    <strong class="vc-name">{{ $vendor->name }}</strong>
                                                    @if ($event->package)
                                                        <span class="vc-note">dalam {{ $event->package->name }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $vendor->category?->name ?? 'Tanpa kategori' }}</td>
                                                <td>
                                                    {{ $schedule->start_time ? \Illuminate\Support\Carbon::parse($schedule->start_time)->format('H:i') : '-' }}
                                                    –
                                                    {{ $schedule->end_time ? \Illuminate\Support\Carbon::parse($schedule->end_time)->format('H:i') : '-' }}
                                                </td>
                                                <td>
                                                    <span class="wo-badge {{ ['approved' => 'wo-badge-green', 'pending' => 'wo-badge-gold', 'rejected' => 'wo-badge-red'][$scheduleStatus] ?? 'wo-badge-neutral' }}">
                                                        {{ ucfirst($scheduleStatus) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <button
                                                        type="button"
                                                        class="vc-detail-button"
                                                        data-name="{{ $vendor->name }}"
                                                        data-category="{{ $vendor->category?->name ?? 'Tanpa kategori' }}"
                                                        data-description="{{ $description }}"
                                                        data-photo="{{ $photo ? asset('storage/' . $photo->photo) : '' }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#vendorDetailModal"
                                                    >Detail</button>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    @unless ($hasVendor)
                                        <tr class="vc-empty-vendors">
                                            <td colspan="5">Belum ada vendor untuk acara ini.</td>
                                        </tr>
                                    @endunless
                                </tbody>
                            </table>
                        </div>
                    </div>
                </article>
            @empty
                <div class="vc-empty">Booking ini belum memiliki data acara.</div>
            @endforelse
        @empty
            <div class="vc-empty">Belum ada booking atau vendor.</div>
        @endforelse
        <div class="vc-empty vc-no-results" hidden>Tidak ada acara yang cocok dengan pencarian dan filter status.</div>
    </div>
</main>

<div class="modal fade" id="vendorDetailModal" tabindex="-1" aria-labelledby="vendorDetailTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content vc-modal">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="vendorDetailTitle">Detail Vendor</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <img id="vendorDetailPhoto" class="img-fluid rounded mb-3 w-100" alt="Foto vendor" hidden>
                <p><strong>Nama:</strong> <span id="vendorDetailName"></span></p>
                <p><strong>Kategori:</strong> <span id="vendorDetailCategory"></span></p>
                <p class="mb-0"><strong>Deskripsi:</strong> <span id="vendorDetailDescription"></span></p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var search = document.getElementById('vcSearch');
    var eventCards = Array.from(document.querySelectorAll('.vc-event-card'));
    var filterButtons = document.querySelectorAll('[data-status-filter]');
    var noResults = document.querySelector('.vc-no-results');
    var activeStatus = 'all';
    var allVendorRows = document.querySelectorAll('.vc-vendor-table tbody tr[data-vendor-status]');

    document.getElementById('vcStatTotal').textContent = allVendorRows.length;
    document.getElementById('vcStatApproved').textContent =
        document.querySelectorAll('[data-vendor-status="approved"]').length;
    document.getElementById('vcStatPending').textContent =
        document.querySelectorAll('[data-vendor-status="pending"]').length;
    function filterEvents() {
        var query = search.value.trim().toLocaleLowerCase();
        var visibleEvents = 0;

        eventCards.forEach(function (card) {
            var nameMatches = card.dataset.eventName.toLocaleLowerCase().includes(query);
            var vendorRows = card.querySelectorAll('.vc-vendor-table tbody tr[data-vendor-status]');
            var visibleVendorCount = 0;

            vendorRows.forEach(function (row) {
                var statusMatches = activeStatus === 'all' || row.dataset.vendorStatus === activeStatus;
                row.hidden = !statusMatches;
                if (statusMatches) visibleVendorCount++;
            });

            var isVisible = nameMatches && (activeStatus === 'all' || visibleVendorCount > 0);
            card.hidden = !isVisible;
            if (isVisible) visibleEvents++;
        });

        noResults.hidden = visibleEvents > 0 || eventCards.length === 0;
    }

    search.addEventListener('input', filterEvents);

    filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            activeStatus = button.dataset.statusFilter;
            filterButtons.forEach(function (filterButton) {
                var isActive = filterButton === button;
                filterButton.classList.toggle('is-active', isActive);
                filterButton.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            });
            filterEvents();
        });
    });

    document.querySelectorAll('.vc-toggle').forEach(function (button) {
        button.addEventListener('click', function () {
            var vendors = button.closest('.vc-event-card').querySelector('.vc-vendors');
            var isExpanded = button.getAttribute('aria-expanded') === 'true';
            button.setAttribute('aria-expanded', isExpanded ? 'false' : 'true');
            vendors.hidden = isExpanded;
        });
    });

    document.querySelectorAll('.vc-detail-button').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('vendorDetailName').textContent = button.dataset.name;
            document.getElementById('vendorDetailCategory').textContent = button.dataset.category;
            document.getElementById('vendorDetailDescription').textContent = button.dataset.description;
            var photo = document.getElementById('vendorDetailPhoto');
            photo.src = button.dataset.photo || '';
            photo.hidden = !button.dataset.photo;
        });
    });
});
</script>
@endpush
