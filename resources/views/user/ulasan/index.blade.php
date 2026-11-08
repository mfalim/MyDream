@extends('user.layouts.app')

@section('title', 'Ulasan Saya')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/wo-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ulasan.css') }}">
@endpush

@section('content')
<main class="wo-page review-page">
    <header class="review-heading"><span class="wo-eyebrow"><i class="bi bi-circle-fill"></i> Cerita Pengalaman Anda</span><h1>Ulasan</h1><p>Apresiasi vendor yang membantu mewujudkan acara Anda.</p></header>

    @if(session('success'))<div class="review-alert review-alert-success"><i class="bi bi-check-circle"></i> {{ session('success') }}</div>@endif
    @if($errors->any())<div class="review-alert review-alert-error"><i class="bi bi-exclamation-circle"></i><div><strong>Mohon periksa kembali data ulasan.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>@endif

    <section class="wo-card review-form-card"><div class="wo-section-head"><div><span class="wo-eyebrow">Bagikan Cerita</span><h2>Tulis Ulasan</h2></div><span class="wo-badge wo-badge-gold"><i class="bi bi-heart-fill"></i> Terima kasih</span></div>
        @if($events->isEmpty())<div class="review-inline-empty"><i class="bi bi-calendar2-heart"></i><p>Belum ada acara selesai yang bisa diulas.</p></div>
        @else
        <form method="GET" action="{{ route('user.reviews.index') }}" class="review-select-event"><label class="review-field"><span>Pilih acara</span><select name="event" required onchange="this.form.submit()"><option value="">Pilih acara selesai</option>@foreach($events as $event)<option value="{{ $event->id }}" @selected($selectedEvent?->id === $event->id)>{{ $event->name }} — {{ $event->event_date?->format('d M Y') }}</option>@endforeach</select></label><noscript><button class="wo-btn wo-btn-outline" type="submit">Pilih Acara</button></noscript></form>
        @if($selectedEvent)
            @if($alreadyReviewed)<div class="review-inline-empty"><i class="bi bi-patch-check"></i><p>Acara ini sudah Anda ulas. Terima kasih telah berbagi pengalaman.</p></div>
            @else
            <form method="POST" action="{{ route('user.reviews.store') }}" class="review-form">@csrf<input type="hidden" name="event_id" value="{{ $selectedEvent->id }}">
                <fieldset class="review-rating"><legend>Rating</legend><div class="review-stars">@for($rating = 5; $rating >= 1; $rating--)<input id="rating-{{ $rating }}" type="radio" name="rating" value="{{ $rating }}" @checked((int) old('rating') === $rating) required><label for="rating-{{ $rating }}" aria-label="{{ $rating }} bintang">★</label>@endfor</div><span>Pilih 1 sampai 5 bintang</span></fieldset>
                <label class="review-field"><span>Komentar <small>(opsional)</small></span><textarea name="comment" rows="4" maxlength="1000" placeholder="Ceritakan pengalaman Anda bersama vendor ini...">{{ old('comment') }}</textarea></label>
                <button class="wo-btn wo-btn-dark" type="submit"><i class="bi bi-send"></i> Kirim Ulasan</button>
            </form>
            @endif
        @endif
        @endif
    </section>

    <section class="review-list-section"><div class="wo-section-head"><div><span class="wo-eyebrow">Arsip Cerita</span><h2>Ulasan Saya</h2></div><span class="wo-badge wo-badge-neutral">{{ $reviews->total() }} ulasan</span></div>
        @if($reviews->isEmpty())<div class="wo-card review-empty"><span class="history-empty-icon"><i class="bi bi-chat-heart"></i></span><h3>Belum ada ulasan</h3><p>Ulasan yang Anda kirim akan tersimpan di sini.</p></div>
        @else<div class="review-list">@foreach($reviews as $review)<article class="wo-card review-item"><div class="review-item-head"><div><span class="wo-eyebrow">{{ $review->created_at->format('d M Y') }}</span><h3>{{ $review->event->name }}</h3><p>{{ $review->event->event_date?->format('d M Y') }}</p></div><span class="review-rating-display" aria-label="{{ $review->rating }} dari 5 bintang">{{ str_repeat('★', $review->rating) }}<span>{{ str_repeat('☆', 5 - $review->rating) }}</span></span></div>@if($review->comment)<p class="review-comment">{{ $review->comment }}</p>@endif</article>@endforeach</div><div class="history-pagination">{{ $reviews->links() }}</div>@endif
    </section>
</main>
@endsection
