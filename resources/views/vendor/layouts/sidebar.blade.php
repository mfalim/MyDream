<aside class="sidebar" id="sidebar">
    <div class="brand-block">
        <div class="brand-mark">
            <i class="bi bi-gem"></i>
        </div>
        <div class="brand-text">
            <div class="brand-name">My<span>Dream</span></div>
            <div class="brand-subtitle">WEDDING ORGANIZER</div>
        </div>
    </div>

    @auth
        <div class="client-card">
            <div class="client-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="client-info">
                <div class="client-card-top">
                    <span>Vendor Partner</span>
                    <i class="bi bi-patch-check-fill text-gold" title="Akses Vendor Terverifikasi"></i>
                </div>
                <div class="client-name">{{ auth()->user()->name }}</div>
            </div>
        </div>
    @endauth

    <nav class="sidebar-nav">
        <div class="sidebar-section">
            <div class="sidebar-section-title">
                <span>RUANG KERJA</span>
            </div>

            <a href="{{ route('vendor.dashboard') }}"
                class="nav-item {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i>
                <span>Dashboard Vendor</span>
            </a>

            <a href="{{ route('vendor.kalender') }}"
                class="nav-item {{ request()->routeIs('vendor.kalender') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i>
                <span>Kalender &amp; Jadwal</span>
            </a>

            <a href="{{ route('vendor.tracking') }}"
                class="nav-item {{ request()->routeIs('vendor.tracking') ? 'active' : '' }}">
                <i class="bi bi-journal-check"></i>
                <span>Tracking Tugas</span>
            </a>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">
                <span>AKUN VENDOR</span>
            </div>

            <a href="{{ route('vendor.profil') }}"
                class="nav-item {{ request()->routeIs('vendor.profil*') ? 'active' : '' }}">
                <i class="bi bi-person-vcard"></i>
                <span>Profil Vendor</span>
            </a>
        </div>

        <div class="sidebar-section mt-2">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-item nav-item-logout">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Keluar Sistem</span>
                </button>
            </form>
        </div>
    </nav>

    <div class="sidebar-director">
        <div class="director-header">
            <div class="eyebrow">VENDOR WORKSPACE</div>
            <span class="status-dot" title="Sistem Aktif"></span>
        </div>
        <p>Kelola jadwal dan informasi vendor Anda dalam satu ruang kerja terpadu.</p>
        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="btn btn-director w-100">
            <i class="bi bi-arrow-up-right-circle"></i>
            <span>Kunjungi Website</span>
        </a>
    </div>
</aside>
