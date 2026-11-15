@extends('layouts.admin')

@section('title', isset($organizerEvent) ? 'Edit Event Organizer' : 'Tambah Event Organizer')
@section('page-title', isset($organizerEvent) ? 'Edit Event Organizer' : 'Tambah Event Organizer')

@push('styles')
    @vite('resources/css/admin/organizer-events/form.css')
@endpush

@section('content')
<div class="org-event-page" style="max-width: 1000px;">
    {{-- Header --}}
    <div class="org-event-header">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.organizer-event.index') }}" class="text-decoration-none text-muted small">Event Organizer</a>
                <span class="text-muted small">/</span>
                <span class="text-muted small">{{ isset($organizerEvent) ? 'Edit Agenda' : 'Tambah Agenda' }}</span>
            </div>
            <h2>{{ isset($organizerEvent) ? 'Edit Agenda: ' . $organizerEvent->title : 'Tambah Agenda Event Organizer' }}</h2>
            <p>Atur jadwal kegiatan internal Wedding Organizer seperti technical meeting, briefing kru, dan survey lapangan.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.organizer-event.index') }}" class="btn-outline-back">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali</span>
            </a>
            <button type="submit" form="organizerEventForm" class="btn-emerald">
                <i class="bi bi-check2-circle"></i>
                <span>{{ isset($organizerEvent) ? 'Simpan Perubahan' : 'Buat Agenda' }}</span>
            </button>
        </div>
    </div>

    <form id="organizerEventForm" method="POST" action="{{ isset($organizerEvent) ? route('admin.organizer-event.update', $organizerEvent->id) : route('admin.organizer-event.store') }}">
        @csrf
        @if(isset($organizerEvent))
            @method('PUT')
        @endif

        {{-- Card 1: Informasi Pokok Agenda --}}
        <div class="org-detail-card mb-4">
            <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: #edf5f2; color: #073d36;">
                    <i class="bi bi-calendar-event fs-5"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0" style="color: #173f39; font-size: 16px;">Informasi Utama Agenda</h5>
                    <small class="text-muted">Tentukan judul, jenis kegiatan, dan jadwal pelaksanaan.</small>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-bold small">Judul Agenda Event <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-lg fs-6 @error('title') is-invalid @enderror" name="title" value="{{ old('title', $organizerEvent->title ?? '') }}" placeholder="Contoh: Technical Meeting Rundown Pernikahan Dimas & Anisa" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small">Prioritas Agenda <span class="text-danger">*</span></label>
                    <select class="form-select form-select-lg fs-6 @error('priority') is-invalid @enderror" name="priority" required>
                        <option value="low" @selected(old('priority', $organizerEvent->priority ?? '') === 'low')>🟢 Low (Biasa)</option>
                        <option value="medium" @selected(old('priority', $organizerEvent->priority ?? 'medium') === 'medium')>🟡 Medium (Penting)</option>
                        <option value="high" @selected(old('priority', $organizerEvent->priority ?? '') === 'high')>🔴 High (Krusial / Mendesak)</option>
                    </select>
                    @error('priority')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small">Tipe Kegiatan <span class="text-danger">*</span></label>
                    <select class="form-select @error('event_type') is-invalid @enderror" name="event_type" required>
                        <option value="">-- Pilih Tipe Kegiatan --</option>
                        <option value="technical_meeting" @selected(old('event_type', $organizerEvent->event_type ?? '') === 'technical_meeting')>Technical Meeting (TM)</option>
                        <option value="briefing" @selected(old('event_type', $organizerEvent->event_type ?? '') === 'briefing')>Briefing Vendor / Kru</option>
                        <option value="training" @selected(old('event_type', $organizerEvent->event_type ?? '') === 'training')>Pelatihan &amp; Gladi Bersih</option>
                        <option value="site_visit" @selected(old('event_type', $organizerEvent->event_type ?? '') === 'site_visit')>Survey Venue (Site Visit)</option>
                        <option value="vendor_meeting" @selected(old('event_type', $organizerEvent->event_type ?? '') === 'vendor_meeting')>Koordinasi Vendor</option>
                        <option value="consultation" @selected(old('event_type', $organizerEvent->event_type ?? '') === 'consultation')>Konsultasi Klien</option>
                        <option value="evaluation" @selected(old('event_type', $organizerEvent->event_type ?? '') === 'evaluation')>Evaluasi Pasca Acara</option>
                        <option value="other" @selected(old('event_type', $organizerEvent->event_type ?? '') === 'other')>Lainnya</option>
                    </select>
                    @error('event_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('event_date') is-invalid @enderror" name="event_date" value="{{ old('event_date', isset($organizerEvent) && $organizerEvent->event_date ? $organizerEvent->event_date->format('Y-m-d') : '') }}" required>
                    @error('event_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small">Waktu Mulai</label>
                    @php
                        $startTime = '';
                        if (isset($organizerEvent) && $organizerEvent->start_time) {
                            $startTime = is_string($organizerEvent->start_time) ? substr($organizerEvent->start_time, 0, 5) : $organizerEvent->start_time->format('H:i');
                        }
                    @endphp
                    <input type="time" class="form-control" name="start_time" value="{{ old('start_time', $startTime) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small">Waktu Selesai</label>
                    @php
                        $endTime = '';
                        if (isset($organizerEvent) && $organizerEvent->end_time) {
                            $endTime = is_string($organizerEvent->end_time) ? substr($organizerEvent->end_time, 0, 5) : $organizerEvent->end_time->format('H:i');
                        }
                    @endphp
                    <input type="time" class="form-control" name="end_time" value="{{ old('end_time', $endTime) }}">
                </div>
            </div>
        </div>

        {{-- Card 2: Lokasi, Peserta, & Catatan --}}
        <div class="org-detail-card mb-4">
            <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: #fdf6ec; color: #92530c;">
                    <i class="bi bi-geo-alt fs-5"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0" style="color: #173f39; font-size: 16px;">Lokasi &amp; Detail Koordinasi</h5>
                    <small class="text-muted">Tentukan tempat berkumpul, daftar kru yang hadir, dan catatan penting.</small>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-bold small">Lokasi / Tempat Pertemuan</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-geo-alt"></i></span>
                        <input type="text" class="form-control" name="location" value="{{ old('location', $organizerEvent->location ?? '') }}" placeholder="Contoh: Meeting Room Office MyDream, Zoom Meeting, Grand Ballroom Mulia">
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold small">Peserta / Kru yang Terlibat</label>
                    <textarea class="form-control" name="attendees" rows="2" placeholder="Contoh: Lead Director, Coordinator Acara, PIC Catering, Tim Sound System">{{ old('attendees', $organizerEvent->attendees ?? '') }}</textarea>
                    <small class="text-muted">Daftar tim atau pihak yang wajib hadir pada agenda ini.</small>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold small">Deskripsi Agenda &amp; Pembahasan</label>
                    <textarea class="form-control" name="description" rows="3" placeholder="Jelaskan topik yang akan dibahas, tujuan rapat, atau rangkaian briefing...">{{ old('description', $organizerEvent->description ?? '') }}</textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold small">Catatan Tambahan / Perlengkapan yang Harus Dibawa</label>
                    <textarea class="form-control" name="notes" rows="2" placeholder="Contoh: Bawa salinan rundown acara, laptop, dan cek kelengkapan walkie-talkie">{{ old('notes', $organizerEvent->notes ?? '') }}</textarea>
                </div>

                @if(isset($organizerEvent))
                    <div class="col-12 pt-2">
                        <label class="form-label fw-bold small">Status Pelaksanaan Agenda</label>
                        <select class="form-select form-select-lg fs-6" name="status" required>
                            <option value="scheduled" @selected(old('status', $organizerEvent->status) === 'scheduled')>🕒 Scheduled (Terjadwal / Menunggu)</option>
                            <option value="completed" @selected(old('status', $organizerEvent->status) === 'completed')>✅ Completed (Selesai Dilaksanakan)</option>
                            <option value="cancelled" @selected(old('status', $organizerEvent->status) === 'cancelled')>❌ Cancelled (Dibatalkan)</option>
                        </select>
                    </div>
                @endif
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex align-items-center justify-content-between p-3 rounded-4 bg-white border shadow-sm">
            <a href="{{ route('admin.organizer-event.index') }}" class="btn-outline-back">
                <i class="bi bi-arrow-left"></i>
                <span>Batal</span>
            </a>
            <button type="submit" class="btn-emerald">
                <i class="bi bi-check2-circle"></i>
                <span>{{ isset($organizerEvent) ? 'Simpan Perubahan Agenda' : 'Simpan Agenda Event' }}</span>
            </button>
        </div>
    </form>
</div>
@endsection
