{{-- resources/views/user/vendor-confirmation/index.blade.php --}}
@extends('user.layouts.app')

@section('title', 'Konfirmasi Vendor')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/wo-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vendor-confirmation.css') }}">
@endpush

@section('content')
<main class="wo-page vendor-confirmation">

    <div class="vc-head">
        <div>
            <span class="wo-eyebrow"><i class="bi bi-circle-fill"></i> Vendor Readiness Tracker &bull; Live Synchronized with Atelier Ops</span>
            <h1>Status Konfirmasi Ketersediaan Vendor</h1>
            <p>Pantau ketersediaan dan kesiapan seluruh mitra vendor rekanan untuk hari pernikahan Anda ({{ $eventDateLabel }} di {{ $venue }}).</p>
        </div>

        <button type="button" class="wo-btn wo-btn-dark">
            <i class="bi bi-plus-circle"></i> Rekomendasikan Vendor Tambahan
        </button>
    </div>


    <div class="vc-stat-grid">
        <div class="wo-stat-mini">
            <div><span>Total Ditugaskan</span><strong>{{ $stats['total'] }}</strong> Vendor</div>
            <span class="wo-stat-icon"><i class="bi bi-people"></i></span>
        </div>
        <div class="wo-stat-mini">
            <div><span>Terkonfirmasi Siap</span><strong>{{ $stats['confirmed'] }} / {{ $stats['total'] }}</strong> Locked</div>
            <span class="wo-stat-icon"><i class="bi bi-check-circle"></i></span>
        </div>
        <div class="wo-stat-mini">
            <div><span>Menunggu Konfirmasi</span><strong>{{ $stats['pending'] }}</strong> SLA &lt; 24 Jam</div>
            <span class="wo-stat-icon"><i class="bi bi-hourglass-split"></i></span>
        </div>
        <div class="wo-stat-mini">
            <div><span>Perlu Alternatif</span><strong>{{ $stats['rejected'] }}</strong> Bentrok Jadwal</div>
            <span class="wo-stat-icon"><i class="bi bi-exclamation-triangle"></i></span>
        </div>
    </div>


    <div class="vc-toolbar">
        <div class="vc-search">
            <i class="bi bi-search"></i>
            <input type="text" id="vcSearch" placeholder="Cari nama vendor atau kategori...">
        </div>

        <div class="vc-tabs" id="vcTabs">
            <button type="button" class="vc-tab is-active" data-filter="all">Semua ({{ $stats['total'] }})</button>
            <button type="button" class="vc-tab" data-filter="confirmed">Terkonfirmasi ({{ $stats['confirmed'] }})</button>
            <button type="button" class="vc-tab" data-filter="pending">Menunggu ({{ $stats['pending'] }})</button>
            <button type="button" class="vc-tab" data-filter="rejected">Butuh Pengganti ({{ $stats['rejected'] }})</button>
        </div>
    </div>


    <div class="wo-card vc-table-card">
        <table class="wo-table" id="vcTable">
            <thead>
                <tr>
                    <th>Kategori &amp; Nama Vendor</th>
                    <th>PIC &amp; Koordinasi</th>
                    <th>Jadwal Hari H</th>
                    <th>Status Ketersediaan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vendors as $v)
                    <tr data-status="{{ $v['status'] }}" class="{{ $v['status'] === 'rejected' ? 'is-alert' : '' }}">
                        <td>
                            <span class="vc-category">{{ $v['category'] }}</span>
                            <strong class="vc-name">{{ $v['name'] }}</strong>
                            <span class="vc-note">{{ $v['note'] }}</span>
                        </td>
                        <td>
                            <strong class="vc-pic">{{ $v['pic'] }}</strong>
                            <span class="vc-role">{{ $v['pic_role'] }}</span>
                            <span class="vc-channel"><i class="bi bi-whatsapp"></i> {{ $v['channel'] }}</span>
                        </td>
                        <td>
                            @foreach ($v['schedule'] as $line)
                                <span class="vc-schedule-line">{{ $line }}</span>
                            @endforeach
                        </td>
                        <td>
                            @if ($v['status'] === 'confirmed')
                                <span class="wo-badge wo-badge-green"><i class="bi bi-check-circle"></i> Bisa &amp; Terkonfirmasi</span>
                            @elseif ($v['status'] === 'pending')
                                <span class="wo-badge wo-badge-gold"><i class="bi bi-hourglass-split"></i> Menunggu Respon</span>
                            @else
                                <span class="wo-badge wo-badge-red"><i class="bi bi-arrow-repeat"></i> Butuh Alternatif</span>
                            @endif
                            <span class="vc-status-note">{{ $v['status_note'] }}</span>
                        </td>
                        <td>
                            @if ($v['status'] === 'rejected')
                                <button type="button" class="wo-btn wo-btn-dark wo-btn-sm">Pilih Opsi Pengganti</button>
                            @elseif ($v['status'] === 'pending')
                                <button type="button" class="wo-btn wo-btn-outline wo-btn-sm">Remind WO</button>
                            @else
                                <i class="bi bi-file-earmark-text vc-icon-action" title="Lihat SPK"></i>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


    <div class="vc-guarantee">
        <span class="wo-stat-icon"><i class="bi bi-shield-check"></i></span>
        <div>
            <strong>Garansi Kesiapan 100% oleh WO PROJECT <span class="wo-badge wo-badge-gold">Atelier Shield</span></strong>
            <p>Jika terdapat vendor utama yang berhalangan atau bentrok jadwal mendekati Hari H, sistem koordinasi WO PROJECT secara otomatis menyediakan mitra rekanan selevel tanpa penambahan biaya selisih dan dengan komitmen kualitas setara.</p>
        </div>
        <button type="button" class="wo-btn wo-btn-dark">
            <i class="bi bi-telephone"></i> Konsultasi Darurat Vendor
        </button>
    </div>

</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var tabs = document.querySelectorAll('.vc-tab');
    var rows = document.querySelectorAll('#vcTable tbody tr');
    var search = document.getElementById('vcSearch');

    function applyFilters() {
        var activeTab = document.querySelector('.vc-tab.is-active').dataset.filter;
        var query = search.value.trim().toLowerCase();

        rows.forEach(function (row) {
            var matchesTab = activeTab === 'all' || row.dataset.status === activeTab;
            var matchesQuery = row.innerText.toLowerCase().indexOf(query) !== -1;
            row.style.display = (matchesTab && matchesQuery) ? '' : 'none';
        });
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t) { t.classList.remove('is-active'); });
            tab.classList.add('is-active');
            applyFilters();
        });
    });

    search.addEventListener('input', applyFilters);
});
</script>
@endpush
