@extends('layouts.admin')

@section('title', 'Inspirasi Client')
@section('page-title', 'Inspirasi Client')

@section('content')

    <div class="mb-4">
        <h3 class="mb-1">Cerita &amp; Inspirasi dari Client</h3>
        <p class="text-muted mb-0">Cerita yang dikirim client setelah pernikahannya berlalu. Tinjau lalu setujui agar tayang di halaman Inspirasi, atau minta revisi. Untuk client yang belum sempat mengisi, tulis ceritanya sebagai Artikel (kategori Real Wedding).</p>
    </div>

    <ul class="nav nav-pills mb-3">
        @foreach ($statuses as $key => $label)
            <li class="nav-item">
                <a class="nav-link {{ $status === $key ? 'active' : '' }}" href="{{ route('admin.inspirations.index', ['status' => $key]) }}">
                    {{ $label }} <span class="badge text-bg-light">{{ $counts[$key] ?? 0 }}</span>
                </a>
            </li>
        @endforeach
    </ul>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 90px;">Foto</th>
                        <th>Cerita</th>
                        <th>Acara</th>
                        <th>Dikirim</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td>
                                <img src="{{ \App\Support\Media::url($item->cover) }}" alt="{{ $item->title }}" style="width: 72px; height: 54px; object-fit: cover; border-radius: 6px;">
                            </td>
                            <td>
                                <strong>{{ $item->title }}</strong><br>
                                <small class="text-muted">{{ $item->couple_names }} &middot; {{ $item->theme }}</small>
                            </td>
                            <td>
                                {{ $item->event_date?->locale('id')->translatedFormat('d F Y') ?? '-' }}<br>
                                <small class="text-muted">{{ $item->venue_name }}{{ $item->city ? ', ' . $item->city : '' }}</small>
                            </td>
                            <td>{{ $item->submitted_at?->locale('id')->translatedFormat('d F Y') ?? '-' }}</td>
                            <td class="text-end">
                                @if ($item->status === 'approved')
                                    <a href="{{ route('inspiration.show', $item->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Lihat</a>
                                @endif
                                <a href="{{ route('admin.inspirations.edit', $item) }}" class="btn btn-sm btn-outline-primary">{{ $item->status === 'pending' ? 'Tinjau' : 'Buka' }}</a>
                                <form action="{{ route('admin.inspirations.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus cerita ini beserta fotonya?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">Tidak ada cerita dengan status "{{ $statuses[$status] }}".</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
