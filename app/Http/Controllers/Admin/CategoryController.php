<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $allCategories = Category::withCount('vendors')->latest('id')->get();
        $totalCategories = $allCategories->count();
        $usedCategoriesCount = $allCategories->where('vendors_count', '>', 0)->count();
        $totalLinkedVendors = (int) $allCategories->sum('vendors_count');

        $categories = $search === ''
            ? $allCategories
            : $allCategories->filter(fn (Category $cat) => str_contains(mb_strtolower($cat->name), mb_strtolower($search)))->values();

        return view('admin.categories.index', compact(
            'categories',
            'search',
            'totalCategories',
            'usedCategoriesCount',
            'totalLinkedVendors'
        ));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'image.image' => 'File gambar kategori harus berupa gambar.',
            'image.mimes' => 'Format gambar harus JPG, PNG, atau WEBP.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        Category::create([
            'name' => $validated['name'],
            'image' => $imagePath,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'image.image' => 'File gambar kategori harus berupa gambar.',
            'image.mimes' => 'Format gambar harus JPG, PNG, atau WEBP.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
        ]);

        $data = [
            'name' => $validated['name'],
        ];

        if ($request->hasFile('image')) {
            $this->deleteUploadedImage($category->image);
            $data['image'] = $request->file('image')->store('categories', 'public');
        } elseif ($request->boolean('remove_image')) {
            $this->deleteUploadedImage($category->image);
            $data['image'] = null;
        }

        $category->update($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        $this->deleteUploadedImage($category->image);
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus. Vendor terkait kini tanpa kategori.');
    }

    /** Hapus hanya file upload di storage/app/public/categories agar aset bawaan public/images tetap aman. */
    private function deleteUploadedImage(?string $path): void
    {
        if ($path && Str::startsWith($path, 'categories/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
