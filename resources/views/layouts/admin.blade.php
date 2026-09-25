<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Wedding Organizer')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @vite('resources/css/admin/layout.css')
    <link rel="stylesheet" href="{{ asset('css/admin-layout.css') }}">
    @stack('styles')
</head>

<body class="admin-layout">
    <div class="dashboard-shell">
        <aside class="sidebar" id="sidebar">
            <div class="brand-block">
                <div class="brand-mark">◉</div>
                <div>
                    <div class="brand-name">WO PROJECT</div>
                    <div class="brand-subtitle">ADMIN MYDREAM</div>
                </div>
            </div>

            @auth
                <div class="client-card">
                    <div class="client-card-top">
                        <span>Administrator</span>
                        <strong><i class="bi bi-shield-check"></i></strong>
                    </div>
                    <div class="client-name">{{ auth()->user()->name }}</div>
                </div>
            @endauth

            <nav class="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i>
                    <span>Dashboard Admin</span>
                </a>

                <a href="{{ route('admin.event_day.index') }}" class="nav-item {{ request()->routeIs('admin.event_day.*') || request()->routeIs('admin.event.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-event"></i>
                    <span>Event Day</span>
                </a>
                <a href="{{ route('admin.calendar') }}" class="nav-item {{ request()->routeIs('admin.calendar') || request()->routeIs('admin.events.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar3"></i>
                    <span>Calendar</span>
                </a>
                <a href="{{ route('admin.tracking.index') }}" class="nav-item {{ request()->routeIs('admin.tracking.*') ? 'active' : '' }}">
                    <i class="bi bi-activity"></i>
                    <span>Tracking Acara Client</span>
                </a>
                <a href="{{ route('admin.organizer-event.index') }}" class="nav-item {{ request()->routeIs('admin.organizer-event.*') ? 'active' : '' }}">
                    <i class="bi bi-diagram-3"></i>
                    <span>Organizer Event</span>
                </a>

                <a href="{{ route('admin.packages.index') }}" class="nav-item {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i>
                    <span>Paket Wedding</span>
                </a>
                <a href="{{ route('admin.vendors.index') }}" class="nav-item {{ request()->routeIs('admin.vendors.*') ? 'active' : '' }}">
                    <i class="bi bi-shop"></i>
                    <span>Vendor</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i>
                    <span>Kategori</span>
                </a>
                <a href="{{ route('admin.members.index') }}" class="nav-item {{ request()->routeIs('admin.members.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Anggota Tim</span>
                </a>

                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="nav-item" style="width: 100%; text-align: left; background: none; border: none; cursor: pointer;">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </nav>

            <div class="sidebar-director">
                <div class="eyebrow">ADMIN CONTROL CENTER</div>
                <p>Kelola seluruh operasional Wedding Organizer dari satu ruang kerja.</p>
                <a href="{{ route('home') }}" class="btn btn-director w-100">
                    <i class="bi bi-arrow-up-right-circle"></i>
                    Lihat Website
                </a>
            </div>
        </aside>

        <main class="admin-main">
            <div class="admin-content">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
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
