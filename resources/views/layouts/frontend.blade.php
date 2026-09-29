<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'MyDream Organizer — Pasti Nikahmu Beda!')</title>

    <meta name="description" content="@yield('description', 'MyDream Organizer — wedding organizer Jember yang mendampingi setiap detail pernikahanmu, dari konsep hingga hari bahagia.')">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,500;0,600;0,700;1,500;1,600&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    {{-- Project CSS --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    {{-- MyDream UI --}}
    <link rel="stylesheet" href="{{ asset('css/mydream.css') }}">

    @stack('styles')
</head>

<body class="@yield('body_class')">

    <x-navbar />

    @hasSection('breadcrumb')
        @yield('breadcrumb')
    @endif

    <main>
        @yield('content')
    </main>

    <x-footer />

    <script src="{{ asset('js/app.js') }}" defer></script>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>

    {{-- MyDream JS --}}
    <script src="{{ asset('js/mydream.js') }}" defer></script>

    @stack('scripts')
</body>

</html>
