@extends('layouts.site')

@section('title', 'Katalog Paket & Vendor - MyDream Wedding Organizer')
@section('description', 'Cari dan filter seluruh paket dan vendor MyDream Wedding Organizer di Jember dan Surabaya.')

@section('content')

    <section class="border-b border-black/5 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <p class="text-xs font-semibold uppercase tracking-widest text-emerald">Pencarian Lengkap</p>
            <h1 class="mt-2 font-display text-4xl font-semibold">Katalog Paket &amp; Vendor</h1>
            <p class="mt-3 max-w-2xl text-ink/60">Gunakan filter untuk menemukan paket atau vendor yang sesuai kebutuhanmu.</p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-4">

            {{-- FILTER --}}
            <form action="{{ route('catalog.index') }}" method="GET" class="h-fit space-y-6 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                <div class="flex items-center justify-between">
                    <h2 class="font-display text-base font-semibold">Filter</h2>
                    <a href="{{ route('catalog.index') }}" class="text-xs font-medium text-maroon hover:underline">Reset</a>
                </div>

                <div>
                    <label for="q" class="text-xs font-semibold uppercase tracking-wide text-ink/50">Kata kunci</label>
                    <input id="q" type="text" name="q" value="{{ $filters['q'] }}" placeholder="Nama paket atau vendor" class="mt-2 w-full rounded-xl border border-black/10 px-4 py-2 text-sm focus:border-maroon focus:outline-none">
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-ink/50">Tipe</p>
                    <div class="mt-2 space-y-2 text-sm text-ink/70">
                        @foreach (['all' => 'Semua', 'package' => 'Paket', 'vendor' => 'Vendor'] as $value => $label)
                            <label class="flex items-center gap-2">
                                <input type="radio" name="type" value="{{ $value }}" @checked($filters['type'] === $value) class="accent-maroon">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-ink/50">Lokasi</p>
                    <div class="mt-2 space-y-2 text-sm text-ink/70">
                        <label class="flex items-center gap-2">
                            <input type="radio" name="city" value="" @checked(! $filters['city']) class="accent-maroon">
                            Semua Lokasi
                        </label>
                        @foreach (config('mydream.cities') as $city)
                            <label class="flex items-center gap-2">
                                <input type="radio" name="city" value="{{ $city }}" @checked($filters['city'] === $city) class="accent-maroon">
                                {{ $city }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label for="category" class="text-xs font-semibold uppercase tracking-wide text-ink/50">Kategori vendor</label>
                    <select id="category" name="category" class="mt-2 w-full rounded-xl border border-black/10 bg-white px-4 py-2 text-sm focus:border-maroon focus:outline-none">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((int) $filters["category"] === (int) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-ink/50">Rentang harga</p>
                    <div class="mt-2 space-y-2 text-sm text-ink/70">
                        <label class="flex items-center gap-2">
                            <input type="radio" name="price" value="" @checked(! $filters['price']) class="accent-maroon">
                            Semua Harga
                        </label>
                        @foreach ($priceBrackets as $key => $label)
                            <label class="flex items-center gap-2">
                                <input type="radio" name="price" value="{{ $key }}" @checked($filters['price'] === $key) class="accent-maroon">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <x-site.button type="submit" class="w-full">Terapkan Filter</x-site.button>
            </form>

            {{-- HASIL --}}
            <div class="space-y-10 lg:col-span-3">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-ink/60">Menampilkan <span class="font-semibold text-ink">{{ $packages->count() + $vendors->count() }}</span> hasil</p>
                    @if (count($chips))
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-medium text-ink/50">Filter aktif:</span>
                            @foreach ($chips as $chip)
                                <a href="{{ $chip['url'] }}" class="inline-flex items-center gap-1.5 rounded-full bg-maroon-light px-3 py-1.5 text-xs font-medium text-maroon transition-colors hover:bg-maroon hover:text-white">
                                    {{ $chip['label'] }} <span aria-hidden="true">&times;</span>
                                </a>
                            @endforeach
                            <a href="{{ route('catalog.index') }}" class="text-xs font-medium text-ink/50 hover:text-maroon">Reset Semua</a>
                        </div>
                    @endif
                </div>

                @if ($filters['type'] !== 'vendor')
                    <div>
                        <h2 class="mb-4 font-display text-xl font-semibold">Paket ({{ $packages->count() }})</h2>
                        @if ($packages->isNotEmpty())
                            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                                @foreach ($packages as $package)
                                    <x-site.package-card :package="$package" />
                                @endforeach
                            </div>
                        @else
                            <x-site.empty-state title="Tidak ada paket yang cocok" description="Coba ubah atau reset filter." />
                        @endif
                    </div>
                @endif

                @if ($filters['type'] !== 'package')
                    <div>
                        <h2 class="mb-4 font-display text-xl font-semibold">Vendor ({{ $vendors->count() }})</h2>
                        @if ($vendors->isNotEmpty())
                            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                                @foreach ($vendors as $vendor)
                                    <x-site.vendor-card :vendor="$vendor" />
                                @endforeach
                            </div>
                        @else
                            <x-site.empty-state title="Tidak ada vendor yang cocok" description="Coba ubah atau reset filter." />
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>

@endsection
