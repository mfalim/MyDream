@extends('layouts.admin')

@section('title', 'Publikasi & Promosi Event')
@section('page-title', 'Event Publik')

@push('styles')
    @vite('resources/css/admin/cms.css')
@endpush

@section('content')
<div class="cms-container">

    {{-- HEADER --}}
    <div class="cms-header">
        <div>
            <h2>Publikasi &amp; Promosi Event ke Website</h2>
            <p>Pilih acara yang sudah ada di sistem (Event Wedding/Resepsi) untuk dipromosikan ke calon klien lain di website publik (<strong>/event</strong>). Publik dapat melihat konsep acara, venue, dan vendor rekanan yang bertugas.</p>
        </div>
        <div class="cms-header-actions">
            <a href="{{ route('event.index') }}" target="_blank" class="btn-outline-web">
                <i class="bi bi-box-arrow-up-right"></i>
                <span>Lihat di Website</span>
            </a>
            <a href="{{ route('admin.public-events.create') }}" class="btn-emerald">
                <i class="bi bi-plus-lg"></i>
                <span>Pilih &amp; Publikasikan Acara</span>
            </a>
        </div>
    </div>

    @php
        $totalPromoted = $events->count();
        $ongoing = $events->where('status', 'ongoing')->count();
        $upcoming = $events->where('status', 'upcoming')->count();
        $unpromotedCount = $operationalEvents->filter(fn($ev) => !$ev->publicEvent)->count();
    @endphp

    {{-- STATS GRID --}}
    <div class="cms-stats-grid">
        <div class="cms-stat-card">
            <div class="cms-stat-info">
                <small>DIPROMOSIKAN DI WEB</small>
                <strong>{{ $totalPromoted }}</strong>
            </div>
            <div class="cms-stat-icon" style="background: rgba(7, 61, 54, 0.08); color: #073d36;">
                <i class="bi bi-megaphone-fill"></i>
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
                <small>ACARA BELUM DIPROMOSIKAN</small>
                <strong style="color: #b7791f;">{{ $unpromotedCount }}</strong>
            </div>
            <div class="cms-stat-icon" style="background: #fff3d6; color: #b7791f;">
                <i class="bi bi-journal-plus"></i>
            </div>
        </div>
    </div>

    {{-- MAIN TABS NAVIGATION --}}
    <div class="nav-tabs-wo">
        <button type="button" class="tab-main-btn active" data-tab="promoted">
            <i class="bi bi-stars"></i>
            <span>Event yang Sedang Dipromosikan</span>
            <span class="badge-count">{{ $totalPromoted }}</span>
        </button>
        <button type="button" class="tab-main-btn" data-tab="operational">
            <i class="bi bi-collection-play"></i>
            <span>Pilih dari Acara yang Ada (Event MyDream)</span>
            <span class="badge-count">{{ $operationalEvents->count() }}</span>
        </button>
    </div>

    {{-- =========================================================================
         TAB 1: EVENT YANG SEDANG DIPROMOSIKAN DI WEBSITE PUBLIK
         ========================================================================= --}}
    <div id="tabContentPromoted" class="tab-pane-content">
        {{-- TOOLBAR FILTER TAB 1 --}}
        <div class="cms-toolbar">
            <div class="cms-tabs">
                <button type="button" class="cms-tab-btn active" data-filter="all">
                    Semua <span class="cms-tab-count">{{ $totalPromoted }}</span>
                </button>
                <button type="button" class="cms-tab-btn" data-filter="ongoing">
                    Berlangsung <span class="cms-tab-count">{{ $ongoing }}</span>
                </button>
                <button type="button" class="cms-tab-btn" data-filter="upcoming">
                    Akan Datang <span class="cms-tab-count">{{ $upcoming }}</span>
                </button>
                <button type="button" class="cms-tab-btn" data-filter="finished">
                    Selesai <span class="cms-tab-count">{{ $events->where('status', 'finished')->count() }}</span>
                </button>
            </div>

            <div class="cms-search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="eventSearch" placeholder="Cari judul promosi atau lokasi...">
            </div>
        </div>

        {{-- TABLE CARD --}}
        <div class="cms-card">
            <div class="table-responsive">
                <table class="cms-table">
                    <thead>
                        <tr>
                            <th style="width: 90px;">Banner</th>
                            <th>Event &amp; Detail Promosi</th>
                            <th style="width: 220px;">Waktu &amp; Tanggal</th>
                            <th style="width: 140px;">Status</th>
                            <th style="width: 130px;">Tampil di Web</th>
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

                                    @if($event->originalEvent)
                                        <div class="small mt-1" style="color: #073d36;">
                                            <i class="bi bi-link-45deg"></i> Terkait Acara: 
                                            <strong>{{ $event->originalEvent->name }}</strong>
                                            @if($event->originalEvent->booking?->client)
                                                (Klien: {{ $event->originalEvent->booking->client->groom_name }} &amp; {{ $event->originalEvent->booking->client->bride_name }})
                                            @endif
                                        </div>
                                    @endif

                                    <div class="event-meta-line">
                                        <span class="event-meta-item">
                                            <i class="bi bi-geo-alt"></i>
                                            {{ $event->location ?: 'Lokasi belum ditentukan' }}
                                        </span>
                                        <span>&bull;</span>
                                        <span class="event-meta-item">
                                            <i class="bi bi-shop"></i>
                                            {{ $event->vendors->count() }} vendor rekanan
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #173f39;">{{ $event->date_label }}</div>
                                    <div style="font-size: 12px; color: #74847c;">
                                        <i class="bi bi-clock"></i> {{ $event->time_label ?? 'Waktu fleksibel' }}
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
                                </td>
                                <td>
                                    <form action="{{ route('admin.public-events.toggle-publish', $event) }}" method="POST" class="d-inline">
                                        @csrf
                                        @if ($event->is_published)
                                            <button type="submit" class="btn btn-sm btn-outline-success fw-semibold" title="Klik untuk sembunyikan dari web">
                                                <i class="bi bi-eye"></i> Aktif (Live)
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-outline-secondary fw-semibold" title="Klik untuk tayangkan di web">
                                                <i class="bi bi-eye-slash"></i> Draft (Tutup)
                                            </button>
                                        @endif
                                    </form>
                                </td>
                                <td style="text-align: right;">
                                    <div class="action-btn-group">
                                        @if ($event->is_published)
                                            <a href="{{ route('event.show', $event->slug) }}" target="_blank" class="btn-action-icon" title="Lihat di Web">
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.public-events.edit', $event) }}" class="btn-action-icon" title="Edit Rincian Promosi">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('admin.public-events.destroy', $event) }}" method="POST" class="d-inline" onsubmit="return confirm('Tarik event ini dari promosi publik di website? (Acara operasional asli tidak akan terhapus)')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action-icon delete" title="Tarik dari Web">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5" style="color: #74847c;">
                                    <i class="bi bi-megaphone fs-1 d-block mb-2 text-muted"></i>
                                    Belum ada event yang dipromosikan ke website publik.<br>
                                    Silakan buka tab <strong>"Pilih dari Acara yang Ada"</strong> untuk mempublikasikan acara pernikahan ke website.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- =========================================================================
         TAB 2: DAFTAR ACARA OPERASIONAL (PILIH UNTUK DIPROMOSIKAN)
         ========================================================================= --}}
    <div id="tabContentOperational" class="tab-pane-content" style="display: none;">
        <div class="cms-toolbar">
            <div class="d-flex align-items-center gap-2">
                <span class="small fw-semibold text-muted">Daftar seluruh acara pernikahan yang terdaftar di MyDream. Klik "Promosikan" untuk mempublikasikannya ke calon klien lain.</span>
            </div>
            <div class="cms-search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="operationalSearch" placeholder="Cari nama acara, klien, atau venue...">
            </div>
        </div>

        <div class="cms-card">
            <div class="table-responsive">
                <table class="cms-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>Acara &amp; Klien (Pengantin)</th>
                            <th style="width: 200px;">Tanggal &amp; Waktu</th>
                            <th>Venue / Lokasi</th>
                            <th style="width: 170px;">Status Promosi</th>
                            <th style="width: 220px; text-align: right;">Aksi Publikasi</th>
                        </tr>
                    </thead>
                    <tbody id="operationalTableBody">
                        @forelse ($operationalEvents as $opEvent)
                            @php
                                $isPromoted = $opEvent->publicEvent !== null;
                                $client = $opEvent->booking?->client;
                                $eventTitle = $opEvent->name;
                                $clientName = $client ? ($client->groom_name . ' & ' . $client->bride_name) : '-';
                                $venueStr = $opEvent->booking?->venue_name ?: ($opEvent->notes ?: 'Venue MyDream');
                            @endphp
                            <tr class="op-event-row" data-search="{{ strtolower($eventTitle . ' ' . $clientName . ' ' . $venueStr) }}">
                                <td>
                                    <span class="badge bg-light text-dark border">#{{ $opEvent->id }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold" style="color: #173f39; font-size: 14.5px;">
                                        {{ $eventTitle }}
                                    </div>
                                    @if($client)
                                        <div class="small text-muted mt-1">
                                            <i class="bi bi-heart-fill text-danger me-1"></i>
                                            Pengantin: <strong>{{ $clientName }}</strong>
                                        </div>
                                    @endif
                                    @if($opEvent->package)
                                        <div class="small text-secondary">
                                            <i class="bi bi-box-seam me-1"></i> Paket: {{ $opEvent->package->name }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #173f39;">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ $opEvent->event_date ? \Carbon\Carbon::parse($opEvent->event_date)->translatedFormat('d M Y') : 'Belum ditentukan' }}
                                    </div>
                                    <div class="small text-muted">
                                        @if($opEvent->start_time)
                                            <i class="bi bi-clock me-1"></i>
                                            {{ substr((string)$opEvent->start_time, 0, 5) }} 
                                            @if($opEvent->end_time) - {{ substr((string)$opEvent->end_time, 0, 5) }} @endif WIB
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="small fw-semibold" style="color: #173f39;">
                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $venueStr }}
                                    </div>
                                    @if($opEvent->booking?->venue_city)
                                        <div class="small text-muted">{{ $opEvent->booking->venue_city }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($isPromoted)
                                        @if($opEvent->publicEvent->is_published)
                                            <span class="badge-promoted-yes">
                                                <i class="bi bi-check-circle-fill"></i> Tayang di Web
                                            </span>
                                        @else
                                            <span class="badge-promoted-no" style="background: #fef3c7; color: #92400e;">
                                                <i class="bi bi-pause-circle"></i> Draft Promosi
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge-promoted-no">
                                            <i class="bi bi-dash-circle"></i> Belum Dipromosikan
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    @if($isPromoted)
                                        <div class="d-flex justify-content-end gap-1">
                                            @if($opEvent->publicEvent->is_published)
                                                <a href="{{ route('event.show', $opEvent->publicEvent->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Lihat Tampilan di Web">
                                                    <i class="bi bi-box-arrow-up-right"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('admin.public-events.edit', $opEvent->publicEvent) }}" class="btn btn-sm btn-outline-primary" title="Edit Detail Promosi">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                        </div>
                                    @else
                                        <div class="d-flex justify-content-end gap-1">
                                            <form action="{{ route('admin.public-events.quick-promote', $opEvent->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-emerald" title="Langsung Promosikan ke Web dengan data bawaan">
                                                    <i class="bi bi-lightning-fill"></i> 1-Klik Promosikan
                                                </button>
                                            </form>
                                            <a href="{{ route('admin.public-events.create', ['event_id' => $opEvent->id]) }}" class="btn btn-sm btn-outline-secondary" title="Atur Deskripsi & Vendor Sebelum Promosi">
                                                <i class="bi bi-sliders"></i>
                                            </a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    Belum ada acara operasional yang terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Tab Switcher (Promoted vs Operational)
        const mainTabBtns = document.querySelectorAll('.tab-main-btn');
        const tabContentPromoted = document.getElementById('tabContentPromoted');
        const tabContentOperational = document.getElementById('tabContentOperational');

        mainTabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                mainTabBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const tab = this.dataset.tab;
                if (tab === 'promoted') {
                    tabContentPromoted.style.display = 'block';
                    tabContentOperational.style.display = 'none';
                } else {
                    tabContentPromoted.style.display = 'none';
                    tabContentOperational.style.display = 'block';
                }
            });
        });

        // Filter status tab 1
        const tabBtns = document.querySelectorAll('.cms-tab-btn');
        const searchInput = document.getElementById('eventSearch');
        const rows = document.querySelectorAll('.event-row');

        let currentFilter = 'all';
        let searchQuery = '';

        function applyFilter() {
            rows.forEach(row => {
                const status = row.dataset.status;
                const title = row.dataset.title;
                const loc = row.dataset.location;

                const matchesFilter = (currentFilter === 'all') || (status === currentFilter);
                const matchesSearch = !searchQuery || title.includes(searchQuery) || loc.includes(searchQuery);

                row.style.display = (matchesFilter && matchesSearch) ? '' : 'none';
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

        // Search tab 2 (Operational events)
        const opSearchInput = document.getElementById('operationalSearch');
        const opRows = document.querySelectorAll('.op-event-row');

        if (opSearchInput) {
            opSearchInput.addEventListener('input', function() {
                const q = this.value.trim().toLowerCase();
                opRows.forEach(row => {
                    const searchData = row.dataset.search;
                    row.style.display = (!q || searchData.includes(q)) ? '' : 'none';
                });
            });
        }
    });
</script>
@endpush
@endsection
