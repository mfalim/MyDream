@extends('layouts.admin')

@section('title', 'Paket Wedding')
@section('page-title', 'Paket Wedding')

@push('styles')
    @vite('resources/css/admin/vendors/index.css')
@endpush

@section('content')
    <div class="vendor-page">
        <div class="vendor-header">
            <div>
                <h2>Direktori Paket Wedding</h2>
                <p>Kelola kumpulan vendor yang ditawarkan dalam setiap paket wedding.</p>
            </div>

            <a href="{{ route('admin.packages.create') }}" class="btn-add-vendor">
                + Tambah Paket
            </a>
        </div>

        <div class="vendor-result-count">
            Menampilkan {{ $packages->count() }} paket
        </div>

        <div class="vendor-grid">
            @forelse ($packages as $package)
                <div class="vendor-card">
                    <div class="vendor-image">
                        @if ($package->photo)
                            <img src="{{ asset('storage/' . $package->photo) }}" alt="{{ $package->name }}">
                        @else
                            <div class="vendor-no-image">
                                <span>📦</span>
                                <small>Tidak ada foto</small>
                            </div>
                        @endif

                        <span class="vendor-category">
                            {{ $package->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>

                    <div class="vendor-card-body">
                        <h4>{{ $package->name }}</h4>

                        <div class="vendor-price">
                            Rp {{ number_format($package->price, 0, ',', '.') }}
                        </div>

                        <div class="vendor-location">
                            Tersedia sampai: {{ $package->availability_date?->format('d-m-Y') ?: '-' }}
                        </div>

                        <div class="vendor-phone">
                            {{ $package->duration_label }} ·
                            {{ $package->guest_capacity ? number_format($package->guest_capacity, 0, ',', '.') . ' tamu' : '-' }}
                        </div>

                        <div class="vendor-phone">
                            {{ $package->vendors->count() }} vendor
                        </div>

                        <div class="package-vendors">
                            @forelse ($package->vendors as $vendor)
                                <span class="package-vendor-tag">{{ $vendor->name }}</span>
                            @empty
                                <span class="text-muted">Belum ada vendor</span>
                            @endforelse
                        </div>

                        <a href="{{ route('admin.packages.show', $package) }}" class="btn-detail-vendor">
                            Lihat Detail
                        </a>

                        <div class="vendor-actions">
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
                </div>
            @empty
                <div class="vendor-empty">
                    <div class="empty-icon">📦</div>
                    <h4>Belum Ada Paket</h4>
                    <p>Tambahkan paket pertama dari vendor yang tersedia.</p>
                    <a href="{{ route('admin.packages.create') }}" class="btn-add-vendor">
                        + Tambah Paket
                    </a>
                </div>
            @endforelse
        </div>
    </div>
@endsection
