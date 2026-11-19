@extends('layouts.admin')

@section('title', 'Honor & Pencairan Fee Kru')
@section('page-title', 'Honor & Pencairan Fee Kru')

@push('styles')
    @vite(['resources/css/admin/tracking.css', 'resources/css/admin/finance/index.css'])
@endpush

@section('content')
<div class="tracking-page" id="payrollWorkspace" data-ajax-workspace>
    {{-- Header --}}
    <div class="tracking-header">
        <div>
            <div class="tracking-eyebrow">Manajemen Keuangan Kru WO</div>
            <h2>Honorarium, Rekening &amp; Bukti Pencairan Kru</h2>
            <p>Validasi jam kerja aktual dari lapangan, setujui honor kru (ACC), dan unggah bukti transfer pencairan ke rekening kru secara transparan.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.payments.index') }}" class="btn-fin-primary">
                <i class="bi bi-wallet2"></i> Pusat Keuangan &amp; Kas
            </a>
            <a href="{{ route('admin.tracking.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Monitoring Tracking
            </a>
        </div>
    </div>

    {{-- Financial Statistics Cards --}}
    <div class="payroll-stat-grid">
        <div class="tracking-stat-card">
            <div class="tracking-stat-info">
                <small>Menunggu Validasi &amp; ACC</small>
                <strong style="color: #b7791f;">Rp {{ number_format($totalPendingFee, 0, ',', '.') }}</strong>
            </div>
            <div class="tracking-stat-icon" style="background: #fff3d6; color: #b7791f;">
                <i class="bi bi-hourglass-top"></i>
            </div>
        </div>

        <div class="tracking-stat-card">
            <div class="tracking-stat-info">
                <small>Disetujui (Siap Dicairkan)</small>
                <strong style="color: #0284c7;">Rp {{ number_format($totalApprovedFee, 0, ',', '.') }}</strong>
            </div>
            <div class="tracking-stat-icon" style="background: #e0f2fe; color: #0284c7;">
                <i class="bi bi-patch-check-fill"></i>
            </div>
        </div>

        <div class="tracking-stat-card">
            <div class="tracking-stat-info">
                <small>Total Lunas Dicairkan</small>
                <strong style="color: #0d6e4c;">Rp {{ number_format($totalPaidFee, 0, ',', '.') }}</strong>
            </div>
            <div class="tracking-stat-icon" style="background: #edf4f1; color: #0d6e4c;">
                <i class="bi bi-cash-stack"></i>
            </div>
        </div>

        <div class="tracking-stat-card">
            <div class="tracking-stat-info">
                <small>Total Kru Terdaftar</small>
                <strong style="color: #073d36;">{{ $totalMembersAssigned }} Orang</strong>
            </div>
            <div class="tracking-stat-icon" style="background: #e6f4ea; color: #073d36;">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="payroll-filter-bar">
        <form method="GET" action="{{ route('admin.payroll.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Status Pembayaran</label>
                <select name="status" class="form-select form-select-sm" data-ajax-auto-submit="true">
                    <option value="">-- Semua Status --</option>
                    <option value="pending" @selected($status === 'pending')>Menunggu ACC Admin</option>
                    <option value="approved" @selected($status === 'approved')>Disetujui (Siap Cair)</option>
                    <option value="paid" @selected($status === 'paid')>Lunas Dicairkan</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted mb-1">Filter Event / Acara</label>
                <select name="event_id" class="form-select form-select-sm" data-ajax-auto-submit="true">
                    <option value="">-- Semua Acara --</option>
                    @foreach($events as $ev)
                        <option value="{{ $ev->id }}" @selected((string)$eventId === (string)$ev->id)>
                            #{{ $ev->id }} - {{ $ev->name ?? $ev->title }} ({{ \Carbon\Carbon::parse($ev->event_date)->translatedFormat('d M Y') }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Cari Nama Kru</label>
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Nama / Call sign..." value="{{ $search }}">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            <div class="col-md-2 d-flex align-items-end mt-auto">
                <a href="{{ route('admin.payroll.index') }}" class="btn btn-sm btn-light border w-100" title="Reset Filter">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Batch Action Bar --}}
    <div id="batchActionBar" class="batch-action-bar">
        <div>
            <i class="bi bi-check2-square me-2"></i>
            <span id="selectedCountText">0 kru dipilih</span>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-warning text-dark fw-semibold" onclick="handleBatchAction('approve')">
                <i class="bi bi-check-all"></i> ACC Semua Terpilih
            </button>
            <button type="button" class="btn btn-sm btn-success fw-semibold" onclick="handleBatchAction('pay')">
                <i class="bi bi-cash"></i> Cairkan &amp; Lunasi Terpilih
            </button>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-payroll">
                <thead>
                    <tr>
                        <th style="width: 40px;" class="text-center">
                            <input type="checkbox" id="checkAll" class="form-check-input">
                        </th>
                        <th>Kru, Peran &amp; Rekening</th>
                        <th>Acara &amp; Tanggal</th>
                        <th>Kehadiran &amp; Jam</th>
                        <th>Tunjangan</th>
                        <th>Total Honor</th>
                        <th>Status &amp; Bukti Transfer</th>
                        <th style="width: 190px;" class="text-center">Aksi Manajemen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eventMembers as $em)
                        @php
                            $isCoord = $em->isCoordinator();
                            $rowStatus = $em->payment_status ?? 'pending';
                        @endphp
                        <tr id="row-{{ $em->id }}">
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input item-checkbox" value="{{ $em->id }}" onchange="onItemCheckChange()">
                            </td>
                            <td>
                                <div class="d-flex align-items-start gap-2">
                                    <div class="member-avatar-circle {{ $isCoord ? 'coord' : 'crew' }}" style="width: 36px; height: 36px; font-size: 13px;">
                                        {{ strtoupper(substr($em->member?->name ?? 'M', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold" style="color: #173b32;">
                                            {{ $em->member?->name ?? 'Member #' . $em->member_id }}
                                        </div>
                                        <div class="small text-muted mb-1">
                                            {{ $em->role }} @if($em->member?->call_sign) • "{{ $em->member->call_sign }}" @endif
                                        </div>
                                        @if($em->member?->formatted_bank_account)
                                            <span class="bank-pill">
                                                <i class="bi bi-bank"></i> {{ $em->member->formatted_bank_account }}
                                            </span>
                                        @else
                                            <span class="bank-pill missing">
                                                <i class="bi bi-exclamation-circle"></i> Rekening belum diisi
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($em->event)
                                    <a href="{{ route('admin.tracking.show', $em->event_id) }}" class="fw-semibold text-decoration-none text-dark d-block">
                                        {{ $em->event->name ?? $em->event->title }}
                                    </a>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar-event"></i> {{ \Carbon\Carbon::parse($em->event->event_date)->translatedFormat('d M Y') }}
                                    </small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <span class="badge bg-light text-dark border">
                                        {{ ucfirst($em->attendance_status ?? 'present') }}
                                    </span>
                                </div>
                                <small class="text-muted">
                                    {{ number_format((float) ($em->actual_hours ?? 0), 1) }} Jam Kerja
                                    @if((float) ($em->member?->daily_fee ?? 0) > 0)
                                        <span class="text-secondary">(Rp {{ number_format((float) $em->member->daily_fee / 8, 0, ',', '.') }}/jam)</span>
                                    @endif
                                </small>
                            </td>
                            <td>
                                @if((float) ($em->allowance ?? 0) > 0)
                                    <span class="fw-semibold text-success">+ Rp {{ number_format((float) $em->allowance, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <strong class="num-tabular" style="color: #073d36; font-size: 0.95rem;">
                                    Rp {{ number_format((float) ($em->total_fee ?? 0), 0, ',', '.') }}
                                </strong>
                            </td>
                            <td>
                                @if($rowStatus === 'paid')
                                    <span class="badge status-badge-paid px-2 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i> Lunas Dibayar
                                    </span>
                                @elseif($rowStatus === 'approved')
                                    <span class="badge status-badge-approved px-2 py-1">
                                        <i class="bi bi-patch-check-fill me-1"></i> Disetujui (Siap Cair)
                                    </span>
                                @else
                                    <span class="badge status-badge-pending px-2 py-1">
                                        <i class="bi bi-hourglass-split me-1"></i> Menunggu ACC
                                    </span>
                                @endif

                                @if($em->payout_proof_url)
                                    <div class="mt-1">
                                        <button type="button" class="btn-fin-outline py-1 px-2"
                                                onclick="openProofPreview('{{ $em->payout_proof_url }}', 'Bukti Transfer Honor Kru — {{ $em->member?->name }} (Rp {{ number_format((float) $em->total_fee, 0, ',', '.') }})')">
                                            <i class="bi bi-receipt text-success"></i> Lihat Struk
                                        </button>
                                    </div>
                                @endif

                                @if($em->notes)
                                    <div class="small text-muted text-truncate mt-1" style="max-width: 150px;" title="{{ $em->notes }}">
                                        <i class="bi bi-chat-left-text"></i> {{ $em->notes }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    @if($rowStatus === 'pending')
                                        <button type="button" class="btn btn-warning text-dark fw-semibold" title="ACC & Setujui Honor" onclick="updateSingleStatus({{ $em->id }}, 'approved')">
                                            <i class="bi bi-check-lg"></i> ACC
                                        </button>
                                        <button type="button" class="btn btn-success fw-semibold" title="Cairkan & Upload Bukti"
                                                data-bs-toggle="modal" data-bs-target="#disburseModal{{ $em->id }}">
                                            <i class="bi bi-cash"></i> Cairkan
                                        </button>
                                    @elseif($rowStatus === 'approved')
                                        <button type="button" class="btn btn-success fw-semibold" title="Cairkan Dana & Upload Bukti"
                                                data-bs-toggle="modal" data-bs-target="#disburseModal{{ $em->id }}">
                                            <i class="bi bi-cash-stack"></i> Cairkan Dana
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-outline-success" title="Update Bukti Transfer"
                                                data-bs-toggle="modal" data-bs-target="#disburseModal{{ $em->id }}">
                                            <i class="bi bi-upload"></i> Bukti
                                        </button>
                                    @endif

                                    @if($em->event_id)
                                        <a href="{{ route('admin.tracking.show', $em->event_id) }}" class="btn btn-outline-secondary" title="Buka Detail Event Tracking">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Pencairan Dana + Rekening + Bukti Transfer per Kru --}}
                        <div class="modal fade" id="disburseModal{{ $em->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                                    <form action="{{ route('admin.payroll.update-status', $em->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="payment_status" value="paid">
                                        <div class="modal-header text-white" style="background: #073d36; border-radius: 16px 16px 0 0;">
                                            <div>
                                                <h6 class="modal-title fw-bold mb-0">
                                                    <i class="bi bi-cash-coin me-1"></i> Pencairan &amp; Bukti Transfer Honor Kru
                                                </h6>
                                                <small class="opacity-75">{{ $em->member?->name }} — Rp {{ number_format((float) $em->total_fee, 0, ',', '.') }}</small>
                                            </div>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="p-3 rounded-3 border bg-light mb-3">
                                                <div class="small fw-bold text-uppercase text-muted mb-2">
                                                    <i class="bi bi-bank me-1"></i> Rekening Bank Tujuan Kru
                                                </div>
                                                <div class="row g-2">
                                                    <div class="col-4">
                                                        <input type="text" name="bank_name" class="form-control form-control-sm"
                                                               value="{{ $em->member?->bank_name }}" placeholder="Bank (BCA/BRI)">
                                                    </div>
                                                    <div class="col-4">
                                                        <input type="text" name="bank_account_number" class="form-control form-control-sm"
                                                               value="{{ $em->member?->bank_account_number }}" placeholder="No. Rekening">
                                                    </div>
                                                    <div class="col-4">
                                                        <input type="text" name="bank_account_holder" class="form-control form-control-sm"
                                                               value="{{ $em->member?->bank_account_holder ?? $em->member?->name }}" placeholder="Atas Nama">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-uppercase">Upload Struk Bukti Transfer (JPG/PNG/PDF)</label>
                                                <input type="file" name="payout_proof" accept=".jpg,.jpeg,.png,.pdf" class="form-control form-control-sm">
                                                @if($em->payout_proof_url)
                                                    <div class="form-text text-success">
                                                        <i class="bi bi-check-circle-fill"></i> Struk sudah diunggah (terlihat juga di Dashboard Member).
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="mb-0">
                                                <label class="form-label small fw-bold text-uppercase">Catatan Transaksi / Referensi Transfer</label>
                                                <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Contoh: Transfer BCA No. Ref 9823412 a.n {{ $em->member?->name }}"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light" style="border-radius: 0 0 16px 16px;">
                                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn-fin-primary">
                                                <i class="bi bi-check2-circle"></i> Simpan Bukti &amp; Tandai Lunas
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-wallet2 fs-2 d-block mb-2"></i>
                                Belum ada data honor kru untuk kriteria filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($eventMembers->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $eventMembers->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal Preview Bukti Transfer --}}
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
                    <i class="bi bi-box-arrow-up-right"></i> Buka Ukuran Penuh di Tab Baru
                </a>
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Batch Action --}}
<div class="modal fade" id="batchActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: #073d36; color: #fff;">
                <h6 class="modal-title fw-bold" id="batchModalTitle">Konfirmasi Aksi Massal</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="batchActionType">
                <p id="batchModalDesc" class="small mb-3"></p>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Catatan untuk Semua Data Terpilih (Opsional)</label>
                    <textarea id="batchNotes" class="form-control form-control-sm" rows="2" placeholder="Catatan massal..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-sm btn-wo-primary" onclick="confirmBatchAction()">
                    <i class="bi bi-check2"></i> Eksekusi Sekarang
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const batchModal = new bootstrap.Modal(document.getElementById('batchActionModal'));

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

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.item-checkbox:checked')).map(cb => cb.value);
}

function onItemCheckChange() {
    const selected = getSelectedIds();
    const bar = document.getElementById('batchActionBar');
    const countText = document.getElementById('selectedCountText');

    if (selected.length > 0) {
        bar.style.display = 'flex';
        countText.textContent = selected.length + ' kru dipilih';
    } else {
        bar.style.display = 'none';
    }
}

document.getElementById('payrollWorkspace')?.addEventListener('change', function(e) {
    if (e.target && e.target.id === 'checkAll') {
        document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = e.target.checked);
        onItemCheckChange();
    }
});

async function updateSingleStatus(id, status, notes = '') {
    try {
        const response = await fetch(`/admin/payroll/${id}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                payment_status: status,
                notes: notes
            })
        });

        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Gagal mengubah status.');
        }

        location.reload();
    } catch (err) {
        alert(err.message || 'Terjadi kesalahan');
    }
}

function handleBatchAction(action) {
    const ids = getSelectedIds();
    if (ids.length === 0) {
        alert('Silakan pilih minimal 1 kru terlebih dahulu.');
        return;
    }

    document.getElementById('batchActionType').value = action;
    document.getElementById('batchNotes').value = '';

    if (action === 'approve') {
        document.getElementById('batchModalTitle').textContent = 'ACC Massal Honor Kru';
        document.getElementById('batchModalDesc').textContent = `Anda akan menyetujui (ACC) honor untuk ${ids.length} kru terpilih.`;
    } else {
        document.getElementById('batchModalTitle').textContent = 'Pencairan Massal Honor Kru';
        document.getElementById('batchModalDesc').textContent = `Anda akan menandai lunas dicairkan untuk ${ids.length} kru terpilih.`;
    }

    batchModal.show();
}

async function confirmBatchAction() {
    const ids = getSelectedIds();
    const action = document.getElementById('batchActionType').value;
    const notes = document.getElementById('batchNotes').value;

    batchModal.hide();

    try {
        const response = await fetch('/admin/payroll/batch-update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                ids: ids,
                action: action,
                notes: notes
            })
        });

        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Gagal melakukan aksi massal.');
        }

        location.reload();
    } catch (err) {
        alert(err.message || 'Terjadi kesalahan');
    }
}
</script>
@endpush
