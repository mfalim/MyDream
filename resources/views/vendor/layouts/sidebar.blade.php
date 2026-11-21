<aside class="sidebar" id="sidebar">
    <div class="brand-block">
        <div class="brand-block-left">
            <div class="brand-mark"><i class="bi bi-gem"></i></div>
            <div>
                <div class="brand-name">MyDream</div>
                <div class="brand-subtitle">Vendor Partner</div>
            </div>
        </div>

        <button type="button" class="sidebar-close-btn" id="sidebarClose" aria-label="Tutup menu">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    @auth
        <div class="client-card">
            <div class="client-card-top">
                <span>Vendor Partner</span>
                <strong>{{ auth()->user()->vendor?->category?->name ?? 'Mitra WO' }}</strong>
            </div>
            <div class="client-name" title="{{ auth()->user()->vendor?->name ?? auth()->user()->name }}">
                {{ auth()->user()->vendor?->name ?? auth()->user()->name }}
            </div>
        </div>
    @endauth

    <nav class="sidebar-nav">
        <a href="{{ route('vendor.dashboard') }}"
            class="nav-item {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i>
            <span>Dashboard Vendor</span>
        </a>

        <a href="{{ route('vendor.kas') }}"
            class="nav-item {{ request()->routeIs('vendor.kas') ? 'active' : '' }}">
            <i class="bi bi-wallet2"></i>
            <span>Kas &amp; Pembayaran</span>
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

        <div class="sidebar-divider"></div>

        <a href="{{ route('vendor.profil') }}"
            class="nav-item {{ request()->routeIs('vendor.profil*') ? 'active' : '' }}">
            <i class="bi bi-person-vcard"></i>
            <span>Profil Vendor</span>
        </a>

        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="nav-item">
            <i class="bi bi-house"></i>
            <span>Kunjungi Website</span>
        </a>

        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="nav-item nav-item-logout">
                <i class="bi bi-box-arrow-right"></i>
                <span>Keluar Sistem</span>
            </button>
        </form>
    </nav>

    <div class="sidebar-director">
        <div class="eyebrow">Vendor Workspace</div>
        <p>Pantau jadwal acara, status tugas, dan pencairan dana vendor Anda dalam satu tempat.</p>
        <a href="{{ route('vendor.kas') }}" class="btn btn-director w-100">
            <i class="bi bi-cash-stack"></i>
            <span>Cek Kas Masuk</span>
        </a>
    </div>
</aside>
