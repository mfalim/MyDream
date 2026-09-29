@extends('vendor.layouts.app')

@section('title', 'Tambah Layanan & Detail Portofolio')

@section('content')

<div class="vendor-service-page">

    <div class="service-breadcrumb">
        MANAJEMEN KATALOG <span>›</span> TAMBAH LAYANAN & PORTOFOLIO BARU
    </div>

    <div class="service-page-heading">
        <div>
            <h1>Tambah Layanan & Detail Portofolio</h1>
            <p>
                Lengkapi detail paket dekorasi panggung, spesifikasi teknis instalasi fisik,
                portofolio visual resolusi tinggi, serta estimasi rincian biaya untuk proses kurasi vendor WO PROJECT.
            </p>
        </div>

        <span class="draft-status">
            <i></i>
            Status Form:
            <strong>Draf Vendor</strong>
        </span>
    </div>

    <form method="POST" action="#" enctype="multipart/form-data" class="service-layout">
        @csrf

        <div class="service-main">

            <section class="service-section">
                <div class="section-heading">
                    <span class="section-number">1</span>
                    <div>
                        <h2>Informasi Dasar Layanan & Paket</h2>
                        <p>Data utama paket yang akan tampil pada katalog vendor.</p>
                    </div>
                    <span class="required-text">WAJIB DIISI</span>
                </div>

                <div class="form-group full">
                    <label>Nama Paket / Layanan Lengkap <b>*</b></label>
                    <input
                        type="text"
                        name="nama_layanan"
                        value="Paket Pelaminan Botanical Opulence Luxury 18m"
                        placeholder="Contoh: Paket Pelaminan Royal Emerald"
                    >
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Kategori Layanan <b>*</b></label>
                        <select name="kategori">
                            <option>Dekorasi Pelaminan & Stage</option>
                            <option>Floral Decoration</option>
                            <option>Lighting & Production</option>
                            <option>Wedding Styling</option>
                        </select>
                    </div>

                    <div class="form-group price-field">
                        <label>Harga Resmi Penawaran (IDR) <b>*</b></label>
                        <div class="price-input">
                            <span>Rp</span>
                            <input type="text" name="harga" value="125.000.000">
                        </div>
                    </div>
                </div>
            </section>

            <section class="service-section">
                <div class="section-heading">
                    <span class="section-number">2</span>
                    <div>
                        <h2>Spesifikasi Teknis & Kebutuhan Lapangan</h2>
                        <p>Data krusial untuk sinkronisasi layout panggung dan loading dock hotel.</p>
                    </div>
                    <span class="section-symbol">⌘</span>
                </div>

                <div class="spec-grid">

                    <div class="spec-box">
                        <label>▤ &nbsp; Dimensi Panggung Minimal (P × L × T)</label>
                        <small>Rekomendasi area clear ballroom</small>
                        <input type="text" name="dimensi" value="18 m × 6 m × 4.8 m">
                    </div>

                    <div class="spec-box">
                        <label>ϟ &nbsp; Daya Listrik & Titik Rigging</label>
                        <small>Kapasitas suplai daya & rigging hook</small>
                        <input type="text" name="listrik" value="16.000 Watt / 4 Titik Gantung Truss">
                    </div>

                    <div class="spec-box">
                        <label>♙ &nbsp; Alokasi Kru & Tenaga Pasang</label>
                        <small>Termasuk lead florist, carpenter & lighting</small>
                        <input type="text" name="kru" value="24 Personel Berseragam">
                    </div>

                    <div class="spec-box">
                        <label>◷ &nbsp; Durasi Loading & Teardown</label>
                        <small>Estimasi persiapan venue s.d. steril</small>
                        <input type="text" name="durasi" value="Loading: 10 Jam | Bongkaran: 3.5 Jam">
                    </div>

                </div>
            </section>

            <section class="service-section">
                <div class="section-heading">
                    <span class="section-number">3</span>
                    <div>
                        <h2>Deskripsi Konsep & Ruang Lingkup Fasilitas</h2>
                        <p>Jelaskan konsep visual dan fasilitas yang termasuk dalam paket.</p>
                    </div>
                </div>

                <div class="form-group full">
                    <label>Detail Komposisi Material & Deskripsi Artistik</label>
                    <textarea name="deskripsi" rows="6">Struktur pelaminan megah dengan arsitektur lengkung neo-klasik dibalut 85% rangkaian bunga segar Grade-A impor (Ecuador Roses, Hydrangea Belanda, Phalaenopsis Putih, Delphinium, serta dedaunan Eucalyptus cinerea). Termasuk panel backdrop beludru premium warna ivory, chandelier kristal gantung bertingkat, serta instalasi floor carpet mirror anti-slip seluas panggung.</textarea>
                </div>

                <div class="facility-title">
                    FASILITAS TAMBAHAN YANG TERMASUK DALAM PAKET
                </div>

                <div class="facility-grid">
                    <label class="facility-item">
                        <input type="checkbox" checked>
                        <span>
                            <strong>Grand Entrance Gate 4×3 Meter</strong>
                            <small>Lengkung floral gerbang sambut mempelai</small>
                        </span>
                    </label>

                    <label class="facility-item">
                        <input type="checkbox" checked>
                        <span>
                            <strong>Meja Akad / Pemberkatan Khusus</strong>
                            <small>6 kursi crossback Tiffany + floral runner</small>
                        </span>
                    </label>

                    <label class="facility-item">
                        <input type="checkbox" checked>
                        <span>
                            <strong>Aisle Carpet & 10 Standing Flowers</strong>
                            <small>Karpet jalan 25m dengan pedestal bunga</small>
                        </span>
                    </label>

                    <label class="facility-item">
                        <input type="checkbox" checked>
                        <span>
                            <strong>Photo Booth & Galeri Foto Interaktif</strong>
                            <small>Dimensi 4×3 meter lengkap dengan spotlight</small>
                        </span>
                    </label>
                </div>
            </section>

        </div>

        <aside class="service-side">

            <section class="portfolio-card">
                <div class="side-card-heading">
                    <div>
                        <h2>Foto Utama / Portofolio Hero</h2>
                        <p>Ditampilkan di kartu pencarian utama calon pengantin</p>
                    </div>
                    <span class="ratio-badge">Rasio<br>16:9</span>
                </div>

                <label class="hero-upload">
                    <input type="file" name="foto_utama" accept="image/*">
                    <div class="hero-placeholder">
                        <div class="placeholder-icon">▧</div>
                        <strong>Foto Portfolio</strong>
                        <span>Resolusi tinggi • 16:9</span>
                    </div>
                    <div class="upload-caption">
                        Klik untuk memilih foto utama
                    </div>
                </label>

                <button type="button" class="outline-button">
                    ↥ &nbsp; Ganti Foto Portofolio Resolusi Tinggi
                </button>
            </section>

            <section class="portfolio-card">
                <div class="side-card-heading">
                    <div>
                        <h2>Galeri Detail Sudut (4 Slot)</h2>
                        <p>Tampilkan detail pengerjaan kriya & material</p>
                    </div>
                    <strong class="slot-count">4 / 4<br><small>Terunggah</small></strong>
                </div>

                <div class="gallery-grid">
                    <label class="gallery-slot">
                        <input type="file" name="galeri[]" accept="image/*">
                        <span>✿</span>
                        <strong>Macro Floral</strong>
                    </label>

                    <label class="gallery-slot">
                        <input type="file" name="galeri[]" accept="image/*">
                        <span>⌂</span>
                        <strong>Entrance Gate</strong>
                    </label>

                    <label class="gallery-slot">
                        <input type="file" name="galeri[]" accept="image/*">
                        <span>▤</span>
                        <strong>Meja Akad</strong>
                    </label>

                    <label class="gallery-slot">
                        <input type="file" name="galeri[]" accept="image/*">
                        <span>◉</span>
                        <strong>Photo Booth</strong>
                    </label>
                </div>
            </section>

            <section class="standard-card">
                <div class="standard-title">
                    <span>◉</span>
                    <div>
                        <h3>Standar Kurasi WO PROJECT</h3>
                        <small>Proteksi Kualitas Vendor Gold Tier</small>
                    </div>
                </div>

                <ul>
                    <li>Pembayaran termin DP dilindungi dalam sistem Rekening Bersama Escrow WO hingga setup H-1 disetujui lead planner.</li>
                    <li>Spesifikasi beban rigging wajib lolos inspeksi K3 teknis venue sebelum instalasi panggung dimulai.</li>
                    <li>Garansi kebersihan area sampai H-18 jam sejak briefing technical meeting selesai.</li>
                </ul>
            </section>

        </aside>

    </form>

    <div class="service-submit-bar">
        <div class="submit-info">
            <span>↥</span>
            <div>
                <strong>Siap Mengajukan Item ke Tim Kurasi?</strong>
                <small>Item akan ditinjau oleh Principal Curators dalam kurun waktu ±24 jam kerja.</small>
            </div>
        </div>

        <div class="submit-actions">
            <button type="button" class="soft-button">Simpan Draf</button>
            <button type="button" class="soft-button">◉ Preview</button>
            <button type="button" class="publish-button">⚙ Terbitkan & Ajukan ke Katalog</button>
        </div>
    </div>

</div>

@endsection
