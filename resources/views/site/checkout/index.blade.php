@extends('layouts.site')

@section('title', 'Validasi Data - MyDream Wedding Organizer')

@section('content')

    @php
        $isPackage = ($cart['type'] ?? null) === 'package' && $package;
        $maxGuests = $isPackage ? $package->guest_capacity : null;
        $dpPercent = (int) config('mydream.dp_percent');
        $field = 'mt-1 w-full rounded-xl border border-black/10 bg-white px-4 py-2.5 text-sm focus:border-maroon focus:outline-none';
    @endphp

    <section class="mx-auto max-w-6xl px-4 pt-8 sm:px-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <x-site.steps :current="2" />
            <a href="{{ route('cart.index') }}" class="text-sm font-medium text-maroon hover:underline">&larr; Kembali ke Keranjang</a>
        </div>
        <h1 class="mt-6 font-display text-3xl font-semibold">Validasi Data Pemesan &amp; Acara</h1>
        <p class="mt-2 text-ink/60">Lengkapi data venue dan acara untuk membuat reservasi.</p>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
        <div class="grid gap-8 lg:grid-cols-3">

            <form action="{{ route('checkout.store') }}" method="POST" class="space-y-6 lg:col-span-2" id="checkout-form">
                @csrf

                {{-- DATA PEMESAN (dari profil mempelai) --}}
                @if ($client)
                    <div class="flex items-center gap-4 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/5">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-ink font-display text-lg text-cream">{{ strtoupper(mb_substr($client->groom_name, 0, 1)) }}</div>
                        <div class="min-w-0">
                            <p class="font-display text-base font-semibold">{{ $client->groom_name }} &amp; {{ $client->bride_name }}</p>
                            <p class="truncate text-xs text-ink/50">{{ $client->email }} · {{ $client->groom_phone }}</p>
                        </div>
                    </div>
                @else
                    <div class="rounded-3xl bg-maroon-light p-5 text-sm text-maroon">
                        Data mempelai belum lengkap.
                        <a href="{{ route('client.profile') }}" class="font-semibold underline">Lengkapi data mempelai</a> sebelum membuat reservasi.
                    </div>
                @endif

                {{-- WAKTU --}}
                <div class="space-y-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                    <h2 class="font-display text-lg font-semibold">1. Waktu Acara</h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="event_date" class="text-xs font-medium text-ink/60">Tanggal pelaksanaan</label>
                            <input id="event_date" type="date" name="event_date" value="{{ old('event_date') }}" min="{{ now()->addDay()->toDateString() }}" required class="{{ $field }}">
                            @error('event_date') <p class="mt-1 text-xs text-maroon">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="guest_count" class="text-xs font-medium text-ink/60">Estimasi jumlah tamu{{ $maxGuests ? ' (maks. ' . $maxGuests . ')' : '' }}</label>
                            <input id="guest_count" type="number" name="guest_count" value="{{ old('guest_count') }}" min="1" @if ($maxGuests) max="{{ $maxGuests }}" @endif required class="{{ $field }}">
                            @error('guest_count') <p class="mt-1 text-xs text-maroon">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- VENUE --}}
                <div class="space-y-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                    <h2 class="font-display text-lg font-semibold">2. Lokasi Acara</h2>
                    <div>
                        <label for="venue_name" class="text-xs font-medium text-ink/60">Nama venue / gedung</label>
                        <input id="venue_name" type="text" name="venue_name" value="{{ old('venue_name') }}" placeholder="mis. Gedung Soetrisno Hadi" required class="{{ $field }}">
                        @error('venue_name') <p class="mt-1 text-xs text-maroon">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="venue_address" class="text-xs font-medium text-ink/60">Alamat lengkap</label>
                        <textarea id="venue_address" name="venue_address" rows="3" required class="{{ $field }}">{{ old('venue_address') }}</textarea>
                        @error('venue_address') <p class="mt-1 text-xs text-maroon">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="venue_city" class="text-xs font-medium text-ink/60">Kota</label>
                            <input id="venue_city" type="text" name="venue_city" value="{{ old('venue_city') }}" required class="{{ $field }}">
                            @error('venue_city') <p class="mt-1 text-xs text-maroon">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="venue_province" class="text-xs font-medium text-ink/60">Provinsi</label>
                            <input id="venue_province" type="text" name="venue_province" value="{{ old('venue_province', 'Jawa Timur') }}" required class="{{ $field }}">
                            @error('venue_province') <p class="mt-1 text-xs text-maroon">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </form>

            {{-- RINGKASAN --}}
            <aside class="h-fit space-y-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 lg:sticky lg:top-24">
                <h2 class="font-display text-lg font-semibold">Ringkasan Pesanan</h2>

                <div class="space-y-2 text-sm">
                    <p class="font-medium">{{ $isPackage ? $package->name : 'Vendor pilihan sendiri' }}</p>
                    @foreach ($vendors as $vendor)
                        <div class="flex justify-between gap-3 text-ink/60">
                            <span class="truncate">{{ $vendor->name }}</span>
                            <span class="shrink-0">{{ \App\Support\Format::rupiah($vendor->price) }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="flex items-center justify-between border-t border-black/5 pt-3">
                    <span class="font-semibold">Total</span>
                    <span class="font-display text-xl font-semibold text-maroon">{{ \App\Support\Format::rupiah($totalPrice) }}</span>
                </div>
                <p class="text-xs text-ink/50">DP minimal {{ $dpPercent }}% ({{ \App\Support\Format::rupiah(ceil($totalPrice * $dpPercent / 100)) }}) dibayar di langkah berikutnya.</p>

                <label class="flex items-start gap-2 text-xs text-ink/60">
                    <input type="checkbox" form="checkout-form" required class="mt-0.5 accent-maroon">
                    Saya menyatakan data yang diberikan sudah benar dan menyetujui ketentuan layanan MyDream.
                </label>

                <x-site.button type="submit" form="checkout-form" size="lg" class="w-full" :disabled="! $client">Konfirmasi &amp; Lanjut Pembayaran</x-site.button>
            </aside>
        </div>
    </section>

@endsection
