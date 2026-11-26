@extends('layouts.admin')

@section('title', 'Kategori Vendor')
@section('page-title', 'Kategori Vendor')

@push('styles')
    @vite('resources/css/admin/members/categories.css')
@endpush

@section('content')
<div class="member-category-page" id="vendorCategoryWorkspace" data-ajax-workspace>
    {{-- Header --}}
    <div class="member-category-header">
        <div>
            <div class="eyebrow">DATA MASTER VENDOR</div>
            <h2>Kategori Vendor Rekanan</h2>
            <p>Kelola pengelompokan layanan vendor seperti dekorasi, catering, MUA, dokumentasi, dan kebutuhan pernikahan lainnya.</p>
        </div>

        <a href="{{ route('admin.categories.create') }}" class="btn-emerald">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah Kategori</span>
        </a>
    </div>

    {{-- Stats Grid --}}
    <div class="category-stats-grid category-stats-grid--3">
        <div class="category-stat-card">
            <div class="category-stat-info">
                <small>TOTAL KATEGORI</small>
                <strong class="is-maroon">{{ $totalCategories ?? $categories->count() }}</strong>
            </div>
            <div class="category-stat-icon is-maroon">
                <i class="bi bi-tags-fill"></i>
            </div>
        </div>

        <div class="category-stat-card">
            <div class="category-stat-info">
                <small>KATEGORI TERPAKAI</small>
                <strong class="is-gold">{{ $usedCategoriesCount ?? $categories->where('vendors_count', '>', 0)->count() }}</strong>
            </div>
            <div class="category-stat-icon is-gold">
                <i class="bi bi-check2-circle"></i>
            </div>
        </div>

        <div class="category-stat-card">
            <div class="category-stat-info">
                <small>TOTAL VENDOR TERHUBUNG</small>
                <strong class="is-emerald">{{ $totalLinkedVendors ?? $categories->sum('vendors_count') }}</strong>
            </div>
            <div class="category-stat-icon is-emerald">
                <i class="bi bi-shop"></i>
            </div>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <div class="category-filter-card">
        <form action="{{ route('admin.categories.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-lg-9 col-md-8">
                <label for="vendorCategorySearch" class="form-label small fw-bold mb-1">CARI KATEGORI VENDOR</label>
                <div class="input-group">
                    <span class="input-group-text border-end-0"><i class="bi bi-search"></i></span>
                    <input
                        id="vendorCategorySearch"
                        type="search"
                        name="q"
                        value="{{ $search ?? request('q') }}"
                        class="form-control border-start-0 ps-0"
                        placeholder="Ketik nama kategori vendor, misal: Dekorasi, MUA, Catering..."
                        aria-label="Cari kategori vendor">
                </div>
            </div>

            <div class="col-lg-3 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-category-filter w-100 py-2">
                    <i class="bi bi-search me-1"></i> Cari
                </button>
                @if (!empty($search ?? request('q')))
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-category-filter py-2 px-3" title="Reset pencarian">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Main Table Card --}}
    <div class="category-table-card">
        <div class="table-responsive">
            <table class="table member-category-table">
                <thead>
                    <tr>
                        <th style="width: 72px;">No.</th>
                        <th>Nama Kategori</th>
                        <th style="width: 210px;">Jumlah Vendor</th>
                        <th style="width: 130px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        @php($vendorCount = (int) ($category->vendors_count ?? 0))
                        <tr>
                            <td class="text-muted small">{{ $loop->iteration }}</td>
                            <td>
                                <div class="category-name-cell">
                                    @if ($category->image)
                                        <img
                                            src="{{ \App\Support\Media::url($category->image) }}"
                                            alt="{{ $category->name }}"
                                            class="category-thumb"
                                            loading="lazy">
                                    @else
                                        <span class="category-name-icon">
                                            <i class="bi bi-tag-fill"></i>
                                        </span>
                                    @endif
                                    <span class="category-name-title">{{ $category->name }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="category-badge-vendor-count {{ $vendorCount === 0 ? 'is-empty' : '' }}">
                                    <i class="bi bi-shop"></i>
                                    {{ $vendorCount }} Vendor
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                        class="btn-category-action"
                                        title="Edit Kategori">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form action="{{ route('admin.categories.destroy', $category) }}"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Hapus kategori {{ addslashes($category->name) }}? Vendor terkait akan menjadi tanpa kategori.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-category-action delete" title="Hapus Kategori">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-0">
                                <div class="category-empty-state">
                                    <i class="bi bi-tags"></i>
                                    <p>
                                        @if (!empty($search ?? request('q')))
                                            Tidak ada kategori vendor yang cocok dengan kata kunci "{{ $search ?? request('q') }}".
                                        @else
                                            Belum ada kategori vendor. Klik <strong>Tambah Kategori</strong> untuk mulai menambahkan.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
