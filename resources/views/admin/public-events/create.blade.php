@extends('layouts.admin')

@section('title', 'Tambah Event Publik')
@section('page-title', 'Tambah Event Publik')

@push('styles')
    @vite('resources/css/admin/cms.css')
@endpush

@section('content')
<div class="cms-container">
    <div class="cms-form-card mx-auto">
        <div class="cms-form-header">
            <h2>Tambah Event Publik</h2>
            <p>Event yang dibuat di sini akan dipublikasikan ke halaman <strong>/event</strong> di website publik MyDream.</p>
        </div>

        <form action="{{ route('admin.public-events.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @include('admin.public-events._form')

            <div class="d-flex align-items-center gap-2 mt-4 pt-3 border-top">
                <button type="submit" class="btn-emerald">
                    <i class="bi bi-check2-circle"></i>
                    <span>Simpan &amp; Publikasikan Event</span>
                </button>
                <a href="{{ route('admin.public-events.index') }}" class="btn-outline-back">
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </form>
    </div>
</div>
@endsection