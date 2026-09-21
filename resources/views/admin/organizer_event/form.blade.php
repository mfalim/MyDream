@extends('layouts.admin')

@section('title', isset($organizerEvent) ? 'Edit Event Organizer' : 'Tambah Event Organizer')
@section('page-title', isset($organizerEvent) ? 'Edit Event Organizer' : 'Form Tambah Event Organizer')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">{{ isset($organizerEvent) ? 'Edit Event Organizer' : 'Tambah Event Organizer' }}</h2>
                    <p class="text-muted mb-0">Event internal seperti Technical Meeting, Briefing, dll</p>
                </div>
                <div>
                    <a href="{{ route('admin.organizer-event.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" form="organizerEventForm" class="btn btn-dark">Simpan</button>
                </div>
            </div>

            <form id="organizerEventForm" method="POST" action="{{ isset($organizerEvent) ? route('admin.organizer-event.update', $organizerEvent->id) : route('admin.organizer-event.store') }}">
                @csrf
                @if(isset($organizerEvent))
                    @method('PUT')
                @endif

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; flex-shrink: 0;">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">Informasi Event</h5>
                                <small class="text-muted">Detail event organizer internal</small>
                            </div>
                            <span class="badge bg-danger ms-auto">WAJIB</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Judul Event <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title" value="{{ old('title', $organizerEvent->title ?? '') }}" placeholder="Contoh: Technical Meeting with Client" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Prioritas <span class="text-danger">*</span></label>
                                <select class="form-select" name="priority" required>
                                    <option value="low" {{ (old('priority', $organizerEvent->priority ?? '') == 'low') ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ (old('priority', $organizerEvent->priority ?? 'medium') == 'medium') ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ (old('priority', $organizerEvent->priority ?? '') == 'high') ? 'selected' : '' }}>High</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Deskripsi</label>
                                <textarea class="form-control" name="description" rows="3" placeholder="Deskripsi event">{{ old('description', $organizerEvent->description ?? '') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tipe Event <span class="text-danger">*</span></label>
                                <select class="form-select" name="event_type" required>
                                    <option value="">Pilih Tipe</option>
                                    <option value="technical_meeting" {{ (old('event_type', $organizerEvent->event_type ?? '') == 'technical_meeting') ? 'selected' : '' }}>Technical Meeting</option>
                                    <option value="briefing" {{ (old('event_type', $organizerEvent->event_type ?? '') == 'briefing') ? 'selected' : '' }}>Briefing</option>
                                    <option value="training" {{ (old('event_type', $organizerEvent->event_type ?? '') == 'training') ? 'selected' : '' }}>Training</option>
                                    <option value="site_visit" {{ (old('event_type', $organizerEvent->event_type ?? '') == 'site_visit') ? 'selected' : '' }}>Site Visit</option>
                                    <option value="vendor_meeting" {{ (old('event_type', $organizerEvent->event_type ?? '') == 'vendor_meeting') ? 'selected' : '' }}>Vendor Meeting</option>
                                    <option value="evaluation" {{ (old('event_type', $organizerEvent->event_type ?? '') == 'evaluation') ? 'selected' : '' }}>Evaluation</option>
                                    <option value="other" {{ (old('event_type', $organizerEvent->event_type ?? '') == 'other') ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="event_date" value="{{ old('event_date', isset($organizerEvent) ? $organizerEvent->event_date->format('Y-m-d') : '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Waktu Mulai</label>
                                <input type="time" class="form-control" name="start_time" value="{{ old('start_time', $organizerEvent->start_time ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Waktu Selesai</label>
                                <input type="time" class="form-control" name="end_time" value="{{ old('end_time', $organizerEvent->end_time ?? '') }}">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Lokasi</label>
                                <input type="text" class="form-control" name="location" value="{{ old('location', $organizerEvent->location ?? '') }}" placeholder="Contoh: Office, Zoom, Client Venue">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Peserta</label>
                                <textarea class="form-control" name="attendees" rows="2" placeholder="Contoh: John (Lead Director), Sarah (Coordinator), Client">{{ old('attendees', $organizerEvent->attendees ?? '') }}</textarea>
                                <small class="text-muted">Pisahkan dengan koma atau baris baru</small>
                            </div>
                            @if(isset($organizerEvent))
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Status</label>
                                <select class="form-select" name="status" required>
                                    <option value="scheduled" {{ (old('status', $organizerEvent->status ?? '') == 'scheduled') ? 'selected' : '' }}>Scheduled</option>
                                    <option value="completed" {{ (old('status', $organizerEvent->status ?? '') == 'completed') ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ (old('status', $organizerEvent->status ?? '') == 'cancelled') ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            @endif
                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan</label>
                                <textarea class="form-control" name="notes" rows="3" placeholder="Catatan tambahan">{{ old('notes', $organizerEvent->notes ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mb-4">
                    <a href="{{ route('admin.organizer-event.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                    <button type="submit" class="btn btn-dark px-5">
                        <i class="bi bi-check-circle me-2"></i> Simpan Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
