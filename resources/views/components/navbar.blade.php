@php
    $kategoriNav = [
        'Semua Vendor',
        'Venue & Ballroom',
        'Fotografi & Videografi',
        'Gaun & Busana Pengantin',
        'Makeup & Hair',
        'Dekorasi & Lighting',
        'Katering & Kue',
        'Wedding Organizer',
        'Perhiasan & Cincin',
    ];
@endphp

<header class="navbar">
    <div class="container navbar-top">

        <a href="{{ route('home') }}" class="navbar-logo">
            MyDream
        </a>

        <form method="GET" action="{{ route('vendor.index') }}" class="navbar-search hide-mobile">
            <span>Semua Vendor</span>
            <span class="sep">|</span>
            <span>Indonesia (Semua)</span>

            <input type="text" name="q" value="{{ request('q') }}" placeholder="searching"
                aria-label="Cari vendor">

            <button type="submit" aria-label="Cari">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <circle cx="11" cy="11" r="7" />
                    <path d="M21 21l-3.8-3.8" stroke-linecap="round" />
                </svg>
            </button>
        </form>

        <div class="navbar-actions">

            {{-- KONDISI JIKA USER BELUM LOGIN --}}
            @guest
                <a href="{{ route('login') }}" class="text-link strong hide-mobile">
                    Daftar Sebagai User
                </a>
            @endguest

            {{-- KONDISI JIKA USER SUDAH LOGIN (Opsional: Menampilkan nama atau teks lain jika mau) --}}
            @auth
                <span class="hide-mobile" style="font-size: 0.85rem; color: #555;">
                    Halo, <strong>{{ auth()->user()->name }}</strong>
                </span>
            @endauth

            {{-- Sisa kode tombol ikon keranjang, akun, dll tetap sama di bawah ini --}}
            <a href="{{ route('cart.index') }}" class="navbar-icon" aria-label="Keranjang">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.8">
                    <path d="M3 4h2l2.2 11.2a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 2-1.6L21 8H6" />
                    <circle cx="10" cy="20" r="1" />
                    <circle cx="18" cy="20" r="1" />
                </svg>
            </a>

            <a href="{{ route('user.dashboard') }}" class="navbar-icon" aria-label="Akun">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.8">
                    <circle cx="12" cy="8" r="3.2" />
                    <path d="M5.5 20c.8-3.4 3-5.2 6.5-5.2s5.7 1.8 6.5 5.2" />
                </svg>
            </a>
        </div>

    </div>

    <nav class="navbar-links">

        {{-- SECTION UTAMA --}}
        <div class="container row">

            <a href="{{ route('vendor.index') }}">
                Cari Vendor
            </a>

            <a href="{{ route('inspiration.index') }}">
                Inspirasi Pernikahan
            </a>

            <a href="{{ route('catalog.index') }}">
                Paket &amp; Promo
            </a>

            <a href="{{ route('user.checkout') }}">
                MyDream Pay
            </a>

            <a href="{{ route('blog.index') }}">
                Artikel &amp; Blog
            </a>

            <a href="{{ route('event.index') }}">
                Wedding Fair
            </a>

        </div>

        {{-- KATEGORI VENDOR --}}
        <div class="container row categories">

            @foreach ($kategoriNav as $kat)
                @if ($kat === 'Semua Vendor')
                    <a href="{{ route('vendor.index') }}">
                        {{ $kat }}
                    </a>
                @else
                    <a href="{{ route('vendor.index', ['kategori' => $kat]) }}">
                        {{ $kat }}
                    </a>
                @endif
            @endforeach

        </div>

    </nav>
</header>
