<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\ProvidesCrudResponses;
use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    use ProvidesCrudResponses;

    public function index(Request $request)
    {
        return $this->paginated($request, Package::class, ['vendors.category', 'vendors.photos']);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $package = new Package();
        $package->fill($data);
        if (blank($package->slug)) {
            $base = Str::slug($package->name) ?: 'paket';
            $slug = $base;
            $suffix = 2;
            while (Package::where('slug', $slug)->exists()) {
                $slug = $base . '-' . $suffix++;
            }
            $package->slug = $slug;
        }
        $package->save();
        if (array_key_exists('vendors', $data)) {
            $package->vendors()->syncWithPivotValues($data['vendors'] ?? [], ['status' => 'active']);
        }
        $package->refresh();

        return response()->json(['data' => $package], 201);
    }

    public function show(int $id)
    {
        return response()->json([
            'data' => $this->findRecord(Package::class, $id, ['vendors.category', 'vendors.photos']),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $package = Package::findOrFail($id);
        $data = $this->validated($request, $package, true);
        $vendors = $data['vendors'] ?? null;
        unset($data['vendors']);
        $package->fill($data)->save();
        if ($vendors !== null) {
            $package->vendors()->syncWithPivotValues($vendors, ['status' => 'active']);
        }

        return response()->json(['data' => $package->refresh()]);
    }

    public function destroy(int $id)
    {
        return $this->deleteRecord(Package::class, $id);
    }

    private function validated(Request $request, ?Package $package = null, bool $partial = false): array
    {
        return $request->validate([
            'name' => [$partial ? 'sometimes' : 'required', 'string', 'max:255', 'unique:packages,name' . ($package ? ',' . $package->getKey() : '')],
            'description' => ['nullable', 'string'],
            'availability_date' => ['nullable', 'date'],
            'duration' => ['nullable', 'numeric', 'min:0.01', 'max:365'],
            'guest_capacity' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'event_period' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['sometimes', 'boolean'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'vendors' => ['sometimes', 'array'],
            'vendors.*' => ['integer', 'distinct', 'exists:vendors,id'],
        ]);
    }
}
