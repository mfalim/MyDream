<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberDivision;
use App\Models\MemberPosition;
use App\Models\MemberSpecialization;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class MemberCategoryController extends Controller
{
    private const CATEGORY_TYPES = [
        'division' => MemberDivision::class,
        'position' => MemberPosition::class,
        'specialization' => MemberSpecialization::class,
    ];

    private const TYPE_LABELS = [
        'division' => 'Division',
        'position' => 'Position',
        'specialization' => 'Specialization',
    ];

    private function ensureAdminAccess(): void
    {
        if (! auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengelola kategori member.');
        }
    }

    private function normalizedType(?string $type): string
    {
        $normalized = strtolower(trim((string) $type));

        return $normalized === '' ? 'all' : $normalized;
    }

    private function getModelForType(string $type): string
    {
        $type = $this->normalizedType($type);

        if ($type === 'all' || ! array_key_exists($type, self::CATEGORY_TYPES)) {
            throw ValidationException::withMessages([
                'type' => ['Jenis kategori tidak valid.'],
            ]);
        }

        return self::CATEGORY_TYPES[$type];
    }

    private function getLabelForType(string $type): string
    {
        return self::TYPE_LABELS[$type] ?? 'Kategori';
    }

    private function hasDuplicateName(string $type, string $name, ?int $ignoreId = null): bool
    {
        $model = $this->getModelForType($type);
        $query = $model::query()->whereRaw('LOWER(name) = ?', [strtolower(trim($name))]);

        if ($ignoreId !== null) {
            $query->whereKeyNot($ignoreId);
        }

        return $query->exists();
    }

    private function isCategoryInUse(string $type, string $name): bool
    {
        return match ($type) {
            'division' => Member::query()->where('division', $name)->exists(),
            'position' => Member::query()->where('position', $name)->exists(),
            'specialization' => Member::query()->where('specialization', $name)->exists(),
            default => false,
        };
    }

    public function index(Request $request)
    {
        $this->ensureAdminAccess();

        $search = trim((string) $request->input('q', ''));
        $typeFilter = $this->normalizedType($request->input('type'));

        $items = collect();

        foreach (self::CATEGORY_TYPES as $type => $modelClass) {
            if ($typeFilter !== 'all' && $typeFilter !== $type) {
                continue;
            }

            $query = $modelClass::query();

            if ($search !== '') {
                $query->where('name', 'like', "%{$search}%");
            }

            $query->orderBy('name')->get()->each(function ($record) use ($items, $type) {
                $items->push([
                    'id' => $record->id,
                    'name' => $record->name,
                    'type' => $type,
                    'label' => self::TYPE_LABELS[$type] ?? 'Kategori',
                ]);
            });
        }

        $items = $items->sortBy([
            ['type', 'asc'],
            ['name', 'asc'],
        ])->values();

        $page = max((int) $request->input('page', 1), 1);
        $perPage = 10;
        $paginated = new LengthAwarePaginator(
            $items->slice(($page - 1) * $perPage, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('admin.member-categories.index', [
            'items' => $paginated,
            'search' => $search,
            'typeFilter' => $typeFilter,
            'typeOptions' => self::TYPE_LABELS,
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureAdminAccess();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|string|in:' . implode(',', array_keys(self::CATEGORY_TYPES)),
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.string' => 'Nama kategori harus berupa teks.',
            'name.max' => 'Nama kategori maksimal 100 karakter.',
            'type.required' => 'Jenis kategori wajib dipilih.',
            'type.in' => 'Jenis kategori yang dipilih tidak valid.',
        ]);

        $type = $validated['type'];
        $name = trim((string) $validated['name']);

        if ($name === '') {
            throw ValidationException::withMessages([
                'name' => ['Nama kategori wajib diisi.'],
            ]);
        }

        if ($this->hasDuplicateName($type, $name)) {
            throw ValidationException::withMessages([
                'name' => ['Nama kategori sudah ada untuk jenis ' . $this->getLabelForType($type) . '.'],
            ]);
        }

        $model = $this->getModelForType($type);
        $model::create(['name' => $name]);

        return redirect()
            ->route('admin.member-categories.index', ['type' => $type])
            ->with('success', 'Kategori ' . strtolower($this->getLabelForType($type)) . ' berhasil ditambahkan.');
    }

    public function update(Request $request, $memberCategory)
    {
        $this->ensureAdminAccess();

        $validated = $request->validate([
            'id' => 'required|integer',
            'name' => 'required|string|max:100',
            'type' => 'required|string|in:' . implode(',', array_keys(self::CATEGORY_TYPES)),
        ], [
            'id.required' => 'Data kategori tidak ditemukan.',
            'id.integer' => 'Data kategori tidak valid.',
            'name.required' => 'Nama kategori wajib diisi.',
            'name.string' => 'Nama kategori harus berupa teks.',
            'name.max' => 'Nama kategori maksimal 100 karakter.',
            'type.required' => 'Jenis kategori wajib dipilih.',
            'type.in' => 'Jenis kategori yang dipilih tidak valid.',
        ]);

        $type = $validated['type'];
        $name = trim((string) $validated['name']);

        if ($name === '') {
            throw ValidationException::withMessages([
                'name' => ['Nama kategori wajib diisi.'],
            ]);
        }

        $model = $this->getModelForType($type);
        $record = $model::find($validated['id']);

        if (! $record) {
            throw ValidationException::withMessages([
                'name' => ['Kategori yang dipilih tidak ditemukan.'],
            ]);
        }

        if ($this->hasDuplicateName($type, $name, (int) $record->id)) {
            throw ValidationException::withMessages([
                'name' => ['Nama kategori sudah ada untuk jenis ' . $this->getLabelForType($type) . '.'],
            ]);
        }

        $record->update(['name' => $name]);

        return redirect()
            ->route('admin.member-categories.index', ['type' => $type])
            ->with('success', 'Kategori ' . strtolower($this->getLabelForType($type)) . ' berhasil diperbarui.');
    }

    public function destroy(Request $request, $memberCategory)
    {
        $this->ensureAdminAccess();

        $type = $this->normalizedType($request->input('type'));

        if ($type === 'all' || ! array_key_exists($type, self::CATEGORY_TYPES)) {
            return back()->withErrors([
                'type' => ['Jenis kategori tidak valid.'],
            ]);
        }

        $model = $this->getModelForType($type);
        $record = $model::find($memberCategory);

        if (! $record) {
            return back()->withErrors([
                'category' => ['Kategori tidak ditemukan.'],
            ]);
        }

        if ($this->isCategoryInUse($type, $record->name)) {
            return back()->withErrors([
                'category' => ['Kategori ' . $record->name . ' masih digunakan oleh data member. Hapus atau ubah relasinya terlebih dahulu agar penghapusan aman.'],
            ]);
        }

        $record->delete();

        return redirect()
            ->route('admin.member-categories.index', ['type' => $type])
            ->with('success', 'Kategori ' . strtolower($this->getLabelForType($type)) . ' berhasil dihapus.');
    }
}
