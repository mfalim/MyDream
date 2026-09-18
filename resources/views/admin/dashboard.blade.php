@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')

    <div class="mb-4">
        <h3 class="mb-1">Dashboard</h3>
        <p class="text-muted">
            Selamat datang di halaman administrasi Wedding Organizer.
        </p>
    </div>

    <div class="row">

        <div class="col-md-3 mb-4">

            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    <p class="text-muted mb-2">
                        Paket Wedding
                    </p>

                    <h3 class="mb-0">
                        0
                    </h3>

                </div>
            </div>

        </div>


        <div class="col-md-3 mb-4">

            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    <p class="text-muted mb-2">
                        Vendor
                    </p>

                    <h3 class="mb-0">
                        0
                    </h3>

                </div>
            </div>

        </div>


        <div class="col-md-3 mb-4">

            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    <p class="text-muted mb-2">
                        Pesanan
                    </p>

                    <h3 class="mb-0">
                        0
                    </h3>

                </div>
            </div>

        </div>


        <div class="col-md-3 mb-4">

            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    <p class="text-muted mb-2">
                        Jadwal Vendor
                    </p>

                    <h3 class="mb-0">
                        0
                    </h3>

                </div>
            </div>

        </div>

    </div>

@endsection
