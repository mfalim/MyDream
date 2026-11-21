@extends('layouts.admin')

@section('title', 'Pusat Keuangan & Kas')
@section('page-title', 'Pusat Keuangan & Kas')

@push('styles')
    @vite('resources/css/admin/finance/index.css')
@endpush

@section('content')
@php
    $rupiah = fn ($n) => 'Rp ' . number_format((float) ($n ?? 0), 0, ',', '.');
    $unpaidVendorCount = $vendorSchedules->where('payment_status', '!=', 'paid')->count();
@endphp

<div class="finance-page">
    {{-- CLEAN HEADER --}}
    <div class="finance-header">
        <div>
            <div class="finance-header-eyebrow">KEUANGAN &amp; ARUS KAS</div>
            <h1>Pusat Keuangan &amp; Kas Acara</h1>
            <p>Pantau buku kas &amp; invoice per booking, atur pencairan dana ke rekening vendor, dan kelola pembayaran klien.</p>
        </div>
        <div class="finance-header-actions no-print">
            <button type="button" class="btn-fin-primary" data-bs-toggle="modal" data-bs-target="#manualPaymentModal">
                <i class="bi bi-plus-circle"></i>
                <span>Catat Bayar Klien</span>
            </button>
            <a href="{{ route('admin.payroll.index') }}" class="btn-fin-outline">
                <i class="bi bi-cash-stack"></i>
                <span>Honor Kru</span>
            </a>
        </div>
    </div>

    {{-- 4 ESSENTIAL KPI CARDS --}}
    <div class="finance-kpi-grid">
        <div class="finance-kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Kas Masuk Klien (Verified)</span>
                <span class="kpi-icon inflow"><i class="bi bi-arrow-down-left-circle-fill"></i></span>
            </div>
            <div class="kpi-value">{{ $rupiah($summary['client_inflow']) }}</div>
            <div class="kpi-sub">
                <span>Total Nilai Kontrak:</span>
                <strong class="num-tabular">{{ $rupiah($summary['total_contract']) }}</strong>
            </div>
        </div>

        <div class="finance-kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Sisa Piutang Klien</span>
                <span class="kpi-icon receivable"><i class="bi bi-hourglass-split"></i></span>
            </div>
            <div class="kpi-value">{{ $rupiah($summary['client_receivables']) }}</div>
            <div class="kpi-sub">
                <span>Booking Belum Lunas:</span>
                <strong class="num-tabular text-warning-emphasis">{{ $bookings->where('payment_status', '!=', 'paid')->count() }} booking</strong>
            </div>
        </div>

        <div class="finance-kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Pencairan Vendor Rekanan</span>
                <span class="kpi-icon outflow"><i class="bi bi-shop"></i></span>
            </div>
            <div class="kpi-value">{{ $rupiah($summary['vendor_disbursed']) }}</div>
            <div class="kpi-sub">
                <span>Sisa Tagihan Vendor:</span>
                <strong class="num-tabular text-danger">{{ $rupiah($summary['vendor_remaining']) }}</strong>
            </div>
        </div>

        <div class="finance-kpi-card highlight">
            <div class="kpi-top">
                <span class="kpi-label">Saldo Kas Riil Saat Ini</span>
                <span class="kpi-icon balance"><i class="bi bi-safe2-fill"></i></span>
            </div>
            <div class="kpi-value">{{ $rupiah($summary['net_cash_on_hand']) }}</div>
            <div class="kpi-sub">
                <span>Honor Kru Terbayar:</span>
                <strong class="num-tabular">{{ $rupiah($summary['crew_disbursed']) }}</strong>
            </div>
        </div>
    </div>

    {{-- UNIFIED WORKSPACE CARD (3 NON-OVERLAPPING TABS) --}}
    <div class="finance-workspace" id="financeWorkspace" data-ajax-workspace>
        <div class="finance-workspace-head">
            <div class="finance-nav-tabs no-print">
                <a href="{{ route('admin.payments.index', ['tab' => 'bookings']) }}"
                   class="finance-tab-link {{ $activeTab === 'bookings' ? 'active' : '' }}">
                    <i class="bi bi-journal-bookmark-fill"></i>
                    <span>Buku Kas &amp; Invoice Booking</span>
                    <span class="finance-tab-badge">{{ $bookings->count() }}</span>
                </a>

                <a href="{{ route('admin.payments.index', ['tab' => 'vendor_payouts']) }}"
                   class="finance-tab-link {{ $activeTab === 'vendor_payouts' ? 'active' : '' }}">
                    <i class="bi bi-send-check"></i>
                    <span>Pencairan Dana Vendor</span>
                    @if ($unpaidVendorCount > 0)
                        <span class="finance-tab-badge">{{ $unpaidVendorCount }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.payments.index', ['tab' => 'report']) }}"
                   class="finance-tab-link {{ $activeTab === 'report' ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                    <span>Laporan Organizer</span>
                </a>
            </div>

            {{-- CONTEXTUAL FILTER RIGHT INSIDE HEADER --}}
            @if ($activeTab === 'vendor_payouts')
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <div class="finance-pill-group">
                        <a href="{{ route('admin.payments.index', ['tab' => 'vendor_payouts', 'booking_id' => $bookingFilter]) }}"
                           class="finance-pill {{ $vendorStatusFilter === '' ? 'active' : '' }}">Semua</a>
                        <a href="{{ route('admin.payments.index', ['tab' => 'vendor_payouts', 'vendor_status' => 'unpaid', 'booking_id' => $bookingFilter]) }}"
                           class="finance-pill {{ $vendorStatusFilter === 'unpaid' ? 'active' : '' }}">Belum Cair</a>
                        <a href="{{ route('admin.payments.index', ['tab' => 'vendor_payouts', 'vendor_status' => 'dp_paid', 'booking_id' => $bookingFilter]) }}"
                           class="finance-pill {{ $vendorStatusFilter === 'dp_paid' ? 'active' : '' }}">DP</a>
                        <a href="{{ route('admin.payments.index', ['tab' => 'vendor_payouts', 'vendor_status' => 'paid', 'booking_id' => $bookingFilter]) }}"
                           class="finance-pill {{ $vendorStatusFilter === 'paid' ? 'active' : '' }}">Lunas</a>
                    </div>
                    <form method="GET" action="{{ route('admin.payments.index') }}">
                        <input type="hidden" name="tab" value="vendor_payouts">
                        <input type="hidden" name="vendor_status" value="{{ $vendorStatusFilter }}">
                        <select name="booking_id" class="form-select form-select-sm" data-ajax-auto-submit="true" style="min-width: 210px;">
                            <option value="">Semua Booking</option>
                            @foreach ($bookings as $b)
                                <option value="{{ $b->id }}" {{ (string) $bookingFilter === (string) $b->id ? 'selected' : '' }}>
                                    #{{ $b->id }} - {{ $b->client->groom_name ?? '' }} &amp; {{ $b->client->bride_name ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            @elseif ($activeTab === 'bookings')
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <div class="finance-pill-group" id="bookingStatusFilterGroup">
                        <button type="button" class="finance-pill active" data-filter="all">
                            <span>Semua</span>
                            <span class="badge rounded-pill text-bg-light">{{ $bookings->count() }}</span>
                        </button>
                        <button type="button" class="finance-pill" data-filter="paid">
                            <span>Lunas</span>
                            <span class="badge rounded-pill text-bg-light">{{ $bookings->where('payment_status', 'paid')->count() }}</span>
                        </button>
                        <button type="button" class="finance-pill" data-filter="dp_paid">
                            <span>DP</span>
                            <span class="badge rounded-pill text-bg-light">{{ $bookings->where('payment_status', 'dp_paid')->count() }}</span>
                        </button>
                        <button type="button" class="finance-pill" data-filter="unpaid">
                            <span>Belum Bayar</span>
                            <span class="badge rounded-pill text-bg-light">{{ $bookings->whereIn('payment_status', ['unpaid', 'awaiting_verification'])->count() }}</span>
                        </button>
                    </div>
                    <div class="ledger-search-box">
                        <i class="bi bi-search text-muted"></i>
                        <input type="text" id="bookingSearchInput" class="form-control form-control-sm border-0"
                               placeholder="Cari mempelai / invoice / venue...">
                        <button type="button" class="btn-clear-search d-none" id="bookingClearSearchBtn" title="Reset pencarian">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    </div>
                </div>
            @elseif ($activeTab === 'report')
                <div class="d-flex align-items-center gap-2 no-print">
                    <button type="button" class="btn-fin-outline" onclick="exportOrganizerReportCsv()">
                        <i class="bi bi-filetype-csv"></i>
                        <span>Unduh CSV</span>
                    </button>
                    <button type="button" class="btn-fin-primary" onclick="window.print()">
                        <i class="bi bi-printer"></i>
                        <span>Cetak Laporan</span>
                    </button>
                </div>
            @endif
        </div>

        {{-- ==================================================================
             TAB 2: PENCAIRAN DANA VENDOR REKANAN (OUTFLOW)
             ================================================================== --}}
        @if ($activeTab === 'vendor_payouts')
            <div class="table-responsive">
                <table class="finance-table">
                    <thead>
                        <tr>
                            <th>Vendor &amp; Rekening Tujuan</th>
                            <th>Acara &amp; Status Kas Klien</th>
                            <th>Harga Mitra (Nett)</th>
                            <th>Dicairkan</th>
                            <th>Sisa</th>
                            <th>Status &amp; Struk</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vendorSchedules as $schedule)
                            @php
                                $event = $schedule->event;
                                $booking = $event?->booking;
                                $vendor = $schedule->vendor;
                                $catalogFee = (float) ($schedule->price ?? $vendor?->price ?? $schedule->effective_fee);
                                $effectiveFee = $schedule->effective_fee;
                                $woCommission = max(0, $catalogFee - $effectiveFee);
                                $paidVendor = (float) ($schedule->paid_amount ?? 0);
                                $remainingVendor = $schedule->remaining_fee;
                                $vPayStatus = $schedule->payment_status ?: 'unpaid';
                                $vPayLabel = match ($vPayStatus) {
                                    'paid' => 'Lunas',
                                    'dp_paid' => 'DP Cair',
                                    default => 'Belum Cair',
                                };
                                $clientPayStatus = $booking?->payment_status ?: 'unpaid';
                                $clientPayLabel = match ($clientPayStatus) {
                                    'paid' => 'Klien Lunas',
                                    'dp_paid' => 'Klien Sudah DP',
                                    'awaiting_verification' => 'Cek Bukti Klien',
                                    default => 'Klien Belum Bayar',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $vendor?->name ?? $schedule->activity }}</div>
                                    <div class="small text-muted mb-1">{{ $vendor?->category?->name ?? 'Vendor' }}</div>
                                    @if ($vendor?->formatted_bank_account)
                                        <span class="bank-pill"><i class="bi bi-bank"></i> {{ $vendor->formatted_bank_account }}</span>
                                    @else
                                        <span class="bank-pill missing"><i class="bi bi-exclamation-circle"></i> Rekening belum diisi</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">#{{ $booking?->id }} · {{ $event?->name ?? 'Acara' }}</div>
                                    <div class="mt-1">
                                        <span class="fin-badge {{ $clientPayStatus }}">{{ $clientPayLabel }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="num-tabular">{{ $rupiah($effectiveFee) }}</div>
                                    @if ($woCommission > 0)
                                        <div class="small text-success fw-semibold num-tabular">Margin: +{{ $rupiah($woCommission) }}</div>
                                    @else
                                        <div class="small text-muted num-tabular">Katalog: {{ $rupiah($catalogFee) }}</div>
                                    @endif
                                </td>
                                <td class="num-tabular text-success">{{ $rupiah($paidVendor) }}</td>
                                <td class="num-tabular {{ $remainingVendor > 0 ? 'text-danger' : 'text-muted' }}">{{ $rupiah($remainingVendor) }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        <span class="fin-badge {{ $vPayStatus }}">{{ $vPayLabel }}</span>
                                        @if ($schedule->payout_proof_url)
                                            <button type="button" class="btn-fin-outline py-1 px-2"
                                                    onclick="openProofPreview('{{ $schedule->payout_proof_url }}', 'Bukti Pencairan Vendor {{ $vendor?->name }} ({{ $rupiah($paidVendor) }})')">
                                                <i class="bi bi-receipt text-success"></i> Struk
                                            </button>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn-fin-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#vendorPayoutModal{{ $schedule->id }}">
                                        <i class="bi bi-wallet2"></i> Cairkan
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">Tidak ada data pencairan vendor.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MODAL PENCAIRAN DANA VENDOR (HOISTED OUTSIDE TABLE FOR VALID HTML) --}}
            @foreach ($vendorSchedules as $schedule)
                @php
                    $event = $schedule->event;
                    $booking = $event?->booking;
                    $vendor = $schedule->vendor;
                    $catalogFee = (float) ($schedule->price ?? $vendor?->price ?? $schedule->effective_fee);
                    $effectiveFee = $schedule->effective_fee;
                    $paidVendor = (float) ($schedule->paid_amount ?? 0);
                    $vPayStatus = $schedule->payment_status ?: 'unpaid';
                    $clientPayStatus = $booking?->payment_status ?: 'unpaid';
                    $clientPayLabel = match ($clientPayStatus) {
                        'paid' => 'Klien Lunas',
                        'dp_paid' => 'Klien Sudah DP',
                        'awaiting_verification' => 'Cek Bukti Klien',
                        default => 'Klien Belum Bayar',
                    };
                @endphp
                <div class="modal fade" id="vendorPayoutModal{{ $schedule->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                            <form action="{{ route('admin.payments.vendor-payout', $schedule) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="redirect_vendor_status" value="{{ $vendorStatusFilter }}">
                                <input type="hidden" name="redirect_booking_id" value="{{ $bookingFilter }}">

                                <div class="modal-header text-white" style="background: #7A1F2B; border-radius: 16px 16px 0 0;">
                                    <div>
                                        <h5 class="modal-title fw-bold mb-0">Pencairan &amp; Bukti Transfer Vendor</h5>
                                        <small class="opacity-75">{{ $vendor?->name ?? $schedule->activity }} — {{ $event?->name }}</small>
                                    </div>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body p-4">
                                    @if (! in_array($clientPayStatus, ['dp_paid', 'paid']))
                                        <div class="alert alert-warning small mb-3">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                            <strong>Perhatian:</strong> Booking #{{ $booking?->id }} masih berstatus <strong>{{ $clientPayLabel }}</strong>.
                                        </div>
                                    @endif

                                    <div class="p-3 rounded-3 border bg-light mb-3">
                                        <div class="small fw-bold text-uppercase text-muted mb-2">
                                            <i class="bi bi-bank me-1"></i> Rekening Bank Tujuan Vendor
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-4">
                                                <input type="text" name="bank_name" class="form-control form-control-sm"
                                                       value="{{ $vendor?->bank_name }}" placeholder="Bank (BCA/BRI)">
                                            </div>
                                            <div class="col-4">
                                                <input type="text" name="bank_account_number" class="form-control form-control-sm"
                                                       value="{{ $vendor?->bank_account_number }}" placeholder="No. Rekening">
                                            </div>
                                            <div class="col-4">
                                                <input type="text" name="bank_account_holder" class="form-control form-control-sm"
                                                       value="{{ $vendor?->bank_account_holder }}" placeholder="Atas Nama">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-uppercase mb-1">Harga Nett Mitra Vendor (Rp)</label>
                                        <input type="number" step="1" min="0" name="agreed_fee"
                                               id="agreedFeeInput{{ $schedule->id }}"
                                               class="form-control num-tabular"
                                               value="{{ (int) $effectiveFee }}" required>
                                        <div class="fee-preset-group">
                                            <button type="button" class="fee-preset-btn"
                                                    onclick="document.getElementById('agreedFeeInput{{ $schedule->id }}').value = {{ (int) round($catalogFee * 0.85) }}">
                                                Mitra -15% ({{ $rupiah($catalogFee * 0.85) }})
                                            </button>
                                            <button type="button" class="fee-preset-btn"
                                                    onclick="document.getElementById('agreedFeeInput{{ $schedule->id }}').value = {{ (int) round($catalogFee * 0.90) }}">
                                                Mitra -10% ({{ $rupiah($catalogFee * 0.90) }})
                                            </button>
                                            <button type="button" class="fee-preset-btn"
                                                    onclick="document.getElementById('agreedFeeInput{{ $schedule->id }}').value = {{ (int) $catalogFee }}">
                                                Katalog ({{ $rupiah($catalogFee) }})
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-uppercase">Skema Pencairan</label>
                                        <select name="payout_scheme" class="form-select vendor-scheme-select" data-target="#customAmountWrap{{ $schedule->id }}">
                                            <option value="dp_50" {{ $vPayStatus === 'dp_paid' ? 'selected' : '' }}>Cairkan DP 50%</option>
                                            <option value="paid_full" {{ $vPayStatus === 'paid' ? 'selected' : '' }}>Pelunasan Penuh 100%</option>
                                            <option value="custom">Input Nominal Manual (Custom)</option>
                                            <option value="unpaid" {{ $vPayStatus === 'unpaid' ? 'selected' : '' }}>Reset Belum Dicairkan (Rp 0)</option>
                                        </select>
                                    </div>

                                    <div class="mb-3" id="customAmountWrap{{ $schedule->id }}" style="display: none;">
                                        <label class="form-label fw-bold small text-uppercase">Nominal Dicairkan (Rp)</label>
                                        <input type="number" step="1" min="0" name="paid_amount"
                                               class="form-control num-tabular"
                                               value="{{ (int) $paidVendor }}">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-uppercase">Upload Struk Bukti Transfer (JPG/PNG/PDF)</label>
                                        <input type="file" name="payout_proof" accept=".jpg,.jpeg,.png,.pdf" class="form-control form-control-sm">
                                    </div>

                                    <div class="mb-0">
                                        <label class="form-label fw-bold small text-uppercase">Catatan Transfer</label>
                                        <textarea name="payment_notes" rows="2" class="form-control form-control-sm"
                                                  placeholder="Contoh: Transfer BCA 50% DP">{{ $schedule->payment_notes }}</textarea>
                                    </div>
                                </div>

                                <div class="modal-footer bg-light" style="border-radius: 0 0 16px 16px;">
                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn-fin-primary">
                                        <i class="bi bi-check2-circle"></i> Simpan Pencairan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        {{-- ==================================================================
             TAB 3: BUKU KAS, PIUTANG & INVOICE PER BOOKING (UNIFIED)
             ================================================================== --}}
        @if ($activeTab === 'bookings')
            <div class="table-responsive">
                <table class="finance-table" id="bookingFinanceTable">
                    <thead>
                        <tr>
                            <th>Booking &amp; Mempelai</th>
                            <th>Tagihan &amp; Kas Masuk Klien</th>
                            <th>Beban Vendor &amp; Kru</th>
                            <th>Saldo Kas &amp; Margin</th>
                            <th class="text-end">Bukti, Invoice &amp; Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($projectLedgers as $row)
                            @php
                                $booking = $row['booking'];
                                $totalPrice = $row['contract_total'];
                                $paidAmount = $row['client_paid'];
                                $remaining = $row['client_remaining'];
                                $pct = $totalPrice > 0 ? min(100, round(($paidAmount / $totalPrice) * 100)) : 0;
                                $payStatus = $booking->payment_status ?: 'unpaid';
                                $payStatusLabel = match ($payStatus) {
                                    'paid' => 'Lunas (100%)',
                                    'dp_paid' => 'DP Terbayar',
                                    'awaiting_verification' => 'Menunggu Verifikasi',
                                    default => 'Belum Bayar',
                                };
                                $searchKey = strtolower(
                                    ($booking->id) . ' ' .
                                    ($booking->client?->groom_name ?? '') . ' ' .
                                    ($booking->client?->bride_name ?? '') . ' ' .
                                    ($booking->invoice_number ?? '') . ' ' .
                                    ($booking->package?->name ?? '') . ' ' .
                                    ($booking->venue_name ?? '') . ' ' .
                                    ($booking->venue_city ?? '')
                                );
                                $marginPct = $totalPrice > 0 ? round(($row['projected_margin'] / $totalPrice) * 100, 1) : 0;
                            @endphp
                            <tr class="booking-ledger-row" data-search="{{ $searchKey }}" data-status="{{ $payStatus }}">
                                <td>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="ledger-id-badge">#{{ $booking->id }}</span>
                                        <span class="ledger-invoice-badge" title="Nomor Invoice">
                                            <i class="bi bi-receipt"></i> {{ $booking->invoice_number ?? 'Belum Terbit' }}
                                        </span>
                                    </div>
                                    <div class="ledger-couple-names">
                                        {{ $booking->client?->groom_name ?? 'N/A' }} <span class="ledger-amp">&amp;</span> {{ $booking->client?->bride_name ?? 'N/A' }}
                                    </div>
                                    <div class="ledger-meta-row">
                                        <span class="ledger-package-tag">
                                            <i class="bi bi-stars"></i> {{ $booking->package?->name ?? 'Custom Vendor' }}
                                        </span>
                                        <span class="ledger-venue-tag" title="{{ $booking->venue_name ?? '-' }}">
                                            <i class="bi bi-geo-alt"></i> {{ $booking->venue_city ?: ($booking->venue_name ?: 'Venue TBD') }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fin-badge {{ $payStatus }}">{{ $payStatusLabel }}</span>
                                        <span class="num-tabular fw-bold text-dark">{{ $rupiah($paidAmount) }}</span>
                                    </div>
                                    <div class="fin-progress" title="Progres Pelunasan {{ $pct }}%">
                                        <div class="fin-progress-bar {{ $pct >= 100 ? 'bg-success' : 'gold' }}" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-1 pt-1" style="font-size: 11.5px;">
                                        <span class="text-muted">Total: <span class="num-tabular fw-semibold">{{ $rupiah($totalPrice) }}</span></span>
                                        @if ($remaining > 0)
                                            <span class="text-danger fw-bold num-tabular" title="Sisa Piutang Klien">
                                                <i class="bi bi-clock-history"></i> Sisa: {{ $rupiah($remaining) }}
                                            </span>
                                        @else
                                            <span class="text-success fw-bold">
                                                <i class="bi bi-check-circle-fill"></i> Lunas 100%
                                            </span>
                                        @endif
                                    </div>
                                    @php
                                        $pSum = $row['payment_summary'] ?? null;
                                    @endphp
                                    @if ($remaining > 0 && ($pSum['deadline'] ?? null))
                                        <div class="mt-1" style="font-size: 11px;">
                                            <span class="badge {{ $pSum['is_overdue'] ? 'text-bg-danger' : ($pSum['is_urgent'] ? 'text-bg-warning' : 'text-bg-light border text-muted') }}">
                                                <i class="bi {{ $pSum['is_overdue'] ? 'bi-exclamation-triangle-fill' : 'bi-calendar-event' }}"></i>
                                                {{ $pSum['deadline_type'] === 'dp' ? 'Batas DP' : 'Tenggat Lunas' }}:
                                                {{ $pSum['deadline']->locale('id')->translatedFormat('d M Y') }}
                                                @if ($pSum['is_overdue'])
                                                    (Lewat tenggat)
                                                @elseif ($pSum['days_to_deadline'] !== null)
                                                    ({{ $pSum['days_to_deadline'] === 0 ? 'Hari ini' : $pSum['days_to_deadline'] . 'h lagi' }})
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="ledger-outflow-card">
                                        <div class="ledger-outflow-item">
                                            <div class="ledger-outflow-header">
                                                <span><i class="bi bi-shop text-primary me-1"></i> Mitra Vendor ({{ $row['vendor_count'] }})</span>
                                                <span class="num-tabular {{ $row['vendor_remaining'] > 0 ? 'text-amber' : 'text-success' }}">
                                                    {{ $rupiah($row['vendor_paid']) }} / {{ $rupiah($row['vendor_total']) }}
                                                </span>
                                            </div>
                                            @if ($row['vendor_total'] > 0)
                                                @php
                                                    $vPct = min(100, round(($row['vendor_paid'] / $row['vendor_total']) * 100));
                                                @endphp
                                                <div class="fin-progress mini">
                                                    <div class="fin-progress-bar bg-info" style="width: {{ $vPct }}%"></div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ledger-outflow-item mt-2">
                                            <div class="ledger-outflow-header">
                                                <span><i class="bi bi-people text-secondary me-1"></i> Tim Kru ({{ $row['crew_count'] }})</span>
                                                <span class="num-tabular {{ $row['crew_remaining'] > 0 ? 'text-muted' : 'text-success' }}">
                                                    {{ $rupiah($row['crew_paid']) }} / {{ $rupiah($row['crew_total']) }}
                                                </span>
                                            </div>
                                            @if ($row['crew_total'] > 0)
                                                @php
                                                    $cPct = min(100, round(($row['crew_paid'] / $row['crew_total']) * 100));
                                                @endphp
                                                <div class="fin-progress mini">
                                                    <div class="fin-progress-bar bg-secondary" style="width: {{ $cPct }}%"></div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="ledger-cash-chip {{ $row['cash_balance_now'] >= 0 ? 'positive' : 'negative' }}">
                                        <div class="ledger-chip-label">Kas Bersih Realtime</div>
                                        <div class="ledger-chip-val num-tabular">
                                            {{ $row['cash_balance_now'] >= 0 ? '+' : '' }}{{ $rupiah($row['cash_balance_now']) }}
                                        </div>
                                    </div>
                                    <div class="ledger-margin-row mt-1">
                                        <span class="text-muted small">Est. Margin:</span>
                                        <span class="num-tabular fw-bold {{ $row['projected_margin'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $row['projected_margin'] >= 0 ? '+' : '' }}{{ $rupiah($row['projected_margin']) }}
                                            <span class="opacity-75">({{ $marginPct }}%)</span>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <button type="button" class="btn-fin-outline"
                                                data-bs-toggle="modal"
                                                data-bs-target="#bookingHistoryModal{{ $booking->id }}"
                                                title="Lihat Riwayat & Bukti Transfer Klien ({{ $booking->payments->count() }})">
                                            <i class="bi bi-images text-primary"></i>
                                            <span>Bukti ({{ $booking->payments->count() }})</span>
                                        </button>
                                        <a href="{{ route('admin.payments.invoice', $booking) }}"
                                           class="btn-fin-outline" title="Cetak Invoice & Kwitansi Resmi">
                                            <i class="bi bi-printer text-dark"></i>
                                            <span>Invoice</span>
                                        </a>
                                        <a href="{{ route('admin.payments.index', ['tab' => 'vendor_payouts', 'booking_id' => $booking->id]) }}"
                                           class="btn-fin-outline" title="Kelola Pencairan Vendor Booking Ini">
                                            <i class="bi bi-wallet2 text-success"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Belum ada data booking klien.</td>
                            </tr>
                        @endforelse
                        <tr id="bookingNoMatchRow" style="display: none;">
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-search fs-3 d-block mb-2 text-muted opacity-50"></i>
                                Tidak ada booking yang cocok dengan kata kunci atau filter status ini.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- MODAL RIWAYAT & BUKTI TRANSFER PER BOOKING (HOISTED OUTSIDE TABLE FOR VALID HTML) --}}
            @foreach ($projectLedgers as $row)
                @php
                    $booking = $row['booking'];
                    $totalPrice = $row['contract_total'];
                @endphp
                <div class="modal fade" id="bookingHistoryModal{{ $booking->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                            <div class="modal-header text-white" style="background: #7A1F2B; border-radius: 16px 16px 0 0;">
                                <div>
                                    <h5 class="modal-title fw-bold mb-0">Riwayat &amp; Bukti Transfer Klien — Booking #{{ $booking->id }}</h5>
                                    <small class="opacity-75">{{ $booking->client?->groom_name ?? '' }} &amp; {{ $booking->client?->bride_name ?? '' }} · Kontrak: {{ $rupiah($totalPrice) }}</small>
                                </div>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4">
                                @if ($booking->payments->isEmpty())
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-receipt fs-2 d-block mb-2 text-muted opacity-50"></i>
                                        Belum ada catatan pembayaran atau bukti transfer pada booking ini.
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table class="finance-table border">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Tahapan</th>
                                                    <th>Nominal</th>
                                                    <th>Metode / Rekening</th>
                                                    <th>Status</th>
                                                    <th class="text-end">Bukti Struk</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($booking->payments as $p)
                                                    @php
                                                        $tName = ['dp' => 'DP', 'full' => 'Lunas', 'remaining' => 'Pelunasan'][$p->type] ?? strtoupper($p->type);
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $p->created_at?->format('d/m/Y H:i') }}</td>
                                                        <td class="fw-bold">{{ $tName }}</td>
                                                        <td class="num-tabular">{{ $rupiah($p->amount) }}</td>
                                                        <td class="small">{{ $p->bank_info ?: '-' }}</td>
                                                        <td><span class="fin-badge {{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                                                        <td class="text-end">
                                                            @if ($p->proof_url)
                                                                <button type="button" class="btn-fin-outline"
                                                                        onclick="openProofPreview('{{ $p->proof_url }}', 'Bukti Transfer #{{ $booking->id }} — {{ $tName }} ({{ $rupiah($p->amount) }})')">
                                                                    <i class="bi bi-file-earmark-image"></i> Lihat Struk
                                                                </button>
                                                            @else
                                                                <span class="text-muted small">Tanpa file</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                            <div class="modal-footer bg-light" style="border-radius: 0 0 16px 16px;">
                                <a href="{{ route('admin.payments.invoice', $booking) }}" class="btn-fin-primary">
                                    <i class="bi bi-printer"></i> Buka Invoice &amp; Kwitansi Resmi
                                </a>
                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        {{-- ==================================================================
             TAB 3: LAPORAN KEUANGAN & ARUS KAS ORGANIZER
             ================================================================== --}}
        @if ($activeTab === 'report')
            <div class="report-shell">
                <div class="report-header-bar">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">Laporan Keuangan &amp; Laba Rugi Organizer</h5>
                        <div class="small text-muted">
                            Dicetak pada {{ now()->locale('id')->translatedFormat('d F Y, H:i') }} WIB
                            · {{ $reportSummary['count'] }} Booking
                            @if ($reportFrom || $reportTo)
                                · Periode: <strong>{{ $reportFrom ?: 'Awal' }}</strong> s.d. <strong>{{ $reportTo ?: 'Sekarang' }}</strong>
                            @endif
                        </div>
                    </div>
                    <span class="fin-badge verified">Margin Estimasi: {{ $reportSummary['margin_pct'] }}%</span>
                </div>

                {{-- FILTER PERIODE & STATUS LAPORAN --}}
                <form method="GET" action="{{ route('admin.payments.index') }}" class="report-filter-bar no-print">
                    <input type="hidden" name="tab" value="report">
                    <div class="report-filter-field">
                        <label for="reportFrom">Dari Tanggal Acara/Booking</label>
                        <input type="date" id="reportFrom" name="report_from" value="{{ $reportFrom }}" class="form-control form-control-sm">
                    </div>
                    <div class="report-filter-field">
                        <label for="reportTo">Sampai Tanggal</label>
                        <input type="date" id="reportTo" name="report_to" value="{{ $reportTo }}" class="form-control form-control-sm">
                    </div>
                    <div class="report-filter-field">
                        <label for="reportStatus">Status Pembayaran</label>
                        <select id="reportStatus" name="report_status" class="form-select form-select-sm" style="min-width: 160px;">
                            <option value="">Semua Status</option>
                            <option value="paid" {{ $reportStatus === 'paid' ? 'selected' : '' }}>Lunas (100%)</option>
                            <option value="dp_paid" {{ $reportStatus === 'dp_paid' ? 'selected' : '' }}>DP Terbayar</option>
                            <option value="unpaid" {{ $reportStatus === 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-fin-primary">
                        <i class="bi bi-funnel"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('admin.payments.index', ['tab' => 'report', 'report_from' => now()->startOfMonth()->toDateString(), 'report_to' => now()->endOfMonth()->toDateString(), 'report_status' => $reportStatus]) }}"
                       class="btn-fin-outline">Bulan Ini</a>
                    @if ($reportFrom || $reportTo || $reportStatus)
                        <a href="{{ route('admin.payments.index', ['tab' => 'report']) }}" class="btn-fin-outline text-danger">Reset</a>
                    @endif
                </form>

                {{-- RINGKASAN ARUS KAS & PROYEKSI LABA SESUAI FILTER --}}
                <div class="report-statement-grid">
                    <div class="report-statement-card">
                        <div class="report-statement-title">
                            <i class="bi bi-cash-coin text-success"></i>
                            <span>Arus Kas Riil (Realized Cash Flow)</span>
                        </div>
                        <div class="report-line">
                            <span>(+) Kas Masuk Klien (Verified)</span>
                            <strong class="num-tabular text-success">{{ $rupiah($reportSummary['client_paid']) }}</strong>
                        </div>
                        <div class="report-line">
                            <span>(-) Pencairan Mitra Vendor</span>
                            <strong class="num-tabular text-danger">-{{ $rupiah($reportSummary['vendor_paid']) }}</strong>
                        </div>
                        <div class="report-line">
                            <span>(-) Pembayaran Honor Kru</span>
                            <strong class="num-tabular text-danger">-{{ $rupiah($reportSummary['crew_paid']) }}</strong>
                        </div>
                        <div class="report-line total">
                            <span>(=) Saldo Kas Riil</span>
                            <span class="num-tabular {{ $reportSummary['cash_balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $rupiah($reportSummary['cash_balance']) }}
                            </span>
                        </div>
                    </div>

                    <div class="report-statement-card">
                        <div class="report-statement-title">
                            <i class="bi bi-graph-up-arrow text-primary"></i>
                            <span>Proyeksi Laba &amp; Kewajiban Organizer</span>
                        </div>
                        <div class="report-line">
                            <span>(+) Nilai Kontrak Terfilter</span>
                            <strong class="num-tabular text-dark">{{ $rupiah($reportSummary['contract_total']) }}</strong>
                        </div>
                        <div class="report-line">
                            <span>(-) Komitmen Mitra Vendor (Nett)</span>
                            <strong class="num-tabular text-danger">-{{ $rupiah($reportSummary['vendor_total']) }}</strong>
                        </div>
                        <div class="report-line">
                            <span>(-) Komitmen Honor Kru</span>
                            <strong class="num-tabular text-danger">-{{ $rupiah($reportSummary['crew_total']) }}</strong>
                        </div>
                        <div class="report-line total">
                            <span>(=) Proyeksi Laba Bersih WO ({{ $reportSummary['margin_pct'] }}%)</span>
                            <span class="num-tabular {{ $reportSummary['projected_margin'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $rupiah($reportSummary['projected_margin']) }}
                            </span>
                        </div>
                        <div class="report-line meta">
                            <span>Piutang Klien: <strong class="num-tabular text-dark">{{ $rupiah($reportSummary['client_remaining']) }}</strong></span>
                            <span>Sisa Hutang Vendor/Kru: <strong class="num-tabular text-danger">{{ $rupiah($reportSummary['outflow_remaining']) }}</strong></span>
                        </div>
                    </div>
                </div>

                {{-- TABEL REKAP PER BOOKING --}}
                <div class="report-table-wrap table-responsive">
                    <table class="finance-table" id="organizerReportTable">
                        <thead>
                            <tr>
                                <th>Booking &amp; Mempelai</th>
                                <th>Tgl Acara</th>
                                <th>Status</th>
                                <th class="text-end">Nilai Kontrak</th>
                                <th class="text-end">Kas Masuk</th>
                                <th class="text-end">Beban Vendor &amp; Kru</th>
                                <th class="text-end">Kas Riil</th>
                                <th class="text-end">Est. Margin WO</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reportLedgers as $row)
                                @php
                                    $b = $row['booking'];
                                    $st = $b->payment_status ?: 'unpaid';
                                    $stLabel = match ($st) {
                                        'paid' => 'Lunas',
                                        'dp_paid' => 'DP Terbayar',
                                        'awaiting_verification' => 'Menunggu Verifikasi',
                                        default => 'Belum Bayar',
                                    };
                                    $totalBeban = $row['vendor_total'] + $row['crew_total'];
                                    $evDate = $row['events']->pluck('event_date')->filter()->sort()->first() ?? $b->created_at;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-bold">#{{ $b->id }} · {{ $b->client?->groom_name ?? 'N/A' }} &amp; {{ $b->client?->bride_name ?? 'N/A' }}</div>
                                        <div class="small text-muted">{{ $b->invoice_number ?? '-' }} · {{ $b->package?->name ?? 'Custom Vendor' }}</div>
                                    </td>
                                    <td class="small text-muted">{{ $evDate ? \Carbon\Carbon::parse($evDate)->format('d/m/Y') : '-' }}</td>
                                    <td><span class="fin-badge {{ $st }}">{{ $stLabel }}</span></td>
                                    <td class="text-end num-tabular">{{ $rupiah($row['contract_total']) }}</td>
                                    <td class="text-end num-tabular text-success">{{ $rupiah($row['client_paid']) }}</td>
                                    <td class="text-end num-tabular">{{ $rupiah($totalBeban) }}</td>
                                    <td class="text-end num-tabular {{ $row['cash_balance_now'] >= 0 ? 'text-dark' : 'text-danger' }}">
                                        {{ $rupiah($row['cash_balance_now']) }}
                                    </td>
                                    <td class="text-end num-tabular fw-bold {{ $row['projected_margin'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $rupiah($row['projected_margin']) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Tidak ada data booking yang cocok dengan filter laporan ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($reportLedgers->isNotEmpty())
                            <tfoot>
                                <tr>
                                    <td colspan="3">TOTAL ({{ $reportSummary['count'] }} BOOKING)</td>
                                    <td class="text-end num-tabular">{{ $rupiah($reportSummary['contract_total']) }}</td>
                                    <td class="text-end num-tabular text-success">{{ $rupiah($reportSummary['client_paid']) }}</td>
                                    <td class="text-end num-tabular">{{ $rupiah($reportSummary['outflow_committed']) }}</td>
                                    <td class="text-end num-tabular {{ $reportSummary['cash_balance'] >= 0 ? 'text-dark' : 'text-danger' }}">{{ $rupiah($reportSummary['cash_balance']) }}</td>
                                    <td class="text-end num-tabular {{ $reportSummary['projected_margin'] >= 0 ? 'text-success' : 'text-danger' }}">{{ $rupiah($reportSummary['projected_margin']) }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- ==========================================================================
     MODAL CATAT PEMBAYARAN KLIEN MANUAL (OFFLINE / TUNAI / TRANSFER VIA WA)
     ========================================================================== --}}
