<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

/** Admin: kelola artikel & cerita pernikahan yang tampil di /blog. */
class ArticleController extends Controller
{
    private const DEFAULT_CATEGORIES = ['Real Wedding', 'Tips & Rencana', 'Vendor Stories', 'Inspirasi'];

    private function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'excerpt' => 'nullable|string|max:300',
            'content' => 'required|string|min:20|max:30000',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'author' => 'nullable|string|max:100',
            'published_at' => 'nullable|date',
            'package_id' => 'nullable|integer|exists:packages,id',
        ];
    }

    private function messages(): array
    {
        return [
            'title.required' => 'Judul harus diisi',
            'title.max' => 'Judul maksimal 255 karakter',
            'category.required' => 'Kategori harus diisi',
            'excerpt.max' => 'Ringkasan maksimal 300 karakter',
            'content.required' => 'Isi artikel harus diisi',
            'content.min' => 'Isi artikel minimal 20 karakter',
            'photo.image' => 'File harus berupa gambar',
            'photo.mimes' => 'Format gambar harus jpg, jpeg, png, atau webp',
            'photo.max' => 'Ukuran gambar maksimal 2MB',
            'published_at.date' => 'Tanggal tayang tidak valid',
            'package_id.exists' => 'Paket tidak valid',
        ];
    }

    private function formData(): array
    {
        return [
            'packages' => Package::orderBy('name')->get(),
            'categories' => collect(self::DEFAULT_CATEGORIES)
                ->merge(Article::distinct()->pluck('category'))->unique()->values(),
        ];
    }

    public function index(): View
    {
        return view('admin.articles.index', [
            'articles' => Article::with('package')->latestFirst()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.articles.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate($this->rules(), $this->messages());

        $data = $request->only(['title', 'category', 'excerpt', 'content', 'author', 'published_at', 'package_id']);
        $data['is_published'] = $request->boolean('is_published');
        $data['author'] = $data['author'] ?: $request->user()?->name;

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('articles', 'public');
        }

        Article::create($data);

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil ditambahkan.');
    }

    public function edit(Article $article): View
    {
        return view('admin.articles.edit', ['article' => $article] + $this->formData());
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $request->validate($this->rules(), $this->messages());

        $data = $request->only(['title', 'category', 'excerpt', 'content', 'author', 'published_at', 'package_id']);
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('photo')) {
            $this->deletePhoto($article);
            $data['photo'] = $request->file('photo')->store('articles', 'public');
        } elseif ($request->boolean('remove_photo')) {
            $this->deletePhoto($article);
            $data['photo'] = null;
        }

        $article->update($data);

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $this->deletePhoto($article);
        $article->delete();

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil dihapus.');
    }

    /** Hapus file foto hanya jika berupa file unggahan (bukan URL luar). */
    private function deletePhoto(Article $article): void
    {
        if ($article->photo && ! Str::startsWith($article->photo, ['http://', 'https://'])) {
            Storage::disk('public')->delete($article->photo);
        }
    }
}
