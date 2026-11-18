@extends('layouts.admin')

@section('title', 'Edit Vendor')
@section('page-title', 'Edit Vendor')

@push('styles')
    <style>
        .edit-photo-card {
            overflow: hidden;
            border: 1px solid #e4ebe8;
            border-radius: 8px;
            background: #ffffff;
        }

        .edit-photo-card > img {
            display: block;
            width: 100%;
            height: 160px;
            object-fit: cover;
        }
    </style>
@endpush

@section('content')

    <div class="mb-4">

        <h3 class="mb-1">
            Edit Vendor
        </h3>

        <p class="text-muted">
            Perbarui informasi dan foto vendor.
        </p>

    </div>


    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form action="{{ route('admin.vendors.update', $vendor) }}"
        method="POST"
        enctype="multipart/form-data">

        @csrf
        @method('PUT')


        {{-- Data Vendor --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body">

                <h5 class="mb-4">
                    Informasi Vendor
                </h5>


                <div class="mb-3">

                    <label class="form-label">
                        Nama Vendor
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="{{ old('name', $vendor->name) }}"
                        pattern="^[A-Za-z0-9À-ž][A-Za-z0-9À-ž &.'-]*$"
                        title="Gunakan huruf, angka, spasi, dan tanda baca umum saja."
                        required
                    >

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Kategori
                    </label>

                    <select name="category_id"
                        class="form-select"
                        required>

                        <option value="">
                            Pilih kategori
                        </option>

                        @foreach ($categories as $category)

                            <option value="{{ $category->id }}"
                                @selected(old('category_id', $vendor->category_id) == $category->id)>

                                {{ $category->name }}

                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Harga Vendor
                    </label>

                    <input
                        type="number"
                        name="price"
                        class="form-control"
                        value="{{ old('price', $vendor->price) }}"
                        min="0"
                        max="999999999"
                        step="1"
                        required
                    >
                    <small class="text-muted">Maksimal Rp 999.999.999.</small>

                </div>

                <div class="mb-3">
                    <label class="form-label">Kapasitas Tamu Catering</label>
                    <input
                        type="number"
                        name="guest_capacity"
                        class="form-control"
                        value="{{ old('guest_capacity', $vendor->guest_capacity) }}"
                        min="1"
                        max="1000000"
                    >
                    <small class="text-muted">
                        Wajib diisi untuk kategori Catering/Katering. Harga dasar berlaku sampai jumlah tamu ini;
                        kategori lain tidak menggunakan kapasitas ini.
                    </small>
                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Nomor Telepon
                    </label>

                    <input
                        type="text"
                        name="phone"
                        class="form-control"
                        value="{{ old('phone', $vendor->phone) }}"
                        required
                    >

                </div>

                <div class="mb-4 p-3 border rounded">
                    <h5 class="mb-2">Akun Login Vendor</h5>

                    @if ($vendor->user)
                        <p class="text-muted">
                            Akun tertaut: {{ $vendor->user->name }}
                            ({{ $vendor->user->status === 'approved' ? 'Aktif' : $vendor->user->status }})
                        </p>
                    @else
                        <p class="text-muted mb-3">
                            Vendor ini belum memiliki akun. Buat akun baru atau tautkan akun vendor yang belum memiliki profil.
                        </p>

                        <div class="mb-3">
                            <label for="vendor-account-user" class="form-label">Akun yang akan ditautkan</label>
                            <select name="user_id" id="vendor-account-user" class="form-select">
                                <option value="">Buat akun vendor baru</option>
                                @foreach ($availableVendorUsers as $availableUser)
                                    <option
                                        value="{{ $availableUser->id }}"
                                        data-username="{{ $availableUser->username }}"
                                        data-email="{{ $availableUser->email }}"
                                        data-has-password="{{ filled($availableUser->password) ? '1' : '0' }}"
                                        @selected((string) old('user_id') === (string) $availableUser->id)
                                    >
                                        {{ $availableUser->name }}
                                        ({{ $availableUser->username ?: $availableUser->email ?: 'belum ada username' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vendor-account-username" class="form-label">Username</label>
                            <input
                                type="text"
                                name="username"
                                id="vendor-account-username"
                                class="form-control"
                                value="{{ old('username', $vendor->user?->username) }}"
                                minlength="3"
                                maxlength="80"
                                autocomplete="off"
                            >
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vendor-account-email" class="form-label">Email (opsional)</label>
                            <input
                                type="email"
                                name="email"
                                id="vendor-account-email"
                                class="form-control"
                                value="{{ old('email', $vendor->user?->email) }}"
                                maxlength="255"
                                autocomplete="email"
                            >
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vendor-account-password" class="form-label">
                                {{ $vendor->user && filled($vendor->user->password) ? 'Password baru (opsional)' : 'Password awal' }}
                            </label>
                            <input
                                type="password"
                                name="password"
                                id="vendor-account-password"
                                class="form-control"
                                minlength="8"
                                autocomplete="new-password"
                            >
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vendor-account-password-confirmation" class="form-label">Konfirmasi password</label>
                            <input
                                type="password"
                                name="password_confirmation"
                                id="vendor-account-password-confirmation"
                                class="form-control"
                                minlength="8"
                                autocomplete="new-password"
                            >
                        </div>
                    </div>
                    <small class="text-muted">Password minimal 8 karakter. Password yang ada tidak ditampilkan.</small>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nama Bank Rekening <small class="text-muted">(opsional)</small></label>
                        <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $vendor->bank_name) }}" placeholder="mis. BCA / Mandiri / BRI">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nomor Rekening <small class="text-muted">(opsional)</small></label>
                        <input type="text" name="bank_account_number" class="form-control" value="{{ old('bank_account_number', $vendor->bank_account_number) }}" placeholder="Nomor rekening pencairan">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Atas Nama Rekening <small class="text-muted">(opsional)</small></label>
                        <input type="text" name="bank_account_holder" class="form-control" value="{{ old('bank_account_holder', $vendor->bank_account_holder) }}" placeholder="Nama pemilik rekening">
                    </div>
                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Alamat
                    </label>

                    <textarea
                        name="address"
                        class="form-control"
                        rows="3"
                        required>{{ old('address', $vendor->address) }}</textarea>

                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kota</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $vendor->city) }}" placeholder="mis. Jember">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Instagram <small class="text-muted">(opsional)</small></label>
                        <input type="text" name="instagram" class="form-control" value="{{ old('instagram', $vendor->instagram) }}" placeholder="@namavendor">
                    </div>
                </div>



                <div class="mb-3">

                    <label class="form-label">
                        Deskripsi
                    </label>

                    <textarea
                        name="description"
                        class="form-control"
                        rows="4">{{ old('description', $vendor->description) }}</textarea>

                </div>

            </div>

        </div>


        {{-- Foto Lama --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div>
                        <h5 class="mb-1">
                            Foto Vendor
                        </h5>

                        <small class="text-muted">
                            Pilih satu foto sebagai foto utama.
                        </small>
                    </div>

                </div>


                @if ($vendor->photos->count())

                    <div class="row g-3">

                        @foreach ($vendor->photos as $photo)

                            <div class="col-md-3">

                                <div class="edit-photo-card">

                                    <img src="{{ asset('storage/' . $photo->photo) }}"
                                        alt="{{ $vendor->name }}">

                                    <div class="p-2">

                                        <div class="form-check mb-2">

                                            <input
                                                type="radio"
                                                name="cover_photo_id"
                                                value="{{ $photo->id }}"
                                                class="form-check-input"
                                                id="cover-{{ $photo->id }}"
                                                @checked($photo->is_cover)
                                            >

                                            <label
                                                class="form-check-label"
                                                for="cover-{{ $photo->id }}">

                                                Foto Utama

                                            </label>

                                        </div>


                                        <div class="form-check">

                                            <input
                                                type="checkbox"
                                                name="delete_photos[]"
                                                value="{{ $photo->id }}"
                                                class="form-check-input"
                                                id="delete-{{ $photo->id }}"
                                            >

                                            <label
                                                class="form-check-label text-danger"
                                                for="delete-{{ $photo->id }}">

                                                Hapus Foto

                                            </label>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="text-muted">
                        Belum ada foto vendor.
                    </div>

                @endif

            </div>

        </div>


        {{-- Tambah Foto --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body">

                <h5 class="mb-3">
                    Tambah Foto Baru
                </h5>

                <input
                    type="file"
                    name="photos[]"
                    class="form-control"
                    accept=".jpg,.jpeg,.png,.webp"
                    multiple
                >

                <small class="text-muted">
                    Maksimal 10 foto tambahan, masing-masing maksimal 2 MB.
                </small>

            </div>

        </div>


        <button type="submit"
            class="btn btn-success">

            Simpan Perubahan

        </button>


        <a href="{{ route('admin.vendors.show', $vendor) }}"
            class="btn btn-secondary">

            Batal

        </a>

    </form>

@endsection

@if (! $vendor->user)
    @push('scripts')
        <script>
            (() => {
                const accountSelect = document.getElementById('vendor-account-user');
                const usernameInput = document.getElementById('vendor-account-username');
                const emailInput = document.getElementById('vendor-account-email');
                const passwordInput = document.getElementById('vendor-account-password');
                const confirmationInput = document.getElementById('vendor-account-password-confirmation');

                const syncAccountFields = (clearNewAccountFields = false) => {
                    const selected = accountSelect.selectedOptions[0];
                    const isExistingAccount = accountSelect.value !== '';

                    if (isExistingAccount) {
                        usernameInput.value = selected.dataset.username || '';
                        emailInput.value = selected.dataset.email || '';
                    } else if (clearNewAccountFields) {
                        usernameInput.value = '';
                        emailInput.value = '';
                    }

                    passwordInput.value = '';
                    confirmationInput.value = '';
                    usernameInput.required = !isExistingAccount || !usernameInput.value;
                    passwordInput.required = !isExistingAccount || selected.dataset.hasPassword !== '1';
                    confirmationInput.required = passwordInput.required;
                };

                accountSelect.addEventListener('change', () => syncAccountFields(true));
                syncAccountFields();
            })();
        </script>
    @endpush
@else
    @push('scripts')
        <script>
            (() => {
                const passwordInput = document.getElementById('vendor-account-password');
                const confirmationInput = document.getElementById('vendor-account-password-confirmation');
                const hasPassword = @json(filled($vendor->user->password));

                usernameInput.required = !usernameInput.value;
                passwordInput.required = !hasPassword;
                confirmationInput.required = !hasPassword;
            })();
        </script>
    @endpush
@endif
