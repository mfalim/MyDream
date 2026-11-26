@extends('layouts.admin')

@section('title', 'Kategori Member')
@section('page-title', 'Kategori Member')

@push('styles')
    @vite('resources/css/admin/members/categories.css')
@endpush

@section('content')
<div class="member-category-page" id="memberCategoryWorkspace" data-ajax-workspace>
    {{-- Header --}}
    <div class="member-category-header">
        <div>
            <div class="eyebrow">PENGATURAN STRUKTUR KRU</div>
            <h2>Kategori &amp; Divisi Member</h2>
            <p>Kelola data divisi, posisi operasional, dan spesialisasi keahlian member Wedding Organizer.</p>
        </div>

        <button type="button"
            class="btn-emerald"
            data-bs-toggle="modal"
            data-bs-target="#memberCategoryModal"
            data-mode="create">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah Kategori</span>
        </button>
    </div>

    {{-- Stats Grid --}}
    <div class="category-stats-grid">
        <div class="category-stat-card">
            <div class="category-stat-info">
                <small>TOTAL KATEGORI</small>
                <strong>{{ ($divisionCount ?? 0) + ($positionCount ?? 0) + ($specializationCount ?? 0) }}</strong>
            </div>
            <div class="category-stat-icon is-plum">
                <i class="bi bi-tags-fill"></i>
            </div>
        </div>

        <div class="category-stat-card">
            <div class="category-stat-info">
                <small>DIVISI (DIVISION)</small>
                <strong class="is-maroon">{{ $divisionCount ?? 0 }}</strong>
            </div>
            <div class="category-stat-icon is-maroon">
                <i class="bi bi-diagram-3-fill"></i>
            </div>
        </div>

        <div class="category-stat-card">
            <div class="category-stat-info">
                <small>POSISI (POSITION)</small>
                <strong class="is-gold">{{ $positionCount ?? 0 }}</strong>
            </div>
            <div class="category-stat-icon is-gold">
                <i class="bi bi-person-badge-fill"></i>
            </div>
        </div>

        <div class="category-stat-card">
            <div class="category-stat-info">
                <small>SPESIALISASI</small>
                <strong class="is-emerald">{{ $specializationCount ?? 0 }}</strong>
            </div>
            <div class="category-stat-icon is-emerald">
                <i class="bi bi-award-fill"></i>
            </div>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <div class="category-filter-card">
        <form action="{{ route('admin.member-categories.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-lg-6 col-md-5">
                <label for="categorySearch" class="form-label small fw-bold mb-1">CARI KATEGORI</label>
                <div class="input-group">
                    <span class="input-group-text border-end-0"><i class="bi bi-search"></i></span>
                    <input
                        id="categorySearch"
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        class="form-control border-start-0 ps-0"
                        placeholder="Ketik nama kategori..."
                        aria-label="Cari kategori member">
                </div>
            </div>

            <div class="col-lg-4 col-md-4">
                <label for="categoryType" class="form-label small fw-bold mb-1">FILTER JENIS</label>
                <select id="categoryType" name="type" class="form-select">
                    <option value="all" @selected($typeFilter === 'all')>Semua Jenis Kategori</option>
                    <option value="division" @selected($typeFilter === 'division')>Division (Divisi)</option>
                    <option value="position" @selected($typeFilter === 'position')>Position (Posisi)</option>
                    <option value="specialization" @selected($typeFilter === 'specialization')>Specialization (Keahlian)</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-category-filter w-100 py-2">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if(request()->filled('q') || (request('type') && request('type') !== 'all'))
                    <a href="{{ route('admin.member-categories.index') }}" class="btn btn-category-filter py-2 px-3" title="Reset filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Main Table Card --}}
    <div class="category-table-card">
        <div class="table-responsive">
            <table class="table member-category-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">No.</th>
                        <th>Nama Kategori</th>
                        <th style="width: 220px;">Jenis Kategori</th>
                        <th style="width: 130px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td class="text-muted small">{{ $loop->iteration + ($items->firstItem() - 1) }}</td>
                            <td>
                                <strong class="category-name-title d-block">{{ $item['name'] }}</strong>
                            </td>
                            <td>
                                @if ($item['type'] === 'division')
                                    <span class="category-badge-division">
                                        <i class="bi bi-diagram-3"></i> Division
                                    </span>
                                @elseif ($item['type'] === 'position')
                                    <span class="category-badge-position">
                                        <i class="bi bi-person-badge"></i> Position
                                    </span>
                                @else
                                    <span class="category-badge-specialization">
                                        <i class="bi bi-award"></i> Specialization
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <button
                                        type="button"
                                        class="btn-category-action"
                                        data-bs-toggle="modal"
                                        data-bs-target="#memberCategoryModal"
                                        data-mode="edit"
                                        data-id="{{ $item['id'] }}"
                                        data-name="{{ $item['name'] }}"
                                        data-type="{{ $item['type'] }}"
                                        title="Edit Kategori">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <form action="{{ route('admin.member-categories.destroy', ['member_category' => $item['id'], 'type' => $item['type']]) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ $item['name'] }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="type" value="{{ $item['type'] }}">
                                        <button type="submit" class="btn-category-action delete" title="Hapus Kategori">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-inboxes text-muted fs-1 d-block mb-2"></i>
                                    <p class="text-muted mb-0">Tidak ada data kategori yang sesuai dengan pencarian atau filter saat ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($items->hasPages())
            <div class="category-table-footer d-flex flex-wrap justify-content-between align-items-center px-4 py-3">
                <div class="text-muted small">
                    Menampilkan <strong>{{ $items->firstItem() ?? 0 }}-{{ $items->lastItem() ?? 0 }}</strong> dari <strong>{{ $items->total() }}</strong> kategori
                </div>
                <div>
                    {{ $items->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Modal Tambah / Edit Kategori --}}
