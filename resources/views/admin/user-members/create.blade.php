@extends('layouts.admin')

@section('title', 'Tambah User Member')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('admin.user-members.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Tambah User Member Baru</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.user-members.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email Member <span class="text-danger">*</span></label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="contoh: member@example.com"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="bi bi-info-circle"></i> Email ini akan digunakan member untuk login via Google OAuth
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="position" class="form-label fw-bold">Posisi / Jabatan <span class="text-danger">*</span></label>
                            <select class="form-select @error('position') is-invalid @enderror" 
                                    id="position" 
                                    name="position" 
                                    required>
                                <option value="">Pilih Posisi</option>
                                @foreach($positions as $pos)
                                    <option value="{{ $pos->name }}" {{ old('position') == $pos->name ? 'selected' : '' }}>
                                        {{ $pos->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="bi bi-info-circle"></i> Tentukan posisi member (Project Manager, Field Coordinator, dll)
                            </small>
                        </div>

                        <div class="mb-4">
                            <label for="division" class="form-label fw-bold">Divisi <span class="text-danger">*</span></label>
                            <select class="form-select @error('division') is-invalid @enderror" 
                                    id="division" 
                                    name="division" 
                                    required>
                                <option value="">Pilih Divisi</option>
                                @foreach($divisions as $key => $label)
                                    <option value="{{ $key }}" {{ old('division') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('division')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="bi bi-info-circle"></i> Tentukan divisi member
                            </small>
                        </div>

                        <div class="alert alert-info">
                            <h6 class="alert-heading"><i class="bi bi-info-circle"></i> Catatan</h6>
                            <ul class="mb-0">
                                <li>Posisi & divisi ditentukan oleh admin</li>
                                <li>Member hanya melengkapi data pribadi setelah login</li>
                                <li>Member login menggunakan Google OAuth dengan email ini</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-plus-circle"></i> Tambah User Member
                            </button>
                            <a href="{{ route('admin.user-members.index') }}" class="btn btn-outline-secondary">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
