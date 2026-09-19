@extends('layouts.admin')

@section('title', 'Detail Vendor')
@section('page-title', 'Detail Vendor')

@section('content')
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-1">Detail Vendor</h3>
            <p class="text-muted mb-0">Informasi lengkap vendor rekanan.</p>
        </div>

        <a href="{{ route('admin.vendors.index') }}" class="btn btn-secondary">
            Kembali
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    @if ($vendor->photo)
                        <img src="{{ asset('storage/' . $vendor->photo) }}" alt="{{ $vendor->name }}"
                            class="img-fluid rounded w-100" style="max-height: 420px; object-fit: cover;">
                    @else
                        <div class="bg-light rounded d-flex flex-column justify-content-center align-items-center"
                            style="height: 320px;">
                            <span class="fs-1">📷</span>
                            <span class="text-muted">Tidak ada foto</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                        <div>
                            <h4 class="mb-2">{{ $vendor->name }}</h4>
                            <span class="badge text-bg-success">
                                {{ $vendor->category?->name ?? '-' }}
                            </span>
                        </div>
                    </div>

                    <div class="border-top pt-3">
                        <div class="mb-3">
                            <small class="text-muted d-block">Alamat</small>
                            <span>{{ $vendor->address }}</span>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">Nomor Telepon</small>
                            <span>{{ $vendor->phone }}</span>
                        </div>

                        @if ($vendor->description)
                            <div>
                                <small class="text-muted d-block">Deskripsi</small>
                                <span>{{ $vendor->description }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
