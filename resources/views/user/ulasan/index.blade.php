@extends('user.layouts.app')

@section('title', 'Ulasan Saya')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/wo-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ulasan.css') }}">
@endpush

@section('content')
<main class="wo-page review-page">
    <header class="review-heading">
        <span class="wo-eyebrow"><i class="bi bi-circle-fill"></i> Cerita Pengalaman Anda</span>
        <h1>Ulasan Acara</h1>
        <p>Bagikan pengalaman Anda. Ulasan yang dipublikasikan akan tampil sebagai cerita di halaman Blog.</p>
    </header>

    @if(session('success'))
        <div class="review-alert review-alert-success">
            <i class="bi bi-check-circle"></i>
            <div>{{ session('success') }} <a class="review-blog-link" href="{{ route('blog.index') }}">Lihat di Blog <i class="bi bi-arrow-up-right"></i></a></div>
        </div>
    @endif
    @if($errors->any())
        <div class="review-alert review-alert-error">
            <i class="bi bi-exclamation-circle"></i>
            <div><strong>Mohon periksa kembali cerita Anda.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        </div>
    @endif

    <section class="wo-card review-form-card">
        <div class="review-form-heading">
            <div><span class="wo-eyebrow">Cerita &amp; Pengalaman</span><h2>Tulis Cerita Acara</h2><p>Hanya acara selesai milik Anda yang dapat dipublikasikan.</p></div>
            <span class="wo-badge wo-badge-gold"><i class="bi bi-heart-fill"></i> Cerita Client</span>
        </div>

        @if($events->isEmpty())
            <div class="review-inline-empty"><i class="bi bi-calendar2-heart"></i><p>Belum ada acara selesai yang dapat diulas.</p></div>
        @else
            <form method="GET" action="{{ route('user.reviews.index') }}" class="review-select-event">
                <label class="review-field" for="review-event"><span>Pilih acara selesai</span>
                    <select id="review-event" name="event" required onchange="this.form.submit()">
                        <option value="">Pilih acara yang ingin diceritakan</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" @selected($selectedEvent?->id === $event->id)>{{ $event->name }} — {{ $event->event_date?->format('d M Y') }}</option>
                        @endforeach
                    </select>
                </label>
                <noscript><button class="wo-btn wo-btn-outline" type="submit">Tampilkan Acara</button></noscript>
            </form>

            @if($selectedEvent)
                <div class="review-event-summary">
                    <span class="review-event-icon"><i class="bi bi-calendar2-heart"></i></span>
                    <div><span class="wo-eyebrow">Acara yang dipilih</span><strong>{{ $selectedEvent->name }}</strong><span>{{ $selectedEvent->event_date?->format('d F Y') }} · {{ ucfirst($selectedEvent->event_type ?? 'Pernikahan') }}</span></div>
                    <span class="wo-badge wo-badge-green">Selesai</span>
                </div>

                @if($alreadyPublished)
                    <div class="review-inline-empty"><i class="bi bi-patch-check"></i><p>Cerita untuk acara ini sudah dipublikasikan di Blog.</p></div>
                @else
                    <form method="POST" action="{{ route('user.reviews.store') }}" enctype="multipart/form-data" class="review-form">
                        @csrf
                        <input type="hidden" name="event_id" value="{{ $selectedEvent->id }}">

                        <div class="review-composer-fields">
                            <label class="review-field"><span>Judul Artikel / Cerita <small>(otomatis)</small></span>
                                <input type="text" value="{{ $selectedEvent->name }}" readonly>
                                <small class="review-help">Judul mengikuti nama acara yang dipilih.</small>
                            </label>
                            <label class="review-field"><span>Kategori</span><input type="text" value="Ulasan Client" readonly></label>
                            <label class="review-field"><span>Penulis / Author</span><input type="text" value="Client MyDream" readonly></label>
                            <label class="review-field"><span>Tanggal Tayang</span><input type="text" value="{{ today()->locale('id')->translatedFormat('d F Y') }}" readonly></label>
                        </div>

                        <label class="review-field"><span>Ringkasan / Excerpt <small>(opsional, maks. 300 karakter)</small></span>
                            <textarea name="excerpt" rows="2" maxlength="300" placeholder="Ringkasan singkat yang akan tampil pada kartu Blog...">{{ old('excerpt') }}</textarea>
                            <small class="review-help">Jika dikosongkan, ringkasan diambil otomatis dari cerita Anda.</small>
                        </label>

                        <fieldset class="review-rating">
                            <legend>Rating acara <span class="review-required">*</span></legend>
                            <div class="review-stars">
                                @for($rating = 5; $rating >= 1; $rating--)
                                    <input id="rating-{{ $rating }}" type="radio" name="rating" value="{{ $rating }}" @checked((int) old('rating') === $rating) required>
                                    <label for="rating-{{ $rating }}" aria-label="{{ $rating }} bintang">★</label>
                                @endfor
                            </div>
                            <span>Pilih rating dari 1 sampai 5 bintang.</span>
                        </fieldset>

                        <label class="review-field"><span>Isi Cerita / Pengalaman <small>(opsional)</small></span>
                            <textarea name="comment" rows="6" maxlength="1000" placeholder="Ceritakan momen dan pengalaman Anda selama acara berlangsung...">{{ old('comment') }}</textarea>
                            <small class="review-help">Maksimal 1.000 karakter. Jika kosong, cerita akan berisi rating acara.</small>
                        </label>

                        <label class="review-field review-photo-field"><span>Foto Sampul <small>(opsional)</small></span>
                            <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp">
                            <small class="review-help">Format JPG, PNG, WEBP. Maksimal 2 MB.</small>
                        </label>

                        <div class="review-publish-note"><i class="bi bi-globe2"></i><span>Ulasan akan langsung dipublikasikan pada halaman Blog dan dapat dilihat pengunjung.</span></div>
                        <div class="review-form-actions"><button class="wo-btn wo-btn-dark" type="submit"><i class="bi bi-send"></i> Publikasikan Cerita</button></div>
                    </form>
                @endif
            @endif
        @endif
    </section>

    <section class="review-list-section">
        <div class="wo-section-head"><div><span class="wo-eyebrow">Arsip Cerita</span><h2>Ulasan Saya</h2></div><span class="wo-badge wo-badge-neutral">{{ $stories->total() }} cerita</span></div>
        @if($stories->isEmpty())
            <div class="wo-card review-empty"><span class="history-empty-icon"><i class="bi bi-chat-heart"></i></span><h3>Belum ada cerita</h3><p>Cerita acara yang Anda publikasikan akan tercatat di sini.</p></div>
        @else
            <div class="review-list">
                @foreach($stories as $story)
                    <article class="wo-card review-item">
                        <div class="review-item-head">
                            <div><span class="wo-eyebrow">{{ $story->created_at?->locale('id')->translatedFormat('d M Y') }}</span><h3>{{ $story->title }}</h3><p>{{ $story->event?->event_date?->locale('id')->translatedFormat('d F Y') }}</p></div>
                            <span class="review-rating-display" aria-label="{{ $story->rating }} dari 5 bintang">{{ str_repeat('★', (int) $story->rating) }}<span>{{ str_repeat('☆', 5 - (int) $story->rating) }}</span></span>
                        </div>
                        @if($story->excerpt)<p class="review-comment">{{ $story->excerpt }}</p>@endif
                        <a class="review-blog-link" href="{{ route('blog.show', $story->slug) }}">Lihat cerita di Blog <i class="bi bi-arrow-up-right"></i></a>
                    </article>
                @endforeach
            </div>
            <div class="history-pagination">{{ $stories->links() }}</div>
        @endif
    </section>
</main>
@endsection
