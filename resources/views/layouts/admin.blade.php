<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Wedding Organizer')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/admin/layout.css'])
    @stack('styles')
</head>

<body>
    <aside class="sidebar">
        <div class="sidebar-brand">Wedding Organizer</div>
        <div class="sidebar-menu">
            <small class="text-muted px-3">OVERVIEW</small>
            <div class="mt-2">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
            </div>

            <small class="text-muted px-3 d-block mt-4">OPERASIONAL</small>
            <div class="mt-2">
                <a href="{{ route('admin.packages.index') }}" class="{{ request()->routeIs('admin.packages.*') ? 'active' : '' }}">Paket Wedding</a>
                <a href="#">Pesanan</a>
                <a href="#">Jadwal Vendor</a>
                <a href="{{ route('admin.vendors.index') }}" class="{{ request()->routeIs('admin.vendors.*') ? 'active' : '' }}">Vendor</a>
            </div>

            <small class="text-muted px-3 d-block mt-4">DATA MASTER</small>
            <div class="mt-2">
                <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">Kategori</a>
                <a href="{{ route('admin.members.index') }}" class="{{ request()->routeIs('admin.members.*') ? 'active' : '' }}">Anggota Tim</a>
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
