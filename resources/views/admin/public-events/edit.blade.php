@extends('layouts.admin')

@section('title', 'Edit Event Publik')
@section('page-title', 'Edit Event Publik')

@section('content')

    <div class="mb-4">
        <h3 class="mb-1">Edit Event Publik</h3>
        <p class="text-muted">Perubahan langsung tampil di halaman Event website.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.public-events.update', $event) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @include('admin.public-events._form')

                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                <a href="{{ route('admin.public-events.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>

@endsection
