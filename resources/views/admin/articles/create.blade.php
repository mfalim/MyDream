@extends('layouts.admin')

@section('title', 'Tulis Artikel Baru')
@section('page-title', 'Tulis Artikel Baru')

@push('styles')
    @vite('resources/css/admin/cms.css')
@endpush

@section('content')
<div class="cms-container">
    <div class="cms-form-card mx-auto">
        <div class="cms-form-header">
            <h2>Tulis Artikel Baru</h2>
            <p>Tulis tips, panduan perencanaan, atau dokumentasi kisah pernikahan klien untuk tayang di <strong>/blog</strong>.</p>
        </div>

        <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @include('admin.articles._form')

            <div class="d-flex align-items-center gap-2 mt-4 pt-3 border-top">
                <button type="submit" class="btn-emerald">
                    <i class="bi bi-check2-circle"></i>
                    <span>Simpan &amp; Publikasikan Artikel</span>
                </button>
                <a href="{{ route('admin.articles.index') }}" class="btn-outline-back">
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </form>
    </div>
</div>
@endsection