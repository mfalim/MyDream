{{-- resources/views/user/inspiration/index.blade.php --}}
@extends('user.layouts.app')

@section('title', 'Cerita & Inspirasi')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/wo-shared.css') }}">
@endpush

@section('content')
<main class="wo-page">

    <div class="mb-4">
        <span class="wo-eyebrow"><i class="bi bi-circle-fill"></i> Cerita &amp; Inspirasi</span>
        <h1>Bagikan Cerita Pernikahan Anda</h1>
        <p>Setelah hari pernikahan berlalu, ceritakan bagaimana acara Anda berlangsung. Setelah ditinjau tim MyDream, cerita dan foto Anda bisa menjadi inspirasi bagi calon pengantin lain.</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @forelse ($items as $item)
        @php
            $booking = $item['booking'];
            $story = $item['story'];
            $dateLabel = $item['date']->copy()->locale('id')->translatedFormat('d F Y');
        @endphp

        <div class="wo-card p-4 mb-3">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <strong class="d-block">{{ $booking->package?->name ?? 'Vendor pilihan sendiri' }}</strong>
                    <span class="text-muted">{{ $booking->venue_name }}, {{ $booking->venue_city }} &middot; {{ $dateLabel }}</span>
                </div>

                <div class="text-end">
                    @if (! $item['open'])
                        <span class="wo-badge wo-badge-gold"><i class="bi bi-lock"></i> Dibuka setelah {{ $dateLabel }}</span>
                    @elseif (! $story)
                        <span class="wo-badge wo-badge-gold">Belum diisi</span>
                    @elseif ($story->status === 'approved')
                        <span class="wo-badge wo-badge-green"><i class="bi bi-check-circle"></i> Tayang</span>
                    @elseif ($story->status === 'rejected')
                        <span class="wo-badge wo-badge-red"><i class="bi bi-arrow-repeat"></i> Perlu Revisi</span>
                    @else
                        <span class="wo-badge wo-badge-gold"><i class="bi bi-hourglass-split"></i> Menunggu Tinjauan</span>
                    @endif
                </div>
            </div>

            @if ($story && $story->status === 'rejected' && $story->admin_note)
                <div class="alert alert-warning mt-3 mb-0">
                    <strong>Catatan dari tim MyDream:</strong> {{ $story->admin_note }}
                </div>
            @endif

            <div class="mt-3">
                @if (! $item['open'])
                    <p class="text-muted mb-0">Form cerita baru bisa diisi setelah tanggal pernikahan Anda berlalu, agar setiap cerita yang tayang benar-benar nyata.</p>
                @elseif ($story && $story->status === 'approved')
                    <a href="{{ route('inspiration.show', $story->slug) }}" target="_blank" class="wo-btn wo-btn-outline wo-btn-sm">Lihat di Halaman Inspirasi</a>
                @elseif ($story && $story->status === 'pending')
                    <a href="{{ route('user.inspiration.form', $booking->id) }}" class="wo-btn wo-btn-outline wo-btn-sm">Ubah Cerita</a>
                @else
                    <a href="{{ route('user.inspiration.form', $booking->id) }}" class="wo-btn wo-btn-dark wo-btn-sm">
                        {{ $story ? 'Revisi & Kirim Ulang' : 'Tulis Cerita' }}
                    </a>
                @endif
            </div>
        </div>
    @empty
        <div class="wo-card p-4 text-center">
            <strong class="d-block mb-1">Belum ada pernikahan yang bisa diceritakan</strong>
            <p class="text-muted mb-0">Pesanan yang sudah disetujui akan muncul di sini. Form cerita terbuka setelah tanggal acara berlalu.</p>
        </div>
    @endforelse

</main>
@endsection
