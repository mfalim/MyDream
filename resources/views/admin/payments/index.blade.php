@extends('layouts.admin')

@section('title', 'Pembayaran')
@section('page-title', 'Verifikasi Pembayaran')

@section('content')

    @php $rupiah = fn ($n) => \App\Support\Format::rupiah($n); @endphp

    <div class="mb-4">
        <h3 class="mb-1">Verifikasi Pembayaran</h3>
        <p class="text-muted mb-0">Cocokkan nominal dengan mutasi rekening dan bukti transfer, lalu setujui atau tolak. Status pembayaran pesanan client otomatis ikut berubah.</p>
    </div>

    <ul class="nav nav-pills mb-3">
        @foreach ($statuses as $key => $label)
            <li class="nav-item">
                <a class="nav-link {{ $status === $key ? 'active' : '' }}" href="{{ route('admin.payments.index', ['status' => $key]) }}">
                    {{ $label }} <span class="badge text-bg-light">{{ $counts[$key] ?? 0 }}</span>
                </a>
            </li>
        @endforeach
    </ul>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Client &amp; Pesanan</th>
                        <th>Pembayaran</th>
                        <th>Rekening</th>
                        <th>Bukti</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                        @php
                            $booking = $payment->booking;
                            $typeLabel = ['dp' => 'DP', 'full' => 'Lunas', 'remaining' => 'Pelunasan'][$payment->type] ?? $payment->type;
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $booking?->client?->groom_name }} &amp; {{ $booking?->client?->bride_name }}</strong><br>
                                <small class="text-muted">
                                    Pesanan #{{ $booking?->id }}{{ $booking?->invoice_number ? ' · ' . $booking->invoice_number : '' }}<br>
                                    {{ $booking?->package?->name ?? 'Vendor pilihan sendiri' }}
                                </small>
                            </td>
                            <td>
                                <strong>{{ $rupiah($payment->amount) }}</strong> <span class="badge text-bg-light border">{{ $typeLabel }}</span><br>
                                <small class="text-muted">
                                    Total pesanan {{ $rupiah($booking?->total_price) }}<br>
                                    Dikirim {{ $payment->created_at->locale('id')->translatedFormat('d F Y, H:i') }}
                                </small>
                            </td>
                            <td><small>{{ $payment->bank_info ?: '-' }}</small></td>
                            <td>
                                @if ($payment->proof_url)
                                    <a href="{{ $payment->proof_url }}" target="_blank" class="btn btn-sm btn-outline-secondary">Lihat bukti</a>
                                @else
                                    <small class="text-muted">Tidak ada</small>
                                @endif
                            </td>
                            <td class="text-end" style="min-width: 260px;">
                                @if ($payment->status === 'pending')
                                    <form action="{{ route('admin.payments.verify', $payment) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Setujui pembayaran {{ $rupiah($payment->amount) }} ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Setujui</button>
                                    </form>
                                    <form action="{{ route('admin.payments.reject', $payment) }}" method="POST" class="d-inline-flex gap-1 mt-1">
                                        @csrf
                                        <input type="text" name="admin_note" class="form-control form-control-sm" placeholder="Alasan (opsional)" style="width: 130px;">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Tolak</button>
                                    </form>
                                @else
                                    <small class="text-muted">
                                        {{ $payment->status === 'verified' ? 'Disetujui' : 'Ditolak' }}
                                        {{ $payment->verified_at?->locale('id')->translatedFormat('d F Y') }}
                                        @if ($payment->admin_note)<br>"{{ $payment->admin_note }}"@endif
                                    </small>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">Tidak ada pembayaran dengan status "{{ $statuses[$status] }}".</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
