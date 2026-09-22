@extends('layouts.admin')

@section('title', 'Edit Vendor')
@section('page-title', 'Edit Vendor')

@section('content')

    <div class="mb-4">

        <h3 class="mb-1">
            Edit Vendor
        </h3>

        <p class="text-muted">
            Perbarui informasi dan foto vendor.
        </p>

    </div>


    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form action="{{ route('admin.vendors.update', $vendor) }}"
        method="POST"
        enctype="multipart/form-data">

        @csrf
        @method('PUT')


        {{-- Data Vendor --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body">

                <h5 class="mb-4">
                    Informasi Vendor
                </h5>


                <div class="mb-3">

                    <label class="form-label">
                        Nama Vendor
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="{{ old('name', $vendor->name) }}"
                        required
                    >

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Kategori
                    </label>

                    <select name="category_id"
                        class="form-select"
                        required>

                        <option value="">
                            Pilih kategori
                        </option>

                        @foreach ($categories as $category)

                            <option value="{{ $category->id }}"
                                @selected(old('category_id', $vendor->category_id) == $category->id)>

                                {{ $category->name }}

                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Harga Vendor
                    </label>

                    <input
                        type="number"
                        name="price"
                        class="form-control"
                        value="{{ old('price', $vendor->price) }}"
                        min="0"
                        step="0.01"
                        required
                    >

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Nomor Telepon
                    </label>

                    <input
                        type="text"
                        name="phone"
                        class="form-control"
                        value="{{ old('phone', $vendor->phone) }}"
                        required
                    >

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Alamat
                    </label>

                    <textarea
                        name="address"
                        class="form-control"
                        rows="3"
                        required>{{ old('address', $vendor->address) }}</textarea>

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Deskripsi
                    </label>

                    <textarea
                        name="description"
                        class="form-control"
                        rows="4">{{ old('description', $vendor->description) }}</textarea>

                </div>

            </div>

        </div>


        {{-- Foto Lama --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div>
                        <h5 class="mb-1">
                            Foto Vendor
                        </h5>

                        <small class="text-muted">
                            Pilih satu foto sebagai foto utama.
                        </small>
                    </div>

                </div>


                @if ($vendor->photos->count())

                    <div class="row g-3">

                        @foreach ($vendor->photos as $photo)

                            <div class="col-md-3">

                                <div class="edit-photo-card">

                                    <img src="{{ asset('storage/' . $photo->photo) }}"
                                        alt="{{ $vendor->name }}">

                                    <div class="p-2">

                                        <div class="form-check mb-2">

                                            <input
                                                type="radio"
                                                name="cover_photo_id"
                                                value="{{ $photo->id }}"
                                                class="form-check-input"
                                                id="cover-{{ $photo->id }}"
                                                @checked($photo->is_cover)
                                            >

                                            <label
                                                class="form-check-label"
                                                for="cover-{{ $photo->id }}">

                                                Foto Utama

                                            </label>

                                        </div>


                                        <div class="form-check">

                                            <input
                                                type="checkbox"
                                                name="delete_photos[]"
                                                value="{{ $photo->id }}"
                                                class="form-check-input"
                                                id="delete-{{ $photo->id }}"
                                            >

                                            <label
                                                class="form-check-label text-danger"
                                                for="delete-{{ $photo->id }}">

                                                Hapus Foto

                                            </label>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="text-muted">
                        Belum ada foto vendor.
                    </div>

                @endif

            </div>

        </div>


        {{-- Tambah Foto --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body">

                <h5 class="mb-3">
                    Tambah Foto Baru
                </h5>

                <input
                    type="file"
                    name="photos[]"
                    class="form-control"
                    accept=".jpg,.jpeg,.png,.webp"
                    multiple
                >

                <small class="text-muted">
                    Maksimal 10 foto tambahan, masing-masing maksimal 2 MB.
                </small>

            </div>

        </div>


        <button type="submit"
            class="btn btn-success">

            Simpan Perubahan

        </button>


        <a href="{{ route('admin.vendors.show', $vendor) }}"
            class="btn btn-secondary">

            Batal

        </a>

    </form>

@endsection
