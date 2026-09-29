```blade
@extends('vendor.layouts.app')

@section('title', 'Katalog & Tambah Layanan')

@section('content')

<div class="catalog-header">

    <div>
        <span class="eyebrow">
            MANAJEMEN KATALOG
        </span>

        <h1>
            Katalog & Tambah Layanan
        </h1>

        <p>
            Kelola paket layanan dan portofolio vendor.
        </p>
    </div>

    <a href="{{ route('vendor.tambahlayanan') }}" class="btn-tambah-layanan">
        <span>+</span>
        Tambah Layanan
    </a>

</div>


<section class="content-card">

    <span class="eyebrow">
        DAFTAR LAYANAN
    </span>

    <h2>
        Daftar Layanan
    </h2>

    <p>
        Daftar paket layanan vendor akan ditampilkan di sini.
    </p>


    <div class="vendor-list">

        <div class="vendor-item">

            <strong>
                Paket Dekorasi Royal Emerald
            </strong>

            <small>
                Dekorasi pelaminan dan area acara
            </small>

            <span>
                Aktif • Rp 25.000.000
            </span>

        </div>


        <div class="vendor-item">

            <strong>
                Paket Floral Premium
            </strong>

            <small>
                Bunga segar untuk pelaminan dan meja tamu
            </small>

            <span>
                Aktif • Rp 12.500.000
            </span>

        </div>


        <div class="vendor-item">

            <strong>
                Paket Garden Wedding
            </strong>

            <small>
                Dekorasi konsep outdoor garden
            </small>

            <span>
                Draft • Rp 18.000.000
            </span>

        </div>

    </div>

</section>

@endsection
```
