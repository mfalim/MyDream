@extends('layouts.admin')

@section('title', 'Anggota Tim')
@section('page-title', 'Tracking Anggota Tim')

@push('styles')
    @vite('resources/css/admin/members/index.css')
    @vite('resources/css/admin/members/page.css')
@endpush

@section('content')

    <div class="member-page">

        {{-- Header --}}
        <div class="member-header">

            <div class="member-header-left">

                <div class="member-eyebrow">
                    Operasional Lapangan & Talenta
                </div>

                <h2>
                    Tracking Anggota Tim & Kru Lapangan
                </h2>

                <p>
                    Monitoring penugasan koordinator lapangan, show director,
                    dan asisten pengantin untuk memastikan aktivitas tim
                    berjalan dengan baik.
                </p>

            </div>

            <div class="member-header-stats">

                <div class="member-stat">
                    <small>Total Anggota</small>
                    <strong>{{ $members->count() }}</strong>
                </div>

                <div class="member-stat">
                    <small>Sedang Bertugas</small>
                    <strong>
                        {{ $members->where('status', 'aktif')->count() }}
                    </strong>
                </div>

            </div>

        </div>


        {{-- Search & Filter --}}
        <div class="member-toolbar">

            <form action="{{ route('admin.members.index') }}"
                method="GET"
                class="member-search">

                <span>⌕</span>

                <input
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari anggota tim, spesialisasi, atau ID..."
                    aria-label="Cari anggota"
                >

            </form>


            <select class="member-filter"
                name="division"
                onchange="this.form.submit()"
                form="member-filter-form">

                <option value="">
                    Semua Divisi
                </option>

                <option value="show-management"
                    @selected(request('division') === 'show-management')>
                    Show & Stage Management
                </option>

                <option value="client-hospitality"
                    @selected(request('division') === 'client-hospitality')>
                    Client & VIP Hospitality
                </option>

                <option value="av-lighting"
                    @selected(request('division') === 'av-lighting')>
                    AV, Lighting & Multimedia
                </option>

                <option value="logistik-protokoler"
                    @selected(request('division') === 'logistik-protokoler')>
                    Logistik & Protokoler
                </option>

            </select>


            <select class="member-filter"
                name="status"
                onchange="this.form.submit()"
                form="member-filter-form">

                <option value="">
                    Semua Status
                </option>

                <option value="aktif"
                    @selected(request('status') === 'aktif')>
                    Aktif di Lapangan
                </option>

                <option value="standby"
                    @selected(request('status') === 'standby')>
                    Standby
                </option>

                <option value="resiko"
                    @selected(request('status') === 'resiko')>
                    Gladi Resik
                </option>

                <option value="selesai"
                    @selected(request('status') === 'selesai')>
                    Selesai
                </option>

            </select>


            <form id="member-filter-form"
                action="{{ route('admin.members.index') }}"
                method="GET">

                <input type="hidden"
                    name="q"
                    value="{{ request('q') }}">

                <input type="hidden"
                    name="division"
                    value="{{ request('division') }}">

                <input type="hidden"
                    name="status"
                    value="{{ request('status') }}">

            </form>


            <a href="{{ route('admin.members.create') }}"
                class="btn-add-member">
                + Rekrut / Tambah Anggota
            </a>

        </div>


        {{-- Status Tabs --}}
        <div class="member-tabs">

            <a href="{{ route('admin.members.index') }}"
                class="member-tab {{ !request('status') ? 'active' : '' }}">
                Semua Anggota ({{ $members->count() }})
            </a>

            <a href="{{ route('admin.members.index', ['status' => 'aktif']) }}"
                class="member-tab {{ request('status') === 'aktif' ? 'active' : '' }}">
                ● Sedang Bertugas
            </a>

            <a href="{{ route('admin.members.index', ['status' => 'resiko']) }}"
                class="member-tab {{ request('status') === 'resiko' ? 'active' : '' }}">
                ◉ Gladi Resik
            </a>

            <a href="{{ route('admin.members.index', ['status' => 'standby']) }}"
                class="member-tab {{ request('status') === 'standby' ? 'active' : '' }}">
                ● Standby
            </a>

        </div>


        <div class="member-result-count">
            Menampilkan {{ $members->count() }} anggota tim
        </div>


        {{-- Member Grid --}}
        <div class="member-grid">

            @forelse ($members as $member)

                @php
                    $status = strtolower($member->status ?? 'standby');

                    $statusText = match ($status) {
                        'aktif' => 'Aktif di Lapangan',
                        'resiko' => 'Gladi Resik',
                        'standby' => 'Standby / On-Call',
                        'selesai' => 'Selesai',
                        default => ucfirst($status),
                    };

                    $statusClass = match ($status) {
                        'resiko' => 'warning',
                        'standby', 'selesai' => 'gray',
                        default => '',
                    };

                    $progress = 0;
                @endphp


                <div class="member-card">

                    <div class="member-card-body">

                        {{-- Status --}}
                        <div class="member-status">

                            <span class="member-status-badge {{ $statusClass }}">
                                <span class="member-status-dot"></span>
                                {{ $statusText }}
                            </span>

                            <a href="#"
                                class="member-menu">
                                ⋮
                            </a>

                        </div>


                        {{-- Profile --}}
                        <div class="member-profile">

                            <div class="member-avatar">

                                @if ($member->photo)
                                    <img
                                        src="{{ asset('storage/' . $member->photo) }}"
                                        alt="{{ $member->name }}"
                                    >
                                @else
                                    👤
                                @endif

                            </div>

                            <div class="member-name">

                                <h4>
                                    {{ $member->name }}
                                </h4>

                                <p>
                                    {{ $member->position ?? 'Anggota Tim' }}
                                </p>

                                <div class="member-id">
                                    ID:
                                    {{ $member->member_code ?? '-' }}
                                </div>

                            </div>

                        </div>


                        {{-- Role / Division --}}
                        <div class="member-role">
                            {{ $member->division ?? 'Belum ada divisi' }}
                        </div>


                        {{-- Project --}}
                        <div class="member-project">

                            <div class="member-project-label">
                                Project Aktif
                            </div>

                            <div class="member-project-name">
                                {{ $member->specialization ?? 'Belum ada spesialisasi' }}
                            </div>

                            <div class="member-project-location">
                                {{ $member->phone ?? 'Nomor belum ditentukan' }}
                            </div>

                        </div>


                        {{-- Progress --}}
                        <div class="member-progress-info">

                            <span>
                                Jadwal & Rundown
                            </span>

                            <strong>
                                {{ $progress }}% Selesai
                            </strong>

                        </div>

                        <div class="member-progress">

                            <div
                                class="member-progress-bar {{ $status === 'resiko' ? 'warning' : '' }}"
                                style="width: {{ min(100, max(0, $progress)) }}%;">
                            </div>

                        </div>


                        {{-- Actions --}}
                        <div class="member-actions">

                            <a href="{{ route('admin.members.show', $member) }}"
                                class="member-action dark">
                                ◉ Overview
                            </a>

                            <a href="{{ route('admin.members.edit', $member) }}"
                                class="member-action"
                                title="Edit anggota">
                                ✎
                            </a>

                            <form action="{{ route('admin.members.destroy', $member) }}"
                                method="POST"
                                onsubmit="return confirm('Hapus anggota ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="member-action"
                                    title="Hapus anggota">
                                    ×
                                </button>
                            </form>

                        </div>

                    </div>

                </div>

            @empty

                <div class="member-empty">

                    <div class="member-empty-icon">
                        👥
                    </div>

                    <h4>
                        Belum Ada Anggota
                    </h4>

                    <p>
                        Tambahkan anggota tim untuk mulai mengelola
                        penugasan dan aktivitas kru.
                    </p>

                    <a href="{{ route('admin.members.create') }}"
                        class="btn-add-member">
                        + Tambah Anggota
                    </a>

                </div>

            @endforelse

        </div>


        {{-- Bottom --}}
        <div class="member-bottom">

            <div class="member-bottom-text">
                Menampilkan {{ $members->count() }} anggota tim terdaftar
            </div>

            <div class="member-pagination">

                <a href="#"
                    class="member-page-btn">
                    ‹
                </a>

                <a href="#"
                    class="member-page-btn active">
                    1
                </a>

                <a href="#"
                    class="member-page-btn">
                    2
                </a>

                <a href="#"
                    class="member-page-btn">
                    3
                </a>

                <a href="#"
                    class="member-page-btn">
                    4
                </a>

                <a href="#"
                    class="member-page-btn">
                    ›
                </a>

            </div>

        </div>

    </div>

@endsection
