@extends('vendor.layouts.app')

@section('title', 'Tambah Layanan & Detail Portofolio')

@section('content')

<div class="add-service-page">

    <div class="add-service-heading">

        <div>

            <span class="breadcrumb">
                MANAJEMEN KATALOG
                ›
                TAMBAH LAYANAN & PORTOFOLIO BARU
            </span>

            <h1>
                Tambah Layanan & Detail Portofolio
            </h1>

            <p>
                Lengkapi detail paket dekorasi panggung,
                spesifikasi teknis instalasi fisik,
                portofolio visual resolusi tinggi,
                serta estimasi rincian biaya untuk proses
                kurasi WO PROJECT.
            </p>

        </div>


        <div class="draft-status">

            <span>
                ● Status Form:
            </span>

            <strong>
                Draf Vendor
            </strong>

        </div>

    </div>


    <div class="add-service-layout">


        {{-- LEFT FORM --}}

        <div class="service-form-column">


            {{-- SECTION 1 --}}

            <section class="form-card">

                <div class="form-section-title">

                    <span class="number-circle">
                        1
                    </span>

                    <div>
                        <h2>
                            Informasi Dasar Layanan & Paket
                        </h2>

                        <small>
                            WAJIB DIISI
                        </small>
                    </div>

                </div>


                <label>
                    NAMA PAKET / LAYANAN LENGKAP
                    <b>*</b>
                </label>

                <input
                    type="text"
                    value="Paket Pelaminan Botanical Opulence Luxury 18m"
                >


                <div class="form-two-column">

                    <div>

                        <label>
                            KATEGORI LAYANAN
                            <b>*</b>
                        </label>

                        <select>

                            <option>
                                Dekorasi Pelaminan & Stage
                            </option>

                            <option>
                                Dekorasi Ballroom
                            </option>

                            <option>
                                Floral Decoration
                            </option>

                        </select>

                    </div>


                    <div>

                        <label>
                            HARGA RESMI PENAWARAN (IDR)
                            <b>*</b>
                        </label>

                        <div class="price-input">

                            <span>
                                Rp
                            </span>

                            <input
                                type="text"
                                value="125.000.000"
                            >

                        </div>

                    </div>

                </div>

            </section>



            {{-- SECTION 2 --}}

            <section class="form-card">

                <div class="form-section-title">

                    <span class="number-circle">
                        2
                    </span>

                    <div>

                        <h2>
                            Spesifikasi Teknis &
                            Kebutuhan Lapangan
                        </h2>

                        <small>
                            Data krusial untuk sinkronisasi layout
                            panggung dan loading dock hotel.
                        </small>

                    </div>

                </div>


                <div class="technical-grid">


                    <div class="technical-box">

                        <strong>
                            ▣ Dimensi Panggung Minimal
                            (P × L × T)
                        </strong>

                        <small>
                            Rekomendasi area clear ballroom
                        </small>

                        <input
                            type="text"
                            value="18 m × 6 m × 4.8 m"
                        >

                    </div>


                    <div class="technical-box">

                        <strong>
                            ⚡ Daya Listrik & Titik Rigging
                        </strong>

                        <small>
                            Kapasitas suplai daya & rigging hook
                        </small>

                        <input
                            type="text"
                            value="16.000 Watt / 4 Titik Gantung Truss"
                        >

                    </div>


                    <div class="technical-box">

                        <strong>
                            ♧ Alokasi Kru & Tenaga Pasang
                        </strong>

                        <small>
                            Termasuk lead florist,
                            carpenter & lighting
                        </small>

                        <input
                            type="text"
                            value="24 Personel Berseragam"
                        >

                    </div>


                    <div class="technical-box">

                        <strong>
                            ◷ Durasi Loading & Teardown
                        </strong>

                        <small>
                            Estimasi persiapan venue s.d. steril
                        </small>

                        <input
                            type="text"
                            value="Loading: 10 Jam | Bongkaran: 3.5 Jam"
                        >

                    </div>

                </div>

            </section>



            {{-- SECTION 3 --}}

            <section class="form-card">

                <div class="form-section-title">

                    <span class="number-circle">
                        3
                    </span>

                    <div>

                        <h2>
                            Deskripsi Konsep &
                            Ruang Lingkup Fasilitas
                        </h2>

                    </div>

                </div>


                <label>
                    DETAIL KOMPOSISI MATERIAL &
                    DESKRIPSI ARTISTIK
                </label>

                <textarea rows="6">Struktur pelaminan megah dengan arsitektur lengkung neo-klasik dibalut 85% rangkaian bunga segar Grade-A impor (Ecuador Roses, Hydrangea Belanda, Phalaenopsis Putih, Delphinium, serta dedaunan Eucalyptus cinerea). Termasuk panel backdrop berbudru premium warna ivory, chandelier kristal gantung bertingkat, serta instalasi floor carpet mirror anti-slip seluas panggung.</textarea>


                <label>
                    FASILITAS TAMBAHAN YANG TERMASUK
                    DALAM PAKET
                </label>


                <div class="facility-grid">

                    <label class="facility-item">
                        <input type="checkbox" checked>
                        <span>
                            <strong>
                                Grand Entrance Gate 4×3 Meter
                            </strong>

                            <small>
                                Lengkung floral gerbang
                                sambut mempelai
                            </small>
                        </span>
                    </label>


                    <label class="facility-item">
                        <input type="checkbox" checked>

                        <span>
                            <strong>
                                Meja Akad / Pemberkatan Khusus
                            </strong>

                            <small>
                                6 kursi crossback Tiffany
                                + floral runner
                            </small>
                        </span>

                    </label>


                    <label class="facility-item">
                        <input type="checkbox" checked>

                        <span>
                            <strong>
                                Aisle Carpet & 10 Standing Flowers
                            </strong>

                            <small>
                                Karpet jalan 25m dengan
                                pedestal bunga
                            </small>
                        </span>

                    </label>


                    <label class="facility-item">
                        <input type="checkbox" checked>

                        <span>
                            <strong>
                                Photo Booth & Galeri Foto Interaktif
                            </strong>

                            <small>
                                Dimensi 4×3 meter lengkap
                                dengan spotlight
                            </small>
                        </span>

                    </label>

                </div>

            </section>


            {{-- BOTTOM ACTION --}}

            <div class="service-submit-bar">

                <div>

                    <strong>
                        ↑ Siap Mengajukan Item ke Tim Kurasi?
                    </strong>

                    <small>
                        Item akan ditinjau oleh Principal Curators
                        dalam kurun waktu ±24 jam kerja.
                    </small>

                </div>


                <div class="submit-actions">

                    <button type="button" class="btn-light">
                        Simpan Draf
                    </button>

                    <button type="button" class="btn-light">
                        ◉ Preview
                    </button>

                    <button type="button" class="btn-primary">
                        ⚙ Terbitkan & Ajukan ke Katalog
                    </button>

                </div>

            </div>

        </div>



        {{-- RIGHT COLUMN --}}

        <aside class="service-preview-column">


            {{-- HERO PHOTO --}}

            <section class="preview-card">

                <div class="preview-card-heading">

                    <div>

                        <h2>
                            Foto Utama / Portofolio Hero
                        </h2>

                        <p>
                            Ditampilkan di kartu pencarian
                            utama calon pengantin
                        </p>

                    </div>

                    <span class="ratio-badge">
                        Rasio 16:9
                    </span>

                </div>


                <div class="hero-photo">

                    <div class="photo-placeholder">
                        FOTO PORTOFOLIO
                    </div>

                    <div class="photo-caption">

                        <strong>
                            DSC08492_Luxury_Stage_Main.raw
                        </strong>

                        <small>
                            6.4 MB • Resolusi Ultra HD
                        </small>

                    </div>

                </div>


                <button type="button" class="photo-button">
                    ⇧ Ganti Foto Portofolio Resolusi Tinggi
                </button>

            </section>



            {{-- GALLERY --}}

            <section class="preview-card">

                <div class="preview-card-heading">

                    <div>

                        <h2>
                            Galeri Detail Sudut
                            (4 Slot)
                        </h2>

                        <p>
                            Tampilkan detail pengerjaan
                            kriya & material
                        </p>

                    </div>

                    <strong>
                        4 / 4
                        <br>
                        Terunggah
                    </strong>

                </div>


                <div class="gallery-grid">

                    <div class="gallery-item">
                        <span>Macro Floral</span>
                    </div>

                    <div class="gallery-item">
                        <span>Entrance Gate</span>
                    </div>

                    <div class="gallery-item">
                        <span>Meja Akad</span>
                    </div>

                    <div class="gallery-item">
                        <span>Photo Booth</span>
                    </div>

                </div>

            </section>



            {{-- STANDARD --}}

            <section class="vendor-standard-card">

                <h2>
                    ◉ Standar Kurasi WO PROJECT
                </h2>

                <small>
                    Proteksi Kualitas Vendor Gold Tier
                </small>


                <ul>

                    <li>
                        Pembayaran termin DP dilindungi
                        dalam sistem rekening bersama
                        WO PROJECT.
                    </li>

                    <li>
                        Spesifikasi beban rigging wajib
                        lolos inspeksi teknis venue.
                    </li>

                    <li>
                        Garansi ketersediaan material
                        sesuai brief teknis.
                    </li>

                </ul>

            </section>

        </aside>

    </div>

</div>

@endsection