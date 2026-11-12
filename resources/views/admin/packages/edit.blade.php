@extends('layouts.admin')

@section('title', 'Edit Paket: ' . $package->name)
@section('page-title', 'Edit Paket Wedding')

@push('styles')
    @vite('resources/css/admin/cms.css')
@endpush

@section('content')
<div class="cms-container">
    {{-- Header --}}
    <div class="cms-header">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.packages.index') }}" class="text-decoration-none text-muted small">Kelola Paket</a>
                <span class="text-muted small">/</span>
                <span class="text-muted small">Edit Paket</span>
            </div>
            <h2>Edit Paket: {{ $package->name }}</h2>
            <p>Perbarui informasi paket, foto sampul, dan susunan vendor rekanan yang termasuk di dalamnya.</p>
        </div>
        <div class="cms-header-actions">
            <a href="{{ route('admin.packages.show', $package) }}" class="btn-outline-web">
                <i class="bi bi-eye"></i>
                <span>Lihat Detail</span>
            </a>
            <a href="{{ route('admin.packages.index') }}" class="btn-outline-back">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali ke Daftar</span>
            </a>
        </div>
    </div>

    <form action="{{ route('admin.packages.update', $package) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            {{-- Kolom Kiri: Informasi Utama & Foto --}}
            <div class="col-lg-7">
                {{-- Card 1: Informasi Dasar --}}
                <div class="cms-card p-4 mb-4">
                    <h5 class="fw-bold mb-3" style="color: #073d36; font-size: 16px;">
                        <i class="bi bi-info-circle me-2"></i> Informasi Paket
                    </h5>

                    <div class="mb-3">
                        <label class="form-label fw-bold small" for="packageName">Nama Paket <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="packageName" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $package->name) }}"
                            pattern="^[A-Za-z0-9À-ž][A-Za-z0-9À-ž &.'-]*$"
                            title="Gunakan huruf, angka, spasi, dan tanda baca umum saja." required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small" for="availDate">Tanggal Ketersediaan <span class="text-danger">*</span></label>
                            <input type="date" name="availability_date" id="availDate" class="form-control @error('availability_date') is-invalid @enderror"
                                value="{{ old('availability_date', optional($package->availability_date)->format('Y-m-d')) }}" required>
                            @error('availability_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small" for="guestCapacity">Kapasitas Tamu <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="guest_capacity" id="guestCapacity" class="form-control @error('guest_capacity') is-invalid @enderror"
                                    value="{{ old('guest_capacity', $package->guest_capacity) }}" min="1" required>
                                <span class="input-group-text bg-light text-muted">tamu</span>
                            </div>
                            @error('guest_capacity')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small" for="eventPeriod">Periode Acara <small class="text-muted fw-normal">(opsional)</small></label>
                        <input type="text" name="event_period" id="eventPeriod" class="form-control"
                            value="{{ old('event_period', $package->event_period) }}" placeholder="Contoh: Sepanjang tahun, Promo Liburan, Akhir Pekan">
                    </div>

                    <div class="form-check form-switch p-3 rounded-3 border bg-light">
                        <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" name="is_featured" value="1" id="is_featured"
                            @checked(old('is_featured', $package->is_featured)) style="width: 2.2em; height: 1.2em; cursor: pointer;">
                        <label class="form-check-label fw-semibold" for="is_featured" style="cursor: pointer; color: #073d36;">
                            Tampilkan sebagai "Pilihan MyDream" di Website
                            <small class="d-block text-muted fw-normal">Paket unggulan akan ditampilkan di bagian teratas etalase paket wedding.</small>
                        </label>
                    </div>
                </div>

                {{-- Card 2: Foto & Deskripsi --}}
                <div class="cms-card p-4">
                    <h5 class="fw-bold mb-3" style="color: #073d36; font-size: 16px;">
                        <i class="bi bi-image me-2"></i> Foto Sampul &amp; Deskripsi
                    </h5>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Foto Sampul Paket</label>

                        @if ($package->photo)
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3 border bg-light mb-3" id="currentPhotoBox">
                                <img src="{{ \App\Support\Media::url($package->photo) }}" alt="{{ $package->name }}"
                                    class="rounded border" style="width: 140px; height: 95px; object-fit: cover;">
                                <div>
                                    <span class="badge bg-secondary mb-2">Foto Saat Ini</span>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remove_photo" value="1" id="removePhotoCheck">
                                        <label class="form-check-label text-danger small fw-semibold" for="removePhotoCheck">
                                            Hapus foto ini saat disimpan
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div id="newPhotoPreviewWrapper" class="d-none mb-3">
                            <span class="badge bg-success mb-2">Preview Foto Baru</span>
                            <div class="p-2 border rounded-3 bg-light d-inline-block">
                                <img id="newPhotoPreview" src="#" alt="Preview Foto" class="rounded" style="width: 140px; height: 95px; object-fit: cover;">
                            </div>
                        </div>

                        <input type="file" name="photo" id="packagePhotoInput" class="form-control @error('photo') is-invalid @enderror"
                            accept=".jpg,.jpeg,.png,.webp">
                        <small class="text-muted d-block mt-1">Format: JPG, JPEG, PNG, WEBP. Maksimal 2 MB.</small>
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold small" for="packageDescription">Deskripsi Paket <small class="text-muted fw-normal">(opsional)</small></label>
                        <textarea name="description" id="packageDescription" class="form-control" rows="4"
                            placeholder="Tuliskan gambaran paket, keunggulan, atau catatan khusus untuk calon pengantin...">{{ old('description', $package->description) }}</textarea>
                        <small class="text-muted">Deskripsi ini akan dibaca oleh pengunjung saat melihat detail paket di website.</small>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Vendor Rekanan dalam Paket --}}
            <div class="col-lg-5">
                <div class="cms-card p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="fw-bold mb-1" style="color: #073d36; font-size: 16px;">
                                <i class="bi bi-shop me-2"></i> Vendor dalam Paket
                            </h5>
                            <small class="text-muted">Pilih vendor rekanan yang termasuk pada paket ini.</small>
                        </div>
                    </div>

                    {{-- Summary Widget --}}
                    <div class="p-3 rounded-3 mb-3 border bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block" style="font-size: 11.5px;">TOTAL VENDOR DIPILIH</small>
                            <strong id="selectedVendorCount" class="fs-5 text-dark">0</strong> <span class="text-muted small">vendor</span>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block" style="font-size: 11.5px;">TOTAL ESTIMASI PAKET</small>
                            <strong id="totalPackagePrice" class="fs-5" style="color: #073d36;">Rp 0</strong>
                        </div>
                    </div>

                    {{-- Search Vendor --}}
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" id="vendorSearchInput" class="form-control border-start-0 ps-0" placeholder="Cari nama atau kategori vendor...">
                        </div>
                    </div>

                    {{-- Vendor Checklist Box --}}
                    <div class="border rounded-3 p-3 bg-white" style="max-height: 480px; overflow-y: auto;" id="vendorListContainer">
                        @php
                            $selectedVendorIds = old('vendors', $package->vendors->pluck('id')->all());
                        @endphp

                        @forelse ($vendors as $vendor)
                            <div class="vendor-check-item p-2 mb-2 rounded border-bottom" data-search="{{ strtolower($vendor->name . ' ' . ($vendor->category?->name ?? '')) }}">
                                <div class="form-check d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <input class="form-check-input vendor-checkbox" type="checkbox" name="vendors[]"
                                            value="{{ $vendor->id }}" id="vendor{{ $vendor->id }}"
                                            data-price="{{ (float) $vendor->price }}"
                                            @checked(in_array($vendor->id, $selectedVendorIds))>
                                        <label class="form-check-label mb-0" for="vendor{{ $vendor->id }}" style="cursor: pointer;">
                                            <strong class="d-block text-dark" style="font-size: 13.5px;">{{ $vendor->name }}</strong>
                                            <span class="badge bg-light text-dark border" style="font-size: 10.5px;">{{ $vendor->category?->name ?? 'Vendor' }}</span>
                                        </label>
                                    </div>
                                    <span class="fw-semibold small text-nowrap" style="color: #073d36;">
                                        Rp {{ number_format($vendor->price, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-warning mb-0">Belum ada vendor terdaftar di master data.</div>
                        @endforelse
                    </div>
                </div>

                {{-- Action Card --}}
                <div class="mt-4 d-flex align-items-center gap-2">
                    <button type="submit" class="btn-emerald flex-grow-1 justify-content-center py-2">
                        <i class="bi bi-check2-circle"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                    <a href="{{ route('admin.packages.show', $package) }}" class="btn-outline-back py-2">
                        <span>Batal</span>
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Live Image Preview
        const photoInput = document.getElementById('packagePhotoInput');
        const previewWrapper = document.getElementById('newPhotoPreviewWrapper');
        const previewImg = document.getElementById('newPhotoPreview');

        if (photoInput && previewWrapper && previewImg) {
            photoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(evt) {
                        previewImg.src = evt.target.result;
                        previewWrapper.classList.remove('d-none');
                    };
                    reader.readAsDataURL(file);
                } else {
                    previewWrapper.classList.add('d-none');
                }
            });
        }

        // 2. Kalkulasi Total Vendor & Harga Paket
        const vendorCheckboxes = document.querySelectorAll('.vendor-checkbox');
        const countDisplay = document.getElementById('selectedVendorCount');
        const priceDisplay = document.getElementById('totalPackagePrice');

        function formatRupiah(number) {
            return 'Rp ' + Math.round(number).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function calculateVendors() {
            let count = 0;
            let totalPrice = 0;

            vendorCheckboxes.forEach(cb => {
                if (cb.checked) {
                    count++;
                    totalPrice += parseFloat(cb.dataset.price || 0);
                }
            });

            if (countDisplay) countDisplay.textContent = count;
            if (priceDisplay) priceDisplay.textContent = formatRupiah(totalPrice);
        }

        vendorCheckboxes.forEach(cb => {
            cb.addEventListener('change', calculateVendors);
        });

        calculateVendors(); // Jalankan saat load awal

        // 3. Pencarian Vendor
        const searchInput = document.getElementById('vendorSearchInput');
        const vendorItems = document.querySelectorAll('.vendor-check-item');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const q = this.value.trim().toLowerCase();
                vendorItems.forEach(item => {
                    const text = item.dataset.search || '';
                    item.style.display = (!q || text.includes(q)) ? '' : 'none';
                });
            });
        }
    });
</script>
@endpush
@endsection
