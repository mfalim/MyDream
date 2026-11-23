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
                    @php
                        $wilayah = \App\Support\Wilayah::all();
                        $selectedProvince = old('venue_province', 'Jawa Timur');
                        $selectedCity = old('venue_city');
                        $cityOptions = \App\Support\Wilayah::cities($selectedProvince);
                    @endphp
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="venue_province" class="text-xs font-medium text-ink/60">Provinsi</label>
                            <select id="venue_province" name="venue_province" required class="{{ $field }}">
                                <option value="">Pilih provinsi</option>
                                @foreach ($wilayah as $row)
                                    <option value="{{ $row['name'] }}" @selected($selectedProvince === $row['name'])>{{ $row['name'] }}</option>
                                @endforeach
                            </select>
                            @error('venue_province') <p class="mt-1 text-xs text-maroon">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="venue_city" class="text-xs font-medium text-ink/60">Kota / Kabupaten</label>
                            <select id="venue_city" name="venue_city" required class="{{ $field }}" @disabled(empty($cityOptions))>
                                <option value="">Pilih kota / kabupaten</option>
                                @foreach ($cityOptions as $city)
                                    <option value="{{ $city }}" @selected($selectedCity === $city)>{{ $city }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-ink/40" id="city-hint">{{ count($cityOptions) ? count($cityOptions) . ' kota / kabupaten di ' . $selectedProvince : 'Pilih provinsi terlebih dahulu' }}</p>
                            @error('venue_city') <p class="mt-1 text-xs text-maroon">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-xs text-ink/50">
                        <span>Wilayah layanan kami:</span>
                        <button type="button" data-wilayah-quick="Jawa Timur|Kabupaten Jember" class="rounded-full border border-maroon/30 px-3 py-1 font-semibold text-maroon transition-colors hover:bg-maroon-light">Jember</button>
                        <button type="button" data-wilayah-quick="Jawa Timur|Kota Surabaya" class="rounded-full border border-maroon/30 px-3 py-1 font-semibold text-maroon transition-colors hover:bg-maroon-light">Surabaya</button>
                    </div>
                    <script type="application/json" id="wilayah-data">{!! json_encode($wilayah, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
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

@push('scripts')
    <script>
        // Provinsi -> Kota/Kabupaten bertingkat. Data tertanam di halaman, tidak memanggil layanan luar.
        (function () {
            var holder = document.getElementById('wilayah-data');
            var prov = document.getElementById('venue_province');
            var city = document.getElementById('venue_city');
            var hint = document.getElementById('city-hint');
            if (!holder || !prov || !city) { return; }
            var data = JSON.parse(holder.textContent);

            function fill(province, selected) {
                var row = data.filter(function (r) { return r.name === province; })[0];
                var list = row ? row.cities : [];
                city.innerHTML = '';
                var first = document.createElement('option');
                first.value = '';
                first.textContent = 'Pilih kota / kabupaten';
                city.appendChild(first);
                list.forEach(function (name) {
                    var o = document.createElement('option');
                    o.value = name;
                    o.textContent = name;
                    city.appendChild(o);
                });
                city.disabled = list.length === 0;
                if (selected && list.indexOf(selected) > -1) { city.value = selected; }
                if (hint) { hint.textContent = list.length ? list.length + ' kota / kabupaten di ' + province : 'Pilih provinsi terlebih dahulu'; }
            }

            prov.addEventListener('change', function () {
                fill(prov.value, '');
                if (!city.disabled) { city.focus(); }
            });

            Array.prototype.forEach.call(document.querySelectorAll('[data-wilayah-quick]'), function (btn) {
                btn.addEventListener('click', function () {
                    var parts = btn.getAttribute('data-wilayah-quick').split('|');
                    prov.value = parts[0];
                    fill(parts[0], parts[1]);
                });
            });
        })();
    </script>
@endpush

@endsection
