@extends('layouts.admin')

@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori')

@section('content')
    <div class="mb-4">
        <h3 class="mb-1">Tambah Kategori</h3>
        <p class="text-muted">Tambahkan kategori vendor baru.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <button type="submit" class="btn btn-success">Simpan Kategori</button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@endsection
