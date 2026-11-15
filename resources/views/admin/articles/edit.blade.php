@extends('layouts.admin')

@section('title', 'Edit Artikel: ' . $article->title)
@section('page-title', 'Edit Artikel')

@push('styles')
    @vite('resources/css/admin/cms.css')
@endpush

@section('content')
<div class="cms-container">
    <div class="cms-form-card mx-auto">
        <div class="cms-form-header">
            <div>
                <h2>Edit Artikel: {{ $article->title }}</h2>
                <p>Perbarui narasi, foto sampul, kategori, atau paket yang ditautkan pada artikel ini.</p>
            </div>
            @if ($article->is_published && (! $article->published_at || ! $article->published_at->isFuture()))
                <a href="{{ route('blog.show', $article->slug) }}" target="_blank" class="btn-outline-web">
                    <i class="bi bi-box-arrow-up-right"></i>
                    <span>Buka di Web</span>
                </a>
            @endif
        </div>

        <form action="{{ route('admin.articles.update', $article) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @include('admin.articles._form')

            <div class="d-flex align-items-center gap-2 mt-4 pt-3 border-top">
                <button type="submit" class="btn-emerald">
                    <i class="bi bi-check2-circle"></i>
                    <span>Simpan Perubahan</span>
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