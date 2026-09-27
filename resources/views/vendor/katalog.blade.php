@extends('vendor.layouts.app')

@section('title', 'Katalog & Tambah Layanan')

@section('content')

<div class="page-header">
    <div>
        <span class="eyebrow">MANAJEMEN KATALOG</span>
        <h1>Katalog & Tambah Layanan</h1>
        <p>
            Kelola paket layanan dan portofolio vendor.
        </p>
    </div>

    <div class="header-actions">
        <button class="btn-primary">
            + Tambah Layanan
        </button>
    </div>
</div>

<div class="panel">
    <h2>Daftar Layanan</h2>
    <p class="panel-description">
        Daftar paket layanan vendor akan ditampilkan di sini.
    </p>
</div>

@endsection