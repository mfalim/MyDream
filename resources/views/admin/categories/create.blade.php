@extends('layouts.admin')

@section('title', 'Tambah Kategori Vendor')
@section('page-title', 'Tambah Kategori Vendor')

@push('styles')
    @vite('resources/css/admin/members/categories.css')
@endpush

@section('content')
<div class="member-category-page">
    <div class="member-category-header">
        <div>
            <div class="eyebrow">DATA MASTER VENDOR</div>
            <h2>Tambah Kategori Vendor</h2>
            <p>Tambahkan kategori layanan baru beserta gambar lokal untuk direktori vendor rekanan.</p>
        </div>

        <a href="{{ route('admin.categories.index') }}" class="btn btn-category-filter px-3 py-2">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="category-form-card">
        <div class="category-form-card__header">
            <small>FORM KATEGORI VENDOR</small>
            <h5>Detail Kategori Baru</h5>
        </div>

        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="category-form-card__body">
                <div class="mb-4">
                    <label for="name" class="form-label fw-bold small">
                        Nama Kategori <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control form-control-lg fs-6"
                        value="{{ old('name') }}"
                        maxlength="255"
                        placeholder="Contoh: Dekorasi, Catering, MUA, Dokumentasi, Entertainment"
                        required
                        autofocus>
                    <small class="text-muted d-block mt-2">
                        Nama kategori ini akan tampil pada filter direktori vendor dan pilihan layanan klien.
                    </small>
                </div>

                <div class="category-upload-box">
                    <label for="categoryImageInput" class="form-label fw-bold small mb-2">
                        Gambar Kategori <span class="text-muted fw-normal">(opsional)</span>
                    </label>

                    <div id="categoryPreviewWrap" class="mb-3 d-none align-items-center gap-3">
                        <img id="categoryPreviewImg" src="" alt="Preview gambar kategori" class="category-preview-img">
                        <small class="text-muted">Preview gambar yang akan diunggah.</small>
                    </div>

                    <input
                        type="file"
                        id="categoryImageInput"
                        name="image"
                        class="form-control"
                        accept=".jpg,.jpeg,.png,.webp">
                    <small class="text-muted d-block mt-2">
                        Disimpan langsung di server lokal (tanpa CDN). Format: JPG, PNG, atau WEBP, maksimal 2 MB.
                    </small>
                </div>
            </div>

            <div class="category-form-card__footer">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-category-filter px-3 py-2">Batal</a>
                <button type="submit" class="btn-emerald">
                    <i class="bi bi-check2-circle"></i>
                    <span>Simpan Kategori</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('categoryImageInput');
        const wrap = document.getElementById('categoryPreviewWrap');
        const img = document.getElementById('categoryPreviewImg');
        if (!input || !wrap || !img) return;

        input.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) {
                wrap.classList.add('d-none');
                wrap.classList.remove('d-flex');
                return;
            }
            img.src = URL.createObjectURL(file);
            wrap.classList.remove('d-none');
            wrap.classList.add('d-flex');
        });
    });
</script>
@endpush
@endsection
