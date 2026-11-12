@extends('layouts.admin')

@section('title', 'Tambah Event Publik')
@section('page-title', 'Tambah Event Publik')

@section('content')

    <div class="mb-4">
        <h3 class="mb-1">Tambah Event Publik</h3>
        <p class="text-muted">Event yang dibuat di sini akan tampil di halaman Event website.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.public-events.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                @include('admin.public-events._form')

                <button type="submit" class="btn btn-success">Simpan Event</button>
                <a href="{{ route('admin.public-events.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>

@endsection
