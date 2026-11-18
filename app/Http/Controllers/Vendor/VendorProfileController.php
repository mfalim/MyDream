<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VendorProfileController extends Controller
{
    /**
     * Menampilkan profil vendor.
     */
    public function index(Request $request)
    {
        $vendor = $request->user()->vendor()
            ->with(['category', 'photos'])
            ->firstOrFail();

        return view('vendor.profil', compact('vendor'));
    }

    /**
     * Menampilkan form edit profil.
     */
    public function edit(Request $request)
    {
        $vendor = $request->user()->vendor()
            ->with(['category', 'photos'])
            ->firstOrFail();
        $categories = Category::orderBy('name')->get();

        return view('vendor.edit', compact('vendor', 'categories'));
    }

    /**
     * Memperbarui profil vendor.
     */
    public function update(Request $request)
    {
        $vendor = $request->user()->vendor()->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'instagram' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'bank_name' => 'nullable|string|max:80',
            'bank_account_number' => 'nullable|string|max:60',
            'bank_account_holder' => 'nullable|string|max:120',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'photos.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $vendor->name = $validated['name'];
        $vendor->category_id = $validated['category_id'] ?? null;
        $vendor->phone = $validated['phone'] ?? null;
        $vendor->address = $validated['address'] ?? null;
        $vendor->city = $validated['city'] ?? null;
        $vendor->instagram = $validated['instagram'] ?? null;
        $vendor->description = $validated['description'] ?? null;
        $vendor->bank_name = $validated['bank_name'] ?? null;
        $vendor->bank_account_number = $validated['bank_account_number'] ?? null;
        $vendor->bank_account_holder = $validated['bank_account_holder'] ?? null;

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
