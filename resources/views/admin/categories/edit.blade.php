@extends('layouts.admin')

@section('title', 'Edit Kategori')
@section('page-title', 'Edit Kategori')

@section('content')
    <div class="mb-4">
        <h3 class="mb-1">Edit Kategori</h3>
        <p class="text-muted">Perbarui nama kategori vendor.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="name" class="form-label">Nama Kategori</label>
                    <input type="text" id="name" name="name" class="form-control"
                        value="{{ old('name', $category->name) }}" maxlength="255" required>
                </div>

                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@endsection
