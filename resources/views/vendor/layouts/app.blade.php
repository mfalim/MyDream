<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Vendor Workspace')</title>

    @vite('resources/css/vendor/vendor-dashboard.css')
</head>

<body>

    <div class="vendor-layout">

        @include('vendor.layouts.sidebar')

        <main class="vendor-main">
            @yield('content')
        </main>

    </div>

</body>
</html>