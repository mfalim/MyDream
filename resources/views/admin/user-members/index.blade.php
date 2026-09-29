@extends('layouts.admin')

@section('title', 'User Member Management')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">User Member Management</h1>
            <p class="text-muted mb-0">Kelola akun user member untuk login via Google OAuth</p>
        </div>
        <a href="{{ route('admin.user-members.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah User Member
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total User Member</p>
                            <h3 class="mb-0">{{ $users->count() }}</h3>
                        </div>
                        <div class="text-primary" style="font-size: 2rem;">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Sudah Login</p>
                            <h3 class="mb-0">{{ $users->whereNotNull('google_id')->count() }}</h3>
                        </div>
                        <div class="text-success" style="font-size: 2rem;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Profile Lengkap</p>
                            <h3 class="mb-0">{{ \App\Models\Member::whereIn('user_id', $users->pluck('id'))->count() }}</h3>
                        </div>
                        <div class="text-info" style="font-size: 2rem;">
                            <i class="bi bi-person-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Email</th>
                            <th>Nama</th>
                            <th>Status OAuth</th>
                            <th>Profile Member</th>
                            <th>Posisi</th>
                            <th>Dibuat</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            @php
                                $member = \App\Models\Member::where('user_id', $user->id)->first();
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $user->email }}</strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($user->avatar)
                                            <img src="{{ $user->avatar }}" class="rounded-circle" width="32" height="32">
                                        @else
                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" 
                                                 style="width: 32px; height: 32px; font-size: 0.75rem; font-weight: 600;">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                        @endif
                                        {{ $user->name }}
                                    </div>
                                </td>
                                <td>
                                    @if($user->google_id)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Terhubung
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-clock"></i> Belum Login
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($member)
                                        <a href="{{ route('admin.members.show', $member->id) }}" class="badge bg-info text-decoration-none">
                                            <i class="bi bi-person-badge"></i> {{ $member->member_code }}
                                        </a>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-dash-circle"></i> Belum Lengkap
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($member)
                                        <small class="text-muted">{{ $member->position }}</small>
                                    @else
                                        <small class="text-muted">-</small>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $user->created_at->format('d M Y') }}</small>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('admin.user-members.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                        <p class="mt-3 mb-0">Belum ada user member</p>
                                        <small>Klik tombol "Tambah User Member" untuk memulai</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="alert alert-info mt-4">
        <h6 class="alert-heading"><i class="bi bi-info-circle"></i> Cara Kerja</h6>
        <ol class="mb-0">
            <li>Admin menambahkan <strong>email member</strong> di sini (hanya email + role member)</li>
            <li>Member <strong>login menggunakan Google OAuth</strong> dengan email tersebut</li>
            <li>Member diarahkan untuk <strong>melengkapi profile</strong> (nama, posisi, divisi, dll)</li>
            <li>Data lengkap member masuk ke <strong>tabel Members</strong></li>
        </ol>
    </div>
</div>
@endsection
