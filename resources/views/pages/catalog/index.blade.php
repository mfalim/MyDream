@extends('layouts.frontend')

@section('title', 'Katalog Paket Pernikahan — MyDream Organizer')

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Paket & Promo']]" />
@endsection

@section('content')

    @php
        $filterPromo = ['Semua Promo', 'Paket All-in-One', 'Hotel Bintang 5', 'Intimate Wedding', 'Adat Tradisional', 'Bali Destination Wedding', '⚡ Flash Sale Hari Ini'];

        $voucher = [
            ['image' => 'voucher/voucher-01.jpg', 'tag' => 'BALI • HONEYMOON TRIP', 'title' => 'Paket Honeymoon 4D3N AYANA Resort Bali', 'desc' => 'Menginap di Ocean View Suite, spa aromaterapi 90 menit pasangan.', 'before' => '27.500.000', 'now' => '16.500.000', 'save' => 'Hemat 11 Jt'],
            ['image' => 'voucher/voucher-02.jpg', 'tag' => 'JEMBER • FOTOGRAFI', 'title' => 'Voucher Pre-Wedding Studio & Outdoor', 'desc' => '1 Fotografer senior, makeup artist, teaser video cinematic 60 detik.', 'before' => '28.000.000', 'now' => '18.000.000', 'save' => 'Diskon 35%'],
            ['image' => 'voucher/voucher-03.jpg', 'tag' => 'NASIONAL • PERHIASAN', 'title' => 'Sepasang Cincin Kawin Rose Gold 18K', 'desc' => 'Sertifikat resmi, gratis grafir nama custom, free resizing seumur hidup.', 'before' => '22.000.000', 'now' => '14.200.000', 'save' => 'Hemat 7,8 Jt'],
            ['image' => 'voucher/voucher-04.jpg', 'tag' => 'JEMBER • PAKET LENGKAP', 'title' => 'All-in-One Intimate Wedding (150 Pax)', 'desc' => 'Venue, katering premium 150 pax, dekorasi tematik, WO 6 kru hari-H.', 'before' => '110.000.000', 'now' => '79.000.000', 'save' => 'Diskon 28%'],
        ];

        $paket = [
            ['slug' => 'oceanfront-wedding-ayana-bali', 'image' => 'package/package-01.jpg', 'discount' => 'Hemat Rp 25 Jt', 'best_seller' => true, 'location' => 'Bali • Venue Pernikahan', 'rating' => '4.9', 'reviews' => 98, 'title' => 'Oceanfront Wedding Ceremony & Dinner Package (200 Pax)', 'vendor' => 'AYANA Resort & Spa Bali', 'features' => ['Eksklusif venue outdoor lawn 6 jam', '5-course International Buffet Dinner 200 pax', 'Menginap 2 malam Bridal Ocean Suite'], 'before' => '185.000.000', 'now' => '160.000.000', 'cicilan' => '6,6 Jt'],
            ['slug' => 'bespoke-gaun-hian-tjen', 'image' => 'package/package-11.jpg', 'discount' => 'Hemat Rp 15 Jt', 'best_seller' => false, 'location' => 'Jember • Busana Pengantin', 'rating' => '5.0', 'reviews' => 64, 'title' => 'Bespoke Bridal Gown Rental & Custom Fitting', 'vendor' => 'Atelier Hian Tjen', 'features' => ['Sewa gaun pengantin pemberkatan/resepsi', 'Custom long cathedral veil + hair piece', '3x sesi custom alteration privat'], 'before' => '65.000.000', 'now' => '50.000.000', 'cicilan' => '2,0 Jt'],
            ['slug' => 'full-day-wo-sehati', 'image' => 'package/package-07.jpg', 'discount' => 'Diskon 25%', 'best_seller' => false, 'location' => 'Jember • Wedding Organizer', 'rating' => '4.95', 'reviews' => 140, 'title' => 'Full Day Wedding Planning & Day of Coordination', 'vendor' => 'Sehati Wedding Planner', 'features' => ['Konsultasi konsep, rundown & budgeting H-6 bulan', '10 kru profesional berseragam rapi di hari-H', 'Personal assistant pengantin & kedua orang tua'], 'before' => '32.000.000', 'now' => '24.000.000', 'cicilan' => '1,0 Jt'],
            ['slug' => 'floral-stage-suryanto', 'image' => 'package/package-08.jpg', 'discount' => 'Hemat Rp 20 Jt', 'best_seller' => false, 'location' => 'Jember • Dekorasi & Lighting', 'rating' => '4.98', 'reviews' => 86, 'title' => 'Enchanted Floral Grand Stage & Ambient Lighting', 'vendor' => 'Suryanto Decor', 'features' => ['Pelaminan utama lebar hingga 18 meter fresh flowers', 'Entrance gate megah + gazebo lorong karpet', 'Lighting ambience panggung & chandelier kristal'], 'before' => '95.000.000', 'now' => '75.000.000', 'cicilan' => '3,1 Jt'],
            ['slug' => 'royal-makeup-anpa-suha', 'image' => 'package/package-05.jpg', 'discount' => 'Voucher Eksklusif', 'best_seller' => false, 'location' => 'Jember & Bali • MUA', 'rating' => '4.99', 'reviews' => 210, 'title' => 'Royal Flawless Makeup & Hair Styling', 'vendor' => 'Anpa Suha Makeup Master', 'features' => ['Rias pengantin akad/pemberkatan & resepsi (touch up)', 'Termasuk makeup & hairdo 2 ibu pengantin', 'Premium false eyelashes & kit branded internasional'], 'before' => '28.000.000', 'now' => '19.500.000', 'cicilan' => '812 Rb'],
            ['slug' => 'heritage-buffet-umara', 'image' => 'package/package-09.jpg', 'discount' => 'Hemat Rp 18 Jt', 'best_seller' => false, 'location' => 'Jember • Katering', 'rating' => '4.92', 'reviews' => 175, 'title' => 'Nusantara Heritage Grand Buffet & 6 Live Food Stalls', 'vendor' => 'Umara Catering Gourmet', 'features' => ['Prasmanan utama 400 porsi + 6 gubukan pilihan', 'Family VIP plated service 20 pax', 'Gratis food tasting untuk 6 orang keluarga'], 'before' => '118.000.000', 'now' => '100.000.000', 'cicilan' => '4,1 Jt'],
            ['slug' => 'acoustic-band-david', 'image' => 'package/package-10.jpg', 'discount' => 'Diskon 20%', 'best_seller' => false, 'location' => 'Jember • Entertainment', 'rating' => '4.90', 'reviews' => 89, 'title' => 'Romantic Chamber Acoustic Band + MC Profesional', 'vendor' => 'David Entertainment Group', 'features' => ['7 piece format lengkap dengan 2 vokalis', '1 MC bilingual profesional', 'Sound system 5000 watt untuk ballroom'], 'before' => '26.000.000', 'now' => '20.800.000', 'cicilan' => '866 Rb'],
            ['slug' => 'invitation-papier-co', 'image' => 'package/package-12.jpg', 'discount' => 'Hemat Rp 4 Jt', 'best_seller' => false, 'location' => 'Jember & Nasional • Undangan', 'rating' => '4.88', 'reviews' => 95, 'title' => 'Bespoke Foil Pressed Invitations & Souvenir', 'vendor' => 'The Papier Co.', 'features' => ['300 set undangan hardcover + foil gold + wax seal', '300 pcs souvenir scented candle custom box', 'Desain undangan digital web interaktif gratis'], 'before' => '19.000.000', 'now' => '15.000.000', 'cicilan' => '625 Rb'],
            ['slug' => 'destination-prewed-moreno', 'image' => 'package/package-06.jpg', 'discount' => 'Hemat Rp 12 Jt', 'best_seller' => true, 'location' => 'Bali & Internasional • Foto Prewed', 'rating' => '4.96', 'reviews' => 112, 'title' => 'Destination Pre-Wedding Trip (2 Hari Sesi)', 'vendor' => 'Moreno & Co Photography', 'features' => ['2 hari sesi pemotretan penuh di 4 lokasi eksotis', 'Semua file hi-res + 60 edited photos + teaser video', 'Tiket & akomodasi tim foto sudah ditanggung'], 'before' => '48.000.000', 'now' => '36.000.000', 'cicilan' => '1,5 Jt'],
        ];
    @endphp

    {{-- ================= PROMO BANNER ================= --}}
    <section class="section-sm">
        <div class="container">
            <div class="promo-banner">
                <div class="flex justify-between flex-wrap gap-2" style="align-items:flex-start;">
                    <div style="max-width:520px;">
                        <span class="badge badge-paper mb-2">Edisi Khusus MyDream Fair 2026</span>
                        <h2 class="h2" style="color:#FBEFE9;">MyDream Fair Deals: Hemat Hingga 35%</h2>
                        <p class="mt-1" style="color:#F3D9CE;font-size:.88rem;">
                            Dapatkan proteksi pembayaran terjamin, diskon langsung vendor terpilih,
                            dan fasilitas cicilan 0% hingga 24 bulan dengan MyDream Pay.
                        </p>
                    </div>
                    <div class="flex" style="gap:10px;flex-direction:column;align-items:flex-end;">
                        <span class="badge badge-paper">🛡 100% Escrow Protection</span>
                        <a href="#paket" class="btn" style="background:#fff;color:var(--maroon);">Klaim Voucher Pameran &rarr;</a>
                    </div>
                </div>
                <div class="filter-pills mt-3">
                    @foreach ($filterPromo as $i => $f)
                        <button type="button" class="filter-pill accent {{ $i === count($filterPromo)-1 ? 'active' : '' }}" style="{{ $i === count($filterPromo)-1 ? '' : 'background:rgba(255,255,255,.14);border-color:rgba(255,255,255,.3);color:#fff;' }}">{{ $f }}</button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ================= FLASH SALE ================= --}}
    <section class="section-sm">
        <div class="container">
            <div class="alert-strip mb-3">
                <div>
                    <h3 style="font-size:1.05rem;">Flash Sale Pernikahan</h3>
                    <p class="small muted">Kuota penawaran sangat terbatas, dikonfirmasi langsung oleh vendor terpilih.</p>
                </div>
                <div class="countdown-box">
                    <span class="tiny" style="text-transform:uppercase;font-weight:700;color:var(--maroon);">Berakhir Dalam</span>
                    <div class="countdown-clock" data-countdown="52361">
                        <span class="seg" data-seg="h">14</span><span class="colon">:</span>
                        <span class="seg" data-seg="m">32</span><span class="colon">:</span>
                        <span class="seg" data-seg="s">41</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-4">
                @foreach ($voucher as $v)
                    <div class="card">
                        <div class="card-media" style="aspect-ratio:4/3.4;">
                            <img src="{{ asset('images/'.$v['image']) }}" alt="{{ $v['title'] }}">
                            <div class="badges-top"><span class="badge badge-dark">{{ $v['save'] }}</span></div>
                        </div>
                        <div class="card-body">
                            <p class="tiny faint" style="text-transform:uppercase;">{{ $v['tag'] }}</p>
                            <h3 class="card-title" style="font-size:1rem;">{{ $v['title'] }}</h3>
                            <p class="small muted mt-1">{{ $v['desc'] }}</p>
                            <div class="card-price"><p class="before">Rp {{ $v['before'] }}</p><p class="now">Rp {{ $v['now'] }}</p></div>
                            <a href="#paket" class="btn btn-outline btn-block btn-sm mt-2">Beli Voucher</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= FILTER + GRID PAKET ================= --}}
    <section id="paket" class="section">
        <div class="container">
            <p class="eyebrow">Katalog Penawaran Resmi</p>
            <h2 class="h2 mt-2">Paket Pilihan Terkurasi</h2>
            <p class="lede mt-1">Temukan paket hemat dari vendor bintang lima dengan proteksi uang kembali.</p>

            <div style="display:grid;grid-template-columns:260px 1fr;gap:32px;margin-top:32px;" class="paket-layout">
                <aside>
                    <div class="filter-box">
                        <h4>Destinasi / Kota</h4>
                        <label class="opt"><input type="checkbox" checked> Jember & Sekitarnya</label>
                        <label class="opt"><input type="checkbox"> Surabaya & Jawa Timur</label>
                        <label class="opt"><input type="checkbox"> Bali & Destinasi</label>
                    </div>
                    <div class="filter-box">
                        <h4>Rentang Budget Pernikahan</h4>
                        <label class="opt"><input type="radio" name="budget"> Di bawah Rp 25 Juta</label>
                        <label class="opt"><input type="radio" name="budget"> Rp 25 - 50 Juta</label>
                        <label class="opt"><input type="radio" name="budget" checked> Rp 50 - 100 Juta</label>
                        <label class="opt"><input type="radio" name="budget"> Di atas Rp 100 Juta</label>
                    </div>
                    <div class="filter-box">
                        <h4>Kapasitas Tamu (Pax)</h4>
                        <div class="radio-pill-group">
                            <label class="radio-pill"><input type="radio" name="pax"> 50-100</label>
                            <label class="radio-pill"><input type="radio" name="pax" checked> 100-300</label>
                            <label class="radio-pill"><input type="radio" name="pax"> 300-500</label>
                            <label class="radio-pill"><input type="radio" name="pax"> 500+</label>
                        </div>
                    </div>
                    <div class="filter-box">
                        <h4>Fasilitas & Jaminan</h4>
                        <label class="opt"><input type="checkbox" checked> MyDream Pay Protected</label>
                        <label class="opt"><input type="checkbox" checked> Cicilan 0% s/d 24 Bulan</label>
                        <label class="opt"><input type="checkbox"> Instant Confirmation</label>
                        <label class="opt"><input type="checkbox"> Verified Pro Merchant</label>
                        <label class="opt"><input type="checkbox"> Rating 4.8 ke Atas</label>
                    </div>
                    <div class="filter-box" style="background:var(--forest-soft);border-color:var(--forest-line);">
                        <h4 style="color:var(--forest);">✓ Checklist Planner Gratis</h4>
                        <p class="small" style="color:var(--forest);">Kelola jadwal pembayaran, timeline hari-H, dan alokasi vendor dalam satu dashboard interaktif.</p>
                        <a href="#" class="link-arrow mt-1" style="display:inline-block;">Buka Wedding Tools &rarr;</a>
                    </div>
                </aside>

                <div>
                    <div class="flex justify-between mb-2" style="align-items:center;flex-wrap:wrap;gap:10px;">
                        <p class="small muted">Menampilkan {{ count($paket) }} dari 186 penawaran paket aktif</p>
                        <select style="border:1px solid var(--line);border-radius:var(--radius-pill);padding:9px 16px;font-size:.8rem;">
                            <option>Urutkan: Diskon Terbesar</option>
                            <option>Harga Terendah</option>
                            <option>Rating Tertinggi</option>
                        </select>
                    </div>

                    <div class="grid grid-2">
                        @foreach ($paket as $item)
                            <x-card-vendor variant="paket" :href="route('catalog.show', $item['slug']) ?? '#'" :image="asset('images/'.$item['image'])"
                                :discount-badge="$item['discount']" :best-seller="$item['best_seller']"
                                :location="$item['location']" :rating="$item['rating']" :reviews="$item['reviews']"
                                :title="$item['title']" :vendor-name="$item['vendor']" :features="$item['features']"
                                :price="$item['now']" :price-before="$item['before']" :cicilan="$item['cicilan']" />
                        @endforeach
                    </div>

                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-outline">Tampilkan Lebih Banyak Paket &darr;</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= KENAPA MEMILIH KAMI ================= --}}
    <section class="section section-cream">
        <div class="container text-center">
            <p class="eyebrow" style="justify-content:center;">Ketenangan Pikiran Pasangan Pengantin</p>
            <h2 class="h2 mt-2">Kenapa Memesan Lewat MyDream?</h2>
            <p class="lede mt-1" style="margin-left:auto;margin-right:auto;">
                Kami mendampingi setiap langkah perencanaan pernikahanmu dengan standar keamanan dan kurasi mutu tertinggi.
            </p>

            <div class="grid grid-4 mt-4">
                <div class="icon-feature">
                    <div class="icon-circle"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 2.25l7.5 3.375v6.375c0 5.06-3.2 8.906-7.5 9.75-4.3-.844-7.5-4.69-7.5-9.75V5.625L12 2.25z"/></svg></div>
                    <h4>Garansi Harga Terbaik</h4>
                    <p>Penawaran resmi dengan garansi harga termurah dari vendor terdaftar.</p>
                </div>
                <div class="icon-feature">
                    <div class="icon-circle"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M2.25 8.25h19.5M2.25 9h19.5M3.75 4.5h16.5a1.5 1.5 0 011.5 1.5v12a1.5 1.5 0 01-1.5 1.5H3.75a1.5 1.5 0 01-1.5-1.5V6a1.5 1.5 0 011.5-1.5z"/></svg></div>
                    <h4>Escrow MyDream Pay</h4>
                    <p>Pembayaran disimpan di rekening bersama, diteruskan sesuai tahapan kontrak.</p>
                </div>
                <div class="icon-feature">
                    <div class="icon-circle"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.625c.621 0 1.125.504 1.125 1.125V6m-19.5 0v6.75"/></svg></div>
                    <h4>Cicilan 0% Multi-Bank</h4>
                    <p>Kelola cash flow lebih tenang bersama 15+ bank rekanan hingga 24 bulan.</p>
                </div>
                <div class="icon-feature">
                    <div class="icon-circle"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM21 12c0 4.556-4.03 8.25-9 8.25a9.76 9.76 0 01-2.555-.337A5.97 5.97 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/></svg></div>
                    <h4>Wedding Expert Gratis</h4>
                    <p>Sesi konsultasi langsung untuk mencocokkan gaya, preferensi, dan budget.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= TESTIMONIAL ================= --}}
    <section class="section section-white">
        <div class="container">
            <p class="eyebrow">Kisah Nyata Pasangan MyDream</p>
            <h2 class="h2 mt-2">Ulasan & Pengalaman Bahagia</h2>
            <div class="grid grid-3 mt-4">
                <div class="testimonial">
                    <p class="stars">★★★★★</p>
                    <p class="quote">Memesan paket ballroom lewat MyDream Store sangat menghemat waktu kami. Ditambah proteksi MyDream Pay, kami sama sekali tidak khawatir soal pembayaran!</p>
                    <p class="who"><strong>Kevin & Amanda</strong><br>Menikah di Jember • Nov 2025</p>
                </div>
                <div class="testimonial">
                    <p class="stars">★★★★★</p>
                    <p class="quote">Flash Deal fotografi kemarin benar-benar penyelamat budget kami! Dapat potongan besar untuk hasil foto yang memukau tanpa biaya tersembunyi.</p>
                    <p class="who"><strong>Dimas & Winona</strong><br>Prewedding di Bali • Jan 2026</p>
                </div>
                <div class="testimonial">
                    <p class="stars">★★★★★</p>
                    <p class="quote">Fitur cicilan 0% hingga 12 bulan sangat membantu kami membagi biaya dekorasi dan gaun tanpa pusing menguras tabungan hari-H.</p>
                    <p class="who"><strong>Rangga & Stefanie</strong><br>Ballroom Jember • Feb 2026</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= FAQ ================= --}}
    <section class="section section-cream">
        <div class="container" style="max-width:760px;">
            <p class="eyebrow" style="justify-content:center;">Pertanyaan yang Sering Diajukan</p>
            <h2 class="h2 mt-2 text-center">Panduan Berbelanja di MyDream Store</h2>

            <div class="mt-4">
                <details class="accordion-item" open>
                    <summary>Bagaimana cara menggunakan voucher setelah transaksi berhasil? <span class="plus">+</span></summary>
                    <div class="accordion-body">Voucher otomatis tersimpan di akun kamu dan bisa langsung dipakai saat checkout paket berikutnya, atau ditukarkan langsung ke vendor terkait sesuai instruksi di e-tiket.</div>
                </details>
                <details class="accordion-item">
                    <summary>Apakah tanggal pernikahan saya bisa diubah setelah membeli paket? <span class="plus">+</span></summary>
                    <div class="accordion-body">Bisa. MyDream memberikan kebijakan reschedule fleksibel hingga H-60 sebelum tanggal acara, mengikuti ketersediaan jadwal vendor terkait.</div>
                </details>
                <details class="accordion-item">
                    <summary>Bagaimana mekanisme Cicilan 0% MyDream Pay? <span class="plus">+</span></summary>
                    <div class="accordion-body">Kamu bisa memilih tenor 3, 6, 12, hingga 24 bulan lewat kartu kredit bank rekanan tanpa bunga tambahan, dipilih saat proses pembayaran.</div>
                </details>
                <details class="accordion-item">
                    <summary>Apakah ada jaminan uang kembali jika terjadi hal di luar kendali? <span class="plus">+</span></summary>
                    <div class="accordion-body">Dana kamu disimpan aman lewat sistem escrow dan baru diteruskan ke vendor sesuai tahapan kontrak, sehingga terlindungi bila terjadi pembatalan sepihak dari vendor.</div>
                </details>
            </div>
        </div>
    </section>

@endsection

@push('styles')
<style>
    @media (max-width: 860px) { .paket-layout { grid-template-columns: 1fr !important; } }
</style>
@endpush
