<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('mydream.name'))</title>
    <meta name="description" content="@yield('description', 'MyDream Wedding Organizer - pendampingan pernikahan di Jember dan Surabaya.')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script>document.documentElement.classList.add('js')</script>
    @vite(['resources/css/site.css'])
    @stack('head')
</head>
<body class="flex min-h-screen flex-col">

    <div id="scroll-progress" aria-hidden="true"></div>

    <x-site.navbar />

    @if (session('success'))
        <div data-flash="success" class="bg-emerald/10 px-4 py-3 text-center text-sm text-emerald">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div data-flash="error" class="bg-maroon-light px-4 py-3 text-center text-sm text-maroon">{{ session('error') }}</div>
    @endif

    <main class="flex-1">
        @yield('content')
    </main>

    <x-site.footer />

    <div id="toasts" aria-live="polite"></div>

    <button type="button" id="to-top" aria-label="Kembali ke atas">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" /></svg>
    </button>

    <script src="{{ asset('js/site.js') }}?v={{ @filemtime(public_path('js/site.js')) }}" defer></script>
    @stack('scripts')
</body>
</html>
