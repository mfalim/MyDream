<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VendorProfileController extends Controller
{
    /**
     * Menampilkan profil vendor.
     */
    public function index()
    {
        $vendor = Vendor::with(['category', 'photos'])
            ->first();

        return view('vendor.profil', compact('vendor'));
    }

    /**
     * Menampilkan form edit profil.
     */
    public function edit()
    {
        $vendor = Vendor::with(['category', 'photos'])
            ->firstOrFail();

        return view('vendor.edit-profil', compact('vendor'));
    }

    /**
     * Memperbarui profil vendor.
     */
    public function update(Request $request)
    {
        $vendor = Vendor::firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'photos.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $vendor->name = $validated['name'];
        $vendor->category_id = $validated['category_id'] ?? null;
        $vendor->phone = $validated['phone'] ?? null;
        $vendor->address = $validated['address'] ?? null;
        $vendor->description = $validated['description'] ?? null;

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');

            $path = $file->store('vendors', 'public');

            $vendor->photos()->updateOrCreate(
                ['is_cover' => true],
                [
                    'photo' => $path,
                    'sort_order' => 0,
                ]
            );
        }

        $vendor->save();

        if ($request->hasFile('photos')) {
            $urutan = $vendor->photos()->max('sort_order') ?? 0;

            foreach ($request->file('photos') as $file) {
                $vendor->photos()->create([
                    'photo' => $file->store('vendors/gallery', 'public'),
                    'is_cover' => false,
                    'sort_order' => ++$urutan,
                ]);
            }
        }

        return redirect()
            ->route('vendor.profil')
            ->with('success', 'Profil vendor berhasil diperbarui.');
    }
}
