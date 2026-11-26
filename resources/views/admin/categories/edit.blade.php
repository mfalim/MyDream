@extends('layouts.admin')

@section('title', 'Edit Kategori Vendor')
@section('page-title', 'Edit Kategori Vendor')

@push('styles')
    @vite('resources/css/admin/members/categories.css')
@endpush

@section('content')
<div class="member-category-page">
    <div class="member-category-header">
        <div>
            <div class="eyebrow">DATA MASTER VENDOR</div>
            <h2>Edit Kategori Vendor</h2>
            <p>Perbarui nama atau gambar kategori vendor rekanan pada sistem Wedding Organizer.</p>
        </div>

        <a href="{{ route('admin.categories.index') }}" class="btn btn-category-filter px-3 py-2">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="category-form-card">
        <div class="category-form-card__header">
            <small>FORM KATEGORI VENDOR</small>
            <h5>Perbarui Kategori: {{ $category->name }}</h5>
        </div>

        <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

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
                        value="{{ old('name', $category->name) }}"
                        maxlength="255"
                        required
                        autofocus>
                    <small class="text-muted d-block mt-2">
                        Perubahan nama kategori akan otomatis terbarui pada seluruh vendor yang terhubung.
                    </small>
                </div>

                <div class="category-upload-box">
                    <label for="categoryImageInput" class="form-label fw-bold small mb-2">
                        Gambar Kategori <span class="text-muted fw-normal">(opsional)</span>
                    </label>

                    @if ($category->image)
                        <div id="categoryPreviewWrap" class="mb-3 d-flex align-items-center gap-3">
                            <img
                                id="categoryPreviewImg"
                                src="{{ \App\Support\Media::url($category->image) }}"
                                alt="{{ $category->name }}"
                                class="category-preview-img">
                            <div>
                                <span class="badge bg-light text-dark border">Gambar Saat Ini</span>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove_image">
                                    <label class="form-check-label text-danger small fw-semibold" for="remove_image">
                                        Hapus gambar ini
                                    </label>
                                </div>
                            </div>
                        </div>
                    @else
                        <div id="categoryPreviewWrap" class="mb-3 d-none align-items-center gap-3">
                            <img id="categoryPreviewImg" src="" alt="Preview gambar kategori" class="category-preview-img">
                            <small class="text-muted">Preview gambar baru yang akan diunggah.</small>
                        </div>
                    @endif

                    <input
                        type="file"
                        id="categoryImageInput"
                        name="image"
                        class="form-control"
                        accept=".jpg,.jpeg,.png,.webp">
                    <small class="text-muted d-block mt-2">
                        Unggah gambar baru untuk mengganti gambar saat ini. Format: JPG, PNG, atau WEBP, maksimal 2 MB.
                    </small>
                </div>
            </div>

            <div class="category-form-card__footer">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-category-filter px-3 py-2">Batal</a>
                <button type="submit" class="btn-emerald">
                    <i class="bi bi-check2-circle"></i>
                    <span>Simpan Perubahan</span>
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
            if (!file) return;
            img.src = URL.createObjectURL(file);
            wrap.classList.remove('d-none');
            wrap.classList.add('d-flex');
        });
    });
</script>
@endpush
@endsection
