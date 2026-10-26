@extends('layouts.admin')

@section('title', 'Event Publik')
@section('page-title', 'Event Publik')

@section('content')

    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
        <div>
            <h3 class="mb-1">Event Publik</h3>
            <p class="text-muted mb-0">Pameran, open house, dan workshop yang tampil di halaman Event website. Terpisah dari acara pernikahan client.</p>
        </div>
        <a href="{{ route('admin.public-events.create') }}" class="btn btn-dark">+ Tambah Event</a>
    </div>

    @php
        $statusBadge = ['upcoming' => ['Akan Datang', 'success'], 'ongoing' => ['Berlangsung', 'danger'], 'finished' => ['Selesai', 'secondary']];
    @endphp

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 90px;">Foto</th>
                        <th>Event</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($events as $event)
                        @php [$label, $color] = $statusBadge[$event->status]; @endphp
                        <tr>
                            <td>
                                <img src="{{ \App\Support\Media::url($event->photo) }}" alt="{{ $event->title }}" style="width: 72px; height: 54px; object-fit: cover; border-radius: 6px;">
                            </td>
                            <td>
                                <strong>{{ $event->title }}</strong><br>
                                <small class="text-muted">{{ $event->location ?: '-' }} · {{ $event->vendors_count }} vendor</small>
                            </td>
                            <td>
                                {{ $event->date_label }}<br>
                                <small class="text-muted">{{ $event->time_label ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge text-bg-{{ $color }}">{{ $label }}</span>
                                @unless ($event->is_published)
                                    <span class="badge text-bg-warning">Disembunyikan</span>
                                @endunless
                            </td>
                            <td class="text-end">
                                @if ($event->is_published)
                                    <a href="{{ route('event.show', $event->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Lihat</a>
                                @endif
                                <a href="{{ route('admin.public-events.edit', $event) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('admin.public-events.destroy', $event) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus event ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">Belum ada event. Klik "+ Tambah Event" untuk membuat yang pertama.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
