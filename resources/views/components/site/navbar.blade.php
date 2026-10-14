@php
    $cart = session('cart', []);
    $cartCount = ($cart['type'] ?? null) === 'package' ? 1 : count($cart['vendor_ids'] ?? []);

    $links = [
        ['Beranda', route('home'), request()->routeIs('home')],
        ['Store', route('store'), request()->routeIs('store')],
        ['Paket & Vendor', route('catalog.index'), request()->routeIs('catalog.*', 'vendor.*')],
        ['Inspirasi', route('inspiration.index'), request()->routeIs('inspiration.*')],
        ['Event', route('event.index'), request()->routeIs('event.*')],
        ['Artikel', route('blog.index'), request()->routeIs('blog.*')],
        ['Tentang', route('about'), request()->routeIs('about')],
    ];
@endphp

<header class="sticky top-0 z-50 border-b border-black/5 bg-cream/95 backdrop-blur">
    <div class="mx-auto flex h-20 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">

        <a href="{{ route('home') }}" class="shrink-0 font-display text-2xl font-semibold text-maroon">MyDream</a>

        <nav class="hidden items-center gap-6 text-sm font-medium lg:flex">
            @foreach ($links as [$label, $url, $active])
                <a href="{{ $url }}" class="transition-colors hover:text-maroon {{ $active ? 'text-maroon' : 'text-ink/80' }}">{{ $label }}</a>
            @endforeach
        </nav>

        <div class="flex items-center gap-3">
            <form action="{{ route('catalog.index') }}" method="GET" class="hidden items-center rounded-full border border-black/10 bg-white px-4 py-1.5 md:flex">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari paket atau vendor" aria-label="Cari" class="w-40 bg-transparent text-sm placeholder:text-ink/40 focus:outline-none">
                <button type="submit" aria-label="Cari" class="text-ink/50 hover:text-maroon">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M18 10.5a7.5 7.5 0 11-15 0 7.5 7.5 0 0115 0z" /></svg>
                </button>
            </form>

            <a href="{{ route('cart.index') }}" aria-label="Keranjang" class="relative p-2 text-ink/70 hover:text-maroon">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.25-4.59 3.375-6.885a1.125 1.125 0 00-1.007-1.615H5.106M7.5 14.25L5.106 5.653M7.5 14.25L4.5 4.395" /></svg>
                @if ($cartCount > 0)
                    <span class="absolute -right-0.5 -top-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-maroon text-[10px] font-semibold text-white">{{ $cartCount }}</span>
                @endif
            </a>

            <div class="hidden items-center gap-2 lg:flex">
                @auth
                    @php
                        $dashboard = match (auth()->user()->role) {
                            'admin' => route('admin.dashboard'),
                            'member' => route('member.dashboard'),
                            default => route('user.dashboard'),
                        };
                    @endphp
                    <x-site.button :href="$dashboard" variant="outline" size="sm">{{ \Illuminate\Support\Str::limit(auth()->user()->name, 14) }}</x-site.button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-site.button type="submit" variant="ghost" size="sm">Keluar</x-site.button>
                    </form>
                @else
                    <x-site.button :href="route('login')" size="sm">Masuk</x-site.button>
                @endauth
            </div>

            <label for="site-menu-toggle" class="cursor-pointer p-2 text-ink lg:hidden" aria-label="Menu">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" /></svg>
            </label>
        </div>
    </div>

    {{-- Menu mobile: checkbox murni CSS, tanpa JavaScript --}}
    <input type="checkbox" id="site-menu-toggle" class="peer hidden">
    <nav class="hidden flex-col gap-1 border-t border-black/5 bg-cream px-4 py-4 peer-checked:flex lg:hidden">
        @foreach ($links as [$label, $url, $active])
            <a href="{{ $url }}" class="rounded-lg px-3 py-2 hover:bg-maroon-light {{ $active ? 'font-semibold text-maroon' : '' }}">{{ $label }}</a>
        @endforeach
        <div class="mt-2 flex gap-3">
            @auth
                <x-site.button :href="$dashboard" variant="outline" size="sm" class="flex-1">Dashboard</x-site.button>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <x-site.button type="submit" variant="ghost" size="sm" class="w-full">Keluar</x-site.button>
                </form>
            @else
                <x-site.button :href="route('login')" size="sm" class="flex-1">Masuk</x-site.button>
            @endauth
        </div>
    </nav>
</header>
