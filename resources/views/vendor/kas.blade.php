@extends('vendor.layouts.app')

@section('title', 'Kas & Pembayaran Masuk')

@section('content')
    <section class="vendor-hero-banner">
        <div class="vendor-hero-copy">
            <span class="wo-eyebrow"><i class="bi bi-circle-fill"></i> Keuangan Vendor</span>
            <h1>Kas &amp; Pembayaran Masuk</h1>
            <p>Pantau dana pencairan dari Admin MyDream untuk setiap acara yang ditugaskan kepada {{ $vendor->name }}.</p>
        </div>
        <div class="vendor-hero-actions">
            <a href="{{ route('vendor.profil.edit') }}" class="wo-btn wo-btn-outline vendor-hero-btn-light">
                <i class="bi bi-bank"></i> Rekening Pencairan
            </a>
        </div>
    </section>

    <section class="vendor-kpi-grid">
        <article class="wo-card vendor-kpi-card">
            <div class="vendor-kpi-icon is-success"><i class="bi bi-cash-stack"></i></div>
            <span class="vendor-kpi-label">Total Kas Masuk</span>
            <strong class="vendor-kpi-value">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</strong>
            <small>{{ $countPaid }} lunas · {{ $countDp }} DP cair</small>
        </article>

        <article class="wo-card vendor-kpi-card">
            <div class="vendor-kpi-icon is-gold"><i class="bi bi-hourglass-split"></i></div>
            <span class="vendor-kpi-label">Sisa Belum Cair</span>
            <strong class="vendor-kpi-value">Rp {{ number_format($sisaPiutang, 0, ',', '.') }}</strong>
            <small>{{ $countUnpaid }} belum dibayar · {{ $countDp }} menunggu pelunasan</small>
        </article>

        <article class="wo-card vendor-kpi-card">
            <div class="vendor-kpi-icon is-maroon"><i class="bi bi-file-earmark-text"></i></div>
            <span class="vendor-kpi-label">Total Nilai Kontrak</span>
            <strong class="vendor-kpi-value">Rp {{ number_format($totalKontrak, 0, ',', '.') }}</strong>
            <small>Dari {{ $countPaid + $countDp + $countUnpaid }} jadwal penugasan</small>
        </article>
    </section>

    <section class="wo-card vendor-bank-strip">
        <div class="vendor-bank-info">
            <span class="vendor-bank-icon"><i class="bi bi-bank2"></i></span>
            <div>
                <span class="wo-eyebrow">Rekening Penerima</span>
                @if ($vendor->bank_name && $vendor->bank_account_number)
                    <strong>{{ $vendor->bank_name }} — {{ $vendor->bank_account_number }}</strong>
                    <small>a.n. {{ $vendor->bank_account_holder ?: $vendor->name }}</small>
                @else
                    <strong>Rekening bank belum diatur</strong>
                    <small>Lengkapi data rekening di Profil Vendor agar Admin dapat mencairkan dana tepat waktu.</small>
                @endif
            </div>
        </div>
        <a href="{{ route('vendor.profil.edit') }}" class="wo-btn wo-btn-outline wo-btn-sm">
            <i class="bi bi-pencil-square"></i> Ubah Rekening
        </a>
    </section>

    <section class="wo-card">
        <div class="wo-section-head">
            <div>
                <span class="wo-eyebrow">Riwayat Pencairan</span>
                <h2>Daftar Pembayaran per Acara</h2>
            </div>

            <div class="vendor-filter-pills">
                <a href="{{ route('vendor.kas') }}" class="vendor-filter-pill {{ $statusFilter === 'all' ? 'is-active' : '' }}">
                    Semua ({{ $countPaid + $countDp + $countUnpaid }})
                </a>
                <a href="{{ route('vendor.kas', ['status' => 'paid']) }}" class="vendor-filter-pill {{ $statusFilter === 'paid' ? 'is-active' : '' }}">
                    Lunas ({{ $countPaid }})
                </a>
                <a href="{{ route('vendor.kas', ['status' => 'dp_paid']) }}" class="vendor-filter-pill {{ $statusFilter === 'dp_paid' ? 'is-active' : '' }}">
                    DP Cair ({{ $countDp }})
                </a>
                <a href="{{ route('vendor.kas', ['status' => 'unpaid']) }}" class="vendor-filter-pill {{ $statusFilter === 'unpaid' ? 'is-active' : '' }}">
                    Belum Cair ({{ $countUnpaid }})
                </a>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="wo-table vendor-table">
                <thead>
                    <tr>
                        <th>Acara &amp; Kegiatan</th>
                        <th>Tanggal</th>
                        <th>Fee Disepakati</th>
                        <th>Dana Masuk</th>
                        <th>Sisa</th>
                        <th>Status</th>
                        <th>Bukti &amp; Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payouts as $row)
                        @php
                            $badgeClass = match ($row->effective_payment_status) {
                                'paid' => 'wo-badge-green',
                                'dp_paid' => 'wo-badge-gold',
                                default => 'wo-badge-neutral',
                            };
                            $badgeLabel = match ($row->effective_payment_status) {
                                'paid' => 'Lunas',
                                'dp_paid' => 'DP Cair',
                                default => 'Belum Cair',
                            };
                        @endphp
                        <tr>
                            <td>
                                <strong style="color: var(--wo-maroon);">{{ $row->event_name ?? 'Acara' }}</strong>
                                <div class="small text-muted">{{ $row->activity }}</div>
                                @if ($row->invoice_number)
                                    <div class="small text-muted">{{ $row->invoice_number }}</div>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                {{ $row->event_date ? \Carbon\Carbon::parse($row->event_date)->locale('id')->translatedFormat('d M Y') : '-' }}
                            </td>
                            <td class="text-nowrap fw-semibold">
                                Rp {{ number_format($row->effective_fee, 0, ',', '.') }}
                            </td>
                            <td class="text-nowrap fw-bold" style="color: var(--wo-success-text);">
                                Rp {{ number_format($row->paid_value, 0, ',', '.') }}
                            </td>
                            <td class="text-nowrap">
                                Rp {{ number_format($row->remaining_value, 0, ',', '.') }}
                            </td>
                            <td>
                                <span class="wo-badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                                @if ($row->paid_at)
                                    <div class="small text-muted mt-1">{{ \Carbon\Carbon::parse($row->paid_at)->locale('id')->translatedFormat('d M Y') }}</div>
                                @endif
                            </td>
                            <td>
                                @if ($row->payout_proof_path)
                                    <a href="{{ \App\Support\Media::url($row->payout_proof_path) }}" target="_blank" rel="noopener" class="wo-link" style="font-size: 0.78rem;">
                                        <i class="bi bi-receipt"></i> Lihat Bukti
                                    </a>
                                @else
                                    <span class="small text-muted">-</span>
                                @endif
                                @if ($row->payment_notes)
                                    <div class="small text-muted mt-1">{{ $row->payment_notes }}</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Belum ada data pencairan pembayaran untuk filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
