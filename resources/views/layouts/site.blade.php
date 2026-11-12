<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('mydream.name'))</title>
    <meta name="description" content="@yield('description', 'MyDream Wedding Organizer - pendampingan pernikahan di Jember dan Surabaya.')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/site.css'])
    @stack('head')
</head>
<body class="flex min-h-screen flex-col">

    <x-site.navbar />

    @if (session('success'))
        <div class="bg-emerald/10 px-4 py-3 text-center text-sm text-emerald">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-maroon-light px-4 py-3 text-center text-sm text-maroon">{{ session('error') }}</div>
    @endif

    <main class="flex-1">
        @yield('content')
    </main>

    <x-site.footer />

    @stack('scripts')
</body>
</html>
