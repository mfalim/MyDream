@extends('layouts.admin')

@section('title', 'Edit Artikel')
@section('page-title', 'Edit Artikel')

@section('content')

    <div class="mb-4">
        <h3 class="mb-1">Edit Artikel</h3>
        <p class="text-muted">Perubahan langsung tampil di halaman Artikel website.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.articles.update', $article) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @include('admin.articles._form')

                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>

@endsection
