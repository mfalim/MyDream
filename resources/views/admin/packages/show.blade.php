@extends('layouts.admin')

@section('title', 'Detail Paket')
@section('page-title', 'Detail Paket')

@push('styles')
    @vite('resources/css/admin/vendors/show.css')
@endpush

@section('content')
    <div class="vendor-detail-page">

        ```
        {{-- Breadcrumb --}}
        <div class="vendor-breadcrumb">
            <a href="{{ route('admin.packages.index') }}">
                Kelola Paket
            </a>

            <span>/</span>

            <span>{{ $package->name }}</span>
        </div>

        {{-- Header --}}
        <div class="vendor-detail-header">
            <div>
                <span class="vendor-detail-category">
                    {{ $package->is_active ? 'Aktif' : 'Tidak Aktif' }}
                    ·
                    {{ $package->vendors->count() }} Vendor
                </span>

                <h2>{{ $package->name }}</h2>

                <p>
                    Paket wedding yang tersusun dari vendor rekanan terpilih.
                </p>
            </div>

            <div class="vendor-detail-header-actions">

                <a href="{{ route('admin.packages.edit', $package) }}" class="btn-edit-vendor">
                    ✏ Edit
                </a>

                <form action="{{ route('admin.packages.destroy', $package) }}" method="POST"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus paket ini?')">

                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn-delete-vendor">
                        🗑 Hapus
                    </button>
                </form>

            </div>
        </div>

        {{-- Main Content --}}
        <div class="vendor-detail-grid">

            {{-- =========================
            BAGIAN KIRI
            ========================== --}}
            <div class="vendor-gallery">

                {{-- Foto Paket --}}
                <div class="vendor-main-photo">

                    @if ($package->photo)

                        <img src="{{ asset('storage/' . $package->photo) }}" alt="{{ $package->name }}">

                    @else

                        <div class="vendor-no-main-photo">
                            <span>📦</span>
                            <p>Belum ada foto paket</p>
                        </div>

                    @endif

                </div>


                {{-- Informasi Singkat Paket --}}
                <div class="package-summary-box">

                    {{-- Periode Acara --}}
                    <div class="package-summary-item">

                        <small>
                            Periode Acara
                        </small>

                        <strong>
                            {{ $package->availability_date?->format('d-m-Y') ?: '-' }}
                        </strong>

                    </div>


                    {{-- Durasi --}}
                    <div class="package-summary-item">

                        <small>
                            Durasi
                        </small>

                        <strong>
                            {{ $package->duration_label }}
                        </strong>

                    </div>


                    {{-- Kapasitas --}}
                    <div class="package-summary-item">

                        <small>
                            Kapasitas
                        </small>

                        <strong>
                            {{ $package->guest_capacity
        ? number_format($package->guest_capacity, 0, ',', '.') . ' tamu'
        : '-' }}
                        </strong>

                    </div>

                </div>

            </div>


            {{-- =========================
            BAGIAN KANAN
            ========================== --}}
            <div class="vendor-info-column">

                {{-- Informasi Paket --}}
                <div class="vendor-info-card">

                    <div class="vendor-info-card-header">
                        <span>
                            INFORMASI PAKET
                        </span>
                    </div>


                    {{-- Total Harga --}}
                    <div class="vendor-info-item">

                        <small>
                            Total Harga
                        </small>

                        <strong>
                            Rp {{ number_format($package->price, 0, ',', '.') }}
                        </strong>

                    </div>


                    {{-- Status --}}
                    <div class="vendor-info-item">

                        <small>
                            Status Paket
                        </small>

                        <strong>
                            {{ $package->is_active ? 'Aktif / tersedia' : 'Tidak aktif' }}
                        </strong>

                    </div>


                    {{-- Jumlah Vendor --}}
                    <div class="vendor-info-item">

                        <small>
                            Jumlah Vendor
                        </small>

                        <strong>
                            {{ $package->vendors->count() }} Vendor
                        </strong>

                    </div>

                </div>


                {{-- Vendor Dalam Paket --}}
                <div class="vendor-info-card">

                    <div class="vendor-info-card-header">
                        <span>
                            VENDOR DALAM PAKET
                        </span>
                    </div>


                    @forelse ($package->vendors as $vendor)

                        <div class="vendor-info-item">

                            <small>
                                {{ $vendor->category?->name ?? 'Vendor' }}
                            </small>

                            <strong>
                                {{ $vendor->name }}
                            </strong>

                            <span>
                                Rp {{ number_format($vendor->price, 0, ',', '.') }}
                            </span>

                        </div>

                    @empty

                        <span class="text-muted">
                            Belum ada vendor dalam paket.
                        </span>

                    @endforelse

                </div>

            </div>

        </div>

    </div>
    ```

@endsection
