{{-- Dipakai create & edit. Variabel: $article (null saat tambah), $packages, $categories --}}
<div class="mb-3">
    <label class="form-label">Judul</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $article->title ?? '') }}" required>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Kategori</label>
        <input type="text" name="category" class="form-control" list="article-categories" value="{{ old('category', $article->category ?? 'Real Wedding') }}" required>
        <datalist id="article-categories">
            @foreach ($categories as $category)
                <option value="{{ $category }}"></option>
            @endforeach
        </datalist>
        <small class="text-muted">Pilih dari saran atau ketik kategori baru.</small>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Penulis</label>
        <input type="text" name="author" class="form-control" value="{{ old('author', $article->author ?? auth()->user()?->name) }}" placeholder="mis. Tim MyDream">
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Ringkasan <small class="text-muted">(opsional, maks. 300 karakter)</small></label>
    <textarea name="excerpt" class="form-control" rows="2" maxlength="300">{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
    <small class="text-muted">Tampil di kartu artikel. Jika kosong, diambil dari awal isi artikel.</small>
</div>

<div class="mb-3">
    <label class="form-label">Isi Artikel</label>
    <textarea name="content" class="form-control" rows="14" required>{{ old('content', $article->content ?? '') }}</textarea>
    <small class="text-muted">Pisahkan paragraf dengan satu baris kosong.</small>
</div>

<div class="mb-3">
    <label class="form-label">Foto Sampul</label>
    @if (! empty($article?->photo))
        <div class="mb-2">
            <img src="{{ \App\Support\Media::url($article->photo) }}" alt="{{ $article->title }}" style="max-height: 140px; border-radius: 8px;">
        </div>
    @endif
    <input type="file" name="photo" class="form-control" accept=".jpg,.jpeg,.png,.webp">
    <small class="text-muted">Maksimal 2 MB. Format jpg, jpeg, png, atau webp.{{ ! empty($article?->photo) ? ' Unggah foto baru untuk mengganti.' : '' }}</small>
    @if (! empty($article?->photo))
        <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" name="remove_photo" value="1" id="remove_photo">
            <label class="form-check-label" for="remove_photo">Hapus foto saat ini</label>
        </div>
    @endif
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Tanggal Tayang <small class="text-muted">(kosong = langsung tayang)</small></label>
        <input type="date" name="published_at" class="form-control" value="{{ old('published_at', isset($article) ? $article->published_at?->toDateString() : now()->toDateString()) }}">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Paket yang Ditautkan <small class="text-muted">(opsional)</small></label>
        <select name="package_id" class="form-select">
            <option value="">Tanpa paket (tombol menuju katalog)</option>
            @foreach ($packages as $package)
                <option value="{{ $package->id }}" @selected((string) old('package_id', $article->package_id ?? '') === (string) $package->id)>{{ $package->name }}</option>
            @endforeach
        </select>
        <small class="text-muted">Dipakai untuk tombol "Lihat Paket" di akhir artikel.</small>
    </div>
</div>

<div class="form-check form-switch mb-4">
    <input class="form-check-input" type="checkbox" role="switch" name="is_published" value="1" id="is_published" @checked(old('is_published', $article->is_published ?? true))>
    <label class="form-check-label" for="is_published">Tampilkan di halaman publik</label>
</div>
