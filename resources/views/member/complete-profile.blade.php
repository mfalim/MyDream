<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lengkapi Profile Member</title>
    @vite(['resources/css/member/shared.css', 'resources/css/member/profile.css'])
</head>

<body>

    <div class="membership-page">
        <div class="profile-shell">
            <div class="card profile-card">
                <div class="card-header">
                    <h4><i class="bi bi-person-circle"></i> Lengkapi Profile Member</h4>
                </div>

                <div class="card-body">
                    <div class="profile-banner">
                        <strong>Selamat datang, {{ $user->name }}!</strong><br>
                        Silakan lengkapi profile Anda untuk dapat mengakses dashboard member.
                    </div>

                    <form action="{{ route('member.profile.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" value="{{ $user->name }}" disabled>
                            <small class="form-text">Diambil dari akun Google Anda</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                            <small class="form-text">Diambil dari akun Google Anda</small>
                        </div>

                        <div class="info-meta">
                            <div class="mb-3">
                                <label class="form-label">Posisi / Jabatan</label>
                                <input type="text" class="form-control" value="{{ $position }}" disabled>
                                <small class="form-text">Ditentukan oleh admin</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Divisi</label>
                                <input type="text" class="form-control" value="{{ ucwords(str_replace('-', ' ', $division)) }}" disabled>
                                <small class="form-text">Ditentukan oleh admin</small>
                            </div>

                        <div class="mb-3">
                            <label for="specialization" class="form-label">Spesialisasi</label>
                            <input type="text" class="form-control" value="{{ ucwords(str_replace('-', ' ', $specialization)) }}" disabled>
                            <small class="form-text">Ditentukan oleh admin</small>
                        </div>
                        </div>

                        <div class="info-meta">
                            <div class="mb-3">
                                <label for="call_sign" class="form-label">Call Sign / Panggilan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('call_sign') is-invalid @enderror" id="call_sign" name="call_sign" value="{{ old('call_sign') }}" required>
                                @error('call_sign')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">No. Telepon <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="domicile" class="form-label">Domisili</label>
                            <input type="text" class="form-control @error('domicile') is-invalid @enderror" id="domicile" name="domicile" value="{{ old('domicile') }}">
                            @error('domicile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="info-meta">
                            <div class="mb-3">
                                <label for="ht_code" class="form-label">Kode HT</label>
                                <input type="text" class="form-control @error('ht_code') is-invalid @enderror" id="ht_code" name="ht_code" value="{{ old('ht_code') }}">
                                @error('ht_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="emergency_name" class="form-label">Nama Kontak Darurat</label>
                                <input type="text" class="form-control @error('emergency_name') is-invalid @enderror" id="emergency_name" name="emergency_name" value="{{ old('emergency_name') }}">
                                @error('emergency_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="emergency_phone" class="form-label">Telepon Kontak Darurat</label>
                            <input type="text" class="form-control @error('emergency_phone') is-invalid @enderror" id="emergency_phone" name="emergency_phone" value="{{ old('emergency_phone') }}">
                            @error('emergency_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn profile-submit">
                            <i class="bi bi-check-circle"></i> Simpan Profile & Lanjutkan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
