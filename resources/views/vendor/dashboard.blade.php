@extends('vendor.layouts.app')

@section('title', 'Dashboard Vendor')

@section('content')

<div class="page-header">

    <div>

        <span class="eyebrow">
            LIVE ON-SITE MODE
        </span>

        <h1>
            Selamat Bekerja, Lotus Floral Atelier
        </h1>

        <p>
            Pantau jadwal loading panggung, serah terima area pelaminan,
            dan sinkronisasi tugas Hari H bersama tim WO PROJECT secara real-time.
        </p>

    </div>


    <div class="header-actions">

        <button class="btn-light">
            ▣ Download SPK & Floorplan
        </button>

        <button class="btn-light">
            ⌘ Scan Gate Pass
        </button>

        <button class="btn-primary">
            ◉ Konfirmasi Kesiapan Loading
        </button>

    </div>

</div>


<div class="stats-grid">

    <div class="stat-card">

        <span>ACARA HARI INI</span>

        <h2>1 Acara Aktif</h2>

        <div class="stat-info">
            <strong>The Royal Emerald</strong>
            <span>85% Selesai</span>
        </div>

        <small>
            Grand Ballroom Hotel Mulia
        </small>

    </div>


    <div class="stat-card">

        <span>JADWAL BULAN INI</span>

        <h2>8 Acara Terplot</h2>

        <div class="mini-stats">

            <div>
                <strong>3</strong>
                <small>SELESAI</small>
            </div>

            <div>
                <strong>1</strong>
                <small>HARI INI</small>
            </div>

            <div>
                <strong>4</strong>
                <small>MENDATANG</small>
            </div>

        </div>

    </div>


    <div class="stat-card">

        <span>STATUS SPK & KONTRAK</span>

        <h2>100% Escrow</h2>

        <p>
            Dana pembayaran aman dan terlindungi
            dalam sistem WO PROJECT.
        </p>

    </div>


    <div class="stat-card">

        <span>RATING KERJASAMA WO</span>

        <h2>4.98 / 5.0</h2>

        <p>
            98 Acara Sukses Bersama
        </p>

        <strong>
            Gold Tier Rekanan
        </strong>

    </div>

</div>


<div class="dashboard-grid">

    <section class="panel">

        <div class="panel-header">

            <div>
                <span class="eyebrow">
                    LIVE TIMELINE PRODUKSI
                </span>

                <h2>
                    The Royal Emerald Wedding
                </h2>
            </div>

            <span class="badge-gold">
                Grand Ballroom Hotel Mulia
            </span>

        </div>


        <div class="director-card">

            <div class="director-avatar">
                D
            </div>

            <div>

                <small>
                    SHOW DIRECTOR ON-SITE
                </small>

                <strong>
                    Dimas Prasetyo (M TC H OI)
                </strong>

            </div>

        </div>


        <div class="timeline">

            <div class="timeline-item done">

                <strong>06:00 WIB</strong>

                <p>
                    Loading In & Gate Pass Ballroom
                </p>

                <span>
                    SELESAI 100%
                </span>

            </div>


            <div class="timeline-item done">

                <strong>09:30 WIB</strong>

                <p>
                    Rangka Truss & Rigging LED Screen Pelaminan
                </p>

                <span>
                    SELESAI 100%
                </span>

            </div>


            <div class="timeline-item active">

                <strong>
                    12:30 WIB • TAHAP KRUSIAL
                </strong>

                <p>
                    Instalasi Bunga Segar & Flooring Pelaminan
                </p>

                <span>
                    BERJALAN 85%
                </span>

            </div>


            <div class="timeline-item">

                <strong>15:30 WIB</strong>

                <p>
                    Final Lighting Cue & Handover Bersama Klien
                </p>

                <span>
                    STANDBY
                </span>

            </div>


            <div class="timeline-item">

                <strong>22:30 WIB</strong>

                <p>
                    Teardown / Loading Out Ballroom
                </p>

                <span>
                    MENUNGGU SELESAI
                </span>

            </div>

        </div>

    </section>


    <section class="panel">

        <div class="panel-header">

            <div>

                <span class="eyebrow">
                    LIVE FLOOR SYNC
                </span>

                <h2>
                    Rekanan Vendor Hari H
                </h2>

            </div>

        </div>


        <p class="panel-description">
            Sinkronisasi ruang gerak panggung bersama mitra resmi
            WO PROJECT yang beroperasi bersama.
        </p>


        <div class="vendor-list">

            <div class="vendor-item">

                <strong>
                    The Leonardi Photography
                </strong>

                <small>
                    PIC: King Leonardi
                </small>

                <span>
                    Sedang Test Lighting Pelaminan
                </span>

            </div>


            <div class="vendor-item">

                <strong>
                    Sound & Light Dynamics
                </strong>

                <small>
                    PIC: Rian Hidayat
                </small>

                <span>
                    Sinkronisasi Beam & Lighting
                </span>

            </div>


            <div class="vendor-item">

                <strong>
                    Puspa Catering VIP
                </strong>

                <small>
                    PIC: Ibu Retno
                </small>

                <span>
                    Setup Dekorasi Table
                </span>

            </div>


            <div class="vendor-item">

                <strong>
                    Le Novelle Cake
                </strong>

                <small>
                    PIC: Miyama
                </small>

                <span>
                    Koordinasi Spot Cake Utama
                </span>

            </div>

        </div>

    </section>

