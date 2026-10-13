@extends('layouts.site')

@section('title', $vendor->name . ' - MyDream Wedding Organizer')
@section('description', \Illuminate\Support\Str::limit($vendor->description ?: 'Vendor rekanan MyDream Wedding Organizer.', 150))

@section('content')

    <section class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
        <x-site.breadcrumb :items="[
            ['label' => 'Beranda', 'href' => route('home')],
            ['label' => 'Paket & Vendor', 'href' => route('catalog.index', ['type' => 'vendor'])],
            ['label' => $vendor->name],
        ]" />
    </section>

    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-3">

            <div class="space-y-8 lg:col-span-2">
                <div class="aspect-[16/10] overflow-hidden rounded-3xl bg-cream-dark">
                    <img src="{{ \App\Support\Media::url($cover?->photo ?? $vendor->photo) }}" alt="{{ $vendor->name }}" class="h-full w-full object-cover">
                </div>
                @if ($otherPhotos->isNotEmpty())
                    <div class="grid grid-cols-4 gap-3">
                        @foreach ($otherPhotos as $photo)
                            <div class="aspect-square overflow-hidden rounded-xl bg-cream-dark">
                                <img src="{{ \App\Support\Media::url($photo->photo) }}" alt="Galeri {{ $vendor->name }}" loading="lazy" class="h-full w-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif

                <div>
                    <x-site.badge variant="category">{{ $vendor->category?->name ?? 'Vendor' }}</x-site.badge>
                    <h1 class="mt-3 font-display text-3xl font-semibold sm:text-4xl">{{ $vendor->name }}</h1>
                    @if ($vendor->description)
                        <p class="mt-4 leading-relaxed text-ink/70">{{ $vendor->description }}</p>
                    @endif
                </div>

                <div class="grid gap-4 rounded-2xl bg-cream-dark/60 p-6 sm:grid-cols-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-ink/40">Lokasi</p>
                        <p class="mt-1 text-sm font-medium">{{ $vendor->city ?: '-' }}</p>
                        <p class="text-xs text-ink/50">{{ $vendor->address }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-ink/40">Kontak</p>
                        <p class="mt-1 text-sm font-medium">{{ $vendor->phone }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-ink/40">Instagram</p>
                        <p class="mt-1 text-sm font-medium">{{ $vendor->instagram ?: '-' }}</p>
                    </div>
                </div>

                @if ($vendor->packages->isNotEmpty())
                    <div>
                        <h2 class="font-display text-xl font-semibold">Paket yang Memakai Vendor Ini</h2>
                        <div class="mt-4 grid gap-6 sm:grid-cols-2">
                            @foreach ($vendor->packages as $package)
                                <x-site.package-card :package="$package" />
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <aside class="h-fit space-y-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 lg:sticky lg:top-24">
                <x-site.price :amount="$vendor->price" prefix="Mulai dari" />
                <p class="text-xs text-ink/50">Pesan vendor satuan: pilih minimal 3 vendor di keranjang untuk lanjut ke checkout, atau pilih paket lengkap.</p>

                @auth
                    <form action="{{ route('user.cart.add-vendor', $vendor->id) }}" method="POST">
                        @csrf
                        <x-site.button type="submit" size="lg" class="w-full">Tambah ke Keranjang</x-site.button>
                    </form>
                @else
                    <x-site.button :href="route('login')" size="lg" class="w-full">Masuk untuk Memesan</x-site.button>
                @endauth

                <x-site.button :href="\App\Support\Format::waLink($vendor->phone, 'Halo ' . $vendor->name . ', saya melihat Anda di MyDream dan ingin bertanya.')" variant="outline" class="w-full">Chat Vendor via WhatsApp</x-site.button>
            </aside>
        </div>
    </section>

@endsection
