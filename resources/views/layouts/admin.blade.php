<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Wedding Organizer')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

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

        /* =========================
   VENDOR PAGE
========================= */

.vendor-page {
    max-width: 1400px;
    margin: 0 auto;
}

/* Header */

.vendor-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.vendor-header h2 {
    margin: 0 0 6px;
    font-size: 28px;
    font-weight: 700;
    color: #173f39;
}

.vendor-header p {
    margin: 0;
    color: #71817d;
    font-size: 14px;
}

/* Button */

.btn-add-vendor {
    display: inline-flex;
    align-items: center;
    padding: 11px 18px;
    border-radius: 8px;
    background: #073d36;
    color: white;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    border: none;
}

.btn-add-vendor:hover {
    background: #0b5148;
    color: white;
}

/* =========================
   VENDOR ACTIONS
========================= */

.btn-whatsapp {
    display: block;
    text-align: center;
    background: #e8f3ef;
    color: #087f5b;
    text-decoration: none;
    padding: 9px;
    border-radius: 7px;
    font-size: 12px;
    font-weight: 600;
    margin-top: 8px;
}

.btn-whatsapp:hover {
    background: #dceee8;
    color: #087f5b;
}

.vendor-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-top: 8px;
}

.vendor-actions form {
    margin: 0;
}

.btn-edit-vendor,
.btn-delete-vendor {
    width: 100%;
    display: block;
    text-align: center;
    padding: 8px;
    border-radius: 7px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
}

.btn-edit-vendor {
    background: white;
    border: 1px solid #d4e0dc;
    color: #315d55;
}

.btn-edit-vendor:hover {
    background: #f1f6f4;
    color: #315d55;
}

.btn-delete-vendor {
    background: white;
    border: 1px solid #f0caca;
    color: #c94a4a;
}

.btn-delete-vendor:hover {
    background: #fff4f4;
    color: #c94a4a;
}

/* Toolbar */

.vendor-toolbar {
    display: flex;
    gap: 12px;
    margin-bottom: 14px;
}

.vendor-search {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 10px;
    background: white;
    border: 1px solid #e4ebe8;
    border-radius: 8px;
    padding: 0 14px;
}

.vendor-search span {
    color: #83918e;
    font-size: 18px;
}

.vendor-search input {
    width: 100%;
    border: none;
    outline: none;
    padding: 12px 0;
    font-size: 14px;
}

.vendor-filter {
    min-width: 180px;
    border: 1px solid #e4ebe8;
    border-radius: 8px;
    background: white;
    padding: 0 14px;
    color: #536460;
    outline: none;
}

/* Category */

.category-filter {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    margin-bottom: 20px;
}

.category-btn {
    display: inline-block;
    text-decoration: none;
    white-space: nowrap;
    border: 1px solid #dfe8e5;
    background: white;
    color: #63736f;
    border-radius: 20px;
    padding: 8px 14px;
    font-size: 13px;
    cursor: pointer;
}

.btn-reset-filter {
    display: inline-flex;
    align-items: center;
    padding: 0 14px;
    border: 1px solid #dfe8e5;
    border-radius: 8px;
    color: #63736f;
    background: white;
    text-decoration: none;
    font-size: 13px;
}

.btn-reset-filter:hover {
    color: #073d36;
    border-color: #073d36;
}

.vendor-result-count {
    color: #71817d;
    font-size: 13px;
    margin-bottom: 12px;
}

.vendor-detail-card {
    display: grid;
    grid-template-columns: minmax(260px, 380px) 1fr;
    gap: 28px;
    background: white;
    border: 1px solid #e4ebe8;
    border-radius: 10px;
    padding: 20px;
}

.vendor-detail-image {
    min-height: 280px;
    background: #edf3f1;
    border-radius: 8px;
    overflow: hidden;
}

.vendor-detail-image img {
    width: 100%;
    height: 100%;
    min-height: 280px;
    object-fit: cover;
}

.vendor-detail-content h3 {
    color: #173f39;
    margin: 18px 0;
}

.vendor-detail-content p {
    color: #687873;
    line-height: 1.6;
}

@media (max-width: 800px) {
    .vendor-detail-card {
        grid-template-columns: 1fr;
    }
}

.category-btn.active,
.category-btn:hover {
    background: #073d36;
    color: white;
    border-color: #073d36;
}

/* Grid */

.vendor-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 16px;
}

/* Card */

.vendor-card {
    background: white;
    border: 1px solid #e4ebe8;
    border-radius: 10px;
    overflow: hidden;
    transition: 0.2s;
}

.vendor-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(20, 50, 45, 0.08);
}

/* Image */

.vendor-image {
    height: 190px;
    position: relative;
    background: #edf3f1;
    overflow: hidden;
}

.vendor-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.vendor-no-image {
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: #899793;
}

.vendor-no-image span {
    font-size: 35px;
    margin-bottom: 5px;
}

/* Category badge */

.vendor-category {
    position: absolute;
    top: 10px;
    left: 10px;
    background: rgba(255, 255, 255, 0.92);
    color: #17463f;
    border-radius: 6px;
    padding: 5px 9px;
    font-size: 11px;
    font-weight: 600;
}

/* Card body */

.vendor-card-body {
    padding: 14px;
}

.vendor-card-body h4 {
    margin: 0 0 9px;
    color: #173f39;
    font-size: 17px;
    font-weight: 700;
}

.vendor-location,
.vendor-phone {
    color: #687873;
    font-size: 12px;
    line-height: 1.5;
    margin-bottom: 6px;
}

.vendor-description {
    color: #71817d;
    font-size: 12px;
    line-height: 1.5;
    margin: 10px 0;
}

/* Detail button */

.btn-detail-vendor {
    display: block;
    text-align: center;
    background: #edf4f1;
    color: #17463f;
    text-decoration: none;
    padding: 9px;
    border-radius: 7px;
    font-size: 12px;
    font-weight: 600;
    margin-top: 12px;
}

.btn-detail-vendor:hover {
    background: #dfece7;
    color: #17463f;
}

/* Empty */

.vendor-empty {
    grid-column: 1 / -1;
    background: white;
    border: 1px dashed #ccd9d5;
    border-radius: 10px;
    padding: 60px 20px;
    text-align: center;
}

.empty-icon {
    font-size: 40px;
    margin-bottom: 10px;
}

.vendor-empty h4 {
    color: #173f39;
    margin-bottom: 5px;
}

.vendor-empty p {
    color: #71817d;
    font-size: 14px;
    margin-bottom: 20px;
}

/* Responsive */

@media (max-width: 1100px) {
    .vendor-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

@media (max-width: 800px) {
    .vendor-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .vendor-header {
        align-items: flex-start;
        gap: 15px;
        flex-direction: column;
    }

    .vendor-toolbar {
        flex-direction: column;
    }
}

@media (max-width: 550px) {
    .vendor-grid {
        grid-template-columns: 1fr;
    }
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

                <a href="{{ route('admin.categories.index') }}"
                   class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    Kategori
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
