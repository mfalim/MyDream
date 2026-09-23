@php
    $kategoriNav = [
        'Semua Vendor', 'Venue & Ballroom', 'Fotografi & Videografi', 'Gaun & Busana Pengantin',
        'Makeup & Hair', 'Dekorasi & Lighting', 'Katering & Kue', 'Wedding Organizer', 'Perhiasan & Cincin',
    ];
@endphp

<header class="navbar">
    <div class="container navbar-top">
        <a href="{{ url('/') }}" class="navbar-logo">MyDream</a>

        <div class="navbar-search hide-mobile">
            <span>Semua Vendor</span>
            <span class="sep">|</span>
            <span>Indonesia (Semua)</span>
            <input type="text" placeholder="searching">
            <button type="button" aria-label="Cari">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-3.8-3.8" stroke-linecap="round"/></svg>
            </button>
        </div>

        <div class="navbar-actions">
            <span class="navbar-currency hide-mobile">Mata Uang: IDR (Rp)</span>
            <a href="#" class="text-link strong hide-mobile">Daftar Sebagai User</a>

            <a href="{{ route('cart.index') ?? '#' }}" class="navbar-icon" aria-label="Wishlist">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                <span class="count">2</span>
            </a>
            <a href="#" class="navbar-icon" aria-label="Notifikasi">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M14.857 17.082a23.85 23.85 0 005.454-1.31A8.97 8.97 0 0118 9.75V9A6 6 0 006 9v.75a8.97 8.97 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.26 24.26 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                <span class="count">1</span>
            </a>
            <span style="width:34px;height:34px;border-radius:50%;background:var(--ink);display:inline-flex;align-items:center;justify-content:center;color:#F4EEE2;font-size:.74rem;font-weight:600;">MD</span>
        </div>
    </div>

    <nav class="navbar-links">
        <div class="container row">
            <a href="#">Cari Vendor</a>
            <a href="{{ route('inspiration.index') ?? '#' }}">Inspirasi Pernikahan</a>
            <a href="{{ route('catalog.index') ?? '#' }}">Paket &amp; Promo</a>
            <a href="#">MyDream Pay</a>
            <a href="{{ route('blog.index') ?? '#' }}">Artikel &amp; Blog</a>
            <a href="{{ route('event.index') ?? '#' }}">Wedding Fair</a>
        </div>
        <div class="container row categories">
            @foreach ($kategoriNav as $kat)
                <a href="{{ route('vendor.index') ?? '#' }}">{{ $kat }}</a>
            @endforeach
        </div>
    </nav>
</header>
