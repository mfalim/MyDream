<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('q'));
        $categoryId = $request->input('category_id');

        $vendors = Vendor::with('category')
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
        $vendor->load('category');

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
            'phone' => 'required|string|max:30',
            'address' => 'required|string|max:1000',
            'description' => 'nullable|string|max:2000',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $photo = null;

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->store('vendors', 'public');
        }

        Vendor::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'phone' => $request->phone,
            'address' => $request->address,
            'description' => $request->description,
            'photo' => $photo,
        ]);

        return redirect()
            ->route('admin.vendors.index')
            ->with('success', 'Vendor berhasil ditambahkan.');
    }

    public function edit(Vendor $vendor)
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.vendors.edit', compact('vendor', 'categories'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:vendors,name,' . $vendor->id,
            'category_id' => 'required|integer|exists:categories,id',
            'phone' => 'required|string|max:30',
            'address' => 'required|string|max:1000',
            'description' => 'nullable|string|max:2000',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'category_id' => $request->category_id,
            'phone' => $request->phone,
            'address' => $request->address,
            'description' => $request->description,
        ];

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')
                ->store('vendors', 'public');
        }

        $vendor->update($data);

        return redirect()
            ->route('admin.vendors.index')
            ->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        return redirect()
            ->route('admin.vendors.index')
            ->with('success', 'Vendor berhasil dihapus.');
    }
}
