@extends('layouts.admin')

@section('title', 'Kategori Member')
@section('page-title', 'Kategori Member')

@push('styles')
    @vite('resources/css/admin/member-categories.css')
@endpush

@section('content')
    <div class="member-category-page">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <div class="text-uppercase small fw-bold text-muted mb-2" style="letter-spacing: 0.12em;">Pengaturan Admin</div>
                <h2 class="mb-1">Kategori Member</h2>
                <p class="text-muted mb-0">Kelola data divisi, posisi, dan spesialisasi member dari satu halaman.</p>
            </div>

            <button type="button"
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#memberCategoryModal"
                data-mode="create">
                <i class="bi bi-plus-lg me-1"></i>
                Tambah Kategori
            </button>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('admin.member-categories.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-lg-7 col-md-6">
                        <label for="categorySearch" class="form-label small text-uppercase text-muted">Pencarian</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input
                                id="categorySearch"
                                type="search"
                                name="q"
                                value="{{ request('q') }}"
                                class="form-control"
                                placeholder="Cari nama kategori..."
                                aria-label="Cari kategori member">
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-4">
                        <label for="categoryType" class="form-label small text-uppercase text-muted">Jenis</label>
                        <select id="categoryType" name="type" class="form-select">
                            <option value="all" @selected($typeFilter === 'all')>Semua Kategori</option>
                            <option value="division" @selected($typeFilter === 'division')>Division</option>
                            <option value="position" @selected($typeFilter === 'position')>Position</option>
                            <option value="specialization" @selected($typeFilter === 'specialization')>Specialization</option>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-2 d-grid">
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="bi bi-funnel me-1"></i>
                            Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 member-category-table">
                        <thead>
                            <tr>
                                <th width="8%">No.</th>
                                <th>Nama Kategori</th>
                                <th>Jenis Kategori</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $item)
                                <tr>
                                    <td>{{ $loop->iteration + ($items->firstItem() - 1) }}</td>
                                    <td class="fw-semibold">{{ $item['name'] }}</td>
                                    <td>
                                        <span class="badge rounded-pill bg-light text-dark border">
                                            {{ $item['label'] }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#memberCategoryModal"
                                                data-mode="edit"
                                                data-id="{{ $item['id'] }}"
                                                data-name="{{ $item['name'] }}"
                                                data-type="{{ $item['type'] }}">
                                                Edit
                                            </button>

                                            <form action="{{ route('admin.member-categories.destroy', ['member_category' => $item['id'], 'type' => $item['type']]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kategori {{ $item['name'] }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="type" value="{{ $item['type'] }}">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">
                                        <div class="py-4">
                                            <i class="bi bi-inboxes fs-2 d-block mb-2"></i>
                                            Tidak ada data kategori yang sesuai dengan pencarian atau filter saat ini.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($items->hasPages())
                    <div class="d-flex justify-content-between align-items-center px-3 py-3 border-top">
                        <div class="text-muted small">
                            Menampilkan {{ $items->firstItem() ?? 0 }}-{{ $items->lastItem() ?? 0 }} dari {{ $items->total() }} data
                        </div>
                        <div>
                            {{ $items->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="memberCategoryModal" tabindex="-1" aria-labelledby="memberCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="memberCategoryForm" method="POST" action="{{ route('admin.member-categories.store') }}">
                    @csrf
                    <input type="hidden" name="id" id="categoryId" value="">

                    <div class="modal-header border-0 pb-0">
                        <div>
                            <div class="small text-uppercase text-muted fw-bold" style="letter-spacing: 0.12em;">Kategori Member</div>
                            <h5 class="modal-title mt-1" id="memberCategoryModalLabel">Tambah Kategori</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body pt-3">
                        <div class="mb-3">
                            <label for="categoryName" class="form-label">Nama kategori</label>
                            <input
                                id="categoryName"
                                name="name"
                                type="text"
                                class="form-control"
                                value="{{ old('name') }}"
                                maxlength="100"
                                placeholder="Masukkan nama kategori"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="categoryType" class="form-label">Jenis kategori</label>
                            <select id="categoryType" name="type" class="form-select" required>
                                <option value="">Pilih jenis kategori</option>
                                <option value="division">Division</option>
                                <option value="position">Position</option>
                                <option value="specialization">Specialization</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="saveCategoryButton">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const memberCategoryModal = document.getElementById('memberCategoryModal');
            const memberCategoryForm = document.getElementById('memberCategoryForm');
            const categoryId = document.getElementById('categoryId');
            const categoryName = document.getElementById('categoryName');
            const categoryType = document.getElementById('categoryType');
            const modalLabel = document.getElementById('memberCategoryModalLabel');
            const saveCategoryButton = document.getElementById('saveCategoryButton');
            const storeUrl = '{{ route('admin.member-categories.store') }}';
            const updateUrlTemplate = '{{ route('admin.member-categories.update', ['member_category' => ':id']) }}';

            memberCategoryModal.addEventListener('show.bs.modal', function (event) {
                const trigger = event.relatedTarget;
                const mode = trigger?.dataset.mode ?? 'create';

                if (mode === 'edit') {
                    const id = trigger.dataset.id;
                    const name = trigger.dataset.name || '';
                    const type = trigger.dataset.type || '';

                    categoryId.value = id;
                    categoryName.value = name;
                    categoryType.value = type;
                    modalLabel.textContent = 'Edit Kategori';
                    saveCategoryButton.textContent = 'Simpan Perubahan';
                    memberCategoryForm.action = updateUrlTemplate.replace(':id', id);
                    memberCategoryForm.method = 'POST';
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PUT';
                    const existingMethod = memberCategoryForm.querySelector('input[name="_method"]');
                    if (existingMethod) {
                        existingMethod.remove();
                    }
                    memberCategoryForm.appendChild(methodInput);
                } else {
                    categoryId.value = '';
                    categoryName.value = '';
                    categoryType.value = '';
                    modalLabel.textContent = 'Tambah Kategori';
                    saveCategoryButton.textContent = 'Simpan';
                    memberCategoryForm.action = storeUrl;
                    memberCategoryForm.method = 'POST';
                    const existingMethod = memberCategoryForm.querySelector('input[name="_method"]');
                    if (existingMethod) {
                        existingMethod.remove();
                    }
                }
            });
        </script>
    @endpush
@endsection
