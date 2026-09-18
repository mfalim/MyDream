@extends('layouts.admin')

@section('title', 'Tambah Paket')
@section('page-title', 'Tambah Paket')

@section('content')

    <div class="mb-4">

        <h3 class="mb-1">
            Tambah Paket Wedding
        </h3>

        <p class="text-muted">
            Buat paket wedding dan tentukan vendor yang termasuk di dalamnya.
        </p>

    </div>


    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <form action="{{ route('admin.packages.store') }}" method="POST" enctype="multipart/form-data">

                @csrf


                <div class="mb-3">

                    <label class="form-label">
                        Nama Paket
                    </label>

                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Harga
                    </label>

                    <input type="number" name="price" class="form-control" value="{{ old('price') }}" required>

                </div>


                <div class="mb-4">

                    <label class="form-label">
                        Foto Paket
                    </label>

                    <input type="file" name="photo" class="form-control">

                </div>


                <div class="mb-4">

                    <label class="form-label">
                        Vendor dalam Paket
                    </label>

                    @forelse($vendors as $vendor)

                        <div class="form-check mb-2">

                            <input class="form-check-input" type="checkbox" name="vendors[]" value="{{ $vendor->id }}"
                                id="vendor{{ $vendor->id }}">

                            <label class="form-check-label" for="vendor{{ $vendor->id }}">

                                {{ $vendor->name }}

                                <span class="text-muted">
                                    ({{ $vendor->category }})
                                </span>

                            </label>

                        </div>

                    @empty

                        <div class="alert alert-warning">
                            Belum ada vendor. Tambahkan vendor terlebih dahulu.
                        </div>

                    @endforelse

                </div>


                <button type="submit" class="btn btn-success">
                    Simpan Paket
                </button>

                <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary">
                    Kembali
                </a>

            </form>

        </div>

    </div>

@endsection
