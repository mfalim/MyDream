{{-- resources/views/user/explore/index.blade.php --}}
@extends('user.layouts.app')

@section('title', 'Eksplor & Tambah Vendor')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/wo-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/explore-vendor.css') }}">
@endpush

@section('content')
<main class="wo-page explore-vendor">

    <div class="ev-head">
        <div>
            <span class="wo-eyebrow"><i class="bi bi-geo-alt-fill"></i> Vendor Marketplace &amp; Curation</span>
            <h1>Eksplor &amp; Tambah Vendor Baru</h1>
            <p>Tambahkan vendor rekanan terakreditasi atau daftarkan vendor pilihan pribadi Anda ke dalam susunan hari pernikahan ({{ $eventDateLabel }}). Tim WO akan mengoordinasikan kontrak dan rundown teknis.</p>
        </div>

        <div class="ev-mode-switch">
            <button type="button" class="is-active">Katalog Rekanan Terkurasi</button>
            <button type="button">Daftarkan Custom Vendor</button>
        </div>
    </div>


    <div class="ev-toolbar">
        <div class="ev-search">
            <i class="bi bi-search"></i>
            <input type="text" id="evSearch" placeholder="Cari vendor berdasarkan nama, gaya estetika dekorasi, atau anggaran...">
        </div>

        <button type="button" class="wo-btn wo-btn-outline">
            <i class="bi bi-sliders"></i> Filter Detail
        </button>
    </div>

    <div class="ev-categories" id="evCategories">
        <button type="button" class="ev-cat is-active" data-cat="all">Semua Kategori ({{ count($vendors) }})</button>
        @foreach ($categories as $cat)
            <button type="button" class="ev-cat" data-cat="{{ $cat }}">{{ $cat }}</button>
        @endforeach
    </div>


    <div class="ev-grid" id="evGrid">
        @foreach ($vendors as $slug => $v)
            <a href="{{ route('user.vendor-overview', $slug) }}" class="ev-card" data-cat="{{ $v['category'] }}" data-name="{{ strtolower($v['name']) }}">

                <div class="ev-card-media">
                    <img src="{{ $v['image'] }}" alt="{{ $v['name'] }}">
                    <span class="ev-card-cat"><i class="bi bi-tag"></i> {{ $v['category'] }}</span>
                    <span class="ev-card-rating"><i class="bi bi-star-fill"></i> {{ $v['rating'] }}</span>
                </div>

                <div class="ev-card-body">
                    <span class="ev-card-meta">{{ $v['tagline'] }} <span>&bull;</span> {{ $v['experience'] }}</span>
                    <strong>{{ $v['name'] }}</strong>
                    <p>{{ \Illuminate\Support\Str::limit($v['description'], 78) }}</p>

                    <div class="ev-card-price">
                        <span>Mulai Dari</span>
                        <strong>{{ $v['price'] }}</strong>
                    </div>

                    <span class="wo-btn wo-btn-dark ev-card-btn">
                        <i class="bi bi-plus-circle"></i> Pilih Vendor Ini
                    </span>
                </div>

            </a>
        @endforeach
    </div>


    {{-- CUSTOM VENDOR --}}
    <div class="wo-card ev-custom">
        <span class="wo-badge wo-badge-gold"><i class="bi bi-gift"></i> Bebas Pilih Vendor Pribadi</span>
        <h2>Punya vendor langganan sendiri di luar rekanan WO PROJECT?</h2>
        <p>Tidak masalah! Anda dapat mendaftarkan vendor independen pilihan keluarga. Tim kami akan melakukan validasi kelayakan teknis panggung, mengoordinasikan loading barang dengan pihak venue, serta memasukkannya ke dalam Rundown Resmi Hari H.</p>

        <form class="ev-custom-form" onsubmit="event.preventDefault(); alert('Pengajuan vendor custom terkirim ke Tim WO untuk kurasi (dummy).');">
            <div class="ev-form-row">
                <div>
                    <label>Nama Brand / Vendor</label>
                    <input type="text" placeholder="Contoh: Sanggar Rias Ibu Dewi">
                </div>
                <div>
                    <label>Kategori Layanan</label>
                    <select>
                        <option>Pilih Kategori Vendor</option>
                        @foreach ($categories as $cat)
                            <option>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="ev-form-row">
                <div>
                    <label>No. WhatsApp PIC Vendor</label>
                    <input type="text" placeholder="Contoh: 0812-3456-7890">
                </div>
                <div>
                    <label>Estimasi Biaya / Invoice (Opsional)</label>
                    <input type="text" placeholder="Contoh: Rp 15.000.000">
                </div>
            </div>

            <div class="ev-form-footer">
                <small><i class="bi bi-info-circle"></i> WO Project tidak memungut komisi dari vendor bawaan pribadi.</small>
                <button type="submit" class="wo-btn wo-btn-dark">
                    <i class="bi bi-send"></i> Ajukan ke Tim WO untuk Kurasi
                </button>
            </div>
        </form>
    </div>

</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var cats = document.querySelectorAll('.ev-cat');
    var cards = document.querySelectorAll('.ev-card');
    var search = document.getElementById('evSearch');

    function applyFilters() {
        var activeCat = document.querySelector('.ev-cat.is-active').dataset.cat;
        var query = search.value.trim().toLowerCase();

        cards.forEach(function (card) {
            var matchesCat = activeCat === 'all' || card.dataset.cat === activeCat;
            var matchesQuery = card.dataset.name.indexOf(query) !== -1;
            card.style.display = (matchesCat && matchesQuery) ? '' : 'none';
        });
    }

    cats.forEach(function (cat) {
        cat.addEventListener('click', function () {
            cats.forEach(function (c) { c.classList.remove('is-active'); });
            cat.classList.add('is-active');
            applyFilters();
        });
    });

    search.addEventListener('input', applyFilters);
});
</script>
@endpush
