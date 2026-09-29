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
        $packages = Package::with('vendors.photos')->latest()->get();

        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        $vendors = Vendor::with(['category', 'photos'])->orderBy('name')->get();

        return view('admin.packages.create', compact('vendors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                "regex:/^[\pL\pN][\pL\pN\s&.'-]*$/u",
                'unique:packages,name',
            ],
            'availability_date' => 'required|date|after_or_equal:today',
            'guest_capacity' => 'required|integer|min:1|max:10000',
            'vendors' => 'nullable|array',
            'vendors.*' => 'integer|distinct|exists:vendors,id',
        ], [
            'name.required' => 'Nama paket harus diisi',
            'name.unique' => 'Nama paket sudah digunakan',
            'name.max' => 'Nama paket maksimal 255 karakter',
            'name.regex' => 'Nama paket hanya boleh berisi huruf, angka, spasi, dan tanda baca umum.',
            'availability_date.required' => 'Tanggal ketersediaan harus diisi',
            'availability_date.after_or_equal' => 'Tanggal ketersediaan tidak boleh kurang dari hari ini',
            'guest_capacity.required' => 'Kapasitas tamu harus diisi',
            'guest_capacity.min' => 'Kapasitas tamu minimal 1',
            'guest_capacity.max' => 'Kapasitas tamu maksimal 10000',
            'vendors.*.exists' => 'Vendor tidak valid',
        ]);

        $package = Package::create([
            'name' => $request->name,
            'availability_date' => $request->availability_date,
            'guest_capacity' => $request->guest_capacity,
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
        $package->load(['vendors.category', 'vendors.photos']);

        return view('admin.packages.show', compact('package'));
    }

    public function edit(Package $package)
    {
        $package->load('vendors.photos');
        $vendors = Vendor::with(['category', 'photos'])->orderBy('name')->get();

        return view('admin.packages.edit', compact('package', 'vendors'));
    }

    public function update(Request $request, Package $package)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                "regex:/^[\pL\pN][\pL\pN\s&.'-]*$/u",
                'unique:packages,name,' . $package->id,
            ],
            'availability_date' => 'required|date|after_or_equal:today',
            'guest_capacity' => 'required|integer|min:1|max:10000',
            'vendors' => 'nullable|array',
            'vendors.*' => 'integer|distinct|exists:vendors,id',
        ], [
            'name.required' => 'Nama paket harus diisi',
            'name.unique' => 'Nama paket sudah digunakan',
            'name.max' => 'Nama paket maksimal 255 karakter',
            'name.regex' => 'Nama paket hanya boleh berisi huruf, angka, spasi, dan tanda baca umum.',
            'availability_date.required' => 'Tanggal ketersediaan harus diisi',
            'availability_date.after_or_equal' => 'Tanggal ketersediaan tidak boleh kurang dari hari ini',
            'guest_capacity.required' => 'Kapasitas tamu harus diisi',
            'guest_capacity.min' => 'Kapasitas tamu minimal 1',
            'guest_capacity.max' => 'Kapasitas tamu maksimal 10000',
            'vendors.*.exists' => 'Vendor tidak valid',
        ]);

        $package->update([
            'name' => $request->name,
            'availability_date' => $request->availability_date,
            'guest_capacity' => $request->guest_capacity,
        ]);

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
        $package->delete();

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Paket berhasil dihapus.');
    }
}
