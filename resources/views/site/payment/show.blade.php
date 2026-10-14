@extends('layouts.site')

@section('title', 'Pembayaran - MyDream Wedding Organizer')

@section('content')

    @php
        $status = $booking->payment_status;
        $statusLabels = [
            'unpaid' => ['Belum Bayar', 'neutral'],
            'awaiting_verification' => ['Menunggu Verifikasi', 'gold'],
            'dp_paid' => ['DP Terverifikasi', 'category'],
            'paid' => ['Lunas', 'category'],
        ];
        [$statusLabel, $statusVariant] = $statusLabels[$status] ?? $statusLabels['unpaid'];

        $paymentLabels = ['dp' => 'DP', 'full' => 'Pembayaran lunas', 'remaining' => 'Pelunasan'];
        $paymentStatusLabels = ['pending' => ['Menunggu verifikasi', 'gold'], 'verified' => ['Terverifikasi', 'category'], 'rejected' => ['Ditolak', 'maroon']];

        $eventDate = $event?->event_date ? \Carbon\Carbon::parse($event->event_date)->locale('id')->translatedFormat('d F Y') : null;
        $lastRejected = $payments->firstWhere('status', 'rejected');
        $field = 'mt-1 block w-full text-sm text-ink/70 file:mr-3 file:rounded-full file:border-0 file:bg-maroon-light file:px-4 file:py-2 file:text-xs file:font-semibold file:text-maroon';
    @endphp

    <section class="mx-auto max-w-6xl px-4 pt-8 sm:px-6">
        <x-site.steps :current="3" />
        <div class="mt-6 flex flex-wrap items-center gap-3">
            <h1 class="font-display text-3xl font-semibold">Pembayaran Reservasi</h1>
            <x-site.badge :variant="$statusVariant">{{ $statusLabel }}</x-site.badge>
        </div>
        <p class="mt-2 text-ink/60">
            @if ($booking->invoice_number) Invoice <span class="font-semibold text-ink">{{ $booking->invoice_number }}</span> · @endif
            Reservasi #{{ $booking->id }}
        </p>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
        <div class="grid gap-8 lg:grid-cols-3">

            <div class="space-y-6 lg:col-span-2">

                {{-- STATUS --}}
                @if ($status === 'awaiting_verification')
                    <div class="rounded-3xl bg-gold/10 p-6 text-sm text-ink/70">
                        <p class="font-display text-lg font-semibold text-ink">Pembayaran sedang diverifikasi</p>
                        <p class="mt-1">Tim MyDream akan memeriksa transfer Anda dan mengonfirmasi lewat WhatsApp. Biasanya dalam 1x24 jam.</p>
                        <x-site.button :href="\App\Support\Format::waLink(config('mydream.whatsapp'), 'Halo MyDream, saya sudah transfer untuk reservasi #' . $booking->id . ($booking->invoice_number ? ' (' . $booking->invoice_number . ')' : '') . '.')" size="sm" class="mt-4">Konfirmasi via WhatsApp</x-site.button>
                    </div>
                @elseif ($status === 'paid')
                    <div class="rounded-3xl bg-emerald/10 p-6 text-sm text-ink/70">
                        <p class="font-display text-lg font-semibold text-emerald">Pembayaran lunas</p>
                        <p class="mt-1">Terima kasih! Seluruh pembayaran sudah kami terima dan terverifikasi.</p>
                    </div>
                @elseif ($status === 'dp_paid')
                    <div class="rounded-3xl bg-emerald/10 p-6 text-sm text-ink/70">
                        <p class="font-display text-lg font-semibold text-emerald">DP sudah terverifikasi</p>
                        <p class="mt-1">Sisa pembayaran {{ \App\Support\Format::rupiah($remaining) }} dapat dilunasi di bawah ini.</p>
                    </div>
                @endif

                @if ($lastRejected && count($options))
                    <div class="rounded-3xl bg-maroon-light p-6 text-sm text-maroon">
                        Pembayaran sebelumnya ({{ \App\Support\Format::rupiah($lastRejected->amount) }}) tidak dapat diverifikasi. Silakan transfer ulang atau hubungi admin.
                    </div>
                @endif

                {{-- FORM PEMBAYARAN --}}
                @if (count($options))
                    @if (! $bank)
                        <div class="rounded-3xl bg-maroon-light p-6 text-sm text-maroon">
                            Rekening transfer belum diatur oleh admin.
                            <a href="{{ \App\Support\Format::waLink(config('mydream.whatsapp'), 'Halo MyDream, saya ingin membayar reservasi #' . $booking->id . '.') }}" class="font-semibold underline">Hubungi admin via WhatsApp</a>.
                        </div>
                    @endif

                    <form action="{{ route('payment.pay', $booking->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div class="space-y-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                            <h2 class="font-display text-lg font-semibold">1. Pilih Skema Pembayaran</h2>
                            <div class="grid gap-4 {{ count($options) > 1 ? 'sm:grid-cols-2' : '' }}">
                                @foreach ($options as $key => $option)
                                    <label class="cursor-pointer rounded-2xl border-2 border-black/10 p-5 transition-colors has-[:checked]:border-maroon has-[:checked]:bg-maroon-light">
                                        <input type="radio" name="scheme" value="{{ $key }}" data-label="{{ $option['label'] }}" data-amount="{{ $option['amount'] }}" class="sr-only" @checked(old('scheme', array_key_first($options)) === $key)>
                                        <p class="font-display text-lg font-semibold">{{ $option['label'] }}</p>
                                        <p class="font-display text-2xl font-semibold text-maroon">{{ \App\Support\Format::rupiah($option['amount']) }}</p>
                                        <p class="mt-2 text-xs text-ink/50">{{ $option['note'] }}</p>
                                    </label>
                                @endforeach
                            </div>
                            @error('scheme') <p class="text-xs text-maroon">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                            <h2 class="font-display text-lg font-semibold">2. Transfer Bank</h2>
                            @if ($bank)
                                <div class="rounded-2xl bg-cream-dark/60 p-5">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-ink/50">Transfer ke rekening</p>
                                    <p class="mt-2 font-display text-lg font-semibold">{{ $bank['name'] }}</p>
                                    <p class="font-display text-2xl font-semibold tracking-wider text-maroon">{{ $bank['number'] }}</p>
                                    @if ($bank['holder'])
                                        <p class="text-sm text-ink/60">a.n. {{ $bank['holder'] }}</p>
                                    @endif
                                </div>
                                <p class="text-xs text-ink/50">Transfer sesuai nominal skema yang dipilih, lalu tekan tombol di bawah.</p>
                            @endif

                            <div>
                                <label for="proof" class="text-xs font-medium text-ink/60">Bukti transfer (opsional, JPG/PNG/PDF maks. 2 MB)</label>
                                <input id="proof" type="file" name="proof" accept=".jpg,.jpeg,.png,.pdf" class="{{ $field }}">
                                @error('proof') <p class="mt-1 text-xs text-maroon">{{ $message }}</p> @enderror
                            </div>

                            <x-site.button type="submit" size="lg" class="w-full" :disabled="! $bank">Saya Sudah Transfer</x-site.button>
                        </div>
                    </form>
                @endif

                {{-- RIWAYAT --}}
                @if ($payments->isNotEmpty())
                    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                        <h2 class="font-display text-lg font-semibold">Riwayat Pembayaran</h2>
                        <ul class="mt-4 divide-y divide-black/5">
                            @foreach ($payments as $payment)
                                @php [$pLabel, $pVariant] = $paymentStatusLabels[$payment->status] ?? $paymentStatusLabels['pending']; @endphp
                                <li class="flex flex-wrap items-center justify-between gap-3 py-3 text-sm">
                                    <div>
                                        <p class="font-medium">{{ $paymentLabels[$payment->type] ?? $payment->type }} · {{ \App\Support\Format::rupiah($payment->amount) }}</p>
                                        <p class="text-xs text-ink/50">
                                            {{ $payment->created_at->locale('id')->translatedFormat('d F Y, H:i') }}
                                            @if ($payment->proof_url) · <a href="{{ $payment->proof_url }}" target="_blank" class="text-maroon hover:underline">Lihat bukti</a> @endif
                                        </p>
                                    </div>
                                    <x-site.badge :variant="$pVariant">{{ $pLabel }}</x-site.badge>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            {{-- RINGKASAN --}}
            <aside class="h-fit space-y-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 lg:sticky lg:top-24">
                <h2 class="font-display text-lg font-semibold">Ringkasan Reservasi</h2>
                <div class="space-y-1 text-sm">
                    <p class="font-medium">{{ $booking->package?->name ?? 'Vendor pilihan sendiri' }}</p>
                    <p class="text-ink/60">{{ $booking->venue_name }}, {{ $booking->venue_city }}</p>
                    @if ($eventDate) <p class="text-ink/60">{{ $eventDate }} · {{ $booking->guest_count }} tamu</p> @endif
                </div>

                <dl class="space-y-2 border-t border-black/5 pt-4 text-sm">
                    <div class="flex justify-between"><dt class="text-ink/60">Total reservasi</dt><dd class="font-medium">{{ \App\Support\Format::rupiah($total) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink/60">Sudah terverifikasi</dt><dd class="font-medium text-emerald">{{ \App\Support\Format::rupiah($paid) }}</dd></div>
                    @if ($pending > 0)
                        <div class="flex justify-between"><dt class="text-ink/60">Menunggu verifikasi</dt><dd class="font-medium">{{ \App\Support\Format::rupiah($pending) }}</dd></div>
                    @endif
                    @unless (count($options))
                        <div class="flex justify-between border-t border-black/5 pt-2"><dt class="font-semibold">Sisa pembayaran</dt><dd class="font-display text-lg font-semibold text-maroon">{{ \App\Support\Format::rupiah($remaining) }}</dd></div>
                    @endunless
                </dl>

                @if (count($options))
                    @php
                        $selected = $options[old('scheme', array_key_first($options))] ?? reset($options);
                    @endphp
                    {{-- Diperbarui otomatis saat skema DP / Lunas dipilih (lihat script di bawah) --}}
                    <div class="space-y-2 rounded-2xl bg-maroon-light p-4 text-sm">
                        <div class="flex justify-between"><span class="text-ink/60">Skema dipilih</span><span id="sum-scheme" class="font-semibold">{{ $selected['label'] }}</span></div>
                        <div class="flex items-end justify-between"><span class="text-ink/60">Dibayar sekarang</span><span id="sum-pay" class="font-display text-xl font-semibold text-maroon">{{ \App\Support\Format::rupiah($selected['amount']) }}</span></div>
                        <div class="flex justify-between border-t border-maroon/10 pt-2"><span class="text-ink/60">Sisa setelah pembayaran ini</span><span id="sum-left" class="font-medium">{{ \App\Support\Format::rupiah(max(0, $remaining - $selected['amount'])) }}</span></div>
                    </div>
                @endif

                <x-site.button :href="\App\Support\Format::waLink(config('mydream.whatsapp'), 'Halo MyDream, saya butuh bantuan soal pembayaran reservasi #' . $booking->id . '.')" variant="ghost" class="w-full">Bantuan via WhatsApp</x-site.button>
                <x-site.button :href="route('user.dashboard')" variant="outline" class="w-full">Ke Dashboard</x-site.button>
            </aside>
        </div>
    </section>

    @if (count($options))
        @push('scripts')
            <script>
                (function () {
                    var remaining = {{ (int) round($remaining) }};
                    var radios = document.querySelectorAll('input[name="scheme"]');
                    var rupiah = function (n) { return 'Rp ' + Math.round(n).toLocaleString('id-ID'); };

                    function update() {
                        var chosen = document.querySelector('input[name="scheme"]:checked');
                        if (!chosen) { return; }
                        var amount = Number(chosen.dataset.amount);
                        document.getElementById('sum-scheme').textContent = chosen.dataset.label;
                        document.getElementById('sum-pay').textContent = rupiah(amount);
                        document.getElementById('sum-left').textContent = rupiah(Math.max(0, remaining - amount));
                    }

                    radios.forEach(function (radio) { radio.addEventListener('change', update); });
                    update();
                })();
            </script>
        @endpush
    @endif

@endsection
