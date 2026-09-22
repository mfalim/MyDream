<aside class="sidebar" id="sidebar">

    {{-- BRAND --}}
    <div class="brand-block">
        <div class="brand-mark">◉</div>

        <div>
            <div class="brand-name">WO PROJECT</div>
            <div class="brand-subtitle">CLIENT ATELIER</div>
        </div>
    </div>

    {{-- CLIENT --}}
    <div class="client-card">
        <div class="client-card-top">
            <span>Client ID</span>
            <strong>#WO-2025-081</strong>
        </div>

        <div class="client-name">
            Aditya &amp; Sarah
        </div>
    </div>

    {{-- NAVIGATION --}}
    <nav class="sidebar-nav">

        {{-- DASHBOARD --}}
        <a
            href="{{ route('user.dashboard') }}"
            class="nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}"
        >
            <i class="bi bi-grid-1x2"></i>
            <span>Dashboard Pernikahan</span>
        </a>

        {{-- VENDOR --}}
        <a href="{{ route('user.vendor-confirmation') }}"
        class="nav-item {{ request()->routeIs('user.vendor-confirmation') ? 'active' : '' }}">
            <i class="bi bi-patch-check"></i>
            <span>Konfirmasi Vendor</span>
        </a>

        {{-- EKSPLOR VENDOR --}}
        <a href="{{ route('user.explore-vendor') }}"
        class="nav-item {{ request()->routeIs('user.explore-vendor') ? 'active' : '' }}">

            <i class="bi bi-shop"></i>

            <span>Eksplor & Tambah Vendor</span>

        </a>

        {{-- RUNDOWN --}}
        <a href="{{ route('user.rundown') }}"
        class="nav-item {{ request()->routeIs('user.rundown') ? 'active' : '' }}">

            <i class="bi bi-shop"></i>

            <span>Eksplor & Tambah Vendor</span>

        </a>

        {{-- ANGGARAN --}}
        <a
            href="#"
            class="nav-item"
        >
            <i class="bi bi-wallet2"></i>
            <span>Anggaran &amp; Pembayaran</span>
        </a>

        {{-- TAMU --}}
        <a
            href="#"
            class="nav-item"
        >
            <i class="bi bi-people"></i>
            <span>Tamu &amp; Meja VIP</span>
        </a>

    </nav>

    {{-- WEDDING DIRECTOR --}}
    <div class="sidebar-director">

        <div class="eyebrow">
            WEDDING DIRECTOR
        </div>

        <p>
            Hubungi wedding planner dedicated Anda kapan pun.
        </p>

        <button class="btn btn-director w-100">
            <i class="bi bi-chat-left-text"></i>
            Chat Director
        </button>

    </div>

</aside>
