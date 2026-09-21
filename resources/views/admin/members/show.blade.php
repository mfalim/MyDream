@extends('layouts.admin')

@section('title', 'Overview Anggota')
@section('page-title', 'Overview Anggota')

@push('styles')
    @vite('resources/css/admin/members/show.css')
@endpush

@section('content')
    @php
        $status = strtolower($member->status ?? 'standby');
        $statusText = match ($status) {
            'aktif' => 'Aktif di Lapangan',
            'resiko' => 'Gladi Resik',
            'selesai' => 'Selesai',
            default => 'Standby / On-Call',
        };
        $statusClass = in_array($status, ['resiko'], true) ? 'warning' : (in_array($status, ['standby', 'selesai'], true) ? 'gray' : '');
    @endphp

    <div class="member-detail-page">
        <div class="member-detail-breadcrumb">
            <a href="{{ route('admin.members.index') }}">Tracking Anggota Tim</a>
            <span>/</span>
            <span>{{ $member->member_code }}</span>
        </div>

        <div class="member-detail-header">
            <div>
                <h2>Overview Anggota</h2>
                <p>Ringkasan identitas, peran, dan kesiapan operasional kru.</p>
            </div>
            <div class="member-detail-actions">
                <a href="{{ route('admin.members.edit', $member) }}">✎ Edit Data</a>
                <form action="{{ route('admin.members.destroy', $member) }}" method="POST" onsubmit="return confirm('Hapus anggota ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit">× Hapus</button>
                </form>
            </div>
        </div>

        <div class="member-detail-grid">
            <div class="member-detail-card">
                <div class="member-detail-profile">
                    <div class="member-detail-avatar">
                        @if ($member->photo)
                            <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->name }}">
                        @else
                            👤
                        @endif
                    </div>
                    <h3>{{ $member->name }}</h3>
                    <p>{{ $member->position ?: 'Anggota Tim' }}</p>
                    <div class="member-detail-code">ID: {{ $member->member_code }}</div>
                    <span class="member-detail-status {{ $statusClass }}">{{ $statusText }}</span>
                </div>
                <div class="member-detail-section">
                    <h4>Penugasan</h4>
                    <div class="member-detail-info">
                        <div><small>Divisi</small><strong>{{ $member->division ?: '-' }}</strong></div>
                        <div><small>Tarif / Hari</small><strong>Rp {{ number_format((float) $member->daily_fee, 0, ',', '.') }}</strong></div>
                        <div><small>HT / Radio</small><strong>{{ $member->ht_code ?: '-' }}</strong></div>
                    </div>
                </div>
            </div>

            <div class="member-detail-card">
                <div class="member-detail-section">
                    <h4>Informasi Personal & Kontak</h4>
                    <div class="member-detail-info">
                        <div><small>Call Sign</small><strong>{{ $member->call_sign }}</strong></div>
                        <div><small>Nomor WhatsApp</small><strong>{{ $member->phone }}</strong></div>
                        <div><small>Email</small><strong>{{ $member->email ?: '-' }}</strong></div>
                        <div><small>Domisili</small><strong>{{ $member->domicile ?: '-' }}</strong></div>
                    </div>
                </div>
                <div class="member-detail-section">
                    <h4>Spesialisasi & Sertifikasi</h4>
                    <div class="member-detail-info">
                        <div><small>Spesialisasi</small><strong>{{ $member->specialization ?: '-' }}</strong></div>
                        <div>
                            <small>Sertifikasi</small>
                            @if ($member->certification)
                                <a href="{{ asset('storage/' . $member->certification) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $member->certification) }}" alt="Sertifikasi {{ $member->name }}" class="certification-detail-image">
                                </a>
                            @else
                                <strong>-</strong>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="member-detail-section">
                    <h4>Kontak Darurat</h4>
                    <div class="member-detail-info">
                        <div><small>Nama</small><strong>{{ $member->emergency_name ?: '-' }}</strong></div>
                        <div><small>Nomor</small><strong>{{ $member->emergency_phone ?: '-' }}</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
