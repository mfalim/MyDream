<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('q'));
        $categoryId = $request->input('category_id');

        $vendors = Vendor::with(['category', 'photos'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->latest()
            ->get();

        $categories = Category::orderBy('name')->get();

        return view(
            'admin.vendors.index',
            compact('vendors', 'categories', 'search', 'categoryId')
        );
    }

    public function show(Vendor $vendor)
    {
        $vendor->load(['category', 'photos']);

        return view('admin.vendors.show', compact('vendor'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.vendors.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:vendors,name',
            'category_id' => 'required|integer|exists:categories,id',
            'price' => 'required|numeric|min:0|max:999999999',
            'phone' => 'required|string|regex:/^[0-9]{10,15}$/|max:15',
            'address' => 'required|string|max:1000',
            'description' => 'nullable|string|max:2000',

            'photos' => 'nullable|array|max:10',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'name.required' => 'Nama vendor harus diisi',
            'name.unique' => 'Nama vendor sudah digunakan',
            'name.max' => 'Nama vendor maksimal 255 karakter',
            'category_id.required' => 'Kategori harus dipilih',
            'category_id.exists' => 'Kategori tidak valid',
            'price.required' => 'Harga harus diisi',
            'price.min' => 'Harga tidak boleh negatif',
            'price.max' => 'Harga terlalu besar',
            'phone.required' => 'Nomor telepon harus diisi',
            'phone.regex' => 'Nomor telepon harus berupa angka 10-15 digit',
            'phone.max' => 'Nomor telepon maksimal 15 digit',
            'address.required' => 'Alamat harus diisi',
            'address.max' => 'Alamat maksimal 1000 karakter',
            'description.max' => 'Deskripsi maksimal 2000 karakter',
            'photos.max' => 'Maksimal 10 foto',
            'photos.*.image' => 'File harus berupa gambar',
            'photos.*.mimes' => 'Format gambar harus jpg, jpeg, png, atau webp',
            'photos.*.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        $vendor = Vendor::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'phone' => $request->phone,
            'address' => $request->address,
            'description' => $request->description,
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $path = $photo->store('vendors', 'public');

                $vendor->photos()->create([
                    'photo' => $path,
                    'is_cover' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()
            ->route('admin.vendors.index')
            ->with('success', 'Vendor berhasil ditambahkan.');
    }

    public function edit(Vendor $vendor)
    {
        $vendor->load('photos');

        $categories = Category::orderBy('name')->get();

        return view(
            'admin.vendors.edit',
            compact('vendor', 'categories')
        );
    }

    public function update(Request $request, Vendor $vendor)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:vendors,name,' . $vendor->id,
            'category_id' => 'required|integer|exists:categories,id',
            'price' => 'required|numeric|min:0|max:999999999',
            'phone' => 'required|string|regex:/^[0-9]{10,15}$/|max:15',
            'address' => 'required|string|max:1000',
            'description' => 'nullable|string|max:2000',

            'photos' => 'nullable|array|max:10',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',

            'delete_photos' => 'nullable|array',
            'delete_photos.*' => 'integer',

            'cover_photo_id' => 'nullable|integer',
        ], [
            'name.required' => 'Nama vendor harus diisi',
            'name.unique' => 'Nama vendor sudah digunakan',
            'name.max' => 'Nama vendor maksimal 255 karakter',
            'category_id.required' => 'Kategori harus dipilih',
            'category_id.exists' => 'Kategori tidak valid',
            'price.required' => 'Harga harus diisi',
            'price.min' => 'Harga tidak boleh negatif',
            'price.max' => 'Harga terlalu besar',
            'phone.required' => 'Nomor telepon harus diisi',
            'phone.regex' => 'Nomor telepon harus berupa angka 10-15 digit',
            'phone.max' => 'Nomor telepon maksimal 15 digit',
            'address.required' => 'Alamat harus diisi',
            'address.max' => 'Alamat maksimal 1000 karakter',
            'description.max' => 'Deskripsi maksimal 2000 karakter',
            'photos.max' => 'Maksimal 10 foto',
            'photos.*.image' => 'File harus berupa gambar',
            'photos.*.mimes' => 'Format gambar harus jpg, jpeg, png, atau webp',
            'photos.*.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        $vendor->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'phone' => $request->phone,
            'address' => $request->address,
            'description' => $request->description,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Hapus foto yang dipilih
        |--------------------------------------------------------------------------
        */

        if ($request->filled('delete_photos')) {
            $photos = $vendor->photos()
                ->whereIn('id', $request->delete_photos)
                ->get();

            foreach ($photos as $photo) {
                Storage::disk('public')->delete($photo->photo);
                $photo->delete();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Upload foto baru
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('photos')) {
            $lastSortOrder = $vendor->photos()->max('sort_order') ?? 0;

            foreach ($request->file('photos') as $index => $photo) {
                $path = $photo->store('vendors', 'public');

                $vendor->photos()->create([
                    'photo' => $path,
                    'is_cover' => false,
                    'sort_order' => $lastSortOrder + $index + 1,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Tentukan foto utama
        |--------------------------------------------------------------------------
        */

        if ($request->filled('cover_photo_id')) {

            $coverPhoto = $vendor->photos()
                ->where('id', $request->cover_photo_id)
                ->first();

            if ($coverPhoto) {
                $vendor->photos()->update([
                    'is_cover' => false,
                ]);

                $coverPhoto->update([
                    'is_cover' => true,
                ]);
            }
        } else {

            $hasCover = $vendor->photos()
                ->where('is_cover', true)
                ->exists();

            if (!$hasCover) {
                $firstPhoto = $vendor->photos()
                    ->orderBy('sort_order')
                    ->first();

                if ($firstPhoto) {
                    $firstPhoto->update([
                        'is_cover' => true,
                    ]);
                }
            }
        }

        return redirect()
            ->route('admin.vendors.show', $vendor)
            ->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->load('photos');

        foreach ($vendor->photos as $photo) {
            Storage::disk('public')->delete($photo->photo);
        }

        $vendor->delete();

        return redirect()
            ->route('admin.vendors.index')
            ->with('success', 'Vendor berhasil dihapus.');
    }
}
