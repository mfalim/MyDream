@extends('layouts.admin')

@section('title', 'Tambah Artikel')
@section('page-title', 'Tambah Artikel')

@section('content')

    <div class="mb-4">
        <h3 class="mb-1">Tambah Artikel</h3>
        <p class="text-muted">Tulis artikel, tips, atau cerita pernikahan client. Akan tampil di halaman Artikel website.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                @include('admin.articles._form')

                <button type="submit" class="btn btn-success">Simpan Artikel</button>
                <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>

@endsection
