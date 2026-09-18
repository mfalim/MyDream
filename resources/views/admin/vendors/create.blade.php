@extends('layouts.admin')

@section('title', 'Tambah Vendor')
@section('page-title', 'Tambah Vendor')

@section('content')

    <div class="mb-4">

        <h3 class="mb-1">
            Tambah Vendor
        </h3>

        <p class="text-muted">
            Tambahkan vendor baru ke dalam sistem.
        </p>

    </div>


    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <form action="{{ route('admin.vendors.store') }}" method="POST" enctype="multipart/form-data">

                @csrf


                <div class="mb-3">

                    <label class="form-label">
                        Nama Vendor
                    </label>

                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Kategori
                    </label>

                    <input type="text" name="category" class="form-control" placeholder="Contoh: Catering"
                        value="{{ old('category') }}" required>

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Nomor Telepon
                    </label>

                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Alamat
                    </label>

                    <textarea name="address" class="form-control" rows="3" required>{{ old('address') }}</textarea>

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Deskripsi
                    </label>

                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>

                </div>


                <div class="mb-4">

                    <label class="form-label">
                        Foto Vendor
                    </label>

                    <input type="file" name="photo" class="form-control">

                </div>


                <button type="submit" class="btn btn-success">
                    Simpan Vendor
                </button>

                <a href="{{ route('admin.vendors.index') }}" class="btn btn-secondary">
                    Kembali
                </a>

            </form>

        </div>

    </div>

@endsection
