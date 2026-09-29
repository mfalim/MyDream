<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lengkapi Profile Member</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
        .profile-card {
            max-width: 600px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="card shadow-lg profile-card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-person-circle"></i> Lengkapi Profile Member</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info">
                            <strong>Selamat datang, {{ $user->name }}!</strong><br>
                            Silakan lengkapi profile Anda untuk dapat mengakses dashboard member.
                        </div>

                        <form action="{{ route('member.profile.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" value="{{ $user->name }}" disabled>
                                <small class="text-muted">Diambil dari akun Google Anda</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                                <small class="text-muted">Diambil dari akun Google Anda</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Posisi / Jabatan</label>
                                    <input type="text" class="form-control" value="{{ $position }}" disabled>
                                    <small class="text-muted">Ditentukan oleh admin</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Divisi</label>
                                    <input type="text" class="form-control" value="{{ ucwords(str_replace('-', ' ', $division)) }}" disabled>
                                    <small class="text-muted">Ditentukan oleh admin</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="call_sign" class="form-label">Call Sign / Panggilan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('call_sign') is-invalid @enderror" 
                                           id="call_sign" name="call_sign" value="{{ old('call_sign') }}" required>
                                    @error('call_sign')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">No. Telepon <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="domicile" class="form-label">Domisili</label>
                                <input type="text" class="form-control @error('domicile') is-invalid @enderror" 
                                       id="domicile" name="domicile" value="{{ old('domicile') }}">
                                @error('domicile')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="specialization" class="form-label">Spesialisasi</label>
                                <input type="text" class="form-control @error('specialization') is-invalid @enderror" 
                                       id="specialization" name="specialization" value="{{ old('specialization') }}" 
                                       placeholder="e.g., Photography, Sound System">
                                @error('specialization')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ht_code" class="form-label">Kode HT</label>
                                    <input type="text" class="form-control @error('ht_code') is-invalid @enderror" 
                                           id="ht_code" name="ht_code" value="{{ old('ht_code') }}">
                                    @error('ht_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="emergency_name" class="form-label">Nama Kontak Darurat</label>
                                    <input type="text" class="form-control @error('emergency_name') is-invalid @enderror" 
                                           id="emergency_name" name="emergency_name" value="{{ old('emergency_name') }}">
                                    @error('emergency_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="emergency_phone" class="form-label">Telepon Kontak Darurat</label>
                                <input type="text" class="form-control @error('emergency_phone') is-invalid @enderror" 
                                       id="emergency_phone" name="emergency_phone" value="{{ old('emergency_phone') }}">
                                @error('emergency_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle"></i> Simpan Profile & Lanjutkan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
