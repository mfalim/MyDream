@extends('layouts.admin')

@section('title', 'Event Organizer Internal')
@section('page-title', 'Event Organizer Internal')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Event Organizer Internal</h2>
            <p class="text-muted mb-0">Manage event internal seperti technical meeting, briefing, dll</p>
        </div>
        <a href="{{ route('admin.organizer-event.create') }}" class="btn btn-dark">
            <i class="bi bi-plus-circle me-2"></i>Tambah Event Organizer
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Judul Event</th>
                            <th class="px-4 py-3">Tipe</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Waktu</th>
                            <th class="px-4 py-3">Lokasi</th>
                            <th class="px-4 py-3">Prioritas</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($organizerEvents as $event)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="fw-semibold">{{ $event->title }}</div>
                                @if($event->description)
                                <small class="text-muted">{{ Str::limit($event->description, 50) }}</small>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $event->event_type)) }}</span>
                            </td>
                            <td class="px-4 py-3">{{ $event->event_date->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                @if($event->start_time)
                                    {{ $event->start_time }} - {{ $event->end_time ?? '-' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $event->location ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if($event->priority == 'high')
                                    <span class="badge bg-danger">High</span>
                                @elseif($event->priority == 'medium')
                                    <span class="badge bg-warning">Medium</span>
                                @else
                                    <span class="badge bg-secondary">Low</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($event->status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($event->status == 'cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                @else
                                    <span class="badge bg-primary">Scheduled</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.organizer-event.show', $event->id) }}" class="btn btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.organizer-event.edit', $event->id) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.organizer-event.destroy', $event->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus event ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                <p class="mt-2">Belum ada event organizer</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($organizerEvents->hasPages())
    <div class="mt-4">
        {{ $organizerEvents->links() }}
    </div>
    @endif
</div>
@endsection
