
<aside class="vendor-sidebar">

    <div class="vendor-brand">
        <div class="brand-dot">•</div>

        <div>
            <div class="brand-label">VENDOR WORKSPACE</div>
            <h2>Lotus Atelier</h2>
            <p>MITRA VENDOR PORTAL</p>
        </div>
    </div>

    <nav class="vendor-nav">

        <a href="{{ route('vendor.dashboard') }}"
           class="vendor-nav-item {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
            <span class="nav-icon">📊</span>
            <span>Dashboard Vendor</span>
        </a>

        <a href="{{ route('vendor.kalender') }}"
           class="vendor-nav-item {{ request()->routeIs('vendor.kalender') ? 'active' : '' }}">
            <span class="nav-icon">📅</span>
            <span>Kalender & Jadwal Acara</span>
        </a>

        <a href="{{ route('vendor.tracking') }}"
           class="vendor-nav-item {{ request()->routeIs('vendor.tracking') ? 'active' : '' }}">
            <span class="nav-icon">🔍</span>
            <span>Tracking Tugas</span>
        </a>

        <a href="{{ route('vendor.profil') }}"
           class="vendor-nav-item {{ request()->routeIs('vendor.profil*') ? 'active' : '' }}">
            <span class="nav-icon">👤</span>
            <span>Profil Vendor</span>
        </a>

    </nav>

    <div class="vendor-sidebar-bottom">
        <div class="duty-label">
            VENDOR WORKSPACE
        </div>

        <strong>Vendor Partner</strong>
        <small>MyDream Wedding Organizer</small>
    </div>

</aside>