<div class="modal fade" id="manualPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <form action="{{ route('admin.payments.store-manual') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header text-white" style="background: #7A1F2B; border-radius: 16px 16px 0 0;">
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Catat Pembayaran Klien (Manual)</h5>
                        <small class="opacity-75">Untuk pembayaran tunai di kantor WO, EDC, atau bukti transfer via WhatsApp</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Pilih Booking Klien <span class="text-danger">*</span></label>
                        <select name="booking_id" id="manualBookingSelect" class="form-select" required>
                            <option value="">-- Pilih Booking --</option>
                            @foreach ($bookings as $b)
                                @php
                                    $rem = max(0, (float) $b->total_price - (float) $b->paid_amount);
                                    $dpAmt = (int) ceil((float) $b->total_price * ($dpPercent ?? 10) / 100);
                                @endphp
                                <option value="{{ $b->id }}"
                                        data-total="{{ (int) $b->total_price }}"
                                        data-dp="{{ $dpAmt }}"
                                        data-remaining="{{ (int) $rem }}">
                                    #{{ $b->id }} - {{ $b->client->groom_name ?? '' }} &amp; {{ $b->client->bride_name ?? '' }} (Sisa: {{ $rupiah($rem) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase">Tahapan Pembayaran</label>
                            <select name="type" id="manualTypeSelect" class="form-select" required>
                                <option value="dp">Down Payment (DP {{ $dpPercent ?? 10 }}%)</option>
                                <option value="remaining">Pelunasan Sisa Tagihan</option>
                                <option value="full">Pembayaran Lunas (100%)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase">Metode Pembayaran</label>
                            <select name="method" class="form-select" required>
                                <option value="bank_transfer">Transfer Bank (Konfirmasi WA)</option>
                                <option value="cash">Tunai di Kantor WO</option>
                                <option value="edc">Debit / QRIS Kantor WO</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Nominal Pembayaran (Rp) <span class="text-danger">*</span></label>
                        <input type="number" step="1" min="1" name="amount" id="manualAmountInput" class="form-control num-tabular" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Info Bank / Referensi Kwitansi</label>
                        <input type="text" name="bank_info" class="form-control form-control-sm" placeholder="Contoh: Transfer BCA a.n. Budi / Kwitansi Tunai #01">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Upload Foto Bukti Transfer / Kwitansi</label>
                        <input type="file" name="proof" accept=".jpg,.jpeg,.png,.pdf" class="form-control form-control-sm">
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold small text-uppercase">Catatan Admin</label>
                        <input type="text" name="admin_note" class="form-control form-control-sm" placeholder="Catatan verifikasi...">
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-radius: 0 0 16px 16px;">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-fin-primary">
                        <i class="bi bi-check2-circle"></i> Simpan &amp; Verifikasi Otomatis
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ==========================================================================
     MODAL PREVIEW BUKTI TRANSFER (GAMBAR / PDF)
     ========================================================================== --}}
<div class="modal fade" id="proofPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header text-white" style="background: #7A1F2B; border-radius: 16px 16px 0 0;">
                <h6 class="modal-title fw-bold mb-0" id="proofPreviewTitle">Preview Bukti Transfer</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div class="proof-preview-box" id="proofPreviewContainer"></div>
            </div>
            <div class="modal-footer bg-light">
                <a href="#" target="_blank" id="proofOpenNewTabBtn" class="btn-fin-outline">
                    <i class="bi bi-box-arrow-up-right"></i> Buka di Tab Baru
                </a>
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    @vite('resources/js/admin/finance/index.js')
@endpush
@endsection