</div>


<section class="panel pipeline-panel">

    <div class="panel-header">

        <div>

            <span class="eyebrow">
                PIPELINE PROJECT
            </span>

            <h2>
                Jadwal Acara & Status Pembayaran Mendatang
            </h2>

        </div>


        <div class="filter-pills">

            <span class="active">
                Semua (8)
            </span>

            <span>
                Pekan Ini (2)
            </span>

            <span>
                Bulan Depan (5)
            </span>

            <span>
                Selesai
            </span>

        </div>

    </div>


    <div class="table-wrapper">

        <table class="vendor-table">

            <thead>

                <tr>
                    <th>ACARA & PENGANTIN</th>
                    <th>TANGGAL & JAM LOADING</th>
                    <th>VENUE / LOKASI</th>
                    <th>TEMA & SPEK DEKORASI</th>
                    <th>STATUS SPK & TASK</th>
                    <th>AKSI</th>
                </tr>

            </thead>


            <tbody>

                <tr>

                    <td>
                        <strong>
                            Aditya Wardhana & Sarah Nadia
                        </strong>

                        <small>
                            WO-ID: #WO-2024-8812
                        </small>
                    </td>


                    <td>
                        Hari Ini, 26 Okt 2024<br>
                        Loading: 06:00 WIB
                    </td>


                    <td>
                        Grand Ballroom,<br>
                        Hotel Mulia Jakarta
                    </td>


                    <td>
                        Royal Emerald<br>
                        Javanese
                    </td>


                    <td>
                        <span class="status-warning">
                            Loading 85%
                        </span>
                    </td>


                    <td>
                        <button class="table-button">
                            Lihat Rundown
                        </button>
                    </td>

                </tr>


                <tr>

                    <td>
                        <strong>
                            Raden Daniswara & Clarissa
                        </strong>

                        <small>
                            WO-ID: #WO-2024-8840
                        </small>
                    </td>


                    <td>
                        Sabtu, 02 Nov 2024<br>
                        Loading: 23:00 WIB
                    </td>


                    <td>
                        Glass House,<br>
                        Plataran Hutan Kota
                    </td>


                    <td>
                        Modern White<br>
                        Floral Arch
                    </td>


                    <td>
                        <span class="status-success">
                            SPK Terbit / Escrow 50%
                        </span>
                    </td>


                    <td>
                        <button class="table-button">
                            Rincian Spek
                        </button>
                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</section>

@endsection