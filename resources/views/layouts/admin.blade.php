<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Wedding Organizer') - MyDream</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @vite('resources/css/admin/layout.css')
    @stack('styles')
</head>

<body class="admin-layout">
    <div class="dashboard-shell">
        <aside class="sidebar" id="sidebar">
            {{-- BRAND HEADER --}}
            <div class="brand-block">
                <div class="brand-mark">
                    <i class="bi bi-gem"></i>
                </div>
                <div class="brand-text">
                    <div class="brand-name">My<span>Dream</span></div>
                    <div class="brand-subtitle">WEDDING ORGANIZER</div>
                </div>
            </div>

            {{-- PROFILE BADGE --}}
            @auth
                <div class="client-card">
                    <div class="client-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="client-info">
                        <div class="client-card-top">
                            <span>Administrator</span>
                            <i class="bi bi-patch-check-fill text-gold" title="Akses Administrator Terverifikasi"></i>
                        </div>
                        <div class="client-name">{{ auth()->user()->name }}</div>
                    </div>
                </div>
            @endauth

            {{-- NAV ITEMS --}}
            <nav class="sidebar-nav">

                {{-- OVERVIEW --}}
                <div class="sidebar-section">
                    <div class="sidebar-section-title">
                        <span>OVERVIEW</span>
                    </div>

                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2"></i>
                        <span>Dashboard Admin</span>
                    </a>
                </div>

                {{-- OPERASIONAL --}}
                <div class="sidebar-section">
                    <div class="sidebar-section-title">
                        <span>OPERASIONAL</span>
                    </div>

                    <a href="{{ route('admin.events.index') }}"
                        class="nav-item {{ request()->routeIs('admin.events.index', 'admin.events.create', 'admin.events.store', 'admin.events.show', 'admin.events.edit', 'admin.events.update') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check"></i>
                        <span>Event Day (Hari H)</span>
                    </a>

                    <a href="{{ route('admin.calendar') }}"
                        class="nav-item {{ request()->routeIs('admin.calendar', 'admin.events.by-date', 'admin.events.by-month', 'admin.events.detail') ? 'active' : '' }}">
                        <i class="bi bi-calendar3"></i>
                        <span>Master Kalender</span>
                    </a>

                    <a href="{{ route('admin.tracking.index') }}"
                        class="nav-item {{ request()->routeIs('admin.tracking.*') ? 'active' : '' }}">
                        <i class="bi bi-journal-check"></i>
                        <span>Tracking Booking</span>
                    </a>

                    <a href="{{ route('admin.payroll.index') }}"
                        class="nav-item {{ request()->routeIs('admin.payroll.*') ? 'active' : '' }}">
                        <i class="bi bi-cash-stack"></i>
                        <span>Honor &amp; Pencairan</span>
                    </a>

                    <a href="{{ route('admin.organizer-event.index') }}"
                        class="nav-item {{ request()->routeIs('admin.organizer-event.*') ? 'active' : '' }}">
                        <i class="bi bi-diagram-3"></i>
                        <span>Agenda Internal WO</span>
                    </a>
                </div>

                {{-- KONTEN WEBSITE --}}
                <div class="sidebar-section">
                    <div class="sidebar-section-title">
                        <span>KONTEN WEBSITE</span>
                    </div>

                    <a href="{{ route('admin.public-events.index') }}"
                        class="nav-item {{ request()->routeIs('admin.public-events.*') ? 'active' : '' }}">
                        <i class="bi bi-megaphone"></i>
                        <span>Event Publik</span>
                    </a>

                    <a href="{{ route('admin.articles.index') }}"
                        class="nav-item {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}">
                        <i class="bi bi-journal-text"></i>
                        <span>Artikel &amp; Blog</span>
                    </a>
                </div>

                {{-- DATA MASTER --}}
                <div class="sidebar-section">
                    <div class="sidebar-section-title">
                        <span>DATA MASTER</span>
                    </div>

                    <a href="{{ route('admin.packages.index') }}"
                        class="nav-item {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i>
                        <span>Paket Wedding</span>
                    </a>

                    <a href="{{ route('admin.vendors.index') }}"
                        class="nav-item {{ request()->routeIs('admin.vendors.*') ? 'active' : '' }}">
                        <i class="bi bi-shop"></i>
                        <span>Vendor Rekanan</span>
                    </a>

                    <a href="{{ route('admin.categories.index') }}"
                        class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i>
                        <span>Kategori Vendor</span>
                    </a>

                    <a href="{{ route('admin.members.index') }}"
                        class="nav-item {{ request()->routeIs('admin.members.*') || request()->routeIs('admin.user-members.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>Anggota Tim / Kru</span>
                    </a>

                    <a href="{{ route('admin.member-categories.index') }}"
                        class="nav-item {{ request()->routeIs('admin.member-categories.*') ? 'active' : '' }}">
                        <i class="bi bi-diagram-3-fill"></i>
                        <span>Kategori Member</span>
                    </a>
                </div>

                {{-- KELUAR --}}
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

            {{-- BOTTOM DIRECTOR CARD --}}
            <div class="sidebar-director">
                <div class="director-header">
                    <div class="eyebrow">ADMIN CONTROL CENTER</div>
                    <span class="status-dot" title="Sistem Aktif & Siap"></span>
                </div>
                <p>Kelola seluruh operasional Wedding Organizer dari satu ruang kerja terpadu.</p>
                <a href="{{ route('home') }}" target="_blank" class="btn btn-director w-100">
                    <i class="bi bi-arrow-up-right-circle"></i>
                    <span>Kunjungi Website</span>
                </a>
            </div>
        </aside>

        <main class="admin-main">
            <div class="admin-content">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('info'))
                    <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                        <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (isset($errors) && $errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                        <ul class="mb-0 d-inline-block ps-2 align-middle">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    @stack('scripts')
</body>

</html>
