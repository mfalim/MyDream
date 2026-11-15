{{-- Dipakai create & edit. Variabel: $event (null saat tambah), $selectedEvent (opsional), $availableEvents, $vendors --}}
@php
    $selectedEvent = $selectedEvent ?? ($event->originalEvent ?? null);
    $currentEventId = old('event_id', $event->event_id ?? ($selectedEvent->id ?? null));
    $time = fn ($value) => $value ? (is_string($value) ? substr($value, 0, 5) : $value->format('H:i')) : '';

    // Data default dari event terpilih jika form create baru
    $defaultTitle = '';
    $defaultLocation = '';
    $defaultDate = '';
    $defaultStartTime = '';
    $defaultEndTime = '';
    $detectedVendors = collect();

    if ($selectedEvent) {
        $client = $selectedEvent->booking?->client;
        $defaultTitle = $client
            ? 'Pernikahan ' . $client->groom_name . ' & ' . $client->bride_name . ($selectedEvent->name ? ' (' . $selectedEvent->name . ')' : '')
            : ($selectedEvent->name ?? 'Acara Pernikahan');
        $defaultLocation = $selectedEvent->booking?->venue_name
            ? $selectedEvent->booking->venue_name . ($selectedEvent->booking->venue_city ? ', ' . $selectedEvent->booking->venue_city : '')
            : ($selectedEvent->notes ?: '');
        $defaultDate = $selectedEvent->event_date ? \Carbon\Carbon::parse($selectedEvent->event_date)->toDateString() : '';
        $defaultStartTime = $time($selectedEvent->start_time);
        $defaultEndTime = $time($selectedEvent->end_time);
        $detectedVendors = $selectedEvent->all_vendors;
    } elseif (isset($event) && $event->vendors->isNotEmpty()) {
        $detectedVendors = $event->vendors;
    }
@endphp

