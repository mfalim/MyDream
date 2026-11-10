@extends('layouts.admin')

@section('title', 'Event Publik & Pameran')
@section('page-title', 'Event Publik')

@push('styles')
    @vite('resources/css/admin/cms.css')
@endpush

@section('content')
<div class="cms-container">

    {{-- HEADER --}}
    <div class="cms-header">
        <div>
            <h2>Event Publik &amp; Pameran</h2>
            <p>Kelola wedding expo, pameran vendor, open house, dan agenda pameran MyDream yang tampil di halaman website publik.</p>
        </div>
        <div class="cms-header-actions">
            <a href="{{ route('event.index') }}" target="_blank" class="btn-outline-web">
                <i class="bi bi-box-arrow-up-right"></i>
                <span>Lihat di Web</span>
            </a>
            <a href="{{ route('admin.public-events.create') }}" class="btn-emerald">
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Event Baru</span>
            </a>
        </div>
    </div>

    @php
        $total = $events->count();
        $ongoing = $events->where('status', 'ongoing')->count();
        $upcoming = $events->where('status', 'upcoming')->count();
        $finished = $events->where('status', 'finished')->count();
    @endphp

    {{-- STATS GRID --}}
    <div class="cms-stats-grid">
        <div class="cms-stat-card">
            <div class="cms-stat-info">
                <small>TOTAL EVENT</small>
                <strong>{{ $total }}</strong>
            </div>
            <div class="cms-stat-icon" style="background: rgba(7, 61, 54, 0.08); color: #073d36;">
                <i class="bi bi-calendar-event"></i>
            </div>
        </div>
        <div class="cms-stat-card">
            <div class="cms-stat-info">
                <small>SEDANG BERLANGSUNG</small>
                <strong style="color: #b91c1c;">{{ $ongoing }}</strong>
            </div>
            <div class="cms-stat-icon" style="background: #fef2f2; color: #b91c1c;">
                <i class="bi bi-broadcast"></i>
            </div>
        </div>
        <div class="cms-stat-card">
            <div class="cms-stat-info">
                <small>AKAN DATANG</small>
                <strong style="color: #047857;">{{ $upcoming }}</strong>
            </div>
            <div class="cms-stat-icon" style="background: #ecfdf5; color: #047857;">
                <i class="bi bi-calendar-check"></i>
            </div>
        </div>
        <div class="cms-stat-card">
            <div class="cms-stat-info">
                <small>SELESAI</small>
                <strong>{{ $finished }}</strong>
            </div>
            <div class="cms-stat-icon" style="background: #f3f4f6; color: #4b5563;">
                <i class="bi bi-clock-history"></i>
            </div>
        </div>
    </div>

    {{-- TOOLBAR --}}
    <div class="cms-toolbar">
        <div class="cms-tabs">
            <button type="button" class="cms-tab-btn active" data-filter="all">
                Semua <span class="cms-tab-count">{{ $total }}</span>
            </button>
            <button type="button" class="cms-tab-btn" data-filter="ongoing">
                Berlangsung <span class="cms-tab-count">{{ $ongoing }}</span>
            </button>
            <button type="button" class="cms-tab-btn" data-filter="upcoming">
                Akan Datang <span class="cms-tab-count">{{ $upcoming }}</span>
            </button>
            <button type="button" class="cms-tab-btn" data-filter="finished">
                Selesai <span class="cms-tab-count">{{ $finished }}</span>
            </button>
        </div>

        <div class="cms-search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="eventSearch" placeholder="Cari nama event atau lokasi...">
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="cms-card">
        <div class="table-responsive">
            <table class="cms-table">
                <thead>
                    <tr>
                        <th style="width: 100px;">Banner</th>
                        <th>Event &amp; Detail</th>
                        <th style="width: 220px;">Waktu &amp; Tanggal</th>
                        <th style="width: 140px;">Status</th>
                        <th style="width: 130px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="eventTableBody">
                    @forelse ($events as $event)
                        <tr class="event-row" data-status="{{ $event->status }}" data-title="{{ strtolower($event->title) }}" data-location="{{ strtolower($event->location ?? '') }}">
                            <td>
                                @if ($event->photo)
                                    <img src="{{ \App\Support\Media::url($event->photo) }}" alt="{{ $event->title }}" class="event-thumb">
                                @else
                                    <div class="event-thumb-fallback">
                                        <i class="bi bi-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.public-events.edit', $event) }}" class="event-title-link">
                                    {{ $event->title }}
                                </a>
                                <div class="event-meta-line">
                                    <span class="event-meta-item">
                                        <i class="bi bi-geo-alt"></i>
                                        {{ $event->location ?: 'Lokasi belum ditentukan' }}
                                    </span>
                                    <span>&bull;</span>
                                    <span class="event-meta-item">
                                        <i class="bi bi-shop"></i>
                                        {{ $event->vendors_count ?? $event->vendors->count() }} vendor rekanan
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #173f39;">{{ $event->date_label }}</div>
                                <div style="font-size: 12px; color: #74847c;">
                                    <i class="bi bi-clock"></i> {{ $event->time_label ?? 'Jadwal fleksibel' }}
                                </div>
                            </td>
                            <td>
                                @if ($event->status === 'ongoing')
                                    <span class="status-badge ongoing">
                                        <span class="pulse-dot"></span> Sedang Live
                                    </span>
                                @elseif ($event->status === 'upcoming')
                                    <span class="status-badge upcoming">
                                        <i class="bi bi-calendar-check"></i> Akan Datang
                                    </span>
                                @else
                                    <span class="status-badge finished">
                                        <i class="bi bi-check2"></i> Selesai
                                    </span>
                                @endif

                                @unless ($event->is_published)
                                    <div class="mt-1">
                                        <span class="status-badge draft">
                                            <i class="bi bi-eye-slash"></i> Draft
                                        </span>
                                    </div>
                                @endunless
                            </td>
                            <td style="text-align: right;">
                                <div class="action-btn-group">
                                    @if ($event->is_published)
                                        <a href="{{ route('event.show', $event->slug) }}" target="_blank" class="btn-action-icon" title="Lihat di Web">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.public-events.edit', $event) }}" class="btn-action-icon" title="Edit Event">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.public-events.destroy', $event) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus event ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-icon delete" title="Hapus Event">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyEventRow">
                            <td colspan="5" class="text-center py-5" style="color: #74847c;">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2 text-muted"></i>
                                Belum ada event publik. Klik <strong>+ Tambah Event Baru</strong> untuk membuat pameran atau open house perdana.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabBtns = document.querySelectorAll('.cms-tab-btn');
        const searchInput = document.getElementById('eventSearch');
        const rows = document.querySelectorAll('.event-row');

        let currentFilter = 'all';
        let searchQuery = '';

        function applyFilter() {
            let visibleCount = 0;
            rows.forEach(row => {
                const status = row.dataset.status;
                const title = row.dataset.title;
                const loc = row.dataset.location;

                const matchesFilter = (currentFilter === 'all') || (status === currentFilter);
                const matchesSearch = !searchQuery || title.includes(searchQuery) || loc.includes(searchQuery);

                if (matchesFilter && matchesSearch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
        }

        tabBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                tabBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentFilter = this.dataset.filter;
                applyFilter();
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                searchQuery = this.value.trim().toLowerCase();
                applyFilter();
            });
        }
    });
</script>
@endpush
@endsection