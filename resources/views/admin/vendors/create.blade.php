@extends('layouts.admin')

@section('title', 'Tambah Vendor')
@section('page-title', 'Tambah Vendor')

@section('content')

    <div class="mb-4">

        <h3 class="mb-1">
            Tambah Vendor
        </h3>

        <p class="text-muted">
            Tambahkan vendor baru ke dalam sistem.
        </p>

    </div>


    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <form action="{{ route('admin.vendors.store') }}" method="POST" enctype="multipart/form-data">

                @csrf


                <div class="mb-3">

                    <label class="form-label">
                        Nama Vendor
                    </label>

                    <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                        pattern="^[A-Za-z0-9À-ž][A-Za-z0-9À-ž &.'-]*$"
                        title="Gunakan huruf, angka, spasi, dan tanda baca umum saja." required>

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Kategori
                    </label>

                    @if($categories->isEmpty())
                        <div class="alert alert-warning mb-0">
                            Belum ada kategori. <a href="{{ route('admin.categories.create') }}">Buat kategori terlebih dahulu.</a>
                        </div>
                    @else
                        <select name="category_id" class="form-select" required>
                            <option value="">Pilih kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Harga Vendor
                    </label>

                    <input type="number" name="price" class="form-control" value="{{ old('price') }}"
                        min="0" max="999999999" step="1" required>
                    <small class="text-muted">Maksimal Rp 999.999.999.</small>

                </div>

                <div class="mb-3">
                    <label class="form-label">Kapasitas Tamu Catering</label>
                    <input
                        type="number"
                        name="guest_capacity"
                        class="form-control"
                        value="{{ old('guest_capacity') }}"
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

                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>

                </div>

                <div class="mb-4 p-3 border rounded">
                    <h5 class="mb-2">Akun Login Vendor</h5>
                    <p class="text-muted mb-3">
                        Buat akun baru atau tautkan akun vendor yang belum memiliki profil.
                    </p>

                    <div class="mb-3">
                        <label for="vendor-account-user" class="form-label">Akun yang akan ditautkan</label>
                        <select name="user_id" id="vendor-account-user" class="form-select">
                            <option value="">Buat akun baru</option>
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
                        @if ($availableVendorUsers->isEmpty())
                            <small class="text-muted">Tidak ada akun vendor tanpa profil untuk ditautkan.</small>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vendor-account-username" class="form-label">Username</label>
                            <input
                                type="text"
                                name="username"
                                id="vendor-account-username"
                                class="form-control"
                                value="{{ old('username') }}"
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
                                value="{{ old('email') }}"
                                maxlength="255"
                                autocomplete="email"
                            >
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vendor-account-password" class="form-label">Password awal</label>
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
                    <small class="text-muted">Password minimal 8 karakter. Sampaikan kredensial kepada vendor melalui saluran yang aman.</small>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nama Bank Rekening <small class="text-muted">(opsional)</small></label>
                        <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name') }}" placeholder="mis. BCA / Mandiri / BRI">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nomor Rekening <small class="text-muted">(opsional)</small></label>
                        <input type="text" name="bank_account_number" class="form-control" value="{{ old('bank_account_number') }}" placeholder="Nomor rekening pencairan">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Atas Nama Rekening <small class="text-muted">(opsional)</small></label>
                        <input type="text" name="bank_account_holder" class="form-control" value="{{ old('bank_account_holder') }}" placeholder="Nama pemilik rekening">
                    </div>
                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Alamat
                    </label>

                    <textarea name="address" class="form-control" rows="3" required>{{ old('address') }}</textarea>

                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kota</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city') }}" placeholder="mis. Jember">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Instagram <small class="text-muted">(opsional)</small></label>
                        <input type="text" name="instagram" class="form-control" value="{{ old('instagram') }}" placeholder="@namavendor">
                    </div>
                </div>



                <div class="mb-3">

                    <label class="form-label">
                        Deskripsi
                    </label>

                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>

                </div>


                <div class="mb-4">

                    <label class="form-label">
                        Foto Vendor
                    </label>

                    <input type="file" name="photos[]" class="form-control" accept=".jpg,.jpeg,.png,.webp" multiple>

                    <small class="text-muted">
                        Kamu dapat memilih beberapa foto sekaligus. Maksimal 10 foto,
                        masing-masing maksimal 2 MB.
                    </small>

                </div>


                <button type="submit" class="btn btn-success">
                    Simpan Vendor
                </button>

                <a href="{{ route('admin.vendors.index') }}" class="btn btn-secondary">
                    Kembali
                </a>

            </form>

        </div>

    </div>

@endsection

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
