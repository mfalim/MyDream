<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Vendor Workspace')</title>

    {{-- CSS LANGSUNG DARI PUBLIC --}}
    <link rel="stylesheet" href="{{ asset('css/vendor/vendor-dashboard.css') }}">
</head>

<body>

<div class="vendor-layout">

    {{-- SIDEBAR --}}
    @include('vendor.layouts.sidebar')

    {{-- AREA KANAN --}}
    <div class="vendor-content">

        {{-- TOPBAR --}}
        @include('vendor.layouts.topbar')

        {{-- CONTENT --}}
        <main class="vendor-main">
            @yield('content')
        </main>

    </div>

</div>

</body>
</html>