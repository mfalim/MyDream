@extends('vendor.layouts.app')

@section('title', 'Edit Layanan')

@section('content')

<div class="vendor-service-page">

    {{-- HEADER --}}
    <div class="vendor-service-header">

        <div class="vendor-service-title-wrap">

            <span class="vendor-service-eyebrow">
                MANAJEMEN KATALOG
                <span class="vendor-breadcrumb-arrow">›</span>
                EDIT LAYANAN
            </span>

            <h1 class="vendor-service-title">
                Edit Layanan & Detail Portofolio
            </h1>

            <p class="vendor-service-description">
                Perbarui informasi paket layanan, spesifikasi teknis,
                fasilitas, dan detail portofolio vendor.
            </p>

        </div>

        <div class="vendor-service-status">

            <span class="status-dot"></span>

            <span>
                Status:
            </span>

            <strong>
                {{ $layanan->status ?? 'Draft Vendor' }}
            </strong>

        </div>

    </div>


    {{-- FORM EDIT --}}
    <form
        action="{{ route('vendor.layanan.update', $layanan->id) }}"
        method="POST"
    >

        @csrf
        @method('PUT')


        {{-- INFORMASI DASAR --}}
        <section class="vendor-form-card">

            <div class="vendor-section-heading">

                <span class="vendor-section-number">
                    1
                </span>

                <div>
                    <h2>
                        Informasi Dasar Layanan & Paket
                    </h2>
                </div>

                <span class="vendor-required-info">
                    WAJIB DIISI
                </span>

            </div>


            <div class="vendor-form-group">

                <label class="vendor-form-label">
                    NAMA PAKET / LAYANAN LENGKAP
                    <span class="vendor-required">*</span>
                </label>

                <input
                    type="text"
                    name="nama_layanan"
                    class="vendor-form-input"
                    value="{{ old('nama_layanan', $layanan->nama_layanan) }}"
                    required
                >

                @error('nama_layanan')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                @enderror

            </div>


            <div class="vendor-form-row">

                <div class="vendor-form-group">

                    <label class="vendor-form-label">
                        KATEGORI LAYANAN
                        <span class="vendor-required">*</span>
                    </label>

                    <select
                        name="kategori"
                        class="vendor-form-select"
                        required
                    >

                        @foreach ([
                            'Dekorasi Pelaminan & Stage',
                            'Floral Decoration',
                            'Wedding Decoration',
                            'Garden Wedding'
                        ] as $kategori)

                            <option
                                value="{{ $kategori }}"
                                @selected(old('kategori', $layanan->kategori) === $kategori)
                            >
                                {{ $kategori }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="vendor-form-group">

                    <label class="vendor-form-label">
                        HARGA RESMI PENAWARAN (IDR)
                        <span class="vendor-required">*</span>
                    </label>

                    <div class="vendor-price-input">

                        <span>
                            Rp
                        </span>

                        <input
                            type="number"
                            name="harga"
                            value="{{ old('harga', $layanan->harga) }}"
                            min="0"
                            step="0.01"
                            required
                        >

                    </div>

                    @error('harga')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                    @enderror

                </div>

            </div>

        </section>


        {{-- SPESIFIKASI --}}
        <section class="vendor-form-card">

            <div class="vendor-section-heading">

                <span class="vendor-section-number">
                    2
                </span>

                <div>

                    <h2>
                        Spesifikasi Teknis & Kebutuhan Lapangan
                    </h2>

                    <p>
                        Data krusial untuk sinkronisasi layout panggung
                        dan loading dock hotel.
                    </p>

                </div>

            </div>


            <div class="vendor-spec-grid">


                {{-- DIMENSI --}}
                <div class="vendor-spec-card">

                    <div class="vendor-spec-title">
                        <span>▣</span>
                        Dimensi Panggung Minimal (P × L × T)
                    </div>

                    <p class="vendor-spec-help">
                        Rekomendasi area clear ballroom
                    </p>

                    <input
                        type="text"
                        name="dimensi_panggung"
                        class="vendor-form-input"
                        value="{{ old('dimensi_panggung', $layanan->dimensi_panggung) }}"
                    >

                </div>


                {{-- DAYA --}}
                <div class="vendor-spec-card">

                    <div class="vendor-spec-title">
                        <span>ϟ</span>
                        Daya Listrik & Titik Rigging
                    </div>

                    <p class="vendor-spec-help">
                        Kapasitas suplai daya & rigging hook
                    </p>

                    <input
                        type="text"
                        name="daya_listrik_rigging"
                        class="vendor-form-input"
                        value="{{ old('daya_listrik_rigging', $layanan->daya_listrik_rigging) }}"
                    >

                </div>


                {{-- KRU --}}
                <div class="vendor-spec-card">

                    <div class="vendor-spec-title">
                        <span>♙</span>
                        Alokasi Kru & Tenaga Pasang
                    </div>

                    <p class="vendor-spec-help">
                        Termasuk lead florist, carpenter & lighting
                    </p>

                    <input
                        type="text"
                        name="alokasi_kru"
                        class="vendor-form-input"
                        value="{{ old('alokasi_kru', $layanan->alokasi_kru) }}"
                    >

                </div>


                {{-- DURASI --}}
                <div class="vendor-spec-card">

                    <div class="vendor-spec-title">
                        <span>◷</span>
                        Durasi Loading & Teardown
                    </div>

                    <p class="vendor-spec-help">
                        Estimasi persiapan venue s.d. steril
                    </p>

                    <input
                        type="text"
                        name="durasi_loading_teardown"
                        class="vendor-form-input"
                        value="{{ old('durasi_loading_teardown', $layanan->durasi_loading_teardown) }}"
                    >

                </div>

            </div>

        </section>


        {{-- DESKRIPSI & FASILITAS --}}
        <section class="vendor-form-card">

            <div class="vendor-section-heading">

                <span class="vendor-section-number">
                    3
                </span>

                <div>

                    <h2>
                        Deskripsi Konsep & Ruang Lingkup Fasilitas
                    </h2>

                </div>

            </div>


            {{-- DESKRIPSI --}}
            <div class="vendor-form-group">

                <label class="vendor-form-label">
                    DETAIL KOMPOSISI MATERIAL & DESKRIPSI ARTISTIK
                </label>

                <textarea
                    name="deskripsi"
                    class="vendor-form-textarea"
                >{{ old('deskripsi', $layanan->deskripsi) }}</textarea>

            </div>


            {{-- FASILITAS --}}
            <div class="vendor-form-group">

                <label class="vendor-form-label">
                    FASILITAS TAMBAHAN YANG TERMASUK DALAM PAKET
                </label>


                <div class="vendor-facility-grid">


                    {{-- GRAND ENTRANCE --}}
                    <label class="vendor-facility-item">

                        {{-- Nilai ini dikirim ketika checkbox tidak dicentang --}}
                        <input
                            type="hidden"
                            name="grand_entrance_gate"
                            value=""
                        >

                        <input
                            type="checkbox"
                            name="grand_entrance_gate"
                            value="Grand Entrance Gate 4×3 Meter"
                            @checked(old('grand_entrance_gate', $layanan->grand_entrance_gate))
                        >

                        <span>

                            <strong>
                                Grand Entrance Gate 4×3 Meter
                            </strong>

                            <small>
                                Lengkungan floral dengan sambut mempelai
                            </small>

                        </span>

                    </label>


                    {{-- MEJA AKAD --}}
                    <label class="vendor-facility-item">

                        <input
                            type="hidden"
                            name="meja_akad"
                            value=""
                        >

                        <input
                            type="checkbox"
                            name="meja_akad"
                            value="Meja Akad / Pemberkatan Khusus"
                            @checked(old('meja_akad', $layanan->meja_akad))
                        >

                        <span>

                            <strong>
                                Meja Akad / Pemberkatan Khusus
                            </strong>

                            <small>
                                6 kursi crossback Tiffany + floral runner
                            </small>

                        </span>

                    </label>


                    {{-- AISLE CARPET --}}
                    <label class="vendor-facility-item">

                        <input
                            type="hidden"
                            name="aisle_carpet"
                            value=""
                        >

                        <input
                            type="checkbox"
                            name="aisle_carpet"
                            value="Aisle Carpet & 10 Standing Flowers"
                            @checked(old('aisle_carpet', $layanan->aisle_carpet))
                        >

                        <span>

                            <strong>
                                Aisle Carpet & 10 Standing Flowers
                            </strong>

                            <small>
                                Karpet jalan 25m dengan pedestal bunga
                            </small>

                        </span>

                    </label>


                    {{-- PHOTO BOOTH --}}
                    <label class="vendor-facility-item">

                        <input
                            type="hidden"
                            name="photo_booth"
                            value=""
                        >

                        <input
                            type="checkbox"
                            name="photo_booth"
                            value="Photo Booth & Galeri Foto Interaktif"
                            @checked(old('photo_booth', $layanan->photo_booth))
                        >

                        <span>

                            <strong>
                                Photo Booth & Galeri Foto Interaktif
                            </strong>

                            <small>
                                Dimensi 4×3 meter lengkap dengan spotlight
                            </small>

                        </span>

                    </label>


                </div>

            </div>

        </section>


        {{-- STATUS --}}
        <section class="vendor-form-card">

            <div class="vendor-form-group">

                <label class="vendor-form-label">
                    STATUS LAYANAN
                </label>

                <select
                    name="status"
                    class="vendor-form-select"
                >

                    <option
                        value="Draft Vendor"
                        @selected(old('status', $layanan->status) === 'Draft Vendor')
                    >
                        Draft Vendor
                    </option>

                    <option
                        value="Aktif"
                        @selected(old('status', $layanan->status) === 'Aktif')
                    >
                        Aktif
                    </option>

                    <option
                        value="Selesai"
                        @selected(old('status', $layanan->status) === 'Selesai')
                    >
                        Selesai
                    </option>

                    <option
                        value="Nonaktif"
                        @selected(old('status', $layanan->status) === 'Nonaktif')
                    >
                        Nonaktif
                    </option>

                </select>

                @error('status')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                @enderror

            </div>

        </section>


        {{-- ACTION BAR --}}
        <div class="vendor-service-actions">

            <div class="vendor-action-information">

                <span class="vendor-action-icon">
                    ✎
                </span>

                <div>

                    <strong>
                        Perbarui Data Layanan
                    </strong>

                    <small>
                        Perubahan akan langsung disimpan ke database.
                    </small>

                </div>

            </div>


            <a
                href="{{ route('vendor.katalog') }}"
                class="vendor-btn vendor-btn-secondary"
            >
                Batal
            </a>


            <button
                type="submit"
                class="vendor-btn vendor-btn-primary"
            >
                ✓ &nbsp; Simpan Perubahan
            </button>

        </div>

    </form>

</div>

@endsection