<div class="modal fade" id="memberCategoryModal" tabindex="-1" aria-labelledby="memberCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <form id="memberCategoryForm" method="POST" action="{{ route('admin.member-categories.store') }}">
                @csrf
                <input type="hidden" name="id" id="categoryId" value="">

                <div class="modal-header modal-header-emerald">
                    <div>
                        <small class="text-uppercase fw-bold" style="color: #B08D57; letter-spacing: 0.1em; font-size: 11px;">FORM KATEGORI KRU</small>
                        <h5 class="modal-title mt-1" id="memberCategoryModalLabel">Tambah Kategori</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label fw-bold small">Nama Kategori <span class="text-danger">*</span></label>
                        <input
                            id="categoryName"
                            name="name"
                            type="text"
                            class="form-control form-control-lg fs-6"
                            value="{{ old('name') }}"
                            maxlength="100"
                            placeholder="Contoh: Logistik & Operasional, Fotografer, dll"
                            required>
                    </div>

                    <div class="mb-2">
                        <label for="categoryTypeSelect" class="form-label fw-bold small">Jenis Kategori <span class="text-danger">*</span></label>
                        <select id="categoryTypeSelect" name="type" class="form-select form-select-lg fs-6" required>
                            <option value="">-- Pilih Jenis Kategori --</option>
                            <option value="division">Division (Divisi Utama)</option>
                            <option value="position">Position (Jabatan / Peran)</option>
                            <option value="specialization">Specialization (Keahlian Spesifik)</option>
                        </select>
                        <small class="text-muted d-block mt-1">Jenis kategori menentukan di bagian mana data ini akan dikelompokkan pada profil member.</small>
                    </div>
                </div>

                <div class="modal-footer category-modal-footer">
                    <button type="button" class="btn btn-category-filter px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-emerald px-4" id="saveCategoryButton">
                        <i class="bi bi-check2-circle"></i>
                        <span>Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const memberCategoryModal = document.getElementById('memberCategoryModal');
        const memberCategoryForm = document.getElementById('memberCategoryForm');
        const categoryId = document.getElementById('categoryId');
        const categoryName = document.getElementById('categoryName');
        const categoryTypeSelect = document.getElementById('categoryTypeSelect');
        const modalLabel = document.getElementById('memberCategoryModalLabel');
        const saveCategoryButton = document.getElementById('saveCategoryButton');
        const storeUrl = '{{ route('admin.member-categories.store') }}';
        const updateUrlTemplate = '{{ route('admin.member-categories.update', ['member_category' => ':id']) }}';

        if (!memberCategoryModal) return;

        // Pastikan modal berada langsung di <body> agar selalu tampil di atas .modal-backdrop
        if (memberCategoryModal.parentElement !== document.body) {
            document.body.appendChild(memberCategoryModal);
        }

        memberCategoryModal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            const mode = trigger?.dataset.mode ?? 'create';

            if (mode === 'edit') {
                const id = trigger.dataset.id;
                const name = trigger.dataset.name || '';
                const type = trigger.dataset.type || '';

                categoryId.value = id;
                categoryName.value = name;
                categoryTypeSelect.value = type;
                modalLabel.textContent = 'Edit Kategori';
                saveCategoryButton.querySelector('span').textContent = 'Simpan Perubahan';
                memberCategoryForm.action = updateUrlTemplate.replace(':id', id);

                let methodInput = memberCategoryForm.querySelector('input[name="_method"]');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    memberCategoryForm.appendChild(methodInput);
                }
                methodInput.value = 'PUT';
            } else {
                categoryId.value = '';
                categoryName.value = '';
                categoryTypeSelect.value = '';
                modalLabel.textContent = 'Tambah Kategori';
                saveCategoryButton.querySelector('span').textContent = 'Simpan';
                memberCategoryForm.action = storeUrl;

                const existingMethod = memberCategoryForm.querySelector('input[name="_method"]');
                if (existingMethod) {
                    existingMethod.remove();
                }
            }
        });
    });
</script>
@endpush
@endsection
