@extends('layouts.admin')

@section('title', 'Vendor')
@section('page-title', 'Vendor Rekanan')

@section('content')

    <div class="vendor-page">

        {{-- Header --}}
        <div class="vendor-header">
            <div>
                <h2>Direktori Vendor Rekanan</h2>
                <p>
                    Kelola vendor dekorasi, catering, MUA, dokumentasi,
                    dan kebutuhan wedding lainnya.
                </p>
            </div>

            <a href="{{ route('admin.vendors.create') }}" class="btn-add-vendor">
                + Tambah Vendor
            </a>
        </div>

        {{-- Search & Filter --}}
        <div class="vendor-toolbar">

            <form action="{{ route('admin.vendors.index') }}" method="GET" class="vendor-search">
                <span>⌕</span>
                <input type="search" name="q" value="{{ $search }}"
                    placeholder="Cari nama vendor, kategori, atau alamat..." aria-label="Cari vendor">
                <input type="hidden" name="category_id" value="{{ $categoryId }}">
            </form>

            <select class="vendor-filter" name="category_id" form="vendor-filter-form"
                onchange="this.form.submit()" aria-label="Filter kategori">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) $categoryId === (string) $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <form id="vendor-filter-form" action="{{ route('admin.vendors.index') }}" method="GET">
                <input type="hidden" name="q" value="{{ $search }}">
            </form>

        </div>

        {{-- Category --}}
        <div class="category-filter">
            <a href="{{ route('admin.vendors.index', ['q' => $search]) }}"
                class="category-btn {{ !$categoryId ? 'active' : '' }}">Semua Vendor</a>
            @foreach ($categories as $category)
                <a href="{{ route('admin.vendors.index', ['q' => $search, 'category_id' => $category->id]) }}"
                    class="category-btn {{ (string) $categoryId === (string) $category->id ? 'active' : '' }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>

        <div class="vendor-result-count">
            Menampilkan {{ $vendors->count() }} vendor
        </div>

        {{-- Vendor Grid --}}
        <div class="vendor-grid">

            @forelse ($vendors as $vendor)

                <div class="vendor-card">

                    {{-- Foto --}}
                    <div class="vendor-image">

                        @if ($vendor->photo)
                            <img src="{{ asset('storage/' . $vendor->photo) }}" alt="{{ $vendor->name }}">
                        @else
                            <div class="vendor-no-image">
                                <span>📷</span>
                                <small>Tidak ada foto</small>
                            </div>
                        @endif

                        <span class="vendor-category">
                            {{ $vendor->category?->name ?? '-' }}
                        </span>

                    </div>

                {{-- Content --}}
                <div class="vendor-card-body">

                    <h4>{{ $vendor->name }}</h4>

                    <div class="vendor-location">
                        📍 {{ $vendor->address }}
                    </div>

                    <div class="vendor-phone">
                        ☎ {{ $vendor->phone }}
                    </div>

                    @if ($vendor->description)
                        <p class="vendor-description">
                            {{ $vendor->description }}
                        </p>
                    @endif

                    {{-- Lihat Detail --}}
                    <a href="{{ route('admin.vendors.show', $vendor) }}" class="btn-detail-vendor">
                        Lihat Detail
                    </a>

                    {{-- Hubungi WhatsApp --}}
                    @php
                        $whatsappNumber = preg_replace('/\D+/', '', $vendor->phone);

                        if (str_starts_with($whatsappNumber, '0')) {
                            $whatsappNumber = '62' . substr($whatsappNumber, 1);
                        }

                        $whatsappMessage = urlencode(
                            'Halo ' . $vendor->name . ', saya ingin menanyakan mengenai layanan vendor wedding Anda.'
                        );
                    @endphp

                    <a href="https://wa.me/{{ $whatsappNumber }}?text={{ $whatsappMessage }}" target="_blank" class="btn-whatsapp">
                        ☎ Hubungi Vendor
                    </a>

                    {{-- Edit & Delete --}}
                    <div class="vendor-actions">

                        <a href="{{ route('admin.vendors.edit', $vendor) }}" class="btn-edit-vendor">
                            ✏ Edit
                        </a>

                        <form action="{{ route('admin.vendors.destroy', $vendor) }}" method="POST"
                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus vendor ini?')">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn-delete-vendor">
                                🗑 Hapus
                            </button>
                        </form>

                    </div>

                </div>

                </div>

            @empty

                <div class="vendor-empty">
                    <div class="empty-icon">🏪</div>
                    @if ($search !== '' || $categoryId)
                        <h4>Vendor Tidak Ditemukan</h4>
                        <p>Coba ubah kata kunci atau kategori filter.</p>
                    @else
                        <h4>Belum Ada Vendor</h4>
                        <p>Tambahkan vendor pertama untuk mulai mengelola vendor rekanan wedding.</p>

                        <a href="{{ route('admin.vendors.create') }}" class="btn-add-vendor">
                            + Tambah Vendor
                        </a>
                    @endif
                </div>

            @endforelse

        </div>

    </div>

@endsection
