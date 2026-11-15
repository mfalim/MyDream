@extends('layouts.admin')

@section('title', 'Artikel & Cerita Pernikahan')
@section('page-title', 'Artikel Website')

@push('styles')
    @vite('resources/css/admin/cms.css')
@endpush

@section('content')
<div class="cms-container">

    {{-- HEADER --}}
    <div class="cms-header">
        <div>
            <h2>Artikel &amp; Cerita Pernikahan</h2>
            <p>Kelola panduan perencanaan, tips persiapan, serta dokumentasi cerita nyata (Real Weddings) yang tampil di website publik.</p>
        </div>
        <div class="cms-header-actions">
            <a href="{{ route('blog.index') }}" target="_blank" class="btn-outline-web">
                <i class="bi bi-box-arrow-up-right"></i>
                <span>Lihat di Web</span>
            </a>
            <a href="{{ route('admin.articles.create') }}" class="btn-emerald">
                <i class="bi bi-plus-lg"></i>
                <span>Tulis Artikel Baru</span>
            </a>
        </div>
    </div>

    @php
        $total = $articles->count();
        $live = $articles->where('is_published', true)->filter(fn($a) => !$a->published_at || !$a->published_at->isFuture())->count();
        $scheduled = $articles->where('is_published', true)->filter(fn($a) => $a->published_at && $a->published_at->isFuture())->count();
        $draft = $articles->where('is_published', false)->count();

        $categories = $articles->pluck('category')->unique()->filter()->values();
    @endphp

    {{-- STATS GRID --}}
    <div class="cms-stats-grid">
        <div class="cms-stat-card">
            <div class="cms-stat-info">
                <small>TOTAL ARTIKEL</small>
                <strong>{{ $total }}</strong>
            </div>
            <div class="cms-stat-icon" style="background: rgba(7, 61, 54, 0.08); color: #073d36;">
                <i class="bi bi-journal-richtext"></i>
            </div>
        </div>
        <div class="cms-stat-card">
            <div class="cms-stat-info">
                <small>TAYANG (LIVE)</small>
                <strong style="color: #047857;">{{ $live }}</strong>
            </div>
            <div class="cms-stat-icon" style="background: #ecfdf5; color: #047857;">
                <i class="bi bi-check2-circle"></i>
            </div>
        </div>
        <div class="cms-stat-card">
            <div class="cms-stat-info">
                <small>TERJADWAL</small>
                <strong style="color: #1d4ed8;">{{ $scheduled }}</strong>
            </div>
            <div class="cms-stat-icon" style="background: #eff6ff; color: #1d4ed8;">
                <i class="bi bi-clock"></i>
            </div>
        </div>
        <div class="cms-stat-card">
            <div class="cms-stat-info">
                <small>DRAFT / TERSEMBUNYI</small>
                <strong style="color: #b45309;">{{ $draft }}</strong>
            </div>
            <div class="cms-stat-icon" style="background: #fffbeb; color: #b45309;">
                <i class="bi bi-eye-slash"></i>
            </div>
        </div>
    </div>

    {{-- TOOLBAR --}}
    <div class="cms-toolbar">
        <div class="cms-tabs">
            <button type="button" class="cms-tab-btn active" data-filter="all">
                Semua <span class="cms-tab-count">{{ $total }}</span>
            </button>
            <button type="button" class="cms-tab-btn" data-filter="live">
                Tayang <span class="cms-tab-count">{{ $live }}</span>
            </button>
            @if ($scheduled > 0)
                <button type="button" class="cms-tab-btn" data-filter="scheduled">
                    Terjadwal <span class="cms-tab-count">{{ $scheduled }}</span>
                </button>
            @endif
            @if ($draft > 0)
                <button type="button" class="cms-tab-btn" data-filter="draft">
                    Draft <span class="cms-tab-count">{{ $draft }}</span>
                </button>
            @endif
        </div>

        <div class="cms-search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="articleSearch" placeholder="Cari judul, kategori, atau penulis...">
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="cms-card">
        <div class="table-responsive">
            <table class="cms-table">
                <thead>
                    <tr>
                        <th style="width: 100px;">Cover</th>
                        <th>Judul &amp; Ringkasan</th>
                        <th style="width: 160px;">Kategori</th>
                        <th style="width: 170px;">Tanggal Tayang</th>
                        <th style="width: 130px;">Status</th>
                        <th style="width: 130px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="articleTableBody">
                    @forelse ($articles as $article)
                        @php
                            $isFuture = $article->published_at && $article->published_at->isFuture();
                            $state = ! $article->is_published ? 'draft' : ($isFuture ? 'scheduled' : 'live');
                        @endphp
                        <tr class="article-row" data-state="{{ $state }}" data-title="{{ strtolower($article->title) }}" data-category="{{ strtolower($article->category ?? '') }}" data-author="{{ strtolower($article->author ?? '') }}">
                            <td>
                                @if ($article->photo)
                                    <img src="{{ \App\Support\Media::url($article->photo) }}" alt="{{ $article->title }}" class="article-thumb">
                                @else
                                    <div class="article-thumb-fallback">
                                        <i class="bi bi-journal-text"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.articles.edit', $article) }}" class="article-title-link">
                                    {{ $article->title }}
                                </a>
                                <div class="article-excerpt">
                                    {{ $article->summary }}
                                </div>
                                <div class="mt-1 d-flex align-items-center gap-2 flex-wrap">
                                    <small class="text-muted"><i class="bi bi-person"></i> {{ $article->author ?: 'Tim MyDream' }}</small>
                                    @if ($article->package)
                                        <span class="pkg-tag">
                                            <i class="bi bi-box-seam"></i> Paket: {{ $article->package->name }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="cat-badge">{{ $article->category }}</span>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #173f39;">{{ $article->date_label }}</div>
                                <small class="text-muted">{{ $isFuture ? 'Dijadwalkan' : 'Diterbitkan' }}</small>
                            </td>
                            <td>
                                @if ($state === 'live')
                                    <span class="status-badge live">
                                        <i class="bi bi-check-circle"></i> Tayang
                                    </span>
                                @elseif ($state === 'scheduled')
                                    <span class="status-badge scheduled">
                                        <i class="bi bi-clock"></i> Terjadwal
                                    </span>
                                @else
                                    <span class="status-badge draft">
                                        <i class="bi bi-eye-slash"></i> Draft
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div class="action-btn-group">
                                    @if ($state === 'live')
                                        <a href="{{ route('blog.show', $article->slug) }}" target="_blank" class="btn-action-icon" title="Lihat di Web">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.articles.edit', $article) }}" class="btn-action-icon" title="Edit Artikel">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-icon delete" title="Hapus Artikel">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyArticleRow">
                            <td colspan="6" class="text-center py-5" style="color: #74847c;">
                                <i class="bi bi-journal-x fs-1 d-block mb-2 text-muted"></i>
                                Belum ada artikel. Klik <strong>+ Tulis Artikel Baru</strong> untuk menulis tips atau cerita pernikahan perdana.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabBtns = document.querySelectorAll('.cms-tab-btn');
        const searchInput = document.getElementById('articleSearch');
        const rows = document.querySelectorAll('.article-row');

        let currentFilter = 'all';
        let searchQuery = '';

        function applyFilter() {
            let visibleCount = 0;
            rows.forEach(row => {
                const state = row.dataset.state;
                const title = row.dataset.title;
                const cat = row.dataset.category;
                const author = row.dataset.author;

                const matchesFilter = (currentFilter === 'all') || (state === currentFilter);
                const matchesSearch = !searchQuery || title.includes(searchQuery) || cat.includes(searchQuery) || author.includes(searchQuery);

                if (matchesFilter && matchesSearch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
        }

        tabBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                tabBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentFilter = this.dataset.filter;
                applyFilter();
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                searchQuery = this.value.trim().toLowerCase();
                applyFilter();
            });
        }
    });
</script>
@endpush
@endsection