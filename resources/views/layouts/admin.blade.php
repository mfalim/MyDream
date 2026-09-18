<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Admin Wedding Organizer')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            background-color: #f5f7f9;
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background-color: #ffffff;
            border-right: 1px solid #e5e7eb;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
        }

        .sidebar-brand {
            height: 70px;
            display: flex;
            align-items: center;
            padding: 0 24px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 20px;
            font-weight: 600;
        }

        .sidebar-menu {
            padding: 20px 15px;
        }

        .sidebar-menu a {
            display: block;
            padding: 11px 15px;
            margin-bottom: 5px;
            color: #4b5563;
            text-decoration: none;
            border-radius: 8px;
        }

        .sidebar-menu a:hover {
            background-color: #f0fdf4;
            color: #198754;
        }

        .sidebar-menu .active {
            background-color: #eaf7ef;
            color: #198754;
        }

        .main-content {
            margin-left: 250px;
            min-height: 100vh;
        }

        .topbar {
            height: 70px;
            background-color: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
        }

        .content {
            padding: 30px;
        }
    </style>

    @stack('styles')
</head>

<body>

    <aside class="sidebar">

        <div class="sidebar-brand">
            Wedding Organizer
        </div>

        <div class="sidebar-menu">

            <small class="text-muted px-3">
                MENU
            </small>

            <div class="mt-2">

                <a href="{{ route('admin.dashboard') }}"
                   class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>

                <a href="{{ route('admin.packages.index') }}"
                   class="{{ request()->routeIs('admin.packages.*') ? 'active' : '' }}">
                    Paket Wedding
                </a>

                <a href="#">
                    Pesanan
                </a>

                <a href="#">
                    Jadwal Vendor
                </a>

                <a href="{{ route('admin.vendors.index') }}"
                   class="{{ request()->routeIs('admin.vendors.*') ? 'active' : '' }}">
                    Vendor
                </a>

            </div>

        </div>

    </aside>


    <main class="main-content">

        <div class="topbar">

            <div>
                <strong>
                    @yield('page-title', 'Dashboard')
                </strong>
            </div>

            <div>
                <span class="text-muted">
                    Admin
                </span>
            </div>

        </div>


        <div class="content">

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

    @stack('scripts')

</body>

</html>
