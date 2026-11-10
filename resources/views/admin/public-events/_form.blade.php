{{-- Dipakai create & edit. Variabel: $event (null saat tambah), $vendors --}}
@php
    $selected = old('vendor_ids', isset($event) ? $event->vendors->pluck('id')->all() : []);
    $time = fn ($value) => $value ? substr($value, 0, 5) : '';
@endphp

<div class="mb-4">
    <label class="form-label fw-bold" style="color: #173f39; font-size: 13.5px;">Judul Event / Pameran <span class="text-danger">*</span></label>
    <input type="text" name="title" class="form-control form-control-lg" style="font-size: 15px; border-color: #d4dfda;" value="{{ old('title', $event->title ?? '') }}" placeholder="Contoh: MyDream Wedding Expo 2026" required>
</div>

<div class="mb-4">
    <label class="form-label fw-bold" style="color: #173f39; font-size: 13.5px;">Deskripsi Acara</label>
    <textarea name="description" class="form-control" rows="4" style="border-color: #d4dfda; font-size: 13.5px;" placeholder="Jelaskan mengenai tujuan acara, sesi konsultasi, penawaran khusus, atau agenda pameran...">{{ old('description', $event->description ?? '') }}</textarea>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <label class="form-label fw-bold" style="color: #173f39; font-size: 13.5px;">Tanggal Mulai <span class="text-danger">*</span></label>
        <input type="date" name="starts_on" class="form-control" style="border-color: #d4dfda;" value="{{ old('starts_on', isset($event) ? $event->starts_on?->toDateString() : '') }}" required>
    </div>
    <div class="col-md-6 mb-4">
        <label class="form-label fw-bold" style="color: #173f39; font-size: 13.5px;">Tanggal Selesai <small class="text-muted fw-normal">(kosongkan jika 1 hari)</small></label>
        <input type="date" name="ends_on" class="form-control" style="border-color: #d4dfda;" value="{{ old('ends_on', isset($event) ? $event->ends_on?->toDateString() : '') }}">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <label class="form-label fw-bold" style="color: #173f39; font-size: 13.5px;">Jam Mulai</label>
        <input type="time" name="start_time" class="form-control" style="border-color: #d4dfda;" value="{{ old('start_time', $time($event->start_time ?? null)) }}">
    </div>
    <div class="col-md-6 mb-4">
        <label class="form-label fw-bold" style="color: #173f39; font-size: 13.5px;">Jam Selesai</label>
        <input type="time" name="end_time" class="form-control" style="border-color: #d4dfda;" value="{{ old('end_time', $time($event->end_time ?? null)) }}">
    </div>
</div>

<div class="mb-4">
    <label class="form-label fw-bold" style="color: #173f39; font-size: 13.5px;">Lokasi / Venue</label>
    <div class="input-group">
        <span class="input-group-text bg-white" style="border-color: #d4dfda; color: #74847c;"><i class="bi bi-geo-alt"></i></span>
        <input type="text" name="location" class="form-control" style="border-color: #d4dfda;" value="{{ old('location', $event->location ?? '') }}" placeholder="Contoh: Grand Ballroom Hotel Aston, Jember">
    </div>
</div>

<div class="mb-4 p-3 rounded-3" style="background: #f7faf9; border: 1px dashed #cad9d3;">
    <label class="form-label fw-bold mb-2" style="color: #173f39; font-size: 13.5px;">Poster / Foto Sampul Event</label>
    @if (! empty($event?->photo))
        <div class="mb-3 d-flex align-items-center gap-3">
            <img src="{{ \App\Support\Media::url($event->photo) }}" alt="{{ $event->title }}" style="width: 140px; height: 95px; object-fit: cover; border-radius: 8px; border: 1px solid #d4dfda;">
            <div>
                <span class="badge bg-light text-dark border">Foto Saat Ini</span>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="remove_photo" value="1" id="remove_photo">
                    <label class="form-check-label text-danger small fw-semibold" for="remove_photo">Hapus foto ini</label>
                </div>
            </div>
        </div>
    @endif
    <input type="file" name="photo" class="form-control" accept=".jpg,.jpeg,.png,.webp" style="border-color: #d4dfda;">
    <small class="text-muted d-block mt-1">Maksimal 2 MB (Format: JPG, PNG, WEBP). Disarankan rasio landscape 16:9 atau 4:3.</small>
</div>

<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <label class="form-label fw-bold mb-0" style="color: #173f39; font-size: 13.5px;">Vendor Rekanan yang Berpartisipasi</label>
        <small class="text-muted">Pilih vendor yang buka booth di event ini</small>
    </div>
    <div class="border rounded-3 p-3" style="max-height: 220px; overflow-y: auto; background: #ffffff; border-color: #d4dfda !important;">
        @forelse ($vendors as $vendor)
            <div class="form-check py-1">
                <input class="form-check-input" type="checkbox" name="vendor_ids[]" value="{{ $vendor->id }}" id="vendor-{{ $vendor->id }}" @checked(in_array($vendor->id, array_map('intval', (array) $selected)))>
                <label class="form-check-label d-flex align-items-center justify-content-between w-100 pe-2" for="vendor-{{ $vendor->id }}" style="font-size: 13.5px; cursor: pointer;">
                    <span><strong>{{ $vendor->name }}</strong></span>
                    <span class="badge" style="background: #eef3f1; color: #073d36; font-size: 11px;">{{ $vendor->category?->name ?? 'Vendor' }}</span>
                </label>
            </div>
        @empty
            <span class="text-muted small">Belum ada vendor terdaftar di master data.</span>
        @endforelse
    </div>
</div>

<div class="form-check form-switch p-3 rounded-3 mb-2" style="background: #f7faf9; border: 1px solid #e4ebe8;">
    <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" name="is_published" value="1" id="is_published" @checked(old('is_published', $event->is_published ?? true)) style="width: 2.2em; height: 1.2em; cursor: pointer;">
    <label class="form-check-label fw-semibold" for="is_published" style="color: #173f39; font-size: 13.5px; cursor: pointer;">
        Publikasikan di Halaman Website
        <small class="d-block text-muted fw-normal">Jika dinonaktifkan, event berstatus Draft dan tidak terlihat oleh calon pengunjung website.</small>
    </label>
</div>