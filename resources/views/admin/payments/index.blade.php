@extends('layouts.admin')

@section('title', 'Pusat Keuangan & Kas')
@section('page-title', 'Pusat Keuangan & Kas')

@push('styles')
    @vite('resources/css/admin/finance/index.css')
@endpush

@section('content')
@php
    $rupiah = fn ($n) => 'Rp ' . number_format((float) ($n ?? 0), 0, ',', '.');
    $pendingCount = (int) ($counts['pending'] ?? 0);
    $unpaidVendorCount = $vendorSchedules->where('payment_status', '!=', 'paid')->count();
@endphp

<div class="finance-page">
    {{-- CLEAN HEADER --}}
    <div class="finance-header">
        <div>
            <div class="finance-header-eyebrow">KEUANGAN &amp; ARUS KAS</div>
            <h1>Pusat Keuangan &amp; Kas Acara</h1>
            <p>Verifikasi bukti transfer klien, atur pencairan dana ke rekening vendor, dan pantau buku kas serta invoice per booking.</p>
        </div>
        <div class="finance-header-actions">
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
                <span>Antrean Verifikasi:</span>
                <strong class="num-tabular text-warning-emphasis">{{ $rupiah($summary['pending_verification_amount']) }}</strong>
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
    <div class="finance-workspace">
        <div class="finance-workspace-head">
            <div class="finance-nav-tabs">
                <a href="{{ route('admin.payments.index', ['tab' => 'verification', 'status' => $status]) }}"
                   class="finance-tab-link {{ $activeTab === 'verification' ? 'active' : '' }}">
                    <i class="bi bi-patch-check"></i>
                    <span>Verifikasi Transfer Klien</span>
                    @if ($pendingCount > 0)
                        <span class="finance-tab-badge">{{ $pendingCount }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.payments.index', ['tab' => 'vendor_payouts']) }}"
                   class="finance-tab-link {{ $activeTab === 'vendor_payouts' ? 'active' : '' }}">
                    <i class="bi bi-send-check"></i>
                    <span>Pencairan Dana Vendor</span>
                    @if ($unpaidVendorCount > 0)
                        <span class="finance-tab-badge">{{ $unpaidVendorCount }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.payments.index', ['tab' => 'bookings']) }}"
                   class="finance-tab-link {{ $activeTab === 'bookings' ? 'active' : '' }}">
                    <i class="bi bi-journal-bookmark-fill"></i>
                    <span>Buku Kas &amp; Invoice Booking</span>
                    <span class="finance-tab-badge">{{ $bookings->count() }}</span>
                </a>
            </div>

            {{-- CONTEXTUAL FILTER RIGHT INSIDE HEADER --}}
            @if ($activeTab === 'verification')
                <div class="finance-pill-group">
                    @foreach ($statuses as $key => $label)
                        <a href="{{ route('admin.payments.index', ['tab' => 'verification', 'status' => $key]) }}"
                           class="finance-pill {{ $status === $key ? 'active' : '' }}">
                            <span>{{ $label }}</span>
                            <span class="badge rounded-pill text-bg-light">{{ $counts[$key] ?? 0 }}</span>
                        </a>
                    @endforeach
                </div>
            @elseif ($activeTab === 'vendor_payouts')
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
                        <select name="booking_id" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 210px;">
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
                <div>
                    <input type="text" id="bookingSearchInput" class="form-control form-control-sm"
                           placeholder="Cari mempelai / invoice / venue..." style="min-width: 240px;">
                </div>
            @endif
        </div>

        {{-- ==================================================================
             TAB 1: VERIFIKASI PEMBAYARAN KLIEN (INFLOW)
             ================================================================== --}}
        @if ($activeTab === 'verification')
            <div class="table-responsive">
                <table class="finance-table">
                    <thead>
                        <tr>
                            <th>Klien &amp; Invoice</th>
                            <th>Nominal &amp; Skema</th>
                            <th>Metode &amp; Rekening</th>
                            <th>Bukti Struk</th>
                            <th>Status</th>
                            <th class="text-end">Aksi Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            @php
                                $booking = $payment->booking;
                                $typeLabel = [
                                    'dp' => 'Down Payment (DP)',
                                    'full' => 'Lunas Penuh (100%)',
                                    'remaining' => 'Pelunasan Sisa',
                                ][$payment->type] ?? strtoupper($payment->type);
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $booking?->client?->groom_name ?? 'N/A' }} &amp; {{ $booking?->client?->bride_name ?? 'N/A' }}</div>
                                    <div class="text-muted small">
                                        #{{ $booking?->id }}{{ $booking?->invoice_number ? ' · ' . $booking->invoice_number : '' }}
                                        · {{ $booking?->package?->name ?? 'Custom Vendor' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="num-tabular fs-6 text-dark">{{ $rupiah($payment->amount) }}</div>
                                    <span class="badge text-bg-light border">{{ $typeLabel }}</span>
                                    <span class="text-muted small ms-1">({{ $payment->created_at?->format('d/m/Y H:i') }})</span>
                                </td>
                                <td>
                                    @if ($payment->method === 'midtrans')
                                        <div class="small fw-semibold text-primary">
                                            <i class="bi bi-lightning-charge-fill"></i> Midtrans Payment Gateway
                                        </div>
                                        <div class="text-muted" style="font-size: 11px;">
                                            Order ID: <code>{{ $payment->midtrans_order_id ?? '-' }}</code>
                                            @if ($payment->transaction_status)
                                                · <span class="badge text-bg-light border">{{ strtoupper($payment->transaction_status) }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="small fw-semibold">{{ $payment->bank_info ?: 'Transfer Rekening WO' }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($payment->proof_url)
                                        <button type="button" class="btn-fin-outline"
                                                onclick="openProofPreview('{{ $payment->proof_url }}', 'Bukti Transfer Klien — #{{ $booking?->id }} ({{ $typeLabel }}: {{ $rupiah($payment->amount) }})')">
                                            <i class="bi bi-eye-fill text-success"></i>
                                            <span>Preview Struk</span>
                                        </button>
                                    @elseif ($payment->method === 'midtrans')
                                        <span class="fin-badge verified">
                                            <i class="bi bi-shield-check"></i> Otomatis Midtrans
                                        </span>
                                    @else
                                        <span class="fin-badge unpaid">Tanpa Struk</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fin-badge {{ $payment->status }}">
                                        {{ $statuses[$payment->status] ?? ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="text-end" style="min-width: 260px;">
                                    @if ($payment->status === 'pending')
                                        <div class="d-inline-flex align-items-center gap-1 flex-wrap justify-content-end">
                                            <form action="{{ route('admin.payments.verify', $payment) }}" method="POST"
                                                  onsubmit="return confirm('Verifikasi pembayaran {{ $rupiah($payment->amount) }} ini?')">
                                                @csrf
                                                <button type="submit" class="btn-fin-primary">
                                                    <i class="bi bi-check2-circle"></i> Setujui
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.payments.reject', $payment) }}" method="POST" class="d-inline-flex gap-1">
                                                @csrf
                                                <input type="text" name="admin_note" class="form-control form-control-sm"
                                                       placeholder="Alasan..." style="width: 115px;">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Tolak</button>
                                            </form>
                                        </div>
                                    @else
                                        <div class="small text-muted">
                                            {{ $payment->verified_at?->format('d/m/Y H:i') ?? '-' }}
                                            @if ($payment->admin_note)
                                                <div class="text-secondary">"{{ $payment->admin_note }}"</div>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    Tidak ada pembayaran dengan status <strong>{{ $statuses[$status] ?? $status }}</strong>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

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

                            {{-- MODAL PENCAIRAN DANA VENDOR --}}
                            <div class="modal fade" id="vendorPayoutModal{{ $schedule->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                                        <form action="{{ route('admin.payments.vendor-payout', $schedule) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="redirect_vendor_status" value="{{ $vendorStatusFilter }}">
                                            <input type="hidden" name="redirect_booking_id" value="{{ $bookingFilter }}">

                                            <div class="modal-header text-white" style="background: #073d36; border-radius: 16px 16px 0 0;">
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
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">Tidak ada data pencairan vendor.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
                                $searchKey = strtolower(($booking->client->groom_name ?? '') . ' ' . ($booking->client->bride_name ?? '') . ' ' . ($booking->invoice_number ?? '') . ' ' . ($booking->venue_name ?? ''));
                            @endphp
                            <tr data-search="{{ $searchKey }}">
                                <td>
                                    <div class="fw-bold">#{{ $booking->id }} · {{ $booking->client->groom_name ?? 'N/A' }} &amp; {{ $booking->client->bride_name ?? 'N/A' }}</div>
                                    <div class="small text-muted">
                                        {{ $booking->invoice_number ?? 'Invoice Belum Terbit' }}
                                        · {{ $booking->package->name ?? 'Custom Vendor' }} ({{ $booking->venue_city }})
                                    </div>
                                </td>
                                <td style="min-width: 210px;">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fin-badge {{ $payStatus }}">{{ $payStatusLabel }}</span>
                                        <span class="num-tabular small">{{ $rupiah($paidAmount) }} / {{ $rupiah($totalPrice) }}</span>
                                    </div>
                                    <div class="fin-progress">
                                        <div class="fin-progress-bar {{ $pct < 100 ? 'gold' : '' }}" style="width: {{ $pct }}%"></div>
                                    </div>
                                    @if ($remaining > 0)
                                        <div class="small text-danger num-tabular mt-1">Piutang: {{ $rupiah($remaining) }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="small num-tabular">
                                        Vendor ({{ $row['vendor_count'] }}): <strong class="text-dark">{{ $rupiah($row['vendor_paid']) }}</strong> / {{ $rupiah($row['vendor_total']) }}
                                    </div>
                                    <div class="small num-tabular mt-1">
                                        Kru ({{ $row['crew_count'] }}): <strong class="text-dark">{{ $rupiah($row['crew_paid']) }}</strong> / {{ $rupiah($row['crew_total']) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="num-tabular {{ $row['cash_balance_now'] >= 0 ? 'text-dark' : 'text-danger' }}">
                                        Kas: {{ $rupiah($row['cash_balance_now']) }}
                                    </div>
                                    <div class="small num-tabular {{ $row['projected_margin'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        Est. Margin: {{ $rupiah($row['projected_margin']) }}
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" class="btn-fin-outline"
                                                data-bs-toggle="modal"
                                                data-bs-target="#bookingHistoryModal{{ $booking->id }}"
                                                title="Lihat Bukti Transfer Klien">
                                            <i class="bi bi-images"></i>
                                            <span>Bukti ({{ $booking->payments->count() }})</span>
                                        </button>
                                        <a href="{{ route('admin.payments.invoice', $booking) }}"
                                           class="btn-fin-outline" title="Cetak Invoice & Kwitansi">
                                            <i class="bi bi-printer"></i>
                                            <span>Invoice</span>
                                        </a>
                                        <a href="{{ route('admin.payments.index', ['tab' => 'vendor_payouts', 'booking_id' => $booking->id]) }}"
                                           class="btn-fin-outline" title="Kelola Pencairan Vendor Booking Ini">
                                            <i class="bi bi-shop"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            {{-- MODAL RIWAYAT & BUKTI TRANSFER PER BOOKING --}}
                            <div class="modal fade" id="bookingHistoryModal{{ $booking->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                                        <div class="modal-header text-white" style="background: #073d36; border-radius: 16px 16px 0 0;">
                                            <div>
                                                <h5 class="modal-title fw-bold mb-0">Riwayat &amp; Bukti Transfer Klien — Booking #{{ $booking->id }}</h5>
                                                <small class="opacity-75">{{ $booking->client->groom_name ?? '' }} &amp; {{ $booking->client->bride_name ?? '' }} · Kontrak: {{ $rupiah($totalPrice) }}</small>
                                            </div>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            @if ($booking->payments->isEmpty())
                                                <div class="text-center py-4 text-muted">
                                                    <i class="bi bi-receipt fs-2 d-block mb-2"></i>
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
                                        <div class="modal-footer bg-light">
                                            <a href="{{ route('admin.payments.invoice', $booking) }}" class="btn-fin-primary">
                                                <i class="bi bi-printer"></i> Buka Invoice &amp; Kwitansi Resmi
                                            </a>
                                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Belum ada data booking klien.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
                <div class="modal-header text-white" style="background: #073d36; border-radius: 16px 16px 0 0;">
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
                                    $dpAmt = (int) ceil((float) $b->total_price * ($dpPercent ?? 30) / 100);
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
                                <option value="dp">Down Payment (DP {{ $dpPercent ?? 30 }}%)</option>
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
            <div class="modal-header text-white" style="background: #073d36; border-radius: 16px 16px 0 0;">
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
<script>
function openProofPreview(url, title) {
    const modalEl = document.getElementById('proofPreviewModal');
    const titleEl = document.getElementById('proofPreviewTitle');
    const container = document.getElementById('proofPreviewContainer');
    const newTabBtn = document.getElementById('proofOpenNewTabBtn');

    titleEl.textContent = title || 'Preview Bukti Transfer';
    newTabBtn.href = url;

    if (url.toLowerCase().endsWith('.pdf')) {
        container.innerHTML = `<iframe src="${url}" style="width:100%; height:65vh; border:0; border-radius:8px; background:#fff;"></iframe>`;
    } else {
        container.innerHTML = `<img src="${url}" alt="Bukti Transfer">`;
    }

    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.vendor-scheme-select').forEach(function (select) {
        const targetSelector = select.getAttribute('data-target');
        const targetEl = targetSelector ? document.querySelector(targetSelector) : null;
        if (!targetEl) return;

        const syncVisibility = () => {
            targetEl.style.display = select.value === 'custom' ? 'block' : 'none';
        };
        syncVisibility();
        select.addEventListener('change', syncVisibility);
    });

    const bookingSelect = document.getElementById('manualBookingSelect');
    const typeSelect = document.getElementById('manualTypeSelect');
    const amountInput = document.getElementById('manualAmountInput');

    function syncManualAmount() {
        if (!bookingSelect || !typeSelect || !amountInput) return;
        const opt = bookingSelect.selectedOptions[0];
        if (!opt || !opt.value) return;

        const type = typeSelect.value;
        if (type === 'dp') {
            amountInput.value = opt.getAttribute('data-dp') || '';
        } else if (type === 'remaining') {
            amountInput.value = opt.getAttribute('data-remaining') || '';
        } else {
            amountInput.value = opt.getAttribute('data-total') || '';
        }
    }

    bookingSelect?.addEventListener('change', syncManualAmount);
    typeSelect?.addEventListener('change', syncManualAmount);

    const searchInput = document.getElementById('bookingSearchInput');
    if (searchInput) {
        const rows = document.querySelectorAll('#bookingFinanceTable tbody tr[data-search]');
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            rows.forEach(function (row) {
                const match = row.getAttribute('data-search').includes(q);
                row.style.display = match ? '' : 'none';
            });
        });
    }
});
</script>
@endpush
@endsection
