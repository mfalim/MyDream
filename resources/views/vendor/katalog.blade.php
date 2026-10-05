@extends('vendor.layouts.app')

@section('title', 'Katalog & Tambah Layanan')

@section('content')

<div class="vendor-service-page">

    {{-- HEADER --}}
    <div class="vendor-service-header">

        <div class="vendor-service-title-wrap">

            <span class="vendor-service-eyebrow">
                MANAJEMEN KATALOG
            </span>

            <h1 class="vendor-service-title">
                Katalog & Tambah Layanan
            </h1>

            <p class="vendor-service-description">
                Kelola paket layanan dan portofolio vendor.
            </p>

        </div>

        <a
            href="{{ route('vendor.tambahlayanan') }}"
            class="vendor-btn vendor-btn-primary"
        >
            + Tambah Layanan
        </a>

    </div>


    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="vendor-alert vendor-alert-success">
            ✓ {{ session('success') }}
        </div>
    @endif


    {{-- ERROR VALIDASI --}}
    @if($errors->any())
        <div class="vendor-alert vendor-alert-danger">

            <strong>
                Data belum dapat disimpan.
            </strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>
    @endif


    {{-- DAFTAR LAYANAN --}}
    <section class="content-card">

        <span class="eyebrow">
            DAFTAR LAYANAN
        </span>

        <h2>
            Daftar Layanan
        </h2>

        <p>
            Seluruh paket layanan yang tersimpan di database vendor.
        </p>


        @forelse($layanans as $layanan)

            <article class="vendor-item">

                {{-- HEADER ITEM --}}
                <div class="vendor-item-header">

                    <div>

                        <span class="vendor-item-number">
                            LAYANAN #{{ $layanan->id }}
                        </span>

                        <h3>
                            {{ $layanan->nama_layanan }}
                        </h3>

                        <small>
                            {{ $layanan->kategori }}
                        </small>

                    </div>


                    {{-- STATUS --}}
                    <span class="vendor-status">

                        {{ $layanan->status }}

                    </span>

                </div>


                {{-- HARGA --}}
                <div class="vendor-item-price">

                    <span>
                        HARGA RESMI PENAWARAN
                    </span>

                    <strong>
                        Rp {{ number_format($layanan->harga, 0, ',', '.') }}
                    </strong>

                </div>


                {{-- INFORMASI TEKNIS --}}
                <div class="vendor-detail-grid">

                    <div class="vendor-detail-box">

                        <span>
                            DIMENSI PANGGUNG
                        </span>

                        <strong>
                            {{ $layanan->dimensi_panggung ?: '-' }}
                        </strong>

                    </div>


                    <div class="vendor-detail-box">

                        <span>
                            DAYA LISTRIK & RIGGING
                        </span>

                        <strong>
                            {{ $layanan->daya_listrik_rigging ?: '-' }}
                        </strong>

                    </div>


                    <div class="vendor-detail-box">

                        <span>
                            ALOKASI KRU
                        </span>

                        <strong>
                            {{ $layanan->alokasi_kru ?: '-' }}
                        </strong>

                    </div>


                    <div class="vendor-detail-box">

                        <span>
                            DURASI LOADING & TEARDOWN
                        </span>

                        <strong>
                            {{ $layanan->durasi_loading_teardown ?: '-' }}
                        </strong>

                    </div>

                </div>


                {{-- DESKRIPSI --}}
                @if($layanan->deskripsi)

                    <div class="vendor-description-box">

                        <span>
                            DESKRIPSI
                        </span>

                        <p>
                            {{ $layanan->deskripsi }}
                        </p>

                    </div>

                @endif


                {{-- FASILITAS --}}
                <div class="vendor-facility-section">

                    <span class="vendor-detail-label">
                        FASILITAS TERMASUK
                    </span>


                    <div class="vendor-facility-list">

                        @if($layanan->grand_entrance_gate)
                            <span class="vendor-facility-badge">
                                ✓ {{ $layanan->grand_entrance_gate }}
                            </span>
                        @endif


                        @if($layanan->meja_akad)
                            <span class="vendor-facility-badge">
                                ✓ {{ $layanan->meja_akad }}
                            </span>
                        @endif


                        @if($layanan->aisle_carpet)
                            <span class="vendor-facility-badge">
                                ✓ {{ $layanan->aisle_carpet }}
                            </span>
                        @endif


                        @if($layanan->photo_booth)
                            <span class="vendor-facility-badge">
                                ✓ {{ $layanan->photo_booth }}
                            </span>
                        @endif


                        @if(
                            !$layanan->grand_entrance_gate &&
                            !$layanan->meja_akad &&
                            !$layanan->aisle_carpet &&
                            !$layanan->photo_booth
                        )

                            <span class="vendor-no-data">
                                Tidak ada fasilitas tambahan.
                            </span>

                        @endif

                    </div>

                </div>


                {{-- PORTOFOLIO --}}
                @if(
                    $layanan->foto_utama ||
                    $layanan->gallery_1 ||
                    $layanan->gallery_2 ||
                    $layanan->gallery_3 ||
                    $layanan->gallery_4
                )

                    <div class="vendor-portfolio-section">

                        <span class="vendor-detail-label">
                            PORTOFOLIO
                        </span>

                        <div class="vendor-gallery">

                            @foreach([
                                $layanan->foto_utama,
                                $layanan->gallery_1,
                                $layanan->gallery_2,
                                $layanan->gallery_3,
                                $layanan->gallery_4
                            ] as $foto)

                                @if($foto)

                                    <img
                                        src="{{ $foto }}"
                                        alt="Portofolio {{ $layanan->nama_layanan }}"
                                        class="vendor-gallery-image"
                                    >

                                @endif

                            @endforeach

                        </div>

                    </div>

                @endif


                {{-- ACTION --}}
                <div class="vendor-item-actions">

                    {{-- PREVIEW --}}
                    <a
                        href="{{ route('vendor.layanan.edit', $layanan->id) }}"
                        class="vendor-btn vendor-btn-secondary"
                    >
                        Preview / Edit
                    </a>


                    {{-- EDIT --}}
                    <a
                        href="{{ route('vendor.layanan.edit', $layanan->id) }}"
                        class="vendor-btn vendor-btn-primary"
                    >
                        Edit
                    </a>


                    {{-- HAPUS --}}
                    <form
                        action="{{ route('vendor.layanan.destroy', $layanan->id) }}"
                        method="POST"
                        onsubmit="return confirm('Yakin ingin menghapus layanan ini?')"
                    >

                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="vendor-btn vendor-btn-danger"
                        >
                            Hapus
                        </button>

                    </form>

                </div>

            </article>

        @empty

            <div class="vendor-empty-state">

                <div class="vendor-empty-icon">
                    +
                </div>

                <h3>
                    Belum Ada Layanan
                </h3>

                <p>
                    Belum ada layanan yang tersimpan di database.
                </p>

                <a
                    href="{{ route('vendor.tambahlayanan') }}"
                    class="vendor-btn vendor-btn-primary"
                >
                    + Tambah Layanan
                </a>

            </div>

        @endforelse

    </section>

</div>

@endsection