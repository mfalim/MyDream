{{-- Dipakai create & edit. Variabel: $article (null saat tambah), $packages, $categories --}}
<div class="mb-4">
    <label class="form-label fw-bold" style="color: #173f39; font-size: 13.5px;">Judul Artikel / Cerita <span class="text-danger">*</span></label>
    <input type="text" name="title" class="form-control form-control-lg" style="font-size: 15px; border-color: #d4dfda;" value="{{ old('title', $article->title ?? '') }}" placeholder="Contoh: 5 Tips Memilih Gaun Pengantin Sesuai Bentuk Tubuh" required>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <label class="form-label fw-bold" style="color: #173f39; font-size: 13.5px;">Kategori <span class="text-danger">*</span></label>
        <input type="text" name="category" class="form-control" list="article-categories" style="border-color: #d4dfda;" value="{{ old('category', $article->category ?? 'Tips & Rencana') }}" placeholder="Pilih atau ketik kategori baru" required>
        <datalist id="article-categories">
            @foreach ($categories as $category)
                <option value="{{ $category }}"></option>
            @endforeach
        </datalist>
        <small class="text-muted">Contoh: Real Wedding, Tips &amp; Rencana, Inspirasi Dekorasi, Gaun &amp; MUA.</small>
    </div>
    <div class="col-md-6 mb-4">
        <label class="form-label fw-bold" style="color: #173f39; font-size: 13.5px;">Penulis / Author</label>
        <div class="input-group">
            <span class="input-group-text bg-white" style="border-color: #d4dfda; color: #74847c;"><i class="bi bi-person"></i></span>
            <input type="text" name="author" class="form-control" style="border-color: #d4dfda;" value="{{ old('author', $article->author ?? (auth()->user()?->name ?? 'Tim MyDream')) }}" placeholder="mis. Tim MyDream">
        </div>
    </div>
</div>

<div class="mb-4">
    <label class="form-label fw-bold" style="color: #173f39; font-size: 13.5px;">Ringkasan / Excerpt <small class="text-muted fw-normal">(maks. 300 karakter)</small></label>
    <textarea name="excerpt" class="form-control" rows="2" maxlength="300" style="border-color: #d4dfda; font-size: 13.5px;" placeholder="Cuplikan singkat yang akan tampil di kartu artikel website...">{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
    <small class="text-muted">Jika dikosongkan, sistem akan otomatis mengambil kalimat pembuka dari isi artikel.</small>
</div>

<div class="mb-4">
    <label class="form-label fw-bold" style="color: #173f39; font-size: 13.5px;">Isi Artikel <span class="text-danger">*</span></label>
    <textarea name="content" class="form-control" rows="12" style="border-color: #d4dfda; font-size: 14px; line-height: 1.6;" placeholder="Tuliskan isi artikel lengkap di sini. Pisahkan antar paragraf dengan satu baris kosong (Enter dua kali)..." required>{{ old('content', $article->content ?? '') }}</textarea>
    <small class="text-muted">Gunakan baris baru (Enter ganda) untuk memisahkan paragraf agar rapi saat tayang di web.</small>
</div>

<div class="mb-4 p-3 rounded-3" style="background: #f7faf9; border: 1px dashed #cad9d3;">
    <label class="form-label fw-bold mb-2" style="color: #173f39; font-size: 13.5px;">Foto Sampul / Cover Editorial</label>
    @if (! empty($article?->photo))
        <div class="mb-3 d-flex align-items-center gap-3">
            <img src="{{ \App\Support\Media::url($article->photo) }}" alt="{{ $article->title }}" style="width: 140px; height: 95px; object-fit: cover; border-radius: 8px; border: 1px solid #d4dfda;">
            <div>
                <span class="badge bg-light text-dark border">Foto Saat Ini</span>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="remove_photo" value="1" id="remove_photo">
                    <label class="form-check-label text-danger small fw-semibold" for="remove_photo">Hapus foto ini</label>
                </div>
            </div>
        </div>
    @endif
    <input type="file" name="photo" class="form-control" accept=".jpg,.jpeg,.png,.webp" style="border-color: #d4dfda;">
    <small class="text-muted d-block mt-1">Maksimal 2 MB (Format: JPG, PNG, WEBP). Disarankan rasio landscape 16:9 atau 4:3.</small>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <label class="form-label fw-bold" style="color: #173f39; font-size: 13.5px;">Tanggal Tayang <small class="text-muted fw-normal">(kosong = langsung tayang)</small></label>
        <input type="date" name="published_at" class="form-control" style="border-color: #d4dfda;" value="{{ old('published_at', isset($article) ? $article->published_at?->toDateString() : now()->toDateString()) }}">
        <small class="text-muted">Bisa diisi tanggal mendatang untuk menjadwalkan publikasi otomatis.</small>
    </div>
    <div class="col-md-6 mb-4">
        <label class="form-label fw-bold" style="color: #173f39; font-size: 13.5px;">Tautkan ke Paket Wedding <small class="text-muted fw-normal">(opsional)</small></label>
        <select name="package_id" class="form-select" style="border-color: #d4dfda;">
            <option value="">-- Tanpa Tautan Paket (Ke Katalog Umum) --</option>
            @foreach ($packages as $package)
                <option value="{{ $package->id }}" @selected((string) old('package_id', $article->package_id ?? '') === (string) $package->id)>
                    {{ $package->name }} (Rp {{ number_format($package->price ?? 0, 0, ',', '.') }})
                </option>
            @endforeach
        </select>
        <small class="text-muted">Tombol Call-to-Action di akhir artikel akan langsung mengarahkan pembaca ke paket ini.</small>
    </div>
</div>

<div class="form-check form-switch p-3 rounded-3 mb-2" style="background: #f7faf9; border: 1px solid #e4ebe8;">
    <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" name="is_published" value="1" id="is_published" @checked(old('is_published', $article->is_published ?? true)) style="width: 2.2em; height: 1.2em; cursor: pointer;">
    <label class="form-check-label fw-semibold" for="is_published" style="color: #173f39; font-size: 13.5px; cursor: pointer;">
        Publikasikan di Halaman Website
        <small class="d-block text-muted fw-normal">Jika dinonaktifkan, artikel berstatus Draft dan tidak dapat diakses pembaca di halaman publik.</small>
    </label>
</div>