{{-- Bagian 1: Pilih Acara yang Sudah Ada --}}
<div class="mb-4 p-3 rounded-3 linked-event-box">
    <label class="form-label fw-bold mb-1" for="eventSelectPicker">
        <i class="bi bi-link-45deg me-1"></i> Tautkan dengan Acara yang Ada (MyDream Event)
    </label>
    <small class="text-muted d-block mb-2">Pilih acara pernikahan yang sudah terdaftar di operasional MyDream untuk dipromosikan ke calon klien lain.</small>

    <select name="event_id" id="eventSelectPicker" class="form-select">
        <option value="">-- Pilih Acara yang Ingin Dipromosikan (atau Buat Promosi Standalone) --</option>
        @foreach ($availableEvents as $opEv)
            @php
                $opClient = $opEv->booking?->client;
                $opLabel = '#' . $opEv->id . ' - ' . ($opEv->name ?: 'Pernikahan') . ($opClient ? ' (Pengantin: ' . $opClient->groom_name . ' & ' . $opClient->bride_name . ')' : '') . ' • ' . ($opEv->event_date ? \Carbon\Carbon::parse($opEv->event_date)->translatedFormat('d M Y') : '');
                $opLocation = $opEv->booking?->venue_name ? $opEv->booking->venue_name . ($opEv->booking->venue_city ? ', ' . $opEv->booking->venue_city : '') : '';
                $opDate = $opEv->event_date ? \Carbon\Carbon::parse($opEv->event_date)->toDateString() : '';
                $opStart = $time($opEv->start_time);
                $opEnd = $time($opEv->end_time);
                $opVendors = $opEv->all_vendors->map(fn($v) => [
                    'id' => $v->id,
                    'name' => $v->name,
                    'category' => $v->category?->name ?? 'Vendor'
                ])->values();
            @endphp
            <option value="{{ $opEv->id }}" 
                @selected((string)$currentEventId === (string)$opEv->id)
                data-title="{{ $opClient ? 'Pernikahan ' . $opClient->groom_name . ' & ' . $opClient->bride_name . ' (' . ($opEv->name ?: 'Resepsi') . ')' : ($opEv->name ?: 'Acara Pernikahan') }}"
                data-location="{{ $opLocation }}"
                data-date="{{ $opDate }}"
                data-start="{{ $opStart }}"
                data-end="{{ $opEnd }}"
                data-vendors='@json($opVendors)'>
                {{ $opLabel }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-4">
    <label class="form-label fw-bold" for="eventTitleInput">Judul Event Promosi <span class="text-danger">*</span></label>
    <input type="text" name="title" id="eventTitleInput" class="form-control form-control-lg" value="{{ old('title', $event->title ?? $defaultTitle) }}" placeholder="Contoh: Pernikahan Fattahul Alim & Lia Bilqis - Resepsi Adat Modern" required>
    <small class="text-muted">Judul ini yang akan tampil sebagai tajuk utama pada banner & kartu event di website publik.</small>
</div>

<div class="mb-4">
    <label class="form-label fw-bold" for="eventDescription">Deskripsi Promosi &amp; Highlight Acara</label>
    <textarea name="description" id="eventDescription" class="form-control" rows="4" placeholder="Jelaskan konsep acara, tema dekorasi, suasana resepsi, atau poin menarik untuk menginspirasi calon klien lain...">{{ old('description', $event->description ?? '') }}</textarea>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <label class="form-label fw-bold" for="startsOnInput">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
        <input type="date" name="starts_on" id="startsOnInput" class="form-control" value="{{ old('starts_on', isset($event) ? $event->starts_on?->toDateString() : $defaultDate) }}" required>
    </div>
    <div class="col-md-6 mb-4">
        <label class="form-label fw-bold" for="endsOnInput">Tanggal Selesai <small class="text-muted fw-normal">(kosongkan jika 1 hari)</small></label>
        <input type="date" name="ends_on" id="endsOnInput" class="form-control" value="{{ old('ends_on', isset($event) ? $event->ends_on?->toDateString() : '') }}">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <label class="form-label fw-bold" for="startTimeInput">Jam Mulai</label>
        <input type="time" name="start_time" id="startTimeInput" class="form-control" value="{{ old('start_time', isset($event) ? $time($event->start_time) : $defaultStartTime) }}">
    </div>
    <div class="col-md-6 mb-4">
        <label class="form-label fw-bold" for="endTimeInput">Jam Selesai</label>
        <input type="time" name="end_time" id="endTimeInput" class="form-control" value="{{ old('end_time', isset($event) ? $time($event->end_time) : $defaultEndTime) }}">
    </div>
</div>

<div class="mb-4">
    <label class="form-label fw-bold" for="locationInput">Lokasi / Venue Acara</label>
    <div class="input-group">
        <span class="input-group-text bg-light text-muted"><i class="bi bi-geo-alt"></i></span>
        <input type="text" name="location" id="locationInput" class="form-control" value="{{ old('location', $event->location ?? $defaultLocation) }}" placeholder="Contoh: Grand Ballroom Hotel Mulia Surya, Jember">
    </div>
</div>

<div class="mb-4 p-3 rounded-3 border bg-light">
    <label class="form-label fw-bold mb-2">Poster / Foto Sampul Promosi</label>
    @if (! empty($event?->photo))
        <div class="mb-3 d-flex align-items-center gap-3">
            <img src="{{ \App\Support\Media::url($event->photo) }}" alt="{{ $event->title }}" class="rounded border" style="width: 140px; height: 95px; object-fit: cover;">
            <div>
                <span class="badge bg-secondary">Foto Saat Ini</span>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="remove_photo" value="1" id="remove_photo">
                    <label class="form-check-label text-danger small fw-semibold" for="remove_photo">Hapus foto ini</label>
                </div>
            </div>
        </div>
    @elseif(! empty($selectedEvent?->photo))
        <div class="mb-3 d-flex align-items-center gap-3">
            <img src="{{ \App\Support\Media::url($selectedEvent->photo) }}" alt="{{ $selectedEvent->name }}" class="rounded border" style="width: 140px; height: 95px; object-fit: cover;">
            <span class="badge bg-secondary">Foto Bawaan Acara</span>
        </div>
    @endif
    <input type="file" name="photo" class="form-control" accept=".jpg,.jpeg,.png,.webp">
    <small class="text-muted d-block mt-1">Maksimal 2 MB (Format: JPG, PNG, WEBP). Bila dikosongkan, sistem akan otomatis menggunakan foto dari acara terkait.</small>
</div>

{{-- Vendor Rekanan: Otomatis tersinkron dari acara terkait --}}
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <label class="form-label fw-bold mb-0">
            <i class="bi bi-shop me-1"></i> Vendor Rekanan yang Bertugas
        </label>
        <span class="badge bg-light text-dark border">
            <i class="bi bi-magic me-1"></i> Otomatis dari Acara
        </span>
    </div>

    {{-- Kotak Tampilan Vendor Otomatis --}}
    <div class="p-3 rounded-3 vendor-auto-box">
        <div id="linkedVendorsContainer" class="d-flex flex-wrap gap-2">
            @forelse ($detectedVendors as $v)
                <span class="badge px-3 py-2 d-inline-flex align-items-center gap-2 vendor-auto-badge">
                    <i class="bi bi-check2 text-success"></i>
                    <strong>{{ $v->name }}</strong>
                    <span class="badge bg-light text-dark border">{{ $v->category?->name ?? 'Vendor' }}</span>
                </span>
            @empty
                <div class="text-muted small py-1" id="emptyVendorNotice">
                    <i class="bi bi-info-circle me-1"></i> Pilih acara di atas untuk otomatis menampilkan vendor yang berkolaborasi (katering, dekorasi, dokumentasi, dll).
                </div>
            @endforelse
        </div>
        <div class="text-muted small mt-2">
            <i class="bi bi-shield-check me-1 text-success"></i> Seluruh vendor di atas otomatis diambil dari paket dan jadwal rundown acara terpilih. Anda tidak perlu memilih vendor secara manual.
        </div>
    </div>

    {{-- Fallback Manual (opsional): Hanya tampil jika acara kosong / promosi mandiri --}}
    <div id="manualVendorsWrapper" class="mt-2 {{ $currentEventId ? 'd-none' : '' }}">
        <small class="text-muted d-block mb-1">Atau pilih vendor manual jika acara tidak ditautkan:</small>
        <div class="border rounded-3 p-3 bg-white" style="max-height: 180px; overflow-y: auto;">
            @forelse ($vendors as $vendor)
                <div class="form-check py-1">
                    <input class="form-check-input vendor-checkbox" type="checkbox" name="vendor_ids[]" value="{{ $vendor->id }}" id="vendor-{{ $vendor->id }}" @checked(in_array($vendor->id, array_map('intval', (array) old('vendor_ids', isset($event) ? $event->vendors->pluck('id')->all() : []))))>
                    <label class="form-check-label d-flex align-items-center justify-content-between w-100 pe-2" for="vendor-{{ $vendor->id }}">
                        <span><strong>{{ $vendor->name }}</strong></span>
                        <span class="badge bg-light text-dark border">{{ $vendor->category?->name ?? 'Vendor' }}</span>
                    </label>
                </div>
            @empty
                <span class="text-muted small">Belum ada vendor terdaftar di master data.</span>
            @endforelse
        </div>
    </div>
</div>

<div class="form-check form-switch p-3 rounded-3 mb-2 border bg-light">
    <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" name="is_published" value="1" id="is_published" @checked(old('is_published', $event->is_published ?? true)) style="width: 2.2em; height: 1.2em; cursor: pointer;">
    <label class="form-check-label fw-semibold" for="is_published" style="cursor: pointer;">
        Publikasikan di Website Publik (/event)
        <small class="d-block text-muted fw-normal">Jika aktif, event akan langsung tayang di website publik dan dapat dilihat oleh calon pengantin lain.</small>
    </label>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const picker = document.getElementById('eventSelectPicker');
        if (!picker) return;

        picker.addEventListener('change', function() {
            const selectedOpt = this.options[this.selectedIndex];
            const manualWrapper = document.getElementById('manualVendorsWrapper');
            const container = document.getElementById('linkedVendorsContainer');

            if (!selectedOpt || !selectedOpt.value) {
                if (manualWrapper) manualWrapper.classList.remove('d-none');
                if (container) {
                    container.innerHTML = '<div class="text-muted small py-1"><i class="bi bi-info-circle me-1"></i> Pilih acara di atas untuk otomatis menampilkan vendor yang berkolaborasi.</div>';
                }
                return;
            }

            if (manualWrapper) manualWrapper.classList.add('d-none');

            const title = selectedOpt.dataset.title;
            const loc = selectedOpt.dataset.location;
            const date = selectedOpt.dataset.date;
            const start = selectedOpt.dataset.start;
            const end = selectedOpt.dataset.end;
            let vendorList = [];
            try {
                vendorList = JSON.parse(selectedOpt.dataset.vendors || '[]');
            } catch (e) {}

            const titleInput = document.getElementById('eventTitleInput');
            const locInput = document.getElementById('locationInput');
            const dateInput = document.getElementById('startsOnInput');
            const startInput = document.getElementById('startTimeInput');
            const endInput = document.getElementById('endTimeInput');

            if (titleInput && (!titleInput.value || confirm('Isi otomatis form promosi dengan data dari acara yang dipilih?'))) {
                if (title) titleInput.value = title;
                if (loc && locInput) locInput.value = loc;
                if (date && dateInput) dateInput.value = date;
                if (start && startInput) startInput.value = start;
                if (end && endInput) endInput.value = end;
            }

            // Tampilkan badge vendor yang otomatis terhubung
            if (container) {
                if (vendorList.length > 0) {
                    container.innerHTML = vendorList.map(v => `
                        <span class="badge px-3 py-2 d-inline-flex align-items-center gap-2 vendor-auto-badge">
                            <i class="bi bi-check2 text-success"></i>
                            <strong>${v.name}</strong>
                            <span class="badge bg-light text-dark border">${v.category}</span>
                        </span>
                    `).join('');
                } else {
                    container.innerHTML = '<div class="text-muted small py-1"><i class="bi bi-info-circle me-1"></i> Belum ada vendor yang ditugaskan pada jadwal/paket acara ini.</div>';
                }
            }
        });
    });
</script>
@endpush