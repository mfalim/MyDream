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
                <div data-gallery="g" data-reveal="mask" style="--mr: 1.5rem" class="group relative aspect-[16/10] overflow-hidden rounded-3xl bg-cream-dark">
                    <img src="{{ \App\Support\Media::url($main) }}" alt="{{ $package->name }}" class="h-full w-full object-cover transition-transform duration-[1200ms] group-hover:scale-105">
                    <span class="pointer-events-none absolute bottom-4 right-4 rounded-full bg-ink/60 px-3 py-1.5 text-xs font-medium text-white opacity-0 backdrop-blur transition-opacity duration-300 group-hover:opacity-100">Klik untuk perbesar</span>
                </div>
                @if ($gallery->count() > 1)
                    <div data-gallery="g" class="grid grid-cols-4 gap-3">
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
                <div data-reveal class="grid gap-4 rounded-2xl bg-cream-dark/60 p-6 sm:grid-cols-4">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-ink/40">Kapasitas</p>
                        <p class="mt-1 font-display text-base font-semibold">@if ($package->guest_capacity)<span data-count="{{ $package->guest_capacity }}">{{ $package->guest_capacity }}</span> tamu @else Hubungi kami @endif</p>
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
                                <li data-reveal="left" style="--d: {{ $loop->index * 0.1 }}s" class="flex items-center justify-between gap-4 p-4 transition-colors duration-300 hover:bg-cream-dark/60">
                                    <div class="min-w-0">
                                        <p class="text-xs font-medium uppercase tracking-wide text-emerald">{{ $vendor->category?->name ?? 'Vendor' }}</p>
                                        <a href="{{ route('vendor.show', $vendor->slug) }}" class="font-display text-base font-semibold hover:text-maroon">{{ $vendor->name }}</a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <x-site.empty-state class="mt-4" title="Isi paket belum ditentukan" description="Hubungi kami untuk detail paket ini." />
                    @endif
                </div>
            </div>

            {{-- PANEL PEMESANAN --}}
            <aside data-reveal="right" style="--d: .25s" class="h-fit space-y-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 lg:sticky lg:top-24">
                <x-site.price :amount="$total" prefix="Total paket" :animate="true" />
                <p class="text-xs text-ink/50">Harga paket adalah jumlah harga seluruh vendor di dalamnya. Opsi pembayaran tersedia setelah checkout.</p>

                @auth
                    <form action="{{ route('user.cart.add-package', $package->id) }}" method="POST" data-fly-cart>
                        @csrf
                        <x-site.button type="submit" size="lg" class="btn-shine w-full">Masukkan Keranjang</x-site.button>
                    </form>
                    <form action="{{ route('user.cart.add-package', $package->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="redirect" value="checkout">
                        <x-site.button type="submit" variant="outline" class="w-full">Pesan Sekarang</x-site.button>
                    </form>
                @else
                    <x-site.button :href="route('login', ['redirect' => url()->full()])" size="lg" class="w-full">Masuk untuk Memesan</x-site.button>
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
