<aside class="vendor-sidebar">

    <div class="vendor-brand">
        <div class="brand-dot">•</div>

        <div>
            <div class="brand-label">VENDOR WORKSPACE</div>
            <h2>Lotus Atelier</h2>
            <p>Partner ID: MITRA-LTS-8821</p>
        </div>
    </div>

    <nav class="vendor-nav">

        <a href="{{ route('vendor.dashboard') }}"
           class="vendor-nav-item {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
            <span class="nav-icon">▣</span>
            <span>Dashboard Vendor</span>
        </a>

        <a href="{{ route('vendor.kalender') }}"
           class="vendor-nav-item {{ request()->routeIs('vendor.kalender') ? 'active' : '' }}">
            <span class="nav-icon">▦</span>
            <span>Kalender & Jadwal Acara</span>
        </a>

        <a href="{{ route('vendor.tracking') }}"
           class="vendor-nav-item {{ request()->routeIs('vendor.tracking') ? 'active' : '' }}">
            <span class="nav-icon">☑</span>
            <span>Tracking Tugas & SPK</span>
        </a>

        <a href="{{ route('vendor.konfirmasi') }}"
           class="vendor-nav-item {{ request()->routeIs('vendor.konfirmasi') ? 'active' : '' }}">
            <span class="nav-icon">▣</span>
            <span>Konfirmasi Lintas Vendor</span>
        </a>

        <a href="{{ route('vendor.katalog') }}"
           class="vendor-nav-item {{ request()->routeIs('vendor.katalog') ? 'active' : '' }}">
            <span class="nav-icon">▤</span>
            <span>Katalog & Tambah Layanan</span>
        </a>

        <a href="{{ route('vendor.invoice') }}"
           class="vendor-nav-item {{ request()->routeIs('vendor.invoice') ? 'active' : '' }}">
            <span class="nav-icon">▣</span>
            <span>Invoice & Escrow</span>
        </a>

        <a href="{{ route('vendor.kolaborasi') }}"
           class="vendor-nav-item {{ request()->routeIs('vendor.kolaborasi') ? 'active' : '' }}">
            <span class="nav-icon">♣</span>
            <span>Kolaborasi Lapangan</span>
        </a>

    </nav>

    <div class="vendor-sidebar-bottom">

        <div class="duty-label">
            <span>LEAD DIRECTOR ON-DUTY</span>
            <span>•</span>
        </div>

        <strong>Saran Nadia, S.Ds</strong>
        <small>+62 812-8890-4100</small>

        <button type="button" class="contact-director">
            ☎ &nbsp; Hubungi Show Director
        </button>

    </div>

</aside>