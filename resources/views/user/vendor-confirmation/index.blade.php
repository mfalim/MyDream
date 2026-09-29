{{-- resources/views/user/vendor-confirmation/index.blade.php --}}
@extends('user.layouts.app')

@section('title', 'Konfirmasi Vendor')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/wo-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vendor-confirmation.css') }}">
    <style>
        /* Satu grup booking = judul + dua card terpisah */
        .vc-booking-group {
            margin-bottom: 2.5rem;
            padding: 1.25rem;
            background: #e7eee9;               /* senada dengan background halaman, sedikit lebih gelap */
            border: 1px solid rgba(23, 50, 42, .08);
            border-radius: 24px;
        }
        .vc-group-title { font-size: 1.25rem; margin: 0 0 1rem; padding: 0 .25rem; }
        .vc-group-body { display: flex; flex-direction: column; gap: 1.25rem; }
        .vc-panel-title { padding: 1rem 1.25rem .25rem; margin: 0; }
    </style>
@endpush

@section('content')
<main class="wo-page vendor-confirmation">
    <div class="vc-head">
        <div>
            <span class="wo-eyebrow"><i class="bi bi-circle-fill"></i> Direktori Vendor</span>
            <h1>Status Ketersediaan Vendor</h1>
            <p>Informasi acara dan vendor dari booking Anda.</p>
        </div>
    </div>

    <div class="vc-toolbar">
        <div class="vc-search">
            <i class="bi bi-search"></i>
            <input type="text" id="vcSearch" placeholder="Cari nama vendor atau kategori..." aria-label="Cari vendor">
        </div>
    </div>

    @forelse ($bookings as $booking)
        <section class="vc-booking-group">

            @forelse ($booking->events as $event)
                <div class="vc-group-body {{ ! $loop->last ? 'mb-4' : '' }}">

                    {{-- CARD 1: Info acara --}}
                    <div class="wo-card vc-table-card">
                        <h3 class="h6 vc-panel-title">Informasi Acara</h3>
                        <div class="table-responsive">
                            <table class="wo-table vcTable">
                                <thead><tr><th>Nama</th><th>Event</th><th>Tanggal</th><th>Tamu</th><th>Mulai</th><th>Selesai</th><th>Status</th></tr></thead>
                                <tbody><tr>
                                    <td>{{ $event->name }}</td>
                                    <td>{{ $event->event_type ?: '-' }}</td>
                                    <td>{{ $event->event_date?->format('d/m/Y') ?? '-' }}</td>
                                    <td>{{ $event->guest_count ?? '-' }}</td>
                                    <td>{{ $event->start_time ? \Illuminate\Support\Carbon::parse($event->start_time)->format('H:i') : '-' }}</td>
                                    <td>{{ $event->end_time ? \Illuminate\Support\Carbon::parse($event->end_time)->format('H:i') : '-' }}</td>
                                    <td>{{ ucfirst($event->status ?? '-') }}</td>
                                </tr></tbody>
                            </table>
                        </div>
                    </div>

                    {{-- CARD 2: Konfirmasi vendor --}}
                    <div class="wo-card vc-table-card">
                        <h3 class="h6 vc-panel-title">Konfirmasi Vendor</h3>
                        <div class="table-responsive">
                            <table class="wo-table vcTable">
                                <thead><tr><th>Vendor</th><th>Kategori</th><th>Waktu</th><th>Status</th><th>Detail</th></tr></thead>
                                <tbody>
                                    @forelse ($event->schedules as $schedule)
                                        @if ($schedule->vendor)
                                            @php
                                                $vendor = $schedule->vendor;
                                                $photo = $vendor->photos->firstWhere('is_cover', true) ?? $vendor->photos->first();
                                                $description = $vendor->description ?: 'Deskripsi vendor belum tersedia.';
                                                $scheduleStatus = $schedule->status ?? '-';
                                                $statusClass = ['approved' => 'wo-badge-green', 'pending' => 'wo-badge-gold', 'rejected' => 'wo-badge-red'][$scheduleStatus] ?? 'wo-badge-gold';
                                            @endphp
                                            <tr>
                                                <td><strong class="vc-name">{{ $vendor->name }}</strong>@if($event->package)<br><span class="vc-note">dalam {{ $event->package->name }}</span>@endif</td>
                                                <td>{{ $vendor->category?->name ?? 'Tanpa kategori' }}</td>
                                                <td>{{ $schedule->start_time ? \Illuminate\Support\Carbon::parse($schedule->start_time)->format('H:i') : '-' }} - {{ $schedule->end_time ? \Illuminate\Support\Carbon::parse($schedule->end_time)->format('H:i') : '-' }}</td>
                                                <td><span class="wo-badge {{ $statusClass }}">{{ ucfirst($scheduleStatus) }}</span></td>
                                                <td><button type="button" class="wo-btn wo-btn-outline wo-btn-sm vc-detail-button" data-bs-toggle="modal" data-bs-target="#vendorDetailModal" data-name="{{ $vendor->name }}" data-category="{{ $vendor->category?->name ?? 'Tanpa kategori' }}" data-description="{{ $description }}" data-photo="{{ $photo ? asset('storage/' . $photo->photo) : '' }}">Cek</button></td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr><td colspan="5" class="text-center py-4">Belum ada vendor untuk acara ini.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            @empty
                <div class="wo-card vc-table-card p-3">Booking Saya belum memiliki data acara.</div>
            @endforelse
        </section>
    @empty
        <div class="wo-card vc-table-card p-4 text-center">Belum ada booking atau vendor.</div>
    @endforelse
</main>

<div class="modal fade" id="vendorDetailModal" tabindex="-1" aria-labelledby="vendorDetailTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="vendorDetailTitle">Detail Vendor</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <img id="vendorDetailPhoto" class="img-fluid rounded mb-3 w-100" alt="Foto vendor" hidden>
                <p class="mb-1"><strong>Nama:</strong> <span id="vendorDetailName"></span></p>
                <p class="mb-1"><strong>Kategori:</strong> <span id="vendorDetailCategory"></span></p>
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
    var rows = document.querySelectorAll('.vcTable tbody tr');

    search.addEventListener('input', function () {
        var query = search.value.trim().toLowerCase();
        rows.forEach(function (row) {
            row.style.display = row.innerText.toLowerCase().includes(query) ? '' : 'none';
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
