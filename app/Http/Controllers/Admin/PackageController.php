<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Vendor;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with('vendors')->latest()->get();

        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        $vendors = Vendor::all();

        return view('admin.packages.create', compact('vendors'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'photo' => 'nullable|image|max:2048',
            'vendors' => 'nullable|array',
            'vendors.*' => 'exists:vendors,id',
        ]);

        $photo = null;

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->store('packages', 'public');
        }

        $package = Package::create([
            'name' => $request->name,
            'price' => $request->price,
            'photo' => $photo,
        ]);

        if ($request->vendors) {
            $package->vendors()->attach(
                $request->vendors,
                ['status' => 'active']
            );
        }

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Paket berhasil ditambahkan.');
    }
}
