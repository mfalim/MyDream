<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with('vendors')->latest()->get();

        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        $vendors = Vendor::with('category')->orderBy('name')->get();

        return view('admin.packages.create', compact('vendors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:packages,name',
            'availability_date' => 'required|date',
            'duration' => 'required|numeric|min:0.01',
            'guest_capacity' => 'required|integer|min:1',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'vendors' => 'nullable|array',
            'vendors.*' => 'integer|distinct|exists:vendors,id',
        ]);

        $photo = null;

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->store('packages', 'public');
        }

        $package = Package::create([
            'name' => $request->name,
            'availability_date' => $request->availability_date,
            'duration' => $request->duration,
            'guest_capacity' => $request->guest_capacity,
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

    public function show(Package $package)
    {
        $package->load('vendors.category');

        return view('admin.packages.show', compact('package'));
    }

    public function edit(Package $package)
    {
        $package->load('vendors');
        $vendors = Vendor::with('category')->orderBy('name')->get();

        return view('admin.packages.edit', compact('package', 'vendors'));
    }

    public function update(Request $request, Package $package)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:packages,name,' . $package->id,
            'availability_date' => 'required|date',
            'duration' => 'required|numeric|min:0.01',
            'guest_capacity' => 'required|integer|min:1',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'vendors' => 'nullable|array',
            'vendors.*' => 'integer|distinct|exists:vendors,id',
        ]);

        $package->update([
            'name' => $request->name,
            'availability_date' => $request->availability_date,
            'duration' => $request->duration,
            'guest_capacity' => $request->guest_capacity,
        ]);

        if ($request->hasFile('photo')) {
            if ($package->photo) {
                Storage::disk('public')->delete($package->photo);
            }

            $package->update([
                'photo' => $request->file('photo')->store('packages', 'public'),
            ]);
        }

        $package->vendors()->syncWithPivotValues(
            $request->input('vendors', []),
            ['status' => 'active']
        );

        return redirect()
            ->route('admin.packages.show', $package)
            ->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy(Package $package)
    {
        if ($package->photo) {
            Storage::disk('public')->delete($package->photo);
        }

        $package->delete();

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Paket berhasil dihapus.');
    }
}
