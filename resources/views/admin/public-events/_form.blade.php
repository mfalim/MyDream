{{-- Dipakai create & edit. Variabel: $event (null saat tambah), $vendors --}}
@php
    $selected = old('vendor_ids', isset($event) ? $event->vendors->pluck('id')->all() : []);
    $time = fn ($value) => $value ? substr($value, 0, 5) : '';
@endphp

<div class="mb-3">
    <label class="form-label">Judul Event</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $event->title ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Deskripsi</label>
    <textarea name="description" class="form-control" rows="4">{{ old('description', $event->description ?? '') }}</textarea>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Tanggal Mulai</label>
        <input type="date" name="starts_on" class="form-control" value="{{ old('starts_on', isset($event) ? $event->starts_on?->toDateString() : '') }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Tanggal Selesai <small class="text-muted">(kosongkan jika satu hari)</small></label>
        <input type="date" name="ends_on" class="form-control" value="{{ old('ends_on', isset($event) ? $event->ends_on?->toDateString() : '') }}">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Jam Mulai</label>
        <input type="time" name="start_time" class="form-control" value="{{ old('start_time', $time($event->start_time ?? null)) }}">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Jam Selesai</label>
        <input type="time" name="end_time" class="form-control" value="{{ old('end_time', $time($event->end_time ?? null)) }}">
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Lokasi</label>
    <input type="text" name="location" class="form-control" value="{{ old('location', $event->location ?? '') }}" placeholder="mis. Hotel Aston, Jember">
</div>

<div class="mb-3">
    <label class="form-label">Foto Event</label>
    @if (! empty($event?->photo))
        <div class="mb-2">
            <img src="{{ \App\Support\Media::url($event->photo) }}" alt="{{ $event->title }}" style="max-height: 140px; border-radius: 8px;">
        </div>
    @endif
    <input type="file" name="photo" class="form-control" accept=".jpg,.jpeg,.png,.webp">
    <small class="text-muted">Maksimal 2 MB. Format jpg, jpeg, png, atau webp.{{ ! empty($event?->photo) ? ' Unggah foto baru untuk mengganti.' : '' }}</small>
    @if (! empty($event?->photo))
        <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" name="remove_photo" value="1" id="remove_photo">
            <label class="form-check-label" for="remove_photo">Hapus foto saat ini</label>
        </div>
    @endif
</div>

<div class="mb-3">
    <label class="form-label">Vendor yang Berpartisipasi <small class="text-muted">(opsional)</small></label>
    <div class="border rounded p-3" style="max-height: 220px; overflow-y: auto;">
        @forelse ($vendors as $vendor)
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="vendor_ids[]" value="{{ $vendor->id }}" id="vendor-{{ $vendor->id }}" @checked(in_array($vendor->id, array_map('intval', (array) $selected)))>
                <label class="form-check-label" for="vendor-{{ $vendor->id }}">
                    {{ $vendor->name }} <small class="text-muted">{{ $vendor->category?->name }}</small>
                </label>
            </div>
        @empty
            <span class="text-muted">Belum ada vendor.</span>
        @endforelse
    </div>
</div>

<div class="form-check form-switch mb-4">
    <input class="form-check-input" type="checkbox" role="switch" name="is_published" value="1" id="is_published" @checked(old('is_published', $event->is_published ?? true))>
    <label class="form-check-label" for="is_published">Tampilkan di halaman publik</label>
</div>
