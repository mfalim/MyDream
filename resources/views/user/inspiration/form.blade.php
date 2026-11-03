{{-- resources/views/user/inspiration/form.blade.php --}}
@extends('user.layouts.app')

@section('title', 'Tulis Cerita Pernikahan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/wo-shared.css') }}">
@endpush

@section('content')
<main class="wo-page">

    @php
        $dateLabel = $date->copy()->locale('id')->translatedFormat('d F Y');
        $existingPhotos = $story?->photos ?? collect();
    @endphp

    <div class="mb-4">
        <a href="{{ route('user.inspiration') }}" class="text-decoration-none">&larr; Kembali</a>
        <h1 class="mt-2">{{ $story ? 'Ubah Cerita Pernikahan' : 'Tulis Cerita Pernikahan' }}</h1>
        <p>Cerita ini akan ditinjau tim MyDream sebelum tayang di halaman Inspirasi.</p>
    </div>

    @if ($story && $story->status === 'rejected' && $story->admin_note)
        <div class="alert alert-warning"><strong>Catatan dari tim MyDream:</strong> {{ $story->admin_note }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Data acara: otomatis dari pesanan, tidak bisa diubah --}}
    <div class="wo-card p-4 mb-3">
        <strong class="d-block mb-2">Data acara (diambil dari pesanan Anda)</strong>
        <div class="row g-3 text-muted">
            <div class="col-md-3"><small>Pasangan</small><div class="text-dark">{{ $booking->client->groom_name }} &amp; {{ $booking->client->bride_name }}</div></div>
            <div class="col-md-3"><small>Tanggal acara</small><div class="text-dark">{{ $dateLabel }}</div></div>
            <div class="col-md-3"><small>Lokasi</small><div class="text-dark">{{ $booking->venue_name }}, {{ $booking->venue_city }}</div></div>
            <div class="col-md-3"><small>Paket</small><div class="text-dark">{{ $booking->package?->name ?? 'Vendor pilihan sendiri' }}</div></div>
        </div>
    </div>

    <form action="{{ route('user.inspiration.save', $booking->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="wo-card p-4 mb-3">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Judul cerita</label>
                    <input type="text" name="title" class="form-control" maxlength="150" value="{{ old('title', $story->title ?? '') }}" placeholder="mis. Akad Outdoor di Tengah Hutan Pinus" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tema acara</label>
                    <select name="theme" class="form-select" required>
                        <option value="">Pilih tema</option>
                        @foreach ($themes as $theme)
                            <option value="{{ $theme }}" @selected(old('theme', $story->theme ?? '') === $theme)>{{ $theme }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Cerita acara Anda</label>
                    <textarea name="story" class="form-control" rows="7" required minlength="50" placeholder="Bagaimana acara berlangsung? Momen apa yang paling berkesan? Tantangan apa yang berhasil diatasi?">{{ old('story', $story->story ?? '') }}</textarea>
                    <small class="text-muted">Minimal 50 karakter.</small>
                </div>
                <div class="col-12">
                    <label class="form-label">Tips untuk calon pengantin <small class="text-muted">(opsional)</small></label>
                    <textarea name="tips" class="form-control" rows="3">{{ old('tips', $story->tips ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="wo-card p-4 mb-3">
            <label class="form-label">Foto acara</label>
            @if ($existingPhotos->isNotEmpty())
                <div class="d-flex flex-wrap gap-3 mb-3">
                    @foreach ($existingPhotos as $photo)
                        <div class="text-center">
                            <img src="{{ \App\Support\Media::url($photo->photo) }}" alt="Foto acara" style="width: 110px; height: 82px; object-fit: cover; border-radius: 8px;">
                            <div class="form-check d-flex justify-content-center gap-1 mt-1">
                                <input class="form-check-input" type="checkbox" name="delete_photos[]" value="{{ $photo->id }}" id="del-{{ $photo->id }}">
                                <label class="form-check-label small" for="del-{{ $photo->id }}">Hapus</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            <input type="file" name="photos[]" class="form-control" accept=".jpg,.jpeg,.png,.webp" multiple>
            <small class="text-muted">Minimal 1 foto, total maksimal {{ $maxPhotos }} foto, masing-masing maksimal 2 MB. Foto pertama menjadi sampul.</small>
        </div>

        <div class="wo-card p-4 mb-3">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label d-block">Penilaian untuk MyDream</label>
                    @for ($i = 1; $i <= 5; $i++)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="rating" id="rating-{{ $i }}" value="{{ $i }}" @checked((int) old('rating', $story->rating ?? 0) === $i) required>
                            <label class="form-check-label" for="rating-{{ $i }}">{{ $i }} &#9733;</label>
                        </div>
                    @endfor
                </div>
                <div class="col-md-8">
                    <label class="form-label">Testimoni <small class="text-muted">(opsional)</small></label>
                    <textarea name="testimonial" class="form-control" rows="3" maxlength="1000">{{ old('testimonial', $story->testimonial ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="wo-card p-4 mb-4">
            <label class="form-label d-block">Nama pasangan di halaman publik</label>
            @php $showNames = (string) old('show_couple_names', isset($story) ? (int) $story->show_couple_names : 1); @endphp
            <div class="form-check">
                <input class="form-check-input" type="radio" name="show_couple_names" id="names-1" value="1" @checked($showNames === '1')>
                <label class="form-check-label" for="names-1">Tampilkan nama saya ({{ $booking->client->groom_name }} &amp; {{ $booking->client->bride_name }})</label>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="show_couple_names" id="names-0" value="0" @checked($showNames === '0')>
                <label class="form-check-label" for="names-0">Samarkan menjadi &ldquo;Pasangan MyDream&rdquo;</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="consent" id="consent" value="1" @checked(old('consent')) required>
                <label class="form-check-label" for="consent">Saya setuju cerita dan foto ini dipublikasikan di website MyDream.</label>
            </div>
        </div>

        <button type="submit" class="wo-btn wo-btn-dark">{{ $story ? 'Simpan & Kirim Ulang' : 'Kirim untuk Ditinjau' }}</button>
        <a href="{{ route('user.inspiration') }}" class="wo-btn wo-btn-outline">Batal</a>
    </form>

</main>
@endsection
