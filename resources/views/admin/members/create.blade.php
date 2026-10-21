@extends('layouts.admin')

@section('title', 'Tambah User Member')
@section('page-title', 'Tambah User Member')

@push('styles')
    @vite('resources/css/admin/members/create.css')
    @vite('resources/css/admin/members/form.css')
@endpush

@section('content')

<div class="member-create-page">

    {{-- Header --}}
    <div class="member-create-header">
        <div>
            <div class="member-breadcrumb">
                <a href="{{ route('admin.members.index') }}">
                    ← Kembali ke Tracking Anggota Tim
                </a>

                <span>/</span>
                <span>Tambah User Member</span>
            </div>

            <h2>Tambah User Member Baru</h2>

            <p>
                Daftarkan email member dan tentukan posisi,
                spesialisasi, serta divisi penugasannya.
            </p>
        </div>
    </div>

    {{-- Error --}}
    @if ($errors->any())
        <div class="error-box">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <form
        action="{{ isset($member) && $member->exists
            ? route('admin.members.update', $member->id)
            : route('admin.members.store') }}"
        method="POST"
    >
        @csrf

        @if(isset($member) && $member->exists)
            @method('PUT')
        @endif

        <div class="member-form-section">

            <div class="section-header">
                <div class="section-title">
                    <div class="section-number">01</div>

                    <div>
                        <h4>Informasi User Member</h4>
                        <p>
                            Tentukan informasi akun dan penugasan member.
                        </p>
                    </div>
                </div>
            </div>

            <div class="section-body">
                <div class="form-grid">

                    {{-- Email --}}
                    <div class="form-group full">
                        <label class="form-label-custom">
                            Email Member
                            <span class="required">*</span>
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control-custom"
                            value="{{ old('name', $member->email ?? '') }}"
                            placeholder="contoh: member@example.com"
                            required
                        >

                        <div class="input-note">
                            Email digunakan untuk login melalui Google OAuth.
                        </div>
                    </div>

                    {{-- Posisi --}}
                    <div class="form-group">
                        <label class="form-label-custom">
                            Posisi / Jabatan
                            <span class="required">*</span>
                        </label>

                        <select
                            name="position"
                            class="form-select-custom"
                            required
                        >
                            <option value="">Pilih posisi</option>

                            @foreach ($positions as $position)
                                <option
                                    value="{{ $position->name }}"
                                    @selected(old('position', $member->position ?? '') === $position->name)
                                >
                                    {{ $position->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Spesialisasi --}}
                    <div class="form-group">
                        <label class="form-label-custom">
                            Spesialisasi
                        </label>

                        <select
                            name="specialization"
                            class="form-select-custom"
                        >
                            <option value="">Pilih spesialisasi</option>

                            @foreach ($specializations as $specialization)
                                <option
                                    value="{{ $specialization->name }}"
                                    @selected(old('specialization', $member->specialization ?? '') === $specialization->name)
                                >
                                    {{ $specialization->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Divisi --}}
                    <div class="form-group full">
                        <label class="form-label-custom">
                            Divisi
                            <span class="required">*</span>
                        </label>

                        <select
                            name="division"
                            class="form-select-custom"
                            required
                        >
                            <option value="">Pilih divisi</option>

                            @foreach ($divisions as $division)
                                <option
                                    value="{{ $division }}"
                                    @selected(old('division', $member->division ?? '') === $division)
                                >
                                    {{ ucwords(str_replace('-', ' ', $division)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="form-group">
                        <label class="form-label-custom">
                            Status Member
                            <span class="required">*</span>
                        </label>

                        <select
                            name="status"
                            class="form-select-custom"
                            required
                        >
                            <option value="">Pilih status</option>
                            <option value="standby" @selected(old('status', $member->status ?? '') === 'standby')>Standby</option>
                            <option value="aktif" @selected(old('status', $member->status ?? '') === 'aktif')>Aktif</option>
                            <option value="inaktif" @selected(old('status', $member->status ?? '') === 'inaktif')>Inaktif</option>
                        </select>
                    </div>

                </div>
            </div>

            {{-- Footer --}}
            <div class="form-footer">
                <a
                    href="{{ route('admin.members.index') }}"
                    class="btn-cancel"
                >
                    Batal
                </a>

                <button type="submit" class="btn-save">
                    Tambah User Member
                </button>
            </div>

        </div>
    </form>

</div>

@endsection