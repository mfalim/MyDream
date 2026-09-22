<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MyDream Organizer — Pasti Nikahmu Beda!')</title>
    <meta name="description" content="@yield('description', 'MyDream Organizer — wedding organizer Jember yang mendampingi setiap detail pernikahanmu, dari konsep hingga hari bahagia.')">

    {{-- Font: Fraunces (judul, editorial-serif) + Inter (body/UI) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,500;0,600;0,700;1,500;1,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- CSS polos milik sendiri — tidak memakai framework --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')
</head>
<body>

    <x-navbar />

    @hasSection('breadcrumb')
        @yield('breadcrumb')
    @endif

    <main>
        @yield('content')
    </main>

    <x-footer />

    <script src="{{ asset('js/app.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
