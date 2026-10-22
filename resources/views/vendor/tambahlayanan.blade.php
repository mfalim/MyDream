@extends('vendor.layouts.app')

@section('title', 'Tambah Layanan & Detail Portofolio')

@section('content')

<div class="vendor-service-page">

{{-- SUCCESS MESSAGE --}}
@if (session('success'))
    <div class="vendor-alert vendor-alert-success">
        ✓ {{ session('success') }}
    </div>
@endif

{{-- VALIDATION ERROR --}}
@if ($errors->any())
    <div class="vendor-alert vendor-alert-error">
        <strong>Data belum dapat disimpan.</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- =====================================================
     HEADER
====================================================== --}}

<div class="vendor-service-header">

    <div class="vendor-service-title-wrap">

        <span class="vendor-service-eyebrow">
            MANAJEMEN KATALOG
            <span class="vendor-breadcrumb-arrow">›</span>
            TAMBAH LAYANAN & PORTOFOLIO BARU
        </span>

        <h1 class="vendor-service-title">
            Tambah Layanan & Detail Portofolio
        </h1>

        <p class="vendor-service-description">
            Lengkapi detail paket dekorasi panggung, spesifikasi teknis instalasi fisik,
            portofolio visual resolusi tinggi, serta estimasi rincian biaya untuk proses
            kurasi kurator WO PROJECT.
        </p>

    </div>

    <div class="vendor-service-status">
        <span class="status-dot"></span>

        <span>Status Form:</span>

        <strong>Draft Vendor</strong>
    </div>

</div>


{{-- =====================================================
     FORM DATABASE
====================================================== --}}

<form
    action="{{ route('vendor.layanan.store') }}"
    method="POST"
    enctype="multipart/form-data"
