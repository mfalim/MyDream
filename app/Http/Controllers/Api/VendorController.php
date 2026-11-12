<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        return response()->json([
            'message' => 'Data vendor berhasil diambil.',
            'data' => $vendors,
        ]);
    }

    public function show(Vendor $vendor)
    {
        $vendor->load(['category', 'photos']);

        return response()->json([
            'message' => 'Detail vendor berhasil diambil.',
            'data' => $vendor,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                "regex:/^[\pL\pN][\pL\pN\s&.'-]*$/u",
                'unique:vendors,name',
            ],
            'category_id' => 'required|integer|exists:categories,id',
            'price' => 'required|numeric|min:0|max:999999999',
            'phone' => 'required|string|regex:/^[0-9]{10,15}$/|max:15',
            'address' => 'required|string|max:1000',
            'description' => 'nullable|string|max:2000',
            'photos' => 'nullable|array|max:10',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $count = 1;

        while (Vendor::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $vendor = Vendor::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'category_id' => $validated['category_id'],
            'price' => $validated['price'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'description' => $validated['description'] ?? null,
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

        $vendor->load(['category', 'photos']);

        return response()->json([
            'message' => 'Vendor berhasil ditambahkan.',
            'data' => $vendor,
        ], 201);
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                "regex:/^[\pL\pN][\pL\pN\s&.'-]*$/u",
                'unique:vendors,name,' . $vendor->id,
            ],
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
        ]);

        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $count = 1;

        while (
            Vendor::where('slug', $slug)
            ->where('id', '!=', $vendor->id)
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $count++;
        }

        $vendor->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'category_id' => $validated['category_id'],
            'price' => $validated['price'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'description' => $validated['description'] ?? null,
        ]);

        if (!empty($validated['delete_photos'])) {
            $photos = $vendor->photos()
                ->whereIn('id', $validated['delete_photos'])
                ->get();

            foreach ($photos as $photo) {
                Storage::disk('public')->delete($photo->photo);
                $photo->delete();
            }
        }

        if ($request->hasFile('photos')) {
            $lastSortOrder = $vendor->photos()->max('sort_order') ?? -1;

            foreach ($request->file('photos') as $index => $photo) {
                $path = $photo->store('vendors', 'public');

                $vendor->photos()->create([
                    'photo' => $path,
                    'is_cover' => false,
                    'sort_order' => $lastSortOrder + $index + 1,
                ]);
            }
        }

        if (isset($validated['cover_photo_id'])) {
            $coverPhoto = $vendor->photos()
                ->where('id', $validated['cover_photo_id'])
                ->first();

            if (!$coverPhoto) {
                return response()->json([
                    'message' => 'Foto utama tidak ditemukan pada vendor ini.',
                ], 422);
            }

            $vendor->photos()->update(['is_cover' => false]);
            $coverPhoto->update(['is_cover' => true]);
        } else {
            $hasCover = $vendor->photos()
                ->where('is_cover', true)
                ->exists();

            if (!$hasCover) {
                $firstPhoto = $vendor->photos()
                    ->orderBy('sort_order')
                    ->first();

                if ($firstPhoto) {
                    $firstPhoto->update(['is_cover' => true]);
                }
            }
        }

        $vendor->load(['category', 'photos']);

        return response()->json([
            'message' => 'Vendor berhasil diperbarui.',
            'data' => $vendor,
        ]);
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->load('photos');

        foreach ($vendor->photos as $photo) {
            Storage::disk('public')->delete($photo->photo);
        }

        $vendor->delete();

        return response()->json([
            'message' => 'Vendor berhasil dihapus.',
        ]);
    }
}
