<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'MyDream Organizer — Pasti Nikahmu BEDA!')</title>
    <meta name="description" content="@yield('description', 'MyDream Organizer — wedding organizer Jember. Pasti nikahmu beda!')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400;1,500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/mydream.css') }}" rel="stylesheet">
    @stack('head')
</head>
<body class="@yield('body_class')">

    @include('mydream.partials.navbar', ['withCategories' => View::hasSection('with_categories')])

    <main>
        @yield('content')
    </main>

    @include('mydream.partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/mydream.js') }}"></script>
    @stack('scripts')
</body>
</html>
