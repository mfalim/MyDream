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
        $paymentStatusLabels = [
            'pending' => ['Menunggu verifikasi', 'gold'],
            'initiated' => ['Menunggu pembayaran', 'gold'],
            'verified' => ['Terverifikasi', 'category'],
            'rejected' => ['Gagal / kedaluwarsa', 'maroon'],
        ];
        $transactionStatusLabels = [
            'created' => 'Transaksi dibuat',
            'pending' => 'Menunggu pembayaran',
            'capture' => 'Pembayaran diproses',
            'challenge' => 'Menunggu pemeriksaan',
            'settlement' => 'Berhasil',
            'expire' => 'Kedaluwarsa',
            'cancel' => 'Dibatalkan',
            'deny' => 'Ditolak',
            'refund' => 'Dikembalikan',
            'partial_refund' => 'Pengembalian sebagian',
            'request_failed' => 'Gagal membuat transaksi',
        ];

        $eventDate = $event?->event_date ? \Carbon\Carbon::parse($event->event_date)->locale('id')->translatedFormat('d F Y') : null;
        $lastRejected = $payments->firstWhere('status', 'rejected');
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
                @if ($status === 'paid')
                    <div class="rounded-3xl bg-emerald/10 p-6 text-sm text-ink/70">
                        <p class="font-display text-lg font-semibold text-emerald">Pembayaran lunas</p>
                        <p class="mt-1">Terima kasih! Seluruh pembayaran sudah kami terima dan terverifikasi.</p>
                    </div>
                @elseif ($status === 'dp_paid')
                    <div class="rounded-3xl bg-emerald/10 p-6 text-sm text-ink/70">
                        <p class="font-display text-lg font-semibold text-emerald">DP sudah terverifikasi ({{ \App\Support\Format::rupiah($paid) }})</p>
                        <p class="mt-1">Tanggal acara Anda sudah terkunci. Sisa tagihan {{ \App\Support\Format::rupiah($remaining) }} dapat dilunasi kapan saja sebelum hari acara.</p>
                    </div>
                @endif

                @if ($pending > 0)
                    <div class="rounded-3xl bg-gold/10 p-6 text-sm text-ink/70">
                        <p class="font-display text-lg font-semibold text-ink">Ada pembayaran menunggu verifikasi ({{ \App\Support\Format::rupiah($pending) }})</p>
                        <p class="mt-1">Tim MyDream akan memeriksa pembayaran Anda dan mengonfirmasi statusnya. Jika Anda membayar lewat Midtrans, status akan otomatis diperbarui.</p>
                        <x-site.button :href="\App\Support\Format::waLink(config('mydream.whatsapp'), 'Halo MyDream, saya sudah melakukan pembayaran untuk reservasi #' . $booking->id . ($booking->invoice_number ? ' (' . $booking->invoice_number . ')' : '') . '.')" size="sm" class="mt-4">Konfirmasi via WhatsApp</x-site.button>
                    </div>
                @endif

                @if ($lastRejected && count($options))
                    <div class="rounded-3xl bg-maroon-light p-6 text-sm text-maroon">
                        Pembayaran sebelumnya ({{ \App\Support\Format::rupiah($lastRejected->amount) }}) tidak dapat diverifikasi. Silakan coba lagi atau hubungi admin.
                        @if ($lastRejected->admin_note)
                            <br><strong>Catatan admin:</strong> {{ $lastRejected->admin_note }}
                        @endif
                    </div>
                @endif

                @php
                    $field = 'mt-1.5 w-full rounded-xl border border-black/10 bg-cream px-4 py-3 text-sm text-ink file:mr-3 file:rounded-lg file:border-0 file:bg-maroon-light file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-maroon focus:border-maroon focus:bg-white focus:outline-none';
                    $defaultScheme = old('scheme', array_key_first($options));
                @endphp

                @if (count($options))
                    <div class="space-y-6">
                        {{-- STEP 1: SKEMA PEMBAYARAN --}}
                        <div class="space-y-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                            <h2 class="font-display text-lg font-semibold">1. Pilih Skema Pembayaran</h2>
                            <div class="grid gap-4 {{ count($options) > 1 ? 'sm:grid-cols-2' : '' }}">
                                @foreach ($options as $key => $option)
                                    <label class="cursor-pointer rounded-2xl border-2 border-black/10 p-5 transition-colors has-[:checked]:border-maroon has-[:checked]:bg-maroon-light">
                                        <input type="radio" name="scheme" value="{{ $key }}" data-label="{{ $option['label'] }}" data-amount="{{ $option['amount'] }}" class="sr-only" @checked($defaultScheme === $key)>
                                        <p class="font-display text-lg font-semibold">{{ $option['label'] }}</p>
                                        <p class="font-display text-2xl font-semibold text-maroon">{{ \App\Support\Format::rupiah($option['amount']) }}</p>
                                        <p class="mt-2 text-xs text-ink/50">{{ $option['note'] }}</p>
                                    </label>
                                @endforeach
                            </div>
                            @error('scheme') <p class="text-xs text-maroon">{{ $message }}</p> @enderror
                        </div>

                        {{-- STEP 2: PEMBAYARAN VIA MIDTRANS + LAMPIRAN BUKTI OPSIONAL --}}
                        <div class="space-y-5 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div>
                                    <h2 class="font-display text-lg font-semibold">2. Pembayaran via Midtrans</h2>
                                    <p class="text-xs text-ink/60">Pilih metode pembayaran (QRIS, Virtual Account BCA/BNI/BRI/Mandiri, GoPay, ShopeePay, atau Kartu) melalui Midtrans.</p>
                                </div>
                                <x-site.badge variant="category">Midtrans Gateway</x-site.badge>
                            </div>

                            {{-- BAYAR INSTAN MIDTRANS --}}
                            <div id="method-panel-midtrans" class="space-y-3 rounded-2xl border border-black/10 bg-cream/40 p-5"
                                 data-pay-url="{{ route('payment.pay', $booking->id) }}" data-csrf="{{ csrf_token() }}">
                                <p class="text-xs text-ink/70">Status pembayaran akan terverifikasi secara otomatis setelah transaksi Midtrans Anda berhasil diselesaikan.</p>
                                <p id="midtrans-payment-error" class="hidden rounded-xl bg-maroon-light p-3 text-sm text-maroon" role="alert"></p>
                                <x-site.button type="button" size="lg" class="w-full" id="midtrans-pay-button">Bayar Sekarang via Midtrans</x-site.button>
                            </div>

                            {{-- UNGGAH BUKTI PEMBAYARAN (OPSIONAL) --}}
                            <form id="method-panel-manual" action="{{ route('payment.pay', $booking->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3 rounded-2xl border border-dashed border-black/15 p-5">
                                @csrf
                                <input type="hidden" name="scheme" id="manual-scheme-input" value="{{ $defaultScheme }}">
                                <input type="hidden" name="payment_channel" value="midtrans">

                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p class="font-display text-base font-semibold text-ink">Unggah Foto Bukti Pembayaran <span class="text-xs font-normal text-ink/50">(Opsional)</span></p>
                                    <x-site.badge variant="neutral">Opsional</x-site.badge>
                                </div>
                                <p class="text-xs text-ink/60">Tidak wajib. Jika ingin melampirkan screenshot atau struk bukti pembayaran Midtrans sebagai arsip tambahan, Anda dapat mengunggahnya di sini.</p>

                                <div>
                                    <label for="proof" class="text-xs font-medium text-ink/60">File foto/struk bukti pembayaran (JPG/PNG/PDF, maks. 4 MB)</label>
                                    <input id="proof" type="file" name="proof" accept=".jpg,.jpeg,.png,.pdf" data-preview="#proof-preview" required class="{{ $field }}">
                                    <div id="proof-preview" class="mt-3 flex flex-wrap gap-3"></div>
                                    @error('proof') <p class="mt-1 text-xs text-maroon">{{ $message }}</p> @enderror
                                </div>

                                <x-site.button type="submit" variant="outline" size="md" class="w-full">Lampirkan Bukti Pembayaran</x-site.button>
                            </form>
                        </div>
                    </div>
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
                                            @if ($payment->method === 'midtrans')
                                                · Midtrans · {{ $transactionStatusLabels[$payment->transaction_status] ?? 'Status diperbarui' }}
                                            @endif
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
                    @if ($paymentSummary['deadline'] ?? null)
                        <div class="flex justify-between gap-2 border-t border-black/5 pt-2">
                            <dt class="text-ink/60">{{ $paymentSummary['deadline_type'] === 'dp' ? 'Batas bayar DP' : 'Tenggat pelunasan' }}</dt>
                            <dd class="text-right font-semibold {{ $paymentSummary['is_overdue'] ? 'text-maroon' : 'text-ink' }}">
                                {{ $paymentSummary['deadline']->locale('id')->translatedFormat('d M Y') }}
                                @if ($paymentSummary['is_overdue'])
                                    <span class="block text-[11px] font-normal text-maroon">Melewati tenggat</span>
                                @elseif ($paymentSummary['days_to_deadline'] !== null)
                                    <span class="block text-[11px] font-normal text-ink/50">{{ $paymentSummary['days_to_deadline'] === 0 ? 'Hari ini' : $paymentSummary['days_to_deadline'] . ' hari lagi' }}</span>
                                @endif
                            </dd>
                        </div>
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
            <script src="{{ $snapScriptUrl }}" data-client-key="{{ $snapClientKey }}" defer></script>
            <script>
                (function () {
                    var remaining = {{ (int) round($remaining) }};
                    var radios = document.querySelectorAll('input[name="scheme"]');
                    var midtransBox = document.getElementById('method-panel-midtrans');
                    var manualSchemeInput = document.getElementById('manual-scheme-input');
                    var button = document.getElementById('midtrans-pay-button');
                    var errorBox = document.getElementById('midtrans-payment-error');
                    var rupiah = function (n) { return 'Rp ' + Math.round(n).toLocaleString('id-ID'); };

                    function update() {
                        var chosen = document.querySelector('input[name="scheme"]:checked');
                        if (!chosen) { return; }
                        var amount = Number(chosen.dataset.amount);
                        document.getElementById('sum-scheme').textContent = chosen.dataset.label;
                        document.getElementById('sum-pay').textContent = rupiah(amount);
                        document.getElementById('sum-left').textContent = rupiah(Math.max(0, remaining - amount));
                        if (manualSchemeInput) {
                            manualSchemeInput.value = chosen.value;
                        }
                    }

                    radios.forEach(function (radio) { radio.addEventListener('change', update); });
                    update();

                    button.addEventListener('click', async function () {
                        errorBox.classList.add('hidden');
                        button.disabled = true;
                        button.textContent = 'Menyiapkan pembayaran...';

                        try {
                            var selected = document.querySelector('input[name="scheme"]:checked');
                            var response = await fetch(midtransBox.dataset.payUrl, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': midtransBox.dataset.csrf
                                },
                                body: JSON.stringify({
                                    scheme: selected ? selected.value : '',
                                    payment_channel: 'midtrans'
                                })
                            });
                            var result = await response.json();

                            if (!response.ok) {
                                throw new Error(result.message || 'Tidak dapat menyiapkan pembayaran.');
                            }
                            if (!window.snap) {
                                throw new Error('Layanan pembayaran belum siap. Muat ulang halaman dan coba lagi.');
                            }

                            window.snap.pay(result.token, {
                                onSuccess: function () {
                                    errorBox.textContent = 'Pembayaran Midtrans berhasil! Menyinkronkan status pembayaran...';
                                    errorBox.classList.remove('hidden');
                                    window.setTimeout(function () { window.location.reload(); }, 800);
                                },
                                onPending: function () {
                                    errorBox.textContent = 'Instruksi pembayaran Midtrans telah dibuat. Selesaikan pembayaran lalu muat ulang halaman ini.';
                                    errorBox.classList.remove('hidden');
                                    button.disabled = false;
                                    button.textContent = 'Bayar Sekarang via Midtrans';
                                },
                                onError: function () {
                                    errorBox.textContent = 'Pembayaran belum berhasil. Silakan coba lagi atau pilih metode Midtrans lainnya.';
                                    errorBox.classList.remove('hidden');
                                    button.disabled = false;
                                    button.textContent = 'Bayar Sekarang via Midtrans';
                                },
                                onClose: function () {
                                    window.location.reload();
                                }
                            });
                        } catch (error) {
                            errorBox.textContent = error.message || 'Terjadi kesalahan saat menyiapkan pembayaran.';
                            errorBox.classList.remove('hidden');
                            button.disabled = false;
                            button.textContent = 'Bayar Sekarang via Midtrans';
                        }
                    });
                })();
            </script>
        @endpush
    @endif

@endsection
