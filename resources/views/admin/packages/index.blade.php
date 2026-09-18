@extends('layouts.admin')

@section('title', 'Paket Wedding')
@section('page-title', 'Paket Wedding')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">
                Paket Wedding
            </h3>

            <p class="text-muted mb-0">
                Kelola paket dan vendor yang tersedia di dalamnya.
            </p>

        </div>

        <a href="{{ route('admin.packages.create') }}" class="btn btn-success">
            + Tambah Paket
        </a>

    </div>


    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>
                            <th>No</th>
                            <th>Nama Paket</th>
                            <th>Harga</th>
                            <th>Vendor</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($packages as $package)

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    {{ $package->name }}
                                </td>

                                <td>
                                    Rp {{ number_format($package->price, 0, ',', '.') }}
                                </td>

                                <td>

                                    @forelse($package->vendors as $vendor)

                                        <span class="badge bg-secondary">
                                            {{ $vendor->name }}
                                        </span>

                                    @empty

                                        <span class="text-muted">
                                            Belum ada vendor
                                        </span>

                                    @endforelse

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="4" class="text-center text-muted py-4">

                                    Belum ada paket.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@endsection
