<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::latest()->get();

        return view('admin.vendors.index', compact('vendors'));
    }

    public function create()
    {
        return view('admin.vendors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'description' => 'nullable',
            'photo' => 'nullable|image|max:2048',
        ]);

        $photo = null;

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->store('vendors', 'public');
        }

        Vendor::create([
            'name' => $request->name,
            'category' => $request->category,
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
