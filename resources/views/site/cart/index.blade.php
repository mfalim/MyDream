@extends('layouts.site')

@section('title', 'Keranjang - MyDream Wedding Organizer')

@section('content')

    @php
        $type = $cart['type'] ?? null;
        $isPackage = $type === 'package' && $package;
        $isCustom = $type === 'custom' && $vendors->isNotEmpty();
        $minVendors = 2;
        $canCheckout = $isPackage || ($isCustom && $vendors->count() >= $minVendors);
        if ($isCustom && $hasCatering && $missingCateringCapacity) {
            $canCheckout = false;
        }
        $dpPercent = (int) config('mydream.dp_percent');
    @endphp

    <section class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
        <x-site.steps :current="1" />
        <h1 class="mt-6 font-display text-3xl font-semibold">Keranjang Reservasi</h1>
        <p class="mt-2 text-ink/60">Tinjau paket atau vendor pilihanmu sebelum melanjutkan ke validasi data.</p>
        @if (session('success'))
            <p class="mt-4 rounded-xl bg-emerald/10 p-3 text-sm text-emerald">{{ session('success') }}</p>
        @endif
        @if (session('error'))
            <p class="mt-4 rounded-xl bg-maroon-light p-3 text-sm text-maroon">{{ session('error') }}</p>
        @endif
    </section>

    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-3">

            <div class="space-y-4 lg:col-span-2">

                @if ($isPackage)
                    @php
                        $packagePhoto = $package->photo ?: $package->vendors->map(fn ($v) => $v->photos->first()?->photo)->filter()->first();
                    @endphp
                    <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-black/5">
                        <div class="flex flex-col gap-4 p-5 sm:flex-row">
                            <img src="{{ \App\Support\Media::url($packagePhoto) }}" alt="{{ $package->name }}" class="h-32 w-full shrink-0 rounded-2xl object-cover sm:w-44">
                            <div class="min-w-0 flex-1">
                                <x-site.badge variant="gold">Paket</x-site.badge>
                                <a href="{{ route('catalog.show', $package->slug) }}" class="mt-2 block font-display text-xl font-semibold hover:text-maroon">{{ $package->name }}</a>
                                <p class="mt-1 text-sm text-ink/60">
                                    {{ $package->guest_capacity ? 'Kapasitas ' . number_format($package->guest_capacity, 0, ',', '.') . ' tamu' : 'Kapasitas fleksibel' }}
                                    @if ($package->event_period) · {{ $package->event_period }} @endif
                                    · {{ $package->vendors->count() }} vendor
                                </p>
                                @if ($package->description)
                                    <p class="mt-2 line-clamp-2 text-xs text-ink/60">{{ $package->description }}</p>
                                @endif
                            </div>
                            <form action="{{ route('user.cart.clear') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-maroon/70 hover:text-maroon">Hapus paket</button>
                            </form>
                        </div>
                        <ul class="divide-y divide-black/5 border-t border-black/5 bg-cream-dark/40">
                            @foreach ($vendors as $vendor)
                                @php
                                    $vPhoto = $vendor->photos->first()?->photo ?? $vendor->photo;
                                @endphp
                                <li class="flex items-center gap-3 px-5 py-3 text-sm">
                                    <img src="{{ \App\Support\Media::url($vPhoto) }}" alt="{{ $vendor->name }}" class="h-11 w-11 shrink-0 rounded-xl object-cover">
                                    <div class="min-w-0 flex-1">
                                        <span class="text-xs font-medium uppercase tracking-wide text-emerald">{{ $vendor->category?->name ?? 'Vendor' }}</span>
                                        <a href="{{ route('vendor.show', $vendor->slug) }}" class="block truncate font-medium hover:text-maroon">{{ $vendor->name }}</a>
                                        <p class="truncate text-xs text-ink/50">
                                            {{ $vendor->city ?: \Illuminate\Support\Str::limit((string) $vendor->address, 45) }}
                                            @if ($vendor->isCatering() && (int) $vendor->guest_capacity > 0)
                                                · Kapasitas s.d. {{ number_format($vendor->guest_capacity, 0, ',', '.') }} tamu
                                            @endif
                                        </p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                @elseif ($isCustom)
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <x-site.badge variant="neutral">Vendor pilihan sendiri</x-site.badge>
                        <p class="text-sm {{ $vendors->count() >= $minVendors ? 'text-emerald' : 'text-maroon' }}">
                            {{ $vendors->count() }} dari minimal {{ $minVendors }} vendor
                        </p>
                    </div>

                    @foreach ($vendors as $vendor)
                        <div class="flex items-center gap-4 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-black/5">
                            <img src="{{ \App\Support\Media::url($vendor->photos->first()?->photo ?? $vendor->photo) }}" alt="{{ $vendor->name }}" class="h-20 w-20 shrink-0 rounded-xl object-cover">
                            <div class="min-w-0 flex-1">
                                <x-site.badge variant="category">{{ $vendor->category?->name ?? 'Vendor' }}</x-site.badge>
                                <a href="{{ route('vendor.show', $vendor->slug) }}" class="mt-1.5 block truncate font-display text-base font-semibold hover:text-maroon">{{ $vendor->name }}</a>
                                <p class="truncate text-xs text-ink/50">
                                    {{ $vendor->city ?: \Illuminate\Support\Str::limit((string) $vendor->address, 40) }}
                                    @if ($vendor->isCatering() && (int) $vendor->guest_capacity > 0)
                                        · Kapasitas s.d. {{ number_format($vendor->guest_capacity, 0, ',', '.') }} tamu
                                    @endif
                                </p>
                                @if ($vendor->description)
                                    <p class="mt-1 line-clamp-1 text-xs text-ink/55">{{ $vendor->description }}</p>
                                @endif
                            </div>
                            <div class="shrink-0">
                                <form action="{{ route('user.cart.remove', $vendor->id) }}" method="POST" class="mt-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-maroon/70 hover:text-maroon">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @endforeach

                    <a href="{{ route('catalog.index', ['type' => 'vendor']) }}" class="block rounded-2xl border border-dashed border-maroon/40 p-4 text-center text-sm font-medium text-maroon hover:bg-maroon-light">+ Tambah vendor lainnya</a>

                @else
                    <x-site.empty-state title="Keranjangmu masih kosong" description="Pilih satu paket lengkap, atau susun sendiri dengan minimal 2 vendor." />
                    <div class="flex flex-wrap justify-center gap-3">
                        <x-site.button :href="route('catalog.index', ['type' => 'package'])">Lihat Paket</x-site.button>
                        <x-site.button :href="route('catalog.index', ['type' => 'vendor'])" variant="outline">Lihat Vendor</x-site.button>
                    </div>
                @endif

                @if ($isPackage || $isCustom)
                    <form action="{{ route('user.cart.clear') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm font-medium text-ink/50 hover:text-maroon">Kosongkan keranjang</button>
                    </form>
                @endif
            </div>

            {{-- RINGKASAN --}}
            @if ($isPackage || $isCustom)
                <aside class="h-fit space-y-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 lg:sticky lg:top-24">
                    <h2 class="font-display text-lg font-semibold">Ringkasan Biaya</h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between gap-2">
                            <span class="text-ink/60">{{ $isPackage ? 'Paket dipilih' : 'Vendor dipilih' }}</span>
                            <span class="text-right font-medium">{{ $isPackage ? $package->name . ' (' . $vendors->count() . ' vendor)' : $vendors->count() . ' vendor' }}</span>
                        </div>
                        @if ($isPackage && $package->guest_capacity)
                            <div class="flex justify-between gap-2">
                                <span class="text-ink/60">Kapasitas</span>
                                <span>Maks. {{ number_format($package->guest_capacity, 0, ',', '.') }} tamu</span>
                            </div>
                        @endif
                        @if ($isPackage || $vendors->count() >= $minVendors)
                            <div class="flex items-center justify-between border-t border-black/5 pt-3">
                                <span class="font-semibold">Total</span>
                                <span class="font-display text-xl font-semibold text-maroon">{{ \App\Support\Format::rupiah($totalPrice) }}</span>
                            </div>
                        @else
                            <p class="border-t border-black/5 pt-3 text-xs text-ink/60">
                                Total biaya ditampilkan setelah minimal {{ $minVendors }} vendor dipilih.
                            </p>
                        @endif
                    </div>

                    @if ($isCustom && $hasCatering)
                        @if ($missingCateringCapacity)
                            <p class="rounded-xl bg-maroon-light p-3 text-xs text-maroon">
                                Kapasitas vendor catering belum diatur. Hubungi admin agar harga dapat dihitung dan checkout dibuka.
                            </p>
                        @else
                            <p class="text-xs text-ink/50">
                                Maksimal {{ number_format($maxGuestCount, 0, ',', '.') }} tamu untuk vendor catering yang dipilih.
                            </p>
                        @endif
                    @endif

                    @if ($isPackage || $vendors->count() >= $minVendors)
                        <p class="rounded-2xl bg-emerald/5 p-4 text-xs text-ink/60">
                            <span class="font-semibold text-emerald">DP minimal {{ $dpPercent }}%</span>
                            ({{ \App\Support\Format::rupiah(ceil($totalPrice * $dpPercent / 100)) }}). Skema DP atau lunas dipilih di langkah pembayaran.
                        </p>
                    @endif

                    @auth
                        @if ($canCheckout)
                            <x-site.button :href="route('checkout.index')" size="lg" class="w-full">Lanjut ke Validasi Data</x-site.button>
                        @else
                            <x-site.button type="button" size="lg" class="w-full" disabled>Lanjut ke Validasi Data</x-site.button>
                            <p class="text-center text-xs text-maroon">Tambahkan minimal {{ $minVendors }} vendor untuk melanjutkan.</p>
                        @endif
                    @else
                        <x-site.button :href="route('login')" size="lg" class="w-full">Masuk untuk Melanjutkan</x-site.button>
                    @endauth

                    <x-site.button :href="\App\Support\Format::waLink(config('mydream.whatsapp'), 'Halo MyDream, saya butuh bantuan soal keranjang reservasi.')" variant="ghost" class="w-full">Tanya Admin via WhatsApp</x-site.button>
                </aside>
            @endif
        </div>
    </section>

@endsection
