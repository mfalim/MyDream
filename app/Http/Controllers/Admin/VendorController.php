<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::with('category')->latest()->get();

        return view('admin.vendors.index', compact('vendors'));
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
}
