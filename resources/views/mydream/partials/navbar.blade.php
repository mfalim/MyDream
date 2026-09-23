@php($categories = \App\MyDream\Catalog::categories())

<header class="md-header">
    <div class="container-xxl md-header__top">
        <a href="{{ route('mydream.home') }}" class="md-brand">
            <span class="md-brand__logo"></span>
            <span class="md-brand__name">MyDream</span>
        </a>

        <form action="{{ route('mydream.store') }}" method="get" class="md-search" role="search">
            <label class="md-search__seg d-none d-md-flex">
                <i class="bi bi-shop"></i>
                <select name="kategori" aria-label="Kategori">
                    <option value="">Semua Vendor</option>
                    @foreach ($categories as $slug => $label)
                        <option value="{{ $slug }}" @selected(request('kategori') === $slug)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <span class="md-search__seg d-none d-lg-flex"><i class="bi bi-geo-alt"></i> Indonesia (Semua)</span>
            <label class="md-search__seg md-search__seg--grow">
                <i class="bi bi-search d-md-none"></i>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari vendor atau paket…" aria-label="Cari">
            </label>
            <button class="md-search__btn" type="submit" aria-label="Cari"><i class="bi bi-search"></i></button>
        </form>

        <div class="md-header__actions">
            @guest
                <a href="{{ route('login') }}" class="md-header__register d-none d-lg-inline">Masuk / Daftar</a>
                <a href="{{ route('login') }}" class="md-icon-btn" aria-label="Login"><i class="bi bi-person-circle"></i></a>
            @else
                <a href="#" class="md-icon-btn" aria-label="Favorit"><i class="bi bi-heart"></i><span class="md-dot">4</span></a>
                <a href="#" class="md-icon-btn" aria-label="Notifikasi"><i class="bi bi-bell"></i></a>
                <a href="{{ route('user.dashboard') }}" class="md-avatar" aria-label="Dashboard"></a>
            @endguest
        </div>
    </div>

    @if ($withCategories)
        <div class="container-xxl md-header__menu d-none d-lg-flex">
            <nav class="md-header__links">
                <a href="{{ route('mydream.store') }}" class="is-active">Cari Vendor</a>
                <a href="{{ route('mydream.store', ['kategori' => 'paket']) }}">Paket & Promo</a>
                <a href="#">Inspirasi Pernikahan</a>
                <a href="#">MyDream Pay</a>
                <a href="#">Artikel & Blog</a>
            </nav>
            <span class="md-header__currency">Mata Uang: <strong>IDR (Rp)</strong></span>
        </div>
        <div class="container-xxl md-pills">
            @foreach (collect($categories)->except('paket') as $slug => $label)
                <a href="{{ route('mydream.store', ['kategori' => $slug]) }}"
                   class="md-pill @if (request('kategori') === $slug) is-active @endif">{{ $label }}</a>
            @endforeach
        </div>
    @endif
</header>
