<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Wedding Organizer')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/css/admin/layout.css'])
    @stack('styles')
</head>

<body>
    <aside class="sidebar">
        <div class="sidebar-brand">Wedding Organizer</div>
        <div class="sidebar-menu">
            <small class="text-muted px-3">OVERVIEW</small>
            <div class="mt-2">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
            </div>

            <small class="text-muted px-3 d-block mt-4">OPERASIONAL</small>
            <div class="mt-2">
                <a href="{{ route('admin.event_day.index') }}" class="{{ request()->routeIs('admin.event_day.*') || request()->routeIs('admin.event.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-event me-2"></i>Event Day
                </a>
                <a href="{{ route('admin.calendar') }}" class="{{ request()->routeIs('admin.calendar') || request()->routeIs('admin.events.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar3 me-2"></i>Calendar
                </a>
                <a href="{{ route('admin.packages.index') }}" class="{{ request()->routeIs('admin.packages.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam me-2"></i>Paket Wedding
                </a>
                <a href="{{ route('admin.vendors.index') }}" class="{{ request()->routeIs('admin.vendors.*') ? 'active' : '' }}">
                    <i class="bi bi-shop me-2"></i>Vendor
                </a>
                <a href="{{ route('admin.tracking.index') }}" class="{{ request()->routeIs('admin.tracking.*') ? 'active' : '' }}">
                    <i class="bi bi-shop me-2"></i>Tracking Acara Client
                </a>
            </div>

            <small class="text-muted px-3 d-block mt-4">DATA MASTER</small>
            <div class="mt-2">
                <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="bi bi-tags me-2"></i>Kategori
                </a>
                <a href="{{ route('admin.members.index') }}" class="{{ request()->routeIs('admin.members.*') ? 'active' : '' }}">
                    <i class="bi bi-people me-2"></i>Anggota Tim
                </a>
            </div>
        </div>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <div><strong>@yield('page-title', 'Dashboard')</strong></div>
            <div><span class="text-muted">Admin</span></div>
        </div>
        <div class="content">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
