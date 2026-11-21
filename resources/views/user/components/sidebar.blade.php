<aside class="sidebar" id="sidebar">

    {{-- BRAND --}}
    <div class="brand-block">
        <div class="brand-block-left">
            <div class="brand-mark"><i class="bi bi-stars"></i></div>

            <div>
                <div class="brand-name">MyDream</div>
                <div class="brand-subtitle">Client Atelier</div>
            </div>
        </div>

        <button type="button" class="sidebar-close-btn" id="sidebarClose" aria-label="Tutup menu">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    {{-- CLIENT --}}
    @auth
        <div class="client-card">
            <div class="client-card-top">
                <span>{{ auth()->user()->role === 'client' ? 'Client' : 'User' }}</span>
                <strong>{{ auth()->user()->name }}</strong>
            </div>

            <div class="client-name" title="{{ auth()->user()->email }}">
                {{ auth()->user()->email }}
            </div>
        </div>
    @endauth

    {{-- NAVIGATION --}}
    <nav class="sidebar-nav">
        <a href="{{ route('user.dashboard') }}"
            class="nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i>
            <span>Dashboard Pernikahan</span>
        </a>

        <a href="{{ route('user.vendor-confirmation') }}"
            class="nav-item {{ request()->routeIs('user.vendor-confirmation') ? 'active' : '' }}">
            <i class="bi bi-patch-check"></i>
            <span>Status &amp; Jadwal Vendor</span>
        </a>

        <a href="{{ route('user.explore-vendor') }}"
            class="nav-item {{ request()->routeIs('user.explore-vendor') ? 'active' : '' }}">
            <i class="bi bi-shop"></i>
            <span>Eksplor &amp; Tambah Vendor</span>
        </a>

        <a href="{{ route('user.rundown') }}"
            class="nav-item {{ request()->routeIs('user.rundown*') ? 'active' : '' }}">
            <i class="bi bi-calendar-event"></i>
            <span>Rundown Timeline</span>
        </a>

        <a href="{{ route('user.reviews.index') }}"
            class="nav-item {{ request()->routeIs('user.reviews.*') ? 'active' : '' }}">
            <i class="bi bi-star"></i>
            <span>Ulasan</span>
        </a>

        <a href="{{ route('user.history.index') }}"
            class="nav-item {{ request()->routeIs('user.history.*') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i>
            <span>Riwayat Pembelian</span>
        </a>

        <div class="sidebar-divider"></div>

        <a href="{{ url('/') }}" class="nav-item">
            <i class="bi bi-house"></i>
            <span>Kembali ke Beranda</span>
        </a>

        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="nav-item nav-item-logout">
                <i class="bi bi-box-arrow-right"></i>
                <span>Keluar</span>
            </button>
        </form>
    </nav>

    {{-- WEDDING DIRECTOR --}}
    <div class="sidebar-director">
        <div class="eyebrow">
            Wedding Concierge
        </div>

        <p>
            Hubungi wedding planner dedicated Anda kapan pun untuk konsultasi persiapan acara.
        </p>

        <a href="{{ url('/#kontak') }}" class="btn btn-director w-100">
            <i class="bi bi-chat-left-text"></i>
            Hubungi Tim Kami
        </a>
    </div>

</aside>