>

    @csrf


    {{-- =================================================
         MAIN LAYOUT
    ================================================== --}}

    <div class="vendor-service-layout">


        {{-- =================================================
             LEFT CONTENT
        ================================================== --}}

        <main class="vendor-service-main">


            {{-- =================================================
                 SECTION 1
            ================================================== --}}

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
                        value="{{ old('nama_layanan', 'Paket Pelaminan Botanical Opulence Luxury 18m') }}"
                        required
                    >

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
                            <option value="Dekorasi Pelaminan & Stage">
                                Dekorasi Pelaminan & Stage
                            </option>

                            <option value="Floral Decoration">
                                Floral Decoration
                            </option>

                            <option value="Wedding Decoration">
                                Wedding Decoration
                            </option>

                            <option value="Garden Wedding">
                                Garden Wedding
                            </option>
                        </select>

                    </div>


                    <div class="vendor-form-group">

                        <label class="vendor-form-label">
                            HARGA RESMI PENAWARAN (IDR)
                            <span class="vendor-required">*</span>
                        </label>

                        <div class="vendor-price-input">

                            <span>Rp</span>

                            <input
                                type="number"
                                name="harga"
                                value="{{ old('harga', 125000000) }}"
                                min="0"
                                required
                            >

                        </div>

                    </div>

                </div>

            </section>



            {{-- =================================================
                 SECTION 2
            ================================================== --}}

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
                            Data krusial untuk sinkronisasi layout panggung dan loading dock hotel.
                        </p>

                    </div>

                </div>


                <div class="vendor-spec-grid">


                    {{-- SPEC 1 --}}
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
                            value="{{ old('dimensi_panggung', '18 m × 6 m × 4.8 m') }}"
                        >

                    </div>


                    {{-- SPEC 2 --}}
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
                            value="{{ old('daya_listrik_rigging', '16.000 Watt / 4 Titik Gantung Truss') }}"
                        >

                    </div>


                    {{-- SPEC 3 --}}
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
                            value="{{ old('alokasi_kru', '24 Personel Berseragam') }}"
                        >

                    </div>


                    {{-- SPEC 4 --}}
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
                            value="{{ old('durasi_loading_teardown', 'Loading: 10 Jam | Bongkaran: 3.5 Jam') }}"
                        >

                    </div>

                </div>

            </section>



            {{-- =================================================
                 SECTION 3
            ================================================== --}}

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


                <div class="vendor-form-group">

                    <label class="vendor-form-label">
                        DETAIL KOMPOSISI MATERIAL & DESKRIPSI ARTISTIK
                    </label>

                    <textarea
                        name="deskripsi"
                        class="vendor-form-textarea"
                    >{{ old('deskripsi', 'Struktur pelaminan megah dengan arsitektur lengkung neo-klasik dibuat 85% rangkaian bunga segar Grade-A impor (Ecuador Roses, Hydrangea Belanda, Phalaenopsis Putih, Delphinium, serta dedaunan Eucalyptus cinerea). Termasuk panel backdrop berudru premium warna ivory, chandelier kristal gantung bertingkat, serta instalasi floor carpet mirror anti-slip seluas panggung.') }}</textarea>

                </div>


                <div class="vendor-form-group">

                    <label class="vendor-form-label">
                        FASILITAS TAMBAHAN YANG TERMASUK DALAM PAKET
                    </label>


                    <div class="vendor-facility-grid">


                        <label class="vendor-facility-item">

                            <input
                                type="checkbox"
                                name="grand_entrance_gate"
                                value="1"
                                checked
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


                        <label class="vendor-facility-item">

                            <input
                                type="checkbox"
                                name="meja_akad"
                                value="1"
                                checked
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


                        <label class="vendor-facility-item">

                            <input
                                type="checkbox"
                                name="aisle_carpet"
                                value="1"
                                checked
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


                        <label class="vendor-facility-item">

                            <input
                                type="checkbox"
                                name="photo_booth"
                                value="1"
                                checked
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

        </main>



        {{-- =================================================
             RIGHT SIDEBAR
        ================================================== --}}

        <aside class="vendor-service-side">


            {{-- HERO PHOTO --}}
            <section class="vendor-side-card">

                <div class="vendor-side-heading-row">

                    <div>

                        <h2 class="vendor-side-title">
                            Foto Utama / Portofolio Hero
                        </h2>

                        <p class="vendor-side-description">
                            Ditampilkan di kartu pencarian utama calon pengantin
                        </p>

                    </div>

                    <span class="vendor-ratio">
                        Rasio<br>
                        <strong>16:9</strong>
                    </span>

                </div>


                <div class="vendor-hero-image">

                    <img id="heroPreview" alt="Portofolio Dekorasi" style="display:none">

                    <div class="vendor-image-placeholder" id="heroPlaceholder">
                        <span>Foto Portofolio</span>
                        <small>16:9</small>
                    </div>

                    <div class="vendor-image-overlay" id="heroInfo" style="display:none">
                        <strong id="heroName"></strong>
                        <span id="heroSize"></span>
                    </div>

                </div>

                <input
                    type="file"
                    name="foto_utama"
                    id="fotoUtama"
                    accept="image/*"
                    hidden
                >

                <button
                    type="button"
                    class="vendor-upload-button"
                    onclick="document.getElementById('fotoUtama').click()"
                >
                    ↗ &nbsp; Pilih Foto Utama
                </button>

            </section>



            {{-- GALLERY --}}
            <section class="vendor-side-card">

                <div class="vendor-gallery-heading">

                    <div>

                        <h2 class="vendor-side-title">
                            Galeri Detail Sudut (4 Slot)
                        </h2>

                        <p class="vendor-side-description">
                            Tampilkan detail pengerjaan kriya & material
                        </p>

                    </div>

                    <span class="gallery-count">
                        <span id="galleryCount">0</span> / 4<br>
                        <small>Terunggah</small>
                    </span>

                </div>


                <div class="vendor-gallery">

                    @foreach ([1 => 'Macro Floral', 2 => 'Entrance Gate', 3 => 'Meja Akad', 4 => 'Photo Booth'] as $i => $label)
                        <label class="vendor-gallery-item vendor-gallery-upload">
                            <img id="galleryPreview{{ $i }}" alt="{{ $label }}" style="display:none">
                            <em class="gallery-plus">+</em>
                            <span>{{ $label }}</span>
                            <input
                                type="file"
                                name="gallery_{{ $i }}"
                                accept="image/*"
                                hidden
                                data-preview="galleryPreview{{ $i }}"
                            >
                        </label>
                    @endforeach

                </div>

            </section>



            {{-- STANDARD CARD --}}
            <section class="vendor-standard-card">

                <div class="vendor-standard-icon">
                    ✓
                </div>

                <h3>
                    Standar Kurasi WO PROJECT
                </h3>

                <p>
                    Proteksi Kualitas Vendor Gold Tier
                </p>


                <ul class="vendor-standard-list">

                    <li>
                        Pembayaran termin DP dilindungi dalam sistem Rekening Bersama Escrow WO hingga setup H-1 disetujui lead planner.
                    </li>

                    <li>
                        Spesifikasi berban rigging wajib lolos inspeksi K3 teknis venue sebelum instalasi panggung dimulai.
                    </li>

                    <li>
                        Garansi kesegaran bunga impor minimal 18 jam sejak briefing technical meeting selesai.
                    </li>

                </ul>

            </section>

        </aside>

    </div>



    {{-- =====================================================
         BOTTOM ACTION BAR
    ====================================================== --}}

    <div class="vendor-service-actions">

        <div class="vendor-action-information">

            <span class="vendor-action-icon">
                ↑
            </span>

            <div>

                <strong>
                    Siap Mengajukan Item ke Tim Kurasi?
                </strong>

                <small>
                    Item akan ditinjau oleh Principal Curators
                    dalam kurun waktu ±24 jam kerja.
                </small>

            </div>

        </div>


        <button
            type="submit"
            name="action"
            value="draft"
            class="vendor-btn vendor-btn-secondary"
        >
            Simpan Draf
        </button>


        <button
            type="button"
            class="vendor-btn vendor-btn-secondary"
            onclick="window.print()"
        >
            ◉ &nbsp; Preview
        </button>


        <button
            type="submit"
            name="action"
            value="publish"
            class="vendor-btn vendor-btn-primary"
        >
            ⚙ &nbsp; Terbitkan & Ajukan ke Katalog
        </button>

    </div>

</form>

</div>

{{-- =====================================================
     SCRIPT PREVIEW FOTO
====================================================== --}}

<script>
    const heroInput = document.getElementById('fotoUtama');

    heroInput.addEventListener('change', () => {
        const file = heroInput.files[0];
        if (!file) return;

        const img = document.getElementById('heroPreview');
        img.src = URL.createObjectURL(file);
        img.style.display = 'block';

        document.getElementById('heroPlaceholder').style.display = 'none';
        document.getElementById('heroInfo').style.display = 'flex';
        document.getElementById('heroName').textContent = file.name;
        document.getElementById('heroSize').textContent =
            (file.size / 1048576).toFixed(1) + ' MB';
    });

    document.querySelectorAll('.vendor-gallery-upload input[type=file]').forEach(input => {
        input.addEventListener('change', () => {
            const file = input.files[0];
            if (!file) return;

            const img = document.getElementById(input.dataset.preview);
            img.src = URL.createObjectURL(file);
            img.style.display = 'block';

            input.closest('label').querySelector('.gallery-plus').style.display = 'none';

            document.getElementById('galleryCount').textContent =
                [...document.querySelectorAll('.vendor-gallery-upload input[type=file]')]
                    .filter(i => i.files.length).length;
        });
    });
</script>

@endsection