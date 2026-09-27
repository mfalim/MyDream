@extends('vendor.layouts.app')

@section('title', 'Overview Detail Acara')

@section('content')

<div class="overview-page">

    <div class="overview-topbar">

        <a href="{{ route('vendor.kalender') }}" class="back-button">
            ← Kembali ke Kalender Vendor
        </a>

        <span class="event-pill">
            ● Acara Terpilih: 25 Oktober 2025 •
            The Royal Emerald Wedding
        </span>

    </div>


    <section class="overview-header-card">

        <div>

            <span class="breadcrumb">
                Kalender Acara
                ›
                <strong>Overview Detail Acara</strong>
                ›
                #WO-2025-081
            </span>

            <p>
                Overview Acara: Aditya Wardhana & Sarah Nadia
            </p>

            <small>
                Tinjau kesiapan penugasan dekorasi bunga,
                sinkronisasi lintas mitra lapangan,
                dan koordinasi loading dock Ballroom Mulia.
            </small>

        </div>


        <div class="overview-actions">

            <button type="button">
                ▣ Cetak Lembar Kerja Hari H
            </button>

            <button type="button">
                ☎ Hubungi Show Director
            </button>

            <button type="button" class="btn-primary">
                ◉ Beri Konfirmasi Kesiapan
            </button>

        </div>

    </section>


    {{-- EVENT SUMMARY --}}

    <section class="event-summary-card">

        <div class="event-main-info">

            <div class="event-tags">

                <span>
                    #WO-2025-081 • PAKET ROYAL EMERALD
                </span>

                <span class="escrow-tag">
                    ● Escrow 100% Terkunci
                </span>

            </div>


            <h2>
                Aditya Wardhana
                &
                Sarah Nadia
            </h2>


            <p>
                ▣ Sabtu, 25 Oktober 2025
            </p>

            <p>
                ⌖ Grand Ballroom Hotel Mulia Senayan
                (Lt. 2 Jakarta)
            </p>


            <div class="event-time-grid">

                <div>
                    <span>AKAD NIKAH & PANGGIH</span>
                    <strong>08:00 - 10:30 WIB</strong>
                    <small>Ballroom A & B</small>
                </div>

                <div>
                    <span>RESEPSI ADAT & MODERN</span>
                    <strong>19:00 - 22:00 WIB</strong>
                    <small>Full Grand Ballroom</small>
                </div>

            </div>

        </div>


        <div class="contract-summary">

            <div class="contract-box">

                <span>
                    Nilai Kontrak Lotus Atelier
                </span>

                <strong>
                    Rp 45.000.000
                </strong>

            </div>


            <div class="escrow-box">

                <span>
                    Escrow
                </span>

                <strong>
                    Aman
                </strong>

            </div>


            <div class="director-summary">

                <strong>
                    Dimas Prasetyo, S.I.Kom
                </strong>

                <small>
                    Show Director WO • HT CH 01 Mulia
                </small>

                <button type="button">
                    ☎ Telepon
                </button>

            </div>


            <div class="sync-progress">

                <p>
                    Sinkronisasi Rekanan Acara:
                    <strong>4 dari 5</strong>
                    80% Terkonfirmasi
                </p>

                <div>
                    <span></span>
                </div>

            </div>

        </div>

    </section>


    {{-- THREE COLUMNS --}}

    <div class="overview-three-column">


        {{-- SPECIFICATION --}}

        <section class="overview-panel">

            <div class="panel-title-row">

                <h2>
                    ♧ Spesifikasi Tugas
                </h2>

                <span class="green-label">
                    DEKORASI
                </span>

            </div>

            <p>
                Rincian instalasi panggung pelaminan,
                gate kirab pengantin, serta photo booth
                yang disetujui pengantin.
            </p>


            <div class="task-box">

                <strong>
                    1. Pelaminan Tradisional
                </strong>

                <span>
                    Lebar 18m
                </span>

                <p>
                    Bunga segar warna champagne &
                    emerald sage mix, backdrop kiri
                    garden putih, sofa pelaminan.
                </p>

            </div>


            <div class="task-box">

                <strong>
                    2. Gate Entrance &
                    Gazebo Kirab
                </strong>

                <span>
                    Grand Foyer
                </span>

                <p>
                    Lengkung gerbang melati gantung,
                    standing pillars dan rangkaian
                    bunga mawar.
                </p>

            </div>


            <div class="task-box">

                <strong>
                    3. Photo Booth 360
                </strong>

                <span>
                    Area Foyer Lt. 2
                </span>

                <p>
                    Dimensi 4×3 meter bertema Emerald Garden
                    dengan lighting warm spotlight.
                </p>

            </div>


            <div class="warning-box">
                ◷ Bongkar Muat Dimulai:
                Jumat, 24 Okt pukul 22:00 WIB
                di Loading Bay 2.
            </div>

        </section>



        {{-- PARTNERS --}}

        <section class="overview-panel">

            <div class="panel-title-row">

                <h2>
                    ◈ Kesiapan Mitra Rekanan
                </h2>

                <span class="ready-count">
                    4/5 Ready
                </span>

            </div>

            <p>
                Pantau konfirmasi ketersediaan dan kesiapan
                vendor rekanan yang berbagi area Ballroom Mulia.
            </p>


            <div class="ready-vendor">

                <strong>
                    The Leonardi Photography
                </strong>

                <small>
                    Dokumentasi & Live Feed
                </small>

                <span>
                    ● Bisa / Siap
                </span>

            </div>


            <div class="ready-vendor">

                <strong>
                    Sound & Light Dynamics
                </strong>

                <small>
                    Tata Suara 15.000W & Rigging
                </small>

                <span>
                    ● Bisa / Siap
                </span>

            </div>


            <div class="ready-vendor">

                <strong>
                    Puspa Catering VIP
                </strong>

                <small>
                    Buffet & Fine Dining
                </small>

                <span>
                    ● Bisa / Siap
                </span>

            </div>


            <div class="ready-vendor waiting">

                <strong>
                    Bennu Sorumba MUA
                </strong>

                <small>
                    Jadwal Final Fitting Terbuka
                </small>

                <span>
                    ◉ Menunggu
                </span>

            </div>


            <a href="{{ route('vendor.kolaborasi') }}" class="text-link">
                Lihat Dokumen Lembar Kesepakatan Lintas Rekanan →
            </a>

        </section>



        {{-- LOGISTICS --}}

        <section class="overview-panel">

            <div class="panel-title-row">

                <h2>
                    ▦ Logistik & Regulasi Venue
                </h2>

                <span>
                    Mulia Senayan
                </span>

            </div>


            <div class="logistic-item">

                <strong>
                    🚚 Akses Loading Dock
                    Basement 2
                </strong>

                <p>
                    Ketinggian clearance truck maks. 3.2m.
                    Wajib gunakan elevator barang.
                </p>

            </div>


            <div class="logistic-item">

                <strong>
                    ↕ Batas Ketinggian
                    Panggung & Rigging
                </strong>

                <p>
                    Ketinggian langit-langit ballroom 7.5m.
                    Beban gantung dekorasi maksimal 250kg.
                </p>

            </div>


            <div class="logistic-item">

                <strong>
                    ☏ Kontak Security & Safety Officer
                </strong>

                <p>
                    Bapak Hendrawan
                    (Duty)
                    0812-9988-2121.
                </p>

            </div>


            <div class="logistic-item">

                <strong>
                    ◉ Batas Waktu Bongkaran
                </strong>

                <p>
                    Mulai Sabtu 25 Okt pukul 23:00 WIB
                    sampai Minggu 26 Okt pukul 05:00 WIB.
                </p>

            </div>


            <button type="button" class="floorplan-button">
                ⇩ Unduh Technical Floorplan
                CAD & Loading Permit Mulia
            </button>

        </section>

    </div>


    {{-- CONFIRMATION --}}

    <section class="confirmation-card">

        <div>

            <h2>
                ✓ Konfirmasi Kesiapan
            </h2>

            <small>
                Status: Menunggu respon dari rekanan
            </small>

            <p>
                Nyatakan kesiapan tim Lotus Floral Atelier
                untuk penugasan pada tanggal 25 Oktober 2025
                di Ballroom Hotel Mulia.
            </p>

        </div>


        <div class="confirmation-options">

            <label class="confirmation-option active">

                <input type="radio" name="confirmation" checked>

                <div>
                    <strong>
                        ● Saya Bisa & Siap Bertugas
                    </strong>

                    <small>
                        Kru, stok material bunga,
                        dan jadwal loading dock
                        24 Okt pukul 22:00 WIB
                        telah disetujui tanpa kendala.
                    </small>
                </div>

            </label>


            <label class="confirmation-option">

                <input type="radio" name="confirmation">

                <div>
                    <strong>
                        ○ Ada Catatan Teknis / Penyesuaian
                    </strong>

                    <small>
                        Perlu penyesuaian jam loading
                        atau klarifikasi spesifikasi
                        pelaminan.
                    </small>
                </div>

            </label>


            <textarea
                placeholder="Catatan Tambahan untuk WO / Rekanan (Opsional)"
            ></textarea>


            <button type="button" class="btn-primary">
                ➤ Kirim Konfirmasi Kesiapan
            </button>

        </div>

    </section>


    {{-- RUNDOWN --}}

    <div class="rundown-grid">

        <section class="rundown-panel">

            <div class="panel-title-row">

                <div>
                    <span class="eyebrow">
                        RUNDOWN TIMELINE
                    </span>

                    <h2>
                        Keterlibatan Vendor
                    </h2>
                </div>

                <span class="soft-badge">
                    Zona Waktu WIB
                </span>

            </div>


            <div class="rundown-item">

                <strong>
                    Jumat, 24 Okt • 22:00 - 05:00 WIB
                </strong>

                <h3>
                    Bongkar Muat & Instalasi
                    Rangka Pelaminan 18m
                </h3>

                <p>
                    Tim logistik memasukkan konstruksi backdrop
                    pelaminan melalui elevator barang B2.
                </p>

            </div>


            <div class="rundown-item">

                <strong>
                    Sabtu, 25 Okt • 05:00 - 07:30 WIB
                </strong>

                <h3>
                    Perakitan Fresh Flowers &
                    Gazebo Kirab
                </h3>

                <p>
                    Rangkaian bunga segar diinstalasi
                    pada pelaminan.
                </p>

            </div>


            <div class="rundown-item">

                <strong>
                    Sabtu, 25 Okt • 07:30 - 10:30 WIB
                </strong>

                <h3>
                    Final Walkthrough Show Director
                </h3>

                <p>
                    Pemeriksaan kesiapan dekorasi bersama
                    Show Director.
                </p>

            </div>


            <div class="rundown-item">

                <strong>
                    Sabtu, 25 Okt • 18:30 - 22:00 WIB
                </strong>

                <h3>
                    Resepsi Malam & Operasional Photo Booth
                </h3>

                <p>
                    Kru memastikan area dekorasi tetap
                    bersih selama acara.
                </p>

            </div>


            <div class="rundown-item">

                <strong>
                    Sabtu, 25 Okt • 22:30 - 04:30 WIB
                </strong>

                <h3>
                    Bongkaran & Serah Terima Kebersihan Area
                </h3>

                <p>
                    Pelepasan ornament backdrop dan
                    pembersihan area.
                </p>

            </div>

        </section>


        <section class="rundown-panel">

            <div class="panel-title-row">

                <div>
                    <span class="eyebrow">
                        RUNDOWN TIMELINE
                    </span>

                    <h2>
                        Keterlibatan Vendor
                    </h2>
                </div>

                <span class="soft-badge">
                    Zona Waktu WIB
                </span>

            </div>


            <div class="rundown-item">
                <strong>
                    Jumat, 24 Okt • 22:00 - 05:00 WIB
                </strong>

                <h3>
                    Bongkar Muat & Instalasi
                    Rangka Pelaminan 18m
                </h3>

                <p>
                    Tim logistik memasukkan konstruksi backdrop
                    pelaminan melalui elevator barang B2.
                </p>
            </div>


            <div class="rundown-item">
                <strong>
                    Sabtu, 25 Okt • 05:00 - 07:30 WIB
                </strong>

                <h3>
                    Perakitan Fresh Flowers &
                    Gazebo Kirab
                </h3>

                <p>
                    Rangkaian bunga segar diinstalasi pada
                    pelaminan.
                </p>
            </div>


            <div class="rundown-item">
                <strong>
                    Sabtu, 25 Okt • 07:30 - 10:30 WIB
                </strong>

                <h3>
                    Final Walkthrough Show Director
                </h3>

                <p>
                    Pemeriksaan kesiapan dekorasi bersama
                    Show Director.
                </p>
            </div>


            <div class="rundown-item">
                <strong>
                    Sabtu, 25 Okt • 18:30 - 22:00 WIB
                </strong>

                <h3>
                    Resepsi Malam & Operasional Photo Booth
                </h3>

                <p>
                    Kru memastikan area dekorasi tetap bersih.
                </p>
            </div>


            <div class="rundown-item">
                <strong>
                    Sabtu, 25 Okt • 22:30 - 04:30 WIB
                </strong>

                <h3>
                    Bongkaran & Serah Terima Kebersihan Area
                </h3>

                <p>
                    Pelepasan ornament dan pembersihan area.
                </p>
            </div>

        </section>

    </div>

</div>

@endsection