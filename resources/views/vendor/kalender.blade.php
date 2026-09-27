@extends('vendor.layouts.app')

@section('title', 'Kalender & Jadwal Acara')

@section('content')

<div class="calendar-page">

    <div class="calendar-module-banner">
        <div>
            <span class="eyebrow">VENDOR WORKSPACE CALENDAR MODULE</span>
            <p>Sinkronisasi Jadwal Acara & Kolaborasi Lintas Vendor resmi WO PROJECT</p>
        </div>

        <div class="banner-actions">
            <span class="soft-badge">◉ Lihat Blueprint Desain Asli</span>
            <span class="gold-badge">Live Operational View</span>
        </div>
    </div>


    <div class="calendar-heading">

        <div>
            <span class="eyebrow">OPERASIONAL & PENJADWALAN MITRA</span>

            <h1>
                Kalender & Jadwal Pelaksanaan
                <br>
                Acara Vendor
            </h1>

            <p>
                Kelola jadwal booking, plotting kru instalasi dekorasi,
                dan detail kolaborasi lintas mitra rekanan WO PROJECT.
            </p>
        </div>


        <div class="calendar-controls">

            <div class="month-selector">
                <button type="button">‹</button>
                <strong>Oktober 2025</strong>
                <button type="button">›</button>
            </div>

            <div class="view-buttons">
                <button class="view-active" type="button">
                    ▦ Tampilan Kalender
                </button>

                <button type="button">
                    ☷ Agenda (List)
                </button>
            </div>

            <button class="sync-button" type="button">
                ⟳ Sinkron ke Google Calendar
            </button>

        </div>

    </div>


    <div class="calendar-filter">

        <div class="filter-pills">

            <span class="active">
                Semua Status 8
            </span>

            <span>
                ● Hari H Terkunci 5
            </span>

            <span>
                ● Draft Loading 2
            </span>

            <span>
                ● Opsi Cadangan 1
            </span>

        </div>

        <div class="calendar-search">
            🔍
            <input
                type="text"
                placeholder="Cari nama klien pengantin, venue, atau rekanan..."
            >
        </div>

    </div>


    {{-- CALENDAR --}}

    <section class="calendar-card">

        <div class="calendar-weekdays">

            <div>SEN</div>
            <div>SEL</div>
            <div>RAB</div>
            <div>KAM</div>
            <div>JUM</div>
            <div class="weekend">SAB</div>
            <div class="weekend">MIN</div>

        </div>


        <div class="calendar-grid">

            <div class="calendar-day muted">
                <span>29</span>
            </div>

            <div class="calendar-day muted">
                <span>30</span>
            </div>


            <div class="calendar-day">
                <span>01</span>

                <div class="calendar-note">
                    Plotting
                    <br>
                    Tim
                    <br>
                    Workshop
                </div>
            </div>


            <div class="calendar-day">
                <span>02</span>

                <div class="calendar-note">
                    Loading
                    <br>
                    In 22:00
                </div>
            </div>


            <div class="calendar-day">
                <span>03</span>

                <div class="calendar-note">
                    Loading
                    <br>
                    22:00
                </div>
            </div>


            <div class="calendar-day event-day">
                <span>04</span>

                <div class="calendar-event completed">
                    <strong>Kevin & Michelle</strong>
                    <small>The Dharmawangsa</small>
                    <em>Selesai</em>
                </div>
            </div>


            <div class="calendar-day">
                <span>05</span>

                <div class="calendar-note">
                    Maintenance
                    <br>
                    Alat
                </div>
            </div>


            <div class="calendar-day">
                <span>06</span>
            </div>


            <div class="calendar-day">
                <span>07</span>
            </div>


            <div class="calendar-day">
                <span>08</span>
            </div>


            <div class="calendar-day">
                <span>09</span>
            </div>


            <div class="calendar-day">
                <span>10</span>

                <div class="calendar-note">
                    Loading
                    <br>
                    Plataran
                </div>
            </div>


            <div class="calendar-day event-day">
                <span>11</span>

                <div class="calendar-event completed">
                    <strong>Arya & Anindita</strong>
                    <small>Plataran Cilandak</small>
                    <em>Selesai</em>
                </div>
            </div>


            <div class="calendar-day">
                <span>12</span>
            </div>


            <div class="calendar-day">
                <span>13</span>
            </div>


            <div class="calendar-day">
                <span>14</span>
            </div>


            <div class="calendar-day">
                <span>15</span>

                <div class="calendar-note">
                    Technical
                    <br>
                    Meeting
                </div>
            </div>


            <div class="calendar-day">
                <span>16</span>
            </div>


            <div class="calendar-day">
                <span>17</span>

                <div class="calendar-note">
                    Loading
                    <br>
                    Ritz 21:00
                </div>
            </div>


            <div class="calendar-day event-day">
                <span>18</span>

                <div class="calendar-event completed">
                    <strong>Clarissa & Danis</strong>
                    <small>Ritz-Carlton Mega K</small>
                    <em>Selesai</em>
                </div>
            </div>


            <div class="calendar-day">
                <span>19</span>
            </div>


            <div class="calendar-day">
                <span>20</span>
            </div>


            <div class="calendar-day">
                <span>21</span>
            </div>


            <div class="calendar-day">
                <span>22</span>
            </div>


            <div class="calendar-day">
                <span>23</span>

                <div class="calendar-note">
                    Bunga
                    <br>
                    Segar
                    <br>
                    Tiba
                </div>
            </div>


            <div class="calendar-day loading-day">
                <span>24</span>

                <div class="stage-label">
                    STAGE
                    <br>
                    1
                </div>

                <div class="calendar-note">
                    Loading In 23:00
                    <br>
                    Ballroom Mulia
                </div>
            </div>


            <a
                href="{{ route('vendor.overview') }}"
                class="calendar-day selected-day"
            >

                <span>25</span>

                <div class="calendar-event main-event">

                    <strong>
                        HARI H
                        <br>
                        UTAMA
                    </strong>

                    <b>
                        Aditya & Sarah
                    </b>

                    <small>
                        Grand Ballroom Mulia
                    </small>

                </div>

            </a>


            <div class="calendar-day">
                <span>26</span>

                <div class="calendar-event standby">
                    <strong>Standby & Bongkar</strong>
                    <small>Daniel & Flora</small>
                </div>

            </div>


            <div class="calendar-day">
                <span>27</span>
            </div>


            <div class="calendar-day">
                <span>28</span>
            </div>


            <div class="calendar-day">
                <span>29</span>
            </div>


            <div class="calendar-day">
                <span>30</span>
            </div>


            <div class="calendar-day">
                <span>31</span>

                <div class="calendar-note">
                    Loading
                    <br>
                    Blokdara
                </div>
            </div>


            <div class="calendar-day">
                <span>01 Nov</span>

                <div class="calendar-event completed">
                    <strong>Fauzan & Gina</strong>
                    <small>Bidakara Grand Hall</small>
                </div>
            </div>


            <div class="calendar-day muted">
                <span>02</span>
            </div>

        </div>


        <div class="calendar-legend">

            <span>
                <i class="legend-dot dark"></i>
                Hari H Berlangsung
            </span>

            <span>
                <i class="legend-dot gold"></i>
                Jadwal Loading In / Out
            </span>

            <span>
                <i class="legend-dot gray"></i>
                Selesai & Closed
            </span>

        </div>

        <p class="calendar-helper">
            ✥ Klik tanggal mana saja untuk melihat detail pesanan & tim
        </p>

    </section>


    {{-- SELECTED DATE --}}

    <div class="selected-date-banner">

        <div class="selected-date-icon">
            ▣
        </div>

        <div>
            <span>TANGGAL TERPILIH SAAT INI</span>

            <strong>
                Sabtu, 25 Oktober 2025 • Acara
                <br>
                Hari H
            </strong>
        </div>

        <span class="selected-status">
            Status: Terkunci & Aktif
        </span>

    </div>


    {{-- DETAIL --}}

    <div class="calendar-detail-grid">

        <section class="detail-client-card">

            <div class="section-label">
                ▣ DETAIL KLIEN PEMESAN
                <span>ID: #WO-2025-081</span>
            </div>

            <div class="client-highlight">

                <small>
                    Pasangan Pengantin:
                </small>

                <h2>
                    Aditya Wardhana
                    &
                    Sarah Nadia
                </h2>

                <p>
                    Paket Layanan:
                    The Royal Emerald Wedding Series
                </p>

            </div>


            <div class="client-info">

                <div>
                    <strong>⌖ Lokasi Venue & Ruangan</strong>
                    <p>
                        Grand Ballroom Hotel Mulia Senayan,
                        Jakarta Pusat (Lt. 2)
                    </p>
                </div>


                <div>
                    <strong>◷ Waktu Pelaksanaan</strong>
                    <p>
                        Akad Nikah: 08:00 - 10:30 WIB
                        <br>
                        Resepsi: 19:00 - 22:00 WIB
                    </p>
                </div>


                <div>
                    <strong>♙ Show Director WO</strong>
                    <p>
                        Dimas Prasetyo, S.I.Kom
                    </p>
                </div>


                <div>
                    <strong>▣ Ruang Lingkup Pekerjaan</strong>
                    <p>
                        Paket Dekorasi Pelaminan Adat Modern
                        Emerald 18 Meter, Gazebo Kirab Bunga
                        Segar, Photo Booth & Gallery.
                    </p>
                </div>


                <div>
                    <strong>▤ Nilai SPK Kontrak Vendor</strong>
                    <p class="contract-value">
                        Rp 45.000.000
                    </p>
                </div>

            </div>

        </section>


        <section class="vendor-partners-card">

            <div class="partner-grid">

                <div class="partner-card">
                    <strong>The Leonardi Photography</strong>
                    <small>Dokumentasi</small>
                    <p>
                        PIC: King Leonardi
                    </p>
                    <span>
                        Sinkronisasi spot lighting
                    </span>
                </div>


                <div class="partner-card">
                    <strong>Bennu Sorumba MUA & Attire</strong>
                    <small>Rias & Busana</small>
                    <p>
                        PIC: Mas Danar
                    </p>
                    <span>
                        Standby cermin rias
                    </span>
                </div>


                <div class="partner-card">
                    <strong>Sound & Light Dynamics</strong>
                    <small>Tata Cahaya</small>
                    <p>
                        PIC: Rian Hidayat
                    </p>
                    <span>
                        Beam follow spot
                    </span>
                </div>


                <div class="partner-card">
                    <strong>Puspa Catering VIP</strong>
                    <small>Katering & Buffet</small>
                    <p>
                        PIC: Ibu Retno
                    </p>
                    <span>
                        Setup meja keluarga
                    </span>
                </div>


                <div class="partner-card">
                    <strong>MC Choky Sitohang & Dwiki Jazz</strong>
                    <small>Show & Music</small>
                    <p>
                        PIC: Tommy
                    </p>
                    <span>
                        Sound monitor panggung
                    </span>
                </div>


                <div class="partner-card">
                    <strong>MC Choky Sitohang & Dwiki Jazz</strong>
                    <small>Show & Music</small>
                    <p>
                        PIC: Tommy
                    </p>
                    <span>
                        Koordinasi cue music
                    </span>
                </div>

            </div>


            <div class="collaboration-box">

                <strong>
                    Mitra Vendor Bekerja Bersama
                </strong>

                <p>
                    Rekan Tim & Vendor Lapangan Hari H
                </p>

                <span>
                    5 Vendor Rekanan
                </span>

            </div>


            <button
                type="button"
                class="btn-primary full-button"
            >
                ◈ Buka Lembar Koordinasi Bersama
            </button>


            <div class="two-buttons">

                <button type="button">
                    ▣ WA Group Hari H
                </button>

                <button type="button">
                    ⇩ Floorplan (PDF)
                </button>

            </div>

        </section>

    </div>

</div>

@endsection