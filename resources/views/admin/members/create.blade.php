@extends('layouts.admin')

@php($isEdit = isset($member))

@section('title', $isEdit ? 'Edit Anggota' : 'Tambah Anggota')
@section('page-title', $isEdit ? 'Edit Anggota' : 'Tambah Anggota')

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

                    <span>
                        Manajemen Talenta & Kru Lapangan
                    </span>
                </div>

                <h2>
                        {{ $isEdit ? 'Perbarui Data Anggota' : 'Pendaftaran & Tambah Anggota' }}
                    <br>
                        {{ $isEdit ? $member->name : 'Tim Baru' }}
                </h2>

                <p>
                    Lengkapi identitas kru, spesialisasi divisi WO,
                    sertifikasi profesi, dan informasi operasional lapangan.
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

        <div class="member-option-modal" id="memberOptionModal" hidden>
            <div class="member-option-dialog" role="dialog" aria-modal="true" aria-labelledby="memberOptionTitle">
                <div class="member-option-dialog-header">
                    <h4 id="memberOptionTitle">Tambah Pilihan</h4>
                    <button type="button" class="member-option-close" data-option-close aria-label="Tutup">&times;</button>
                </div>
                <form id="memberOptionForm">
                    <div class="member-option-dialog-body">
                        <label for="memberOptionName">Nama pilihan</label>
                        <input type="text" id="memberOptionName" class="form-control-custom" required maxlength="255">
                        <div class="input-note" id="memberOptionError" hidden></div>
                    </div>
                    <div class="member-option-dialog-footer">
                        <button type="button" class="member-option-cancel" data-option-close>Batal</button>
                        <button type="submit" class="member-option-submit">Tambah Pilihan</button>
                    </div>
                </form>
            </div>
        </div>


        <form action="{{ $isEdit ? route('admin.members.update', $member) : route('admin.members.store') }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif


            {{-- =========================
                 SECTION 01
            ========================== --}}
            <div class="member-form-section">

                <div class="section-header">

                    <div class="section-title">

                        <div class="section-number">
                            01
                        </div>

                        <div>
                            <h4>
                                Data Personal & Akun Kru Lapangan
                            </h4>

                            <p>
                                Identitas resmi, call sign koordinasi lapangan,
                                dan kontak internal.
                            </p>
                        </div>

                    </div>

                    <span class="required-label">
                        ● WAJIB DIISI
                    </span>

                </div>


                <div class="section-body">

                    <div class="personal-grid">

                        {{-- Photo --}}
                        <div class="photo-upload-box">

                            <div class="photo-preview" id="photoPreview">
                                @if ($isEdit && $member->photo)
                                    <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->name }}">
                                @else
                                    👤
                                @endif
                            </div>

                            <strong>
                                Foto Profil Seragam WO
                            </strong>

                            <small>
                                Format JPG/PNG/WEBP, resolusi maksimal
                                800×800 px agar seragam.
                            </small>

                            <label for="photo" class="btn-photo">
                                Ganti Berkas Foto
                            </label>

                            <input
                                type="file"
                                name="photo"
                                id="photo"
                                class="photo-input"
                                accept=".jpg,.jpeg,.png,.webp"
                            >

                        </div>


                        {{-- Personal fields --}}
                        <div class="form-grid">

                            <div class="form-group full">

                                <label class="form-label-custom">
                                    Nama Lengkap & Gelar Resmi
                                    <span class="required">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    class="form-control-custom"
                                    value="{{ old('name', $isEdit ? $member->name : '') }}"
                                    placeholder="Contoh: Dimas Prasetyo, S.I.Kom"
                                    required
                                >

                            </div>


                            <div class="form-group">

                                <label class="form-label-custom">
                                    Call Sign Lapangan (Radio Code)
                                    <span class="required">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="call_sign"
                                    class="form-control-custom"
                                    value="{{ old('call_sign', $isEdit ? $member->call_sign : '') }}"
                                    placeholder="Contoh: Dimas / Alpha Lead"
                                    required
                                >

                            </div>


                            <div class="form-group">

                                <label class="form-label-custom">
                                    Nomor Induk Kru
                                </label>

                                <input
                                    type="text"
                                    class="form-control-custom readonly-input"
                                    value="{{ $isEdit ? $member->member_code : 'Akan dibuat otomatis' }}"
                                    readonly
                                >

                                <div class="input-note">
                                    Sistem akan membuat ID otomatis setelah data disimpan.
                                </div>

                            </div>


                            <div class="form-group">

                                <label class="form-label-custom">
                                    Nomor WhatsApp Koordinasi
                                    <span class="required">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="phone"
                                    class="form-control-custom"
                                    value="{{ old('phone', $isEdit ? $member->phone : '') }}"
                                    placeholder="08xxxxxxxxxx"
                                    required
                                >

                            </div>


                            <div class="form-group">

                                <label class="form-label-custom">
                                    Email Internal
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    class="form-control-custom"
                                    value="{{ old('email', $isEdit ? $member->email : '') }}"
                                    placeholder="nama@weddingorganizer.id"
                                >

                            </div>


                            <div class="form-group">

                                <label class="form-label-custom">
                                    Domisili
                                </label>

                                <input
                                    type="text"
                                    name="domicile"
                                    class="form-control-custom"
                                    value="{{ old('domicile', $isEdit ? $member->domicile : '') }}"
                                    placeholder="Contoh: Jember, Jawa Timur"
                                >

                            </div>


                            <div class="form-group">

                                <label class="form-label-custom">
                                    Nomor HT / Radio Code
                                </label>

                                <input
                                    type="text"
                                    name="ht_code"
                                    class="form-control-custom"
                                    value="{{ old('ht_code', $isEdit ? $member->ht_code : '') }}"
                                    placeholder="Contoh: HT-05"
                                >

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =========================
                 SECTION 02
            ========================== --}}
            <div class="member-form-section">

                <div class="section-header">

                    <div class="section-title">

                        <div class="section-number">
                            02
                        </div>

                        <div>
                            <h4>
                                Peran, Divisi & Hierarki Lapangan
                            </h4>

                            <p>
                                Alokasi struktur kerja dan peran operasional kru.
                            </p>
                        </div>

                    </div>

                </div>


                <div class="section-body">

                    <div class="form-grid">

                        <div class="form-group full">

                            <label class="form-label-custom">
                                Pilih Divisi Utama Penugasan
                                <span class="required">*</span>
                            </label>

                            <div class="division-grid">

                                <div class="division-option">

                                    <input
                                        type="radio"
                                        name="division"
                                        value="show-management"
                                        id="division-show"
                                        @checked(old('division', $isEdit ? $member->division : '') === 'show-management')
                                        required
                                    >

                                    <label
                                        for="division-show"
                                        class="division-label">

                                        <strong>
                                            ◉ Show & Stage Management
                                        </strong>

                                        <small>
                                            Lead Show Director, Floor Director,
                                            Stage Manager, Lighting & Sound Cue.
                                        </small>

                                    </label>

                                </div>


                                <div class="division-option">

                                    <input
                                        type="radio"
                                        name="division"
                                        value="client-hospitality"
                                        id="division-client"
                                        @checked(old('division', $isEdit ? $member->division : '') === 'client-hospitality')
                                    >

                                    <label
                                        for="division-client"
                                        class="division-label">

                                        <strong>
                                            ◉ Client & VIP Hospitality
                                        </strong>

                                        <small>
                                            Head Wedding Planner, Bride & Groom
                                            Liaison, VIP Family Liaison.
                                        </small>

                                    </label>

                                </div>


                                <div class="division-option">

                                    <input
                                        type="radio"
                                        name="division"
                                        value="av-lighting"
                                        id="division-av"
                                        @checked(old('division', $isEdit ? $member->division : '') === 'av-lighting')
                                    >

                                    <label
                                        for="division-av"
                                        class="division-label">

                                        <strong>
                                            ◉ AV, Lighting & Multimedia
                                        </strong>

                                        <small>
                                            Technical Lead, Live Cam Feed,
                                            Video Wall Operator, Audio Tech.
                                        </small>

                                    </label>

                                </div>


                                <div class="division-option">

                                    <input
                                        type="radio"
                                        name="division"
                                        value="logistik-protokoler"
                                        id="division-logistic"
                                        @checked(old('division', $isEdit ? $member->division : '') === 'logistik-protokoler')
                                    >

                                    <label
                                        for="division-logistic"
                                        class="division-label">

                                        <strong>
                                            ◉ Logistik & Protokoler Kenegaraan
                                        </strong>

                                        <small>
                                            Guest Registration Desk,
                                            Usher Coordinator, Souvenir.
                                        </small>

                                    </label>

                                </div>

                            </div>

                        </div>


                        <div class="form-group">
                            <label class="form-label-custom">
                                Spesialisasi
                            </label>

                            <div class="option-input-row">
                                <select name="specialization" class="form-select-custom">
                                    <option value="">Pilih spesialisasi</option>
                                    @foreach ($specializations as $specialization)
                                        <option value="{{ $specialization->name }}" @selected(old('specialization', $isEdit ? $member->specialization : '') === $specialization->name)>
                                            {{ $specialization->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn-add-option" data-option-type="specialization" data-option-url="{{ route('admin.members.options.specializations.store') }}">+ Tambah</button>
                            </div>

                        </div>

                        <div class="form-group">
                            <label class="form-label-custom">
                                Jabatan Lapangan
                                <span class="required">*</span>
                            </label>

                            <div class="option-input-row">
                                <select name="position" class="form-select-custom" required>
                                    <option value="">Pilih jabatan</option>
                                    @foreach ($positions as $position)
                                        <option value="{{ $position->name }}" @selected(old('position', $isEdit ? $member->position : '') === $position->name)>
                                            {{ $position->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn-add-option" data-option-type="position" data-option-url="{{ route('admin.members.options.positions.store') }}">+ Tambah</button>
                            </div>
                        </div>


                        <div class="form-group">

                            <label class="form-label-custom">
                                Tarif Fee Operasional per Hari
                                <span class="required">*</span>
                            </label>

                            <div class="fee-wrapper">

                                <span class="fee-prefix">
                                    IDR
                                </span>

                                <input
                                    type="number"
                                    name="daily_fee"
                                    class="form-control-custom fee-input"
                                    value="{{ old('daily_fee', $isEdit ? $member->daily_fee : 0) }}"
                                    min="0"
                                    required
                                >

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =========================
                 SECTION 03
            ========================== --}}
            <div class="member-form-section">

                <div class="section-header">

                    <div class="section-title">

                        <div class="section-number">
                            03
                        </div>

                        <div>
                            <h4>
                                Kompetensi & Kontak Darurat
                            </h4>

                            <p>
                                Informasi tambahan untuk operasional lapangan.
                            </p>
                        </div>

                    </div>

                </div>


                <div class="section-body">

                    <div class="form-grid">

                        <div class="form-group full">

                            <label class="form-label-custom">
                                Sertifikasi / Kompetensi
                            </label>

                            <input
                                type="file"
                                name="certification"
                                id="certification"
                                class="form-control-custom"
                                accept=".jpg,.jpeg,.png,.webp"
                            >

                            <div class="input-note">Upload gambar sertifikasi, maksimal 4 MB.</div>

                            @if ($isEdit && $member->certification)
                                <a href="{{ asset('storage/' . $member->certification) }}" target="_blank" class="certification-preview-link">
                                    <img src="{{ asset('storage/' . $member->certification) }}" alt="Sertifikasi {{ $member->name }}" class="certification-preview">
                                </a>
                            @endif

                        </div>


                        <div class="form-group">

                            <label class="form-label-custom">
                                Nama Kontak Darurat
                            </label>

                            <input
                                type="text"
                                name="emergency_name"
                                class="form-control-custom"
                                value="{{ old('emergency_name', $isEdit ? $member->emergency_name : '') }}"
                                placeholder="Nama kontak darurat"
                            >

                        </div>


                        <div class="form-group">

                            <label class="form-label-custom">
                                Nomor Kontak Darurat
                            </label>

                            <input
                                type="text"
                                name="emergency_phone"
                                class="form-control-custom"
                                value="{{ old('emergency_phone', $isEdit ? $member->emergency_phone : '') }}"
                                placeholder="08xxxxxxxxxx"
                            >

                        </div>


                        <div class="form-group">

                            <label class="form-label-custom">
                                Status Ketersediaan
                            </label>

                            <select
                                name="status"
                                class="form-select-custom"
                            >

                                <option value="standby"
                                    @selected(old('status', $isEdit ? $member->status : 'standby') === 'standby')>
                                    Standby / On-Call
                                </option>

                                <option value="aktif"
                                    @selected(old('status', $isEdit ? $member->status : '') === 'aktif')>
                                    Aktif di Lapangan
                                </option>

                                <option value="resiko"
                                    @selected(old('status', $isEdit ? $member->status : '') === 'resiko')>
                                    Gladi Resik
                                </option>

                                <option value="selesai"
                                    @selected(old('status', $isEdit ? $member->status : '') === 'selesai')>
                                    Selesai
                                </option>

                            </select>

                        </div>

                    </div>

                </div>


                <div class="form-footer">

                    <a href="{{ route('admin.members.index') }}"
                        class="btn-cancel">
                        Batal
                    </a>

                    <button type="submit"
                        class="btn-save">
                        {{ $isEdit ? 'Simpan Perubahan' : 'Simpan & Tambahkan Anggota' }}
                    </button>

                </div>

            </div>

        </form>

    </div>


    @push('scripts')
        @vite('resources/js/admin/members/create.js')
    @endpush

@endsection
