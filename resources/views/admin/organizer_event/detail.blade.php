@extends('layouts.admin')

@section('title', 'Detail Event: ' . $organizerEvent->title)
@section('page-title', 'Detail Event Organizer')

@push('styles')
    @vite('resources/css/admin/organizer-events/detail.css')
@endpush

@section('content')
<div class="org-event-page">
    {{-- Header --}}
    <div class="org-event-header">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <a href="{{ route('admin.organizer-event.index') }}" class="text-decoration-none text-muted small">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Agenda
                </a>
            </div>
            <h2>{{ $organizerEvent->title }}</h2>
            <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                {{-- Status Badge --}}
                @if($organizerEvent->status === 'completed')
                    <span class="badge-status-completed"><i class="bi bi-check2"></i> Completed (Selesai)</span>
                @elseif($organizerEvent->status === 'cancelled')
                    <span class="badge-status-cancelled"><i class="bi bi-x"></i> Cancelled (Dibatalkan)</span>
                @else
                    <span class="badge-status-scheduled"><i class="bi bi-clock"></i> Scheduled (Terjadwal)</span>
                @endif

                {{-- Priority Badge --}}
                @if($organizerEvent->priority === 'high')
                    <span class="badge-priority-high"><i class="bi bi-fire"></i> High Priority</span>
                @elseif($organizerEvent->priority === 'medium')
                    <span class="badge-priority-medium"><i class="bi bi-exclamation-circle"></i> Medium Priority</span>
                @else
                    <span class="badge-priority-low">Low Priority</span>
                @endif

                {{-- Type Badge --}}
                <span class="badge-type-tag">
                    <i class="bi bi-tag me-1"></i> {{ ucfirst(str_replace('_', ' ', $organizerEvent->event_type)) }}
                </span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.organizer-event.edit', $organizerEvent->id) }}" class="btn-emerald">
                <i class="bi bi-pencil-square"></i>
                <span>Edit Agenda</span>
            </a>
            <form action="{{ route('admin.organizer-event.destroy', $organizerEvent->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus agenda event ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger py-2 px-3 rounded-3" title="Hapus Agenda">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- Highlight Metrics Grid --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="p-3 rounded-4 bg-white border shadow-sm d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #edf5f2; color: #073d36; flex-shrink: 0;">
                    <i class="bi bi-calendar3 fs-4"></i>
                </div>
                <div>
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">TANGGAL KEGIATAN</small>
                    <div class="fw-bold" style="color: #173f39; font-size: 15.5px;">
                        {{ $organizerEvent->event_date ? $organizerEvent->event_date->translatedFormat('l, d F Y') : '-' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="p-3 rounded-4 bg-white border shadow-sm d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #eff6ff; color: #1e40af; flex-shrink: 0;">
                    <i class="bi bi-clock-history fs-4"></i>
                </div>
                <div>
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">WAKTU PELAKSANAAN</small>
                    <div class="fw-bold" style="color: #173f39; font-size: 15.5px;">
                        @if($organizerEvent->start_time)
                            {{ is_string($organizerEvent->start_time) ? substr($organizerEvent->start_time, 0, 5) : $organizerEvent->start_time->format('H:i') }}
                            @if($organizerEvent->end_time)
                                - {{ is_string($organizerEvent->end_time) ? substr($organizerEvent->end_time, 0, 5) : $organizerEvent->end_time->format('H:i') }}
                            @endif
                            WIB
                        @else
                            Waktu belum ditentukan
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="p-3 rounded-4 bg-white border shadow-sm d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #fdf6ec; color: #92530c; flex-shrink: 0;">
                    <i class="bi bi-geo-alt-fill fs-4"></i>
                </div>
                <div>
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">LOKASI / TEMPAT</small>
                    <div class="fw-bold text-truncate" style="color: #173f39; font-size: 15.5px; max-width: 260px;">
                        {{ $organizerEvent->location ?: 'Internal Office MyDream' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Info Grid --}}
    <div class="row g-4">
        {{-- Kolom Kiri: Deskripsi & Catatan --}}
        <div class="col-lg-8">
            {{-- Card Deskripsi --}}
            <div class="org-detail-card mb-4">
                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2" style="color: #173f39; font-size: 16px;">
                    <i class="bi bi-file-earmark-text" style="color: #073d36;"></i>
                    <span>Deskripsi &amp; Agenda Pembahasan</span>
                </h5>
                @if($organizerEvent->description)
                    <div class="p-3 rounded-3 bg-light border text-secondary" style="font-size: 14.5px; line-height: 1.6; white-space: pre-line;">
                        {{ $organizerEvent->description }}
                    </div>
                @else
                    <div class="text-muted small py-3 fst-italic">
                        Tidak ada deskripsi rinci untuk agenda kegiatan ini.
                    </div>
                @endif
            </div>

            {{-- Card Catatan Khusus --}}
            @if($organizerEvent->notes)
                <div class="org-detail-card mb-4">
                    <h5 class="fw-bold mb-3 d-flex align-items-center gap-2" style="color: #173f39; font-size: 16px;">
                        <i class="bi bi-journal-check" style="color: #92530c;"></i>
                        <span>Catatan Khusus &amp; Perlengkapan</span>
                    </h5>
                    <div class="p-3 rounded-3 border text-secondary" style="background: #fffdfa; border-color: #f7e8ce !important; font-size: 14px; line-height: 1.6; white-space: pre-line;">
                        {{ $organizerEvent->notes }}
                    </div>
                </div>
            @endif
        </div>

        {{-- Kolom Kanan: Peserta & Info Meta --}}
        <div class="col-lg-4">
            {{-- Card Peserta --}}
            <div class="org-detail-card mb-4">
                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2" style="color: #173f39; font-size: 16px;">
                    <i class="bi bi-people" style="color: #073d36;"></i>
                    <span>Peserta &amp; Kru Terlibat</span>
                </h5>

                @if($organizerEvent->attendees)
                    @php
                        $attendeeList = array_map('trim', explode(',', str_replace(["\r\n", "\n", "\r"], ',', $organizerEvent->attendees)));
                    @endphp
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($attendeeList as $attendee)
                            @if(!empty($attendee))
                                <span class="badge px-3 py-2 d-inline-flex align-items-center gap-2" style="background: #edf5f2; color: #073d36; border: 1px solid #c2dbd0; font-size: 12.5px; font-weight: 500;">
                                    <i class="bi bi-person-check-fill"></i>
                                    <span>{{ $attendee }}</span>
                                </span>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="text-muted small py-2">
                        Belum ada daftar peserta khusus yang dicantumkan.
                    </div>
                @endif
            </div>

            {{-- Card Metadata --}}
            <div class="org-detail-card">
                <h5 class="fw-bold mb-3" style="color: #173f39; font-size: 16px;">
                    <i class="bi bi-info-circle me-1" style="color: #073d36;"></i> Informasi Sistem
                </h5>
                <ul class="list-unstyled mb-0" style="font-size: 13px;">
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">ID Agenda:</span>
                        <strong class="text-dark">#{{ $organizerEvent->id }}</strong>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Dibuat Pada:</span>
                        <span>{{ $organizerEvent->created_at ? $organizerEvent->created_at->format('d M Y H:i') : '-' }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2">
                        <span class="text-muted">Terakhir Diperbarui:</span>
                        <span>{{ $organizerEvent->updated_at ? $organizerEvent->updated_at->format('d M Y H:i') : '-' }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
