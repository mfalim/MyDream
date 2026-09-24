@extends('layouts.admin')

@section('title', 'Dashboard Vendor')
@section('page-title', 'Dashboard Vendor')

@section('content')

<link rel="stylesheet" href="{{ asset('css/vendor/vendor-dashboard.css') }}">

<div class="vendor-dashboard">

    <!-- WELCOME -->
    <div class="welcome-card">
        <div class="welcome-info">
            <div class="status-label">
                <span></span>
                VENDOR PORTAL
            </div>

            <p class="gate-info">
                Selamat datang kembali, <strong>Vendor Partner</strong>
            </p>

            <h1>Dashboard Vendor</h1>

            <p class="welcome-desc">
                Kelola layanan, pantau jadwal, dan lihat perkembangan
                pekerjaan Anda dalam satu tempat.
            </p>
        </div>

        <div class="welcome-actions">
            <button class="btn-primary">
                + Tambah Layanan
            </button>

            <button class="btn-secondary">
                Lihat Kalender
            </button>
        </div>
    </div>


    <!-- STATISTICS -->
    <div class="stats-grid">

        <div class="stat-card">
            <div class="stat-top">
                <div>
                    <p>TOTAL LAYANAN</p>
                    <h2>12 <small>layanan</small></h2>
                </div>

                <div class="stat-icon">
                    ◈
                </div>
            </div>

            <div class="event-mini">
                <div class="event-title">
                    <strong>Layanan Aktif</strong>
                    <span>8 Aktif</span>
                </div>

                <div class="progress">
                    <div style="width: 67%;"></div>
                </div>

                <small>67% dari seluruh layanan</small>
            </div>
        </div>


        <div class="stat-card">
            <div class="stat-top">
                <div>
                    <p>EVENT BERJALAN</p>
                    <h2>4 <small>event</small></h2>
                </div>

                <div class="stat-icon light">
                    ◷
                </div>
            </div>

            <div class="schedule-number">
                <div>
                    <strong>2</strong>
                    <span>MINGGU INI</span>
                </div>

                <div class="today">
                    <strong>1</strong>
                    <span>HARI INI</span>
                </div>

                <div>
                    <strong>1</strong>
                    <span>MENDATANG</span>
                </div>
            </div>
        </div>


        <div class="stat-card">
            <div class="stat-top">
                <div>
                    <p>PENDAPATAN</p>
                    <h2>Rp 24,5jt</h2>
                </div>

                <div class="stat-icon">
                    Rp
                </div>
            </div>

            <div class="escrow-box">
                <strong>✓</strong>

                <span>
                    Pembayaran aman dan tercatat
                    dalam sistem.
                </span>
            </div>
        </div>


        <div class="stat-card">
            <div class="stat-top">
                <div>
                    <p>RATING VENDOR</p>
                    <h2>4.8 <small>/ 5.0</small></h2>
                </div>

                <div class="star-icon">
                    ★
                </div>
            </div>

            <div class="rating-footer">
                <span>
                    Berdasarkan 38 ulasan
                </span>

                <strong>
                    Sangat Baik
                </strong>
            </div>
        </div>

    </div>


    <!-- MAIN CONTENT -->
    <div class="main-grid">

        <!-- TIMELINE -->
        <div class="panel">

            <div class="panel-heading">
                <div>
                    <span class="section-label">
                        WORKFLOW
                    </span>

                    <h2>
                        Timeline Pekerjaan
                    </h2>
                </div>

                <div class="location-badge">
                    📍 Jember, Jawa Timur
                </div>
            </div>


            <div class="director-card">

                <div class="director-icon">
                    👤
                </div>

                <div>
                    <small>PROJECT DIRECTOR</small>
                    <strong>Wedding Organizer Team</strong>
                </div>

                <span class="stage-captain">
                    Event Captain
                </span>

            </div>


            <div class="timeline">

                <div class="timeline-item completed">

                    <div class="timeline-dot">
                        ✓
                    </div>

                    <div class="timeline-content">
                        <div class="time">
                            08:00 WIB
                        </div>

                        <strong>
                            Persiapan Vendor
                        </strong>

                        <p>
                            Seluruh kebutuhan vendor
                            telah dipersiapkan.
                        </p>
                    </div>

                    <div class="task-status">
                        SELESAI
                    </div>

                </div>


                <div class="timeline-item completed">

                    <div class="timeline-dot">
                        ✓
                    </div>

                    <div class="timeline-content">
                        <div class="time">
                            10:00 WIB
                        </div>

                        <strong>
                            Setup Equipment
                        </strong>

                        <p>
                            Peralatan mulai dipasang
                            di lokasi acara.
                        </p>
                    </div>

                    <div class="task-status">
                        SELESAI
                    </div>

                </div>


                <div class="timeline-item active">

                    <div class="timeline-dot">
                        •
                    </div>

                    <div class="timeline-content">
                        <div class="time">
                            13:00 WIB
                        </div>

                        <strong>
                            Persiapan Acara
                        </strong>

                        <p>
                            Vendor sedang melakukan
                            persiapan akhir sebelum acara.
                        </p>

                        <div class="active-progress">
                            <span></span>
                        </div>
                    </div>

                    <div class="running-status">
                        BERJALAN
                    </div>

                </div>


                <div class="timeline-item">

                    <div class="timeline-dot"></div>

                    <div class="timeline-content">
                        <div class="time">
                            16:00 WIB
                        </div>

                        <strong>
                            Acara Dimulai
                        </strong>

                        <p>
                            Pelaksanaan acara sesuai
                            jadwal yang telah ditentukan.
                        </p>
                    </div>

                    <div class="task-status">
                        MENUNGGU
                    </div>

                </div>

            </div>


            <div class="timeline-actions">

                <button class="btn-primary">
                    Lihat Detail Event
                </button>

                <button class="btn-secondary">
                    Buka Kalender
                </button>

            </div>

        </div>


        <!-- VENDOR SYNC -->
        <div class="panel">

            <div class="panel-heading">

                <div>
                    <span class="section-label">
                        VENDOR MANAGEMENT
                    </span>

                    <h2>
                        Vendor Sync
                    </h2>
                </div>

                <span class="on-site">
                    ● ON SITE
                </span>

            </div>


            <p class="sync-desc">
                Daftar vendor yang sedang bekerja
                dalam event aktif.
            </p>


            <div class="vendor-list">

                <div class="vendor-item">

                    <div class="vendor-icon">
                        ♫
                    </div>

                    <div>
                        <strong>
                            Sound & Music
                        </strong>

                        <small>
                            Audio System
                        </small>

                        <span>
                            ✓ Terverifikasi
                        </span>
                    </div>

                    <button>
                        →
                    </button>

                </div>


                <div class="vendor-item">

                    <div class="vendor-icon">
                        ✦
                    </div>

                    <div>
                        <strong>
                            Decoration
                        </strong>

                        <small>
                            Wedding Decoration
                        </small>

                        <span>
                            ✓ Terverifikasi
                        </span>
                    </div>

                    <button>
                        →
                    </button>

                </div>


                <div class="vendor-item">

                    <div class="vendor-icon">
                        📷
                    </div>

                    <div>
                        <strong>
                            Photography
                        </strong>

                        <small>
                            Photo & Video
                        </small>

                        <span>
                            ✓ Terverifikasi
                        </span>
                    </div>

                    <button>
                        →
                    </button>

                </div>


                <div class="vendor-item">

                    <div class="vendor-icon">
                        ♨
                    </div>

                    <div>
                        <strong>
                            Catering
                        </strong>

                        <small>
                            Food & Beverage
                        </small>

                        <span>
                            ✓ Terverifikasi
                        </span>
                    </div>

                    <button>
                        →
                    </button>

                </div>

            </div>


            <button class="radio-button">
                + Tambah Vendor
            </button>

        </div>

    </div>


    <!-- PIPELINE -->
    <div class="panel pipeline">

        <div class="pipeline-header">

            <div>
                <span class="section-label">
                    PROJECT PIPELINE
                </span>

                <h2>
                    Daftar Event
                </h2>
            </div>

            <div class="filter-buttons">

                <button class="active">
                    Semua
                </button>

                <button>
                    Berjalan
                </button>

                <button>
                    Mendatang
                </button>

                <button>
                    Selesai
                </button>

            </div>

        </div>


        <div class="table-wrapper">

            <table>

                <thead>
                    <tr>
                        <th>EVENT</th>
                        <th>TANGGAL</th>
                        <th>LOKASI</th>
                        <th>LAYANAN</th>
                        <th>STATUS</th>
                        <th>AKSI</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td>
                            <strong>
                                Wedding A & B
                            </strong>
                            <small>
                                WO-2026-001
                            </small>
                        </td>

                        <td>
                            24 Sep 2026
                        </td>

                        <td>
                            Jember
                        </td>

                        <td>
                            <span class="tag">
                                Decoration
                            </span>
                        </td>

                        <td>
                            <span class="status orange">
                                BERJALAN
                            </span>
                        </td>

                        <td>
                            <button class="detail-button">
                                Detail
                            </button>
                        </td>
                    </tr>


                    <tr>
                        <td>
                            <strong>
                                Wedding C & D
                            </strong>
                            <small>
                                WO-2026-002
                            </small>
                        </td>

                        <td>
                            28 Sep 2026
                        </td>

                        <td>
                            Bondowoso
                        </td>

                        <td>
                            <span class="tag">
                                Catering
                            </span>
                        </td>

                        <td>
                            <span class="status orange">
                                MENDATANG
                            </span>
                        </td>

                        <td>
                            <button class="detail-button">
                                Detail
                            </button>
                        </td>
                    </tr>


                    <tr>
                        <td>
                            <strong>
                                Wedding E & F
                            </strong>
                            <small>
                                WO-2026-003
                            </small>
                        </td>

                        <td>
                            05 Okt 2026
                        </td>

                        <td>
                            Banyuwangi
                        </td>

                        <td>
                            <span class="tag">
                                Photography
                            </span>
                        </td>

                        <td>
                            <span class="status green">
                                TERKONFIRMASI
                            </span>
                        </td>

                        <td>
                            <button class="detail-button">
                                Detail
                            </button>
                        </td>
                    </tr>


                    <tr>
                        <td>
                            <strong>
                                Wedding G & H
                            </strong>
                            <small>
                                WO-2026-004
                            </small>
                        </td>

                        <td>
                            12 Okt 2026
                        </td>

                        <td>
                            Lumajang
                        </td>

                        <td>
                            <span class="tag">
                                Sound System
                            </span>
                        </td>

                        <td>
                            <span class="status green">
                                TERKONFIRMASI
                            </span>
                        </td>

                        <td>
                            <button class="detail-button">
                                Detail
                            </button>
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>


    <!-- FOOTER -->
    <div class="dashboard-footer">

        <span>
            © 2026 Wedding Organizer Vendor Portal
        </span>

        <div class="footer-links">
            <span>Help Center</span>
            <span>•</span>
            <span>Terms</span>
            <span>•</span>
            <span>Privacy</span>
        </div>

    </div>

</div>

@endsection