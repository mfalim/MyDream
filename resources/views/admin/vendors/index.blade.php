@extends('layouts.admin')

@section('title', 'Vendor')
@section('page-title', 'Vendor')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h3 class="mb-1">
                Vendor
            </h3>

            <p class="text-muted mb-0">
                Kelola vendor Wedding Organizer.
            </p>
        </div>

        <a href="{{ route('admin.vendors.create') }}" class="btn btn-success">
            + Tambah Vendor
        </a>

    </div>


    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Vendor</th>
                            <th>Kategori</th>
                            <th>Telepon</th>
                            <th>Alamat</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($vendors as $vendor)

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    {{ $vendor->name }}
                                </td>

                                <td>
                                    {{ $vendor->category }}
                                </td>

                                <td>
                                    {{ $vendor->phone }}
                                </td>

                                <td>
                                    {{ $vendor->address }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">

                                    Belum ada vendor.

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@endsection
