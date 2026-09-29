@extends('layouts.admin')

@section('title', 'Edit Paket')
@section('page-title', 'Edit Paket')

@section('content')
    <div class="mb-4">
        <h3 class="mb-1">Edit Paket Wedding</h3>
        <p class="text-muted">Perbarui informasi dan vendor yang termasuk dalam paket.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.packages.update', $package) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-4">Informasi Paket</h5>

                <div class="mb-3">
                    <label class="form-label">Nama Paket</label>
                    <input type="text" name="name" class="form-control"
                        value="{{ old('name', $package->name) }}"
                        pattern="^[A-Za-z0-9À-ž][A-Za-z0-9À-ž &.'-]*$"
                        title="Gunakan huruf, angka, spasi, dan tanda baca umum saja." required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal Ketersediaan Paket</label>
                    <input type="date" name="availability_date" class="form-control"
                        value="{{ old('availability_date', optional($package->availability_date)->format('Y-m-d')) }}" required>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Kapasitas Tamu</label>
                        <div class="input-group">
                            <input type="number" name="guest_capacity" class="form-control"
                                value="{{ old('guest_capacity', $package->guest_capacity) }}" min="1" required>
                            <span class="input-group-text">tamu</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-3">Vendor dalam Paket</h5>

                @forelse ($vendors as $vendor)
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="vendors[]"
                            value="{{ $vendor->id }}" id="vendor{{ $vendor->id }}"
                            @checked(in_array($vendor->id, old('vendors', $package->vendors->pluck('id')->all())))>
                        <label class="form-check-label" for="vendor{{ $vendor->id }}">
                            {{ $vendor->name }}
                            <span class="text-muted">
                                ({{ $vendor->category?->name ?? '-' }} · Rp {{ number_format($vendor->price, 0, ',', '.') }})
                            </span>
                        </label>
                    </div>
                @empty
                    <div class="alert alert-warning mb-0">Belum ada vendor. Tambahkan vendor terlebih dahulu.</div>
                @endforelse
            </div>
        </div>

        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        <a href="{{ route('admin.packages.show', $package) }}" class="btn btn-secondary">Kembali</a>
    </form>
@endsection
