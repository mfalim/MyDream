@extends('layouts.admin')

@section('title', 'Honor & Pencairan Fee Kru')
@section('page-title', 'Honor & Pencairan Fee Kru')

@push('styles')
    @vite('resources/css/admin/tracking.css')
    <style>
        .payroll-stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .payroll-filter-bar {
            background: #fff;
            border-radius: 12px;
            padding: 16px;
            border: 1px solid #e2e8f0;
            margin-bottom: 20px;
        }
        .status-badge-pending {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .status-badge-approved {
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }
        .status-badge-paid {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }
        .batch-action-bar {
            background: #073d36;
            color: #fff;
            border-radius: 10px;
            padding: 12px 18px;
            margin-bottom: 16px;
            display: none;
            align-items: center;
            justify-content: space-between;
            animation: fadeIn 0.2s ease-in-out;
        }
        .table-payroll th {
            background: #f8fafc;
            color: #475569;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
<div class="tracking-page">
    {{-- Header --}}
    <div class="tracking-header">
        <div>
            <div class="tracking-eyebrow">Manajemen Keuangan Kru WO</div>
            <h2>Honorarium & Pencairan Fee Kru</h2>
            <p>Validasi jam kerja aktual dari lapangan, setujui honor kru (ACC), dan catat pencairan pembayaran secara transparan.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.tracking.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Ke Monitoring Tracking
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Financial Statistics Cards --}}
    <div class="payroll-stat-grid">
        <div class="tracking-stat-card">
            <div class="tracking-stat-info">
                <small>Menunggu Validasi & ACC</small>
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
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Status --</option>
                    <option value="pending" @selected($status === 'pending')>⏳ Menunggu ACC Admin</option>
                    <option value="approved" @selected($status === 'approved')>✨ Disetujui (Siap Cair)</option>
                    <option value="paid" @selected($status === 'paid')>✅ Lunas Dicairkan</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted mb-1">Filter Event / Acara</label>
                <select name="event_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Acara --</option>
                    @foreach($events as $ev)
                        <option value="{{ $ev->id }}" @selected((string)$eventId === (string)$ev->id)>
                            #{{ $ev->id }} - {{ $ev->title }} ({{ \Carbon\Carbon::parse($ev->event_date)->translatedFormat('d M Y') }})
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
                <i class="bi bi-cash"></i> Cairkan & Lunasi Terpilih
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
                        <th>Kru & Peran</th>
                        <th>Acara & Tanggal</th>
                        <th>Kehadiran & Jam</th>
                        <th>Tunjangan</th>
                        <th>Total Honor</th>
                        <th>Status</th>
                        <th style="width: 180px;" class="text-center">Aksi Manajemen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eventMembers as $em)
                        @php
                            $isCoord = $em->isCoordinator();
                            $status = $em->payment_status ?? 'pending';
                        @endphp
                        <tr id="row-{{ $em->id }}">
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input item-checkbox" value="{{ $em->id }}" onchange="onItemCheckChange()">
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="member-avatar-circle {{ $isCoord ? 'coord' : 'crew' }}" style="width: 36px; height: 36px; font-size: 13px;">
                                        {{ strtoupper(substr($em->member?->name ?? 'M', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold" style="color: #173b32;">
                                            {{ $em->member?->name ?? 'Member #' . $em->member_id }}
                                        </div>
                                        <div class="small text-muted">
                                            {{ $em->role }} @if($em->member?->call_sign) • "{{ $em->member->call_sign }}" @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($em->event)
                                    <a href="{{ route('admin.tracking.show', $em->event_id) }}" class="fw-semibold text-decoration-none text-dark d-block">
                                        {{ $em->event->title }}
                                    </a>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar-event"></i> {{ \Carbon\Carbon::parse($em->event->event_date)->translatedFormat('d M Y') }}
                                        @if($em->event->booking?->client)
                                            • Klien: {{ $em->event->booking->client->name }}
                                        @endif
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
                                <strong style="color: #073d36; font-size: 0.95rem;">
                                    Rp {{ number_format((float) ($em->total_fee ?? 0), 0, ',', '.') }}
                                </strong>
                            </td>
                            <td>
                                @if($status === 'paid')
                                    <span class="badge status-badge-paid px-2 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i> Lunas Dibayar
                                    </span>
                                @elseif($status === 'approved')
                                    <span class="badge status-badge-approved px-2 py-1">
                                        <i class="bi bi-patch-check-fill me-1"></i> Disetujui (Siap Cair)
                                    </span>
                                @else
                                    <span class="badge status-badge-pending px-2 py-1">
                                        <i class="bi bi-hourglass-split me-1"></i> Menunggu ACC
                                    </span>
                                @endif

                                @if($em->notes)
                                    <div class="small text-muted text-truncate mt-1" style="max-width: 140px;" title="{{ $em->notes }}">
                                        <i class="bi bi-chat-left-text"></i> {{ $em->notes }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    @if($status === 'pending')
                                        <button type="button" class="btn btn-warning text-dark fw-semibold" title="ACC & Setujui Honor" onclick="updateSingleStatus({{ $em->id }}, 'approved')">
                                            <i class="bi bi-check-lg"></i> ACC
                                        </button>
                                        <button type="button" class="btn btn-success fw-semibold" title="Langsung Cairkan" onclick="promptDisburse({{ $em->id }})">
                                            <i class="bi bi-cash"></i> Cairkan
                                        </button>
                                    @elseif($status === 'approved')
                                        <button type="button" class="btn btn-success fw-semibold" title="Cairkan Dana & Tandai Lunas" onclick="promptDisburse({{ $em->id }})">
                                            <i class="bi bi-cash-stack"></i> Cairkan Dana
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-outline-secondary" disabled title="Sudah Lunas">
                                            <i class="bi bi-check2-all text-success"></i> Selesai
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

{{-- Modal Catatan Pencairan / Disbursement --}}
<div class="modal fade" id="disburseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: #073d36; color: #fff;">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-cash-coin me-1"></i> Konfirmasi Pencairan Dana Honor Kru
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="disburseMemberId">
                <input type="hidden" id="disburseTargetStatus" value="paid">
                
                <p class="small text-muted mb-3">
                    Pastikan honorarium telah ditransfer ke kru bersangkutan sebelum menandai pembayaran sebagai <strong>Lunas Dicairkan</strong>.
                </p>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Catatan Transaksi / Bukti Transfer (Opsional)</label>
                    <textarea id="disburseNotes" class="form-control form-control-sm" rows="3" placeholder="Contoh: Transfer via BCA No. Ref 9823412 a.n Kru"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-sm btn-success fw-semibold" onclick="confirmDisburse()">
                    <i class="bi bi-check-circle"></i> Konfirmasi & Tandai Lunas
                </button>
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
const disburseModal = new bootstrap.Modal(document.getElementById('disburseModal'));
const batchModal = new bootstrap.Modal(document.getElementById('batchActionModal'));

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

document.getElementById('checkAll')?.addEventListener('change', function() {
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = this.checked);
    onItemCheckChange();
});

function promptDisburse(id) {
    document.getElementById('disburseMemberId').value = id;
    document.getElementById('disburseNotes').value = '';
    disburseModal.show();
}

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

function confirmDisburse() {
    const id = document.getElementById('disburseMemberId').value;
    const notes = document.getElementById('disburseNotes').value;
    disburseModal.hide();
    updateSingleStatus(id, 'paid', notes);
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

