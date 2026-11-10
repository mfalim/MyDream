@extends('layouts.admin')

@section('title', 'Tinjau Cerita')
@section('page-title', 'Tinjau Cerita Client')

@section('content')

    <div class="mb-4">
        <a href="{{ route('admin.inspirations.index', ['status' => $item->status]) }}" class="text-decoration-none">&larr; Kembali ke daftar</a>
        <h3 class="mb-1 mt-2">{{ $item->title }}</h3>
        <p class="text-muted mb-0">Dikirim {{ $item->submitted_at?->locale('id')->translatedFormat('d F Y, H:i') ?? '-' }} &middot; Status: <strong>{{ $item->status_label }}</strong></p>
    </div>

    <form action="{{ route('admin.inspirations.update', $item) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row g-3 mb-3 text-muted">
                            <div class="col-md-4"><small>Pasangan (asli)</small><div class="text-dark">{{ $item->couple_names }}</div></div>
                            <div class="col-md-4"><small>Tanggal & lokasi acara</small><div class="text-dark">{{ $item->event_date?->locale('id')->translatedFormat('d F Y') ?? '-' }}, {{ $item->venue_name }} {{ $item->city }}</div></div>
                            <div class="col-md-4"><small>Paket</small><div class="text-dark">{{ $item->package?->name ?? 'Vendor pilihan sendiri' }}</div></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Judul</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $item->title) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tema</label>
                            <select name="theme" class="form-select" required>
                                @foreach ($themes as $theme)
                                    <option value="{{ $theme }}" @selected(old('theme', $item->theme) === $theme)>{{ $theme }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cerita</label>
                            <textarea name="story" class="form-control" rows="8" required>{{ old('story', $item->story) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tips</label>
                            <textarea name="tips" class="form-control" rows="3">{{ old('tips', $item->tips) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Testimoni {{ $item->rating ? '(penilaian ' . $item->rating . '/5)' : '' }}</label>
                            <textarea name="testimonial" class="form-control" rows="3">{{ old('testimonial', $item->testimonial) }}</textarea>
                        </div>
                        <div>
                            <label class="form-label d-block">Nama pasangan di publik</label>
                            @php $showNames = (string) old('show_couple_names', (int) $item->show_couple_names); @endphp
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="show_couple_names" id="n1" value="1" @checked($showNames === '1')>
                                <label class="form-check-label" for="n1">Tampilkan ({{ $item->couple_names }})</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="show_couple_names" id="n0" value="0" @checked($showNames === '0')>
                                <label class="form-check-label" for="n0">Samarkan (&ldquo;Pasangan MyDream&rdquo;)</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <label class="form-label">Foto ({{ $item->photos->count() }})</label>
                        <div class="d-flex flex-wrap gap-3">
                            @forelse ($item->photos as $photo)
                                <div class="text-center">
                                    <a href="{{ \App\Support\Media::url($photo->photo) }}" target="_blank">
                                        <img src="{{ \App\Support\Media::url($photo->photo) }}" alt="Foto" style="width: 130px; height: 98px; object-fit: cover; border-radius: 8px;">
                                    </a>
                                    <div class="form-check d-flex justify-content-center gap-1 mt-1">
                                        <input class="form-check-input" type="checkbox" name="delete_photos[]" value="{{ $photo->id }}" id="del-{{ $photo->id }}">
                                        <label class="form-check-label small" for="del-{{ $photo->id }}">Hapus</label>
                                    </div>
                                </div>
                            @empty
                                <span class="text-muted">Tidak ada foto.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3">Keputusan</h5>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                @foreach ($statuses as $key => $label)
                                    <option value="{{ $key }}" @selected(old('status', $item->status) === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Tayang = tampil di halaman Inspirasi. Perlu Revisi = dikembalikan ke client dengan catatan.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan untuk client</label>
                            <textarea name="admin_note" class="form-control" rows="4" placeholder="Wajib diisi jika meminta revisi">{{ old('admin_note', $item->admin_note) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Simpan Keputusan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection
