@extends('layouts.admin')

@section('title', 'Edit Event: ' . $event->title)
@section('page-title', 'Edit Event Publik')

@push('styles')
    @vite('resources/css/admin/cms.css')
@endpush

@section('content')
<div class="cms-container">
    <div class="cms-form-card mx-auto">
        <div class="cms-form-header">
            <div>
                <h2>Edit Event: {{ $event->title }}</h2>
                <p>Perbarui informasi detail acara, poster, atau daftar vendor yang berpartisipasi.</p>
            </div>
            @if ($event->is_published)
                <a href="{{ route('event.show', $event->slug) }}" target="_blank" class="btn-outline-web">
                    <i class="bi bi-box-arrow-up-right"></i>
                    <span>Buka di Web</span>
                </a>
            @endif
        </div>

        <form action="{{ route('admin.public-events.update', $event) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @include('admin.public-events._form')

            <div class="d-flex align-items-center gap-2 mt-4 pt-3 border-top">
                <button type="submit" class="btn-emerald">
                    <i class="bi bi-check2-circle"></i>
                    <span>Simpan Perubahan</span>
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