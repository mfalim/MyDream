<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MyDream Wedding')</title>

    {{-- Bootstrap --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    {{-- Bootstrap Icons --}}
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    {{-- Google Fonts (Selaras dengan Landing Page MyDream: Fraunces + Plus Jakarta Sans) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    {{-- Dashboard Shell CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    @stack('styles')
</head>

<body>
    <x-login-curtain subtitle="Client Portal" />

    {{-- MOBILE TOPBAR (Hanya tampil di layar <= 992px) --}}
    <header class="mobile-topbar">
        <button type="button" class="mobile-menu-btn" id="sidebarToggle" aria-label="Buka menu navigasi">
            <i class="bi bi-list"></i>
        </button>

        <a href="{{ route('user.dashboard') }}" class="mobile-brand">
            <span class="mobile-brand-mark"><i class="bi bi-stars"></i></span>
            <span>
                <strong>MyDream</strong>
                <small>Client Atelier</small>
            </span>
        </a>

        <a href="{{ url('/') }}" class="mobile-home-btn" aria-label="Ke Beranda" title="Beranda">
            <i class="bi bi-house"></i>
        </a>
    </header>

    <div class="dashboard-shell">

        {{-- SIDEBAR --}}
        @include('user.components.sidebar')

        {{-- CONTENT --}}
        @yield('content')

    </div>

    {{-- SIDEBAR OVERLAY --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Dashboard JS --}}
    <script src="{{ asset('js/dashboard.js') }}"></script>

    @stack('scripts')

</body>

</html>
