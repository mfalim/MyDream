@extends('layouts.admin')

@section('title', 'Artikel')
@section('page-title', 'Artikel')

@section('content')

    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
        <div>
            <h3 class="mb-1">Artikel &amp; Cerita</h3>
            <p class="text-muted mb-0">Tips, panduan, dan cerita pernikahan client yang tampil di halaman Artikel website.</p>
        </div>
        <a href="{{ route('admin.articles.create') }}" class="btn btn-dark">+ Tambah Artikel</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 90px;">Foto</th>
                        <th>Artikel</th>
                        <th>Kategori</th>
                        <th>Tayang</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($articles as $article)
                        @php
                            $scheduled = $article->published_at && $article->published_at->isFuture();
                        @endphp
                        <tr>
                            <td>
                                <img src="{{ \App\Support\Media::url($article->photo) }}" alt="{{ $article->title }}" style="width: 72px; height: 54px; object-fit: cover; border-radius: 6px;">
                            </td>
                            <td>
                                <strong>{{ $article->title }}</strong><br>
                                <small class="text-muted">{{ $article->author ?: '-' }}{{ $article->package ? ' · paket: ' . $article->package->name : '' }}</small>
                            </td>
                            <td><span class="badge text-bg-light border">{{ $article->category }}</span></td>
                            <td>{{ $article->date_label }}</td>
                            <td>
                                @if (! $article->is_published)
                                    <span class="badge text-bg-warning">Disembunyikan</span>
                                @elseif ($scheduled)
                                    <span class="badge text-bg-info">Terjadwal</span>
                                @else
                                    <span class="badge text-bg-success">Tayang</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if ($article->is_published && ! $scheduled)
                                    <a href="{{ route('blog.show', $article->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Lihat</a>
                                @endif
                                <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus artikel ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">Belum ada artikel. Klik "+ Tambah Artikel" untuk menulis yang pertama.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
