<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('q'));
        $categoryId = $request->input('category_id');

        $vendors = Vendor::with(['category', 'photos', 'user'])
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
        $vendor->load(['category', 'photos', 'user']);

        return view('admin.vendors.show', compact('vendor'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $availableVendorUsers = User::query()
            ->where('role', 'vendor')
            ->whereDoesntHave('vendor')
            ->orderBy('name')
            ->get();

        return view('admin.vendors.create', compact('categories', 'availableVendorUsers'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'username' => $request->filled('username')
                ? Str::lower(trim((string) $request->input('username')))
                : null,
            'email' => $request->filled('email')
                ? Str::lower(trim((string) $request->input('email')))
                : null,
        ]);
        $category = Category::find($request->input('category_id'));

        $existingAccount = $request->filled('user_id')
            ? User::query()
                ->where('role', 'vendor')
                ->whereDoesntHave('vendor')
                ->find($request->input('user_id'))
            : null;

        $data = $request->validate([
            'user_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where('role', 'vendor'),
                Rule::unique('vendors', 'user_id'),
            ],
            'username' => [
                Rule::requiredIf(! $existingAccount || ! $existingAccount->username),
                'nullable',
                'string',
                'min:3',
                'max:80',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($existingAccount?->id),
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($existingAccount?->id),
            ],
            'password' => [
                Rule::requiredIf(! $existingAccount || ! $existingAccount->password),
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                "regex:/^[\pL\pN][\pL\pN\s&.'-]*$/u",
                'unique:vendors,name',
            ],
            'category_id' => 'required|integer|exists:categories,id',
            'price' => 'required|numeric|min:0|max:999999999',
            'guest_capacity' => [
                Rule::requiredIf($category?->isCatering()),
                'nullable',
                'integer',
                'min:1',
                'max:1000000',
            ],
            'phone' => 'required|string|regex:/^[0-9]{10,15}$/|max:15',
            'bank_name' => 'nullable|string|max:80',
            'bank_account_number' => 'nullable|string|max:60',
            'bank_account_holder' => 'nullable|string|max:120',
            'address' => 'required|string|max:1000',
            'description' => 'nullable|string|max:2000',
            'city' => 'nullable|string|max:100',
            'instagram' => 'nullable|string|max:100',

            'photos' => 'nullable|array|max:10',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'name.required' => 'Nama vendor harus diisi',
            'name.unique' => 'Nama vendor sudah digunakan',
            'name.max' => 'Nama vendor maksimal 255 karakter',
            'name.regex' => 'Nama vendor hanya boleh berisi huruf, angka, spasi, dan tanda baca umum.',
            'category_id.required' => 'Kategori harus dipilih',
            'category_id.exists' => 'Kategori tidak valid',
            'price.required' => 'Harga harus diisi',
            'price.min' => 'Harga tidak boleh negatif',
            'price.max' => 'Harga terlalu besar',
            'guest_capacity.required' => 'Kapasitas tamu wajib diisi untuk vendor catering.',
            'guest_capacity.integer' => 'Kapasitas tamu harus berupa angka bulat.',
            'guest_capacity.min' => 'Kapasitas tamu minimal 1 orang.',
            'guest_capacity.max' => 'Kapasitas tamu maksimal 1.000.000 orang.',
            'phone.required' => 'Nomor telepon harus diisi',
            'phone.regex' => 'Nomor telepon harus berupa angka 10-15 digit',
            'phone.max' => 'Nomor telepon maksimal 15 digit',
            'address.required' => 'Alamat harus diisi',
            'address.max' => 'Alamat maksimal 1000 karakter',
            'description.max' => 'Deskripsi maksimal 2000 karakter',
            'photos.max' => 'Maksimal 10 foto',
            'photos.*.image' => 'File harus berupa gambar',
            'photos.*.mimes' => 'Format gambar harus jpg, jpeg, png, atau webp',
            'photos.*.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        $slug = Str::slug($data['name']);

        $originalSlug = $slug;
        $count = 1;

        while (Vendor::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $vendor = DB::transaction(function () use ($data, $slug, $existingAccount, $category) {
            if ($existingAccount) {
                $user = User::query()
                    ->where('role', 'vendor')
                    ->whereDoesntHave('vendor')
                    ->lockForUpdate()
                    ->findOrFail($existingAccount->id);

                if (filled($data['username'] ?? null)) {
                    $user->username = $data['username'];
                }

                if (filled($data['email'] ?? null)) {
                    $user->email = $data['email'];
                }

                if (filled($data['password'] ?? null)) {
                    $user->password = $data['password'];
                }

                if (blank($user->name)) {
                    $user->name = $data['name'];
                }

                $user->save();
            } else {
                $user = User::create([
                    'name' => $data['name'],
                    'username' => $data['username'],
                    'email' => $data['email'] ?? null,
                    'password' => $data['password'],
                    'role' => 'vendor',
                    'status' => 'approved',
                ]);
            }

            return Vendor::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'slug' => $slug,
                'category_id' => $data['category_id'],
                'price' => $data['price'],
                'guest_capacity' => $category?->isCatering() ? ($data['guest_capacity'] ?? null) : null,
                'phone' => $data['phone'],
                'bank_name' => $data['bank_name'] ?? null,
                'bank_account_number' => $data['bank_account_number'] ?? null,
                'bank_account_holder' => $data['bank_account_holder'] ?? null,
                'address' => $data['address'],
                'description' => $data['description'] ?? null,
                'city' => $data['city'] ?? null,
                'instagram' => $data['instagram'] ?? null,
            ]);
        });

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

        return redirect()
            ->route('admin.vendors.index')
            ->with('success', 'Vendor berhasil ditambahkan.');
    }

    public function edit(Vendor $vendor)
    {
        $vendor->load(['photos', 'user']);

        $categories = Category::orderBy('name')->get();
        $availableVendorUsers = $vendor->user
            ? collect()
            : User::query()
                ->where('role', 'vendor')
                ->whereDoesntHave('vendor')
                ->orderBy('name')
                ->get();

        return view(
            'admin.vendors.edit',
            compact('vendor', 'categories', 'availableVendorUsers')
        );
    }

    public function update(Request $request, Vendor $vendor)
    {
        $vendor->loadMissing('user');

        $request->merge([
            'username' => $request->filled('username')
                ? Str::lower(trim((string) $request->input('username')))
                : null,
            'email' => $request->filled('email')
                ? Str::lower(trim((string) $request->input('email')))
                : null,
        ]);
        $category = Category::find($request->input('category_id'));

        $selectedAccount = ! $vendor->user && $request->filled('user_id')
            ? User::query()
                ->where('role', 'vendor')
                ->whereDoesntHave('vendor')
                ->find($request->input('user_id'))
            : null;
        $account = $vendor->user ?? $selectedAccount;

        $data = $request->validate([
            'user_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where('role', 'vendor'),
                Rule::unique('vendors', 'user_id')->ignore($vendor->id),
            ],
            'username' => [
                Rule::requiredIf(! $account || ! $account->username),
                'nullable',
                'string',
                'min:3',
                'max:80',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($account?->id),
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($account?->id),
            ],
            'password' => [
                Rule::requiredIf(! $account || ! $account->password),
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                "regex:/^[\pL\pN][\pL\pN\s&.'-]*$/u",
                'unique:vendors,name,' . $vendor->id,
            ],
            'category_id' => 'required|integer|exists:categories,id',
            'price' => 'required|numeric|min:0|max:999999999',
            'guest_capacity' => [
                Rule::requiredIf($category?->isCatering()),
                'nullable',
                'integer',
                'min:1',
                'max:1000000',
            ],
            'phone' => 'required|string|regex:/^[0-9]{10,15}$/|max:15',
            'bank_name' => 'nullable|string|max:80',
            'bank_account_number' => 'nullable|string|max:60',
            'bank_account_holder' => 'nullable|string|max:120',
            'address' => 'required|string|max:1000',
            'description' => 'nullable|string|max:2000',
            'city' => 'nullable|string|max:100',
            'instagram' => 'nullable|string|max:100',

            'photos' => 'nullable|array|max:10',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',

            'delete_photos' => 'nullable|array',
            'delete_photos.*' => 'integer',

            'cover_photo_id' => 'nullable|integer',
        ], [
            'name.required' => 'Nama vendor harus diisi',
            'name.unique' => 'Nama vendor sudah digunakan',
            'name.max' => 'Nama vendor maksimal 255 karakter',
            'name.regex' => 'Nama vendor hanya boleh berisi huruf, angka, spasi, dan tanda baca umum.',
            'category_id.required' => 'Kategori harus dipilih',
            'category_id.exists' => 'Kategori tidak valid',
            'price.required' => 'Harga harus diisi',
            'price.min' => 'Harga tidak boleh negatif',
            'price.max' => 'Harga terlalu besar',
            'guest_capacity.required' => 'Kapasitas tamu wajib diisi untuk vendor catering.',
            'guest_capacity.integer' => 'Kapasitas tamu harus berupa angka bulat.',
            'guest_capacity.min' => 'Kapasitas tamu minimal 1 orang.',
            'guest_capacity.max' => 'Kapasitas tamu maksimal 1.000.000 orang.',
            'phone.required' => 'Nomor telepon harus diisi',
            'phone.regex' => 'Nomor telepon harus berupa angka 10-15 digit',
            'phone.max' => 'Nomor telepon maksimal 15 digit',
            'address.required' => 'Alamat harus diisi',
            'address.max' => 'Alamat maksimal 1000 karakter',
            'description.max' => 'Deskripsi maksimal 2000 karakter',
            'photos.max' => 'Maksimal 10 foto',
            'photos.*.image' => 'File harus berupa gambar',
            'photos.*.mimes' => 'Format gambar harus jpg, jpeg, png, atau webp',
            'photos.*.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        $slug = Str::slug($data['name']);

        $originalSlug = $slug;
        $count = 1;

        while (
            Vendor::where('slug', $slug)
                ->where('id', '!=', $vendor->id)
                ->exists()
        ) {
            $slug = $originalSlug . '-' . $count++;
        }

        DB::transaction(function () use ($vendor, $data, $slug, $account, $selectedAccount, $category) {
            $vendor->update([
                'name' => $data['name'],
                'slug' => $slug,
                'category_id' => $data['category_id'],
                'price' => $data['price'],
                'guest_capacity' => $category?->isCatering() ? ($data['guest_capacity'] ?? null) : null,
                'phone' => $data['phone'],
                'bank_name' => $data['bank_name'] ?? null,
                'bank_account_number' => $data['bank_account_number'] ?? null,
                'bank_account_holder' => $data['bank_account_holder'] ?? null,
                'address' => $data['address'],
                'description' => $data['description'] ?? null,
                'city' => $data['city'] ?? null,
                'instagram' => $data['instagram'] ?? null,
            ]);

            $user = $account;

            if (! $user) {
                $user = User::create([
                    'name' => $data['name'],
                    'username' => $data['username'],
                    'email' => $data['email'] ?? null,
                    'password' => $data['password'],
                    'role' => 'vendor',
                    'status' => 'approved',
                ]);
            } else {
                if (filled($data['username'] ?? null)) {
                    $user->username = $data['username'];
                }

                if (filled($data['email'] ?? null)) {
                    $user->email = $data['email'];
                }

                if (filled($data['password'] ?? null)) {
                    $user->password = $data['password'];
                }

                $user->role = 'vendor';
                $user->save();
            }

            if (! $vendor->user_id || $selectedAccount) {
                $vendor->user()->associate($user);
                $vendor->save();
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Hapus foto yang dipilih
        |--------------------------------------------------------------------------
        */

        if ($request->filled('delete_photos')) {
            $photos = $vendor->photos()
                ->whereIn('id', $request->delete_photos)
                ->get();

            foreach ($photos as $photo) {
                Storage::disk('public')->delete($photo->photo);
                $photo->delete();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Upload foto baru
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('photos')) {
            $lastSortOrder = $vendor->photos()->max('sort_order') ?? 0;

            foreach ($request->file('photos') as $index => $photo) {
                $path = $photo->store('vendors', 'public');

                $vendor->photos()->create([
                    'photo' => $path,
                    'is_cover' => false,
                    'sort_order' => $lastSortOrder + $index + 1,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Tentukan foto utama
        |--------------------------------------------------------------------------
        */

        if ($request->filled('cover_photo_id')) {

            $coverPhoto = $vendor->photos()
                ->where('id', $request->cover_photo_id)
                ->first();

            if ($coverPhoto) {
                $vendor->photos()->update([
                    'is_cover' => false,
                ]);

                $coverPhoto->update([
                    'is_cover' => true,
                ]);
            }
        } else {

            $hasCover = $vendor->photos()
                ->where('is_cover', true)
                ->exists();

            if (!$hasCover) {
                $firstPhoto = $vendor->photos()
                    ->orderBy('sort_order')
                    ->first();

                if ($firstPhoto) {
                    $firstPhoto->update([
                        'is_cover' => true,
                    ]);
                }
            }
        }

        return redirect()
            ->route('admin.vendors.show', $vendor)
            ->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->load(['photos', 'user']);

        foreach ($vendor->photos as $photo) {
            Storage::disk('public')->delete($photo->photo);
        }

        DB::transaction(function () use ($vendor) {
            $vendor->user?->delete();
            $vendor->delete();
        });

        return redirect()
            ->route('admin.vendors.index')
            ->with('success', 'Vendor berhasil dihapus.');
    }
}
