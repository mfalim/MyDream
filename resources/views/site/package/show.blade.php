@extends('layouts.site')

@section('title', $package->name . ' - MyDream Wedding Organizer')
@section('description', \Illuminate\Support\Str::limit($package->description ?: 'Paket pernikahan MyDream Wedding Organizer.', 150))

@section('content')

    <section class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
        <x-site.breadcrumb :items="[
            ['label' => 'Beranda', 'href' => route('home')],
            ['label' => 'Paket & Vendor', 'href' => route('catalog.index')],
            ['label' => $package->name],
        ]" />
    </section>

    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-3">

            <div class="space-y-8 lg:col-span-2">
                {{-- GALERI --}}
                @php $main = $gallery->first(); @endphp
                <div class="aspect-[16/10] overflow-hidden rounded-3xl bg-cream-dark">
                    <img src="{{ \App\Support\Media::url($main) }}" alt="{{ $package->name }}" class="h-full w-full object-cover">
                </div>
                @if ($gallery->count() > 1)
                    <div class="grid grid-cols-4 gap-3">
                        @foreach ($gallery->slice(1) as $photo)
                            <div class="aspect-square overflow-hidden rounded-xl bg-cream-dark">
                                <img src="{{ \App\Support\Media::url($photo) }}" alt="Galeri {{ $package->name }}" loading="lazy" class="h-full w-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif

                <div>
                    @if ($package->is_featured)
                        <x-site.badge variant="gold">Pilihan MyDream</x-site.badge>
                    @endif
                    <h1 class="mt-3 font-display text-3xl font-semibold sm:text-4xl">{{ $package->name }}</h1>
                    @if ($package->description)
                        <p class="mt-4 leading-relaxed text-ink/70">{{ $package->description }}</p>
                    @endif
                </div>

                {{-- FAKTA --}}
                <div class="grid gap-4 rounded-2xl bg-cream-dark/60 p-6 sm:grid-cols-4">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-ink/40">Kapasitas</p>
                        <p class="mt-1 font-display text-base font-semibold">{{ $package->guest_capacity ? $package->guest_capacity . ' tamu' : 'Hubungi kami' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-ink/40">Periode</p>
                        <p class="mt-1 font-display text-base font-semibold">{{ $package->event_period ?: 'Fleksibel' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-ink/40">Durasi</p>
                        <p class="mt-1 font-display text-base font-semibold">{{ $package->duration ? (float) $package->duration . ' jam' : 'Sesuai paket' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-ink/40">Vendor</p>
                        <p class="mt-1 font-display text-base font-semibold">{{ $package->vendors->count() }} vendor</p>
                    </div>
                </div>

                {{-- ISI PAKET --}}
                <div>
                    <h2 class="font-display text-xl font-semibold">Isi Paket</h2>
                    @if ($package->vendors->isNotEmpty())
                        <ul class="mt-4 divide-y divide-black/5 rounded-2xl bg-white ring-1 ring-black/5">
                            @foreach ($package->vendors as $vendor)
                                <li class="flex items-center justify-between gap-4 p-4">
                                    <div class="min-w-0">
                                        <p class="text-xs font-medium uppercase tracking-wide text-emerald">{{ $vendor->category?->name ?? 'Vendor' }}</p>
                                        <a href="{{ route('vendor.show', $vendor->slug) }}" class="font-display text-base font-semibold hover:text-maroon">{{ $vendor->name }}</a>
                                    </div>
                                    <span class="shrink-0 text-sm font-medium text-ink/70">{{ \App\Support\Format::rupiah($vendor->price) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <x-site.empty-state class="mt-4" title="Isi paket belum ditentukan" description="Hubungi kami untuk detail paket ini." />
                    @endif
                </div>
            </div>

            {{-- PANEL PEMESANAN --}}
            <aside class="h-fit space-y-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 lg:sticky lg:top-24">
                <x-site.price :amount="$total" prefix="Total paket" />
                <p class="text-xs text-ink/50">Harga paket adalah jumlah harga seluruh vendor di dalamnya. Opsi pembayaran tersedia setelah checkout.</p>

                @auth
                    <form action="{{ route('user.cart.add-package', $package->id) }}" method="POST">
                        @csrf
                        <x-site.button type="submit" size="lg" class="w-full">Masukkan Keranjang</x-site.button>
                    </form>
                    <form action="{{ route('user.cart.add-package', $package->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="redirect" value="checkout">
                        <x-site.button type="submit" variant="outline" class="w-full">Pesan Sekarang</x-site.button>
                    </form>
                @else
                    <x-site.button :href="route('login')" size="lg" class="w-full">Masuk untuk Memesan</x-site.button>
                @endauth

                <x-site.button :href="\App\Support\Format::waLink(config('mydream.whatsapp'), 'Halo MyDream, saya tertarik dengan paket ' . $package->name)" variant="ghost" class="w-full">Hubungi Admin via WhatsApp</x-site.button>
            </aside>
        </div>
    </section>

    @if ($related->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <h2 class="mb-8 font-display text-2xl font-semibold">Paket Lainnya</h2>
            <div class="grid gap-6 sm:grid-cols-3">
                @foreach ($related as $item)
                    <x-site.package-card :package="$item" />
                @endforeach
            </div>
        </section>
    @endif

@endsection
