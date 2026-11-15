<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberPosition;
use App\Models\MemberSpecialization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    private const DIVISIONS = [
        'show-management',
        'client-hospitality',
        'av-lighting',
        'logistik-protokoler',
    ];

    private const STATUSES = [
        'standby',
        'aktif',
        'inaktif',
    ];

    public function index(Request $request)
    {
        $search = trim((string) $request->input('q'));
        $division = $request->input('division');
        $status = $request->input('status');

        $members = Member::query()
            ->with('user')
            ->whereNotNull('call_sign')
            ->where('call_sign', '!=', '')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('member_code', 'like', "%{$search}%")
                        ->orWhere('call_sign', 'like', "%{$search}%")
                        ->orWhere('specialization', 'like', "%{$search}%");
                });
            })
            ->when(in_array($division, self::DIVISIONS, true), function ($query) use ($division) {
                $query->where('division', $division);
            })
            ->when(in_array($status, self::STATUSES, true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->get();

        return view('admin.members.index', compact('members'));
    }

    public function create()
    {
        return view('admin.members.create', [
            'positions' => MemberPosition::orderBy('name')->get(),
            'specializations' => MemberSpecialization::orderBy('name')->get(),
            'divisions' => self::DIVISIONS,
        ]);
    }

    public function show(Member $member)
    {
        return view('admin.members.show', compact('member'));
    }

    public function storePosition(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:member_positions,name',
        ]);

        return response()->json(MemberPosition::create($data), 201);
    }

    public function storeSpecialization(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:member_specializations,name',
        ]);

        return response()->json(MemberSpecialization::create($data), 201);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:users,email',
            'username' => 'nullable|string|min:3|max:50|alpha_dash|unique:users,username',
            'password' => 'nullable|string|min:6',
            'position' => 'required|string|max:100',
            'specialization' => 'nullable|string|max:255',
            'division' => 'required|string|in:' . implode(',', self::DIVISIONS),
            'daily_fee' => 'required|numeric|min:0',
            'bank_name' => 'nullable|string|max:80',
            'bank_account_number' => 'nullable|string|max:60',
            'bank_account_holder' => 'nullable|string|max:120',
        ], [
            'email.required' => 'Email member wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email tersebut sudah terdaftar.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, tanda hubung (-), atau garis bawah (_).',
            'username.unique' => 'Username tersebut sudah digunakan.',
            'password.min' => 'Password minimal 6 karakter.',
            'position.required' => 'Posisi atau jabatan wajib dipilih.',
            'specialization.string' => 'Spesialisasi tidak valid.',
            'division.required' => 'Divisi wajib dipilih.',
            'division.in' => 'Divisi yang dipilih tidak valid.',
            'daily_fee.required' => 'Tarif harian member wajib diisi.',
            'daily_fee.numeric' => 'Tarif harian harus berupa angka.',
            'daily_fee.min' => 'Tarif harian tidak boleh kurang dari nol.',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $user = User::create([
                'email' => strtolower(trim($validated['email'])),
                'username' => ! empty($validated['username']) ? strtolower(trim($validated['username'])) : null,
                'password' => ! empty($validated['password']) ? $validated['password'] : null,
                'name' => explode('@', $validated['email'])[0],
                'role' => 'member',
                'status' => 'approved',
                'google_id' => null,
                'avatar' => null,
            ]);

            Member::create([
                'user_id' => $user->id,
                'position' => $validated['position'],
                'status' => $request->status,
                'specialization' => $validated['specialization'] ?? null,
                'division' => $validated['division'],
                'daily_fee' => $validated['daily_fee'],
                'bank_name' => $validated['bank_name'] ?? null,
                'bank_account_number' => $validated['bank_account_number'] ?? null,
                'bank_account_holder' => $validated['bank_account_holder'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.members.index')
            ->with(
                'success',
                'Akun member berhasil didaftarkan. Member dapat login melalui Google OAuth atau Username/Email & Password.'
            );
    }

    public function edit(Member $member)
    {
        $member->loadMissing('user');

        return view('admin.members.edit', [
            'member' => $member,
            'positions' => MemberPosition::orderBy('name')->get(),
            'specializations' => MemberSpecialization::orderBy('name')->get(),
            'divisions' => self::DIVISIONS,
        ]);
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:users,email,' . $member->user_id,
            'username' => 'nullable|string|min:3|max:50|alpha_dash|unique:users,username,' . $member->user_id,
            'password' => 'nullable|string|min:6',
            'position' => 'required|string|max:100',
            'specialization' => 'nullable|string|max:255',
            'division' => 'required|string|in:' . implode(',', self::DIVISIONS),
            'daily_fee' => 'required|numeric|min:0',
            'bank_name' => 'nullable|string|max:80',
            'bank_account_number' => 'nullable|string|max:60',
            'bank_account_holder' => 'nullable|string|max:120',
        ], [
            'email.required' => 'Email member wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email tersebut sudah digunakan.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, tanda hubung (-), atau garis bawah (_).',
            'username.unique' => 'Username tersebut sudah digunakan.',
            'password.min' => 'Password minimal 6 karakter.',
            'position.required' => 'Posisi atau jabatan wajib dipilih.',
            'division.required' => 'Divisi wajib dipilih.',
            'division.in' => 'Divisi yang dipilih tidak valid.',
            'daily_fee.required' => 'Tarif harian member wajib diisi.',
            'daily_fee.numeric' => 'Tarif harian harus berupa angka.',
            'daily_fee.min' => 'Tarif harian tidak boleh kurang dari nol.',
        ]);

        $member->update([
            'position' => $validated['position'],
            'status' => $request->status,
            'specialization' => $validated['specialization'] ?? null,
            'division' => $validated['division'],
            'daily_fee' => $validated['daily_fee'],
            'bank_name' => $validated['bank_name'] ?? $member->bank_name,
            'bank_account_number' => $validated['bank_account_number'] ?? $member->bank_account_number,
            'bank_account_holder' => $validated['bank_account_holder'] ?? $member->bank_account_holder,
        ]);

        if ($member->user) {
            $userPayload = [
                'email' => strtolower(trim($validated['email'])),
                'username' => ! empty($validated['username']) ? strtolower(trim($validated['username'])) : null,
            ];

            if (! empty($validated['password'])) {
                $userPayload['password'] = $validated['password'];
            }

            $member->user->update($userPayload);
        }

        return redirect()
            ->route('admin.members.index')
            ->with('success', 'Data member berhasil diperbarui.');
    }

    public function destroy(Member $member)
    {
        $this->deleteStoredFile($member->certification);
        $member->delete();

        return redirect()
            ->route('admin.members.index')
            ->with('success', 'Anggota berhasil dihapus.');
    }

    // private function validatedData(Request $request, ?Member $member = null): array
    // {
    //     $data = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'call_sign' => 'required|string|max:100',
    //         'phone' => 'required|string|max:30',
    //         'email' => 'nullable|email|max:255',
    //         'domicile' => 'nullable|string|max:500',
    //         'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    //         'division' => 'required|string|max:100',
    //         'position' => 'required|string|max:100',
    //         'specialization' => 'nullable|string|max:255',
    //         'certification' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
    //         'ht_code' => 'nullable|string|max:50',
    //         'emergency_name' => 'nullable|string|max:255',
    //         'emergency_phone' => 'nullable|string|max:30',
    //         'daily_fee' => 'required|numeric|min:0',
    //         'status' => 'required|string|in:standby,aktif,resiko,selesai',
    //     ], [
    //         'name.required' => 'Nama anggota wajib diisi.',
    //         'call_sign.required' => 'Nama panggilan wajib diisi.',
    //         'phone.required' => 'Nomor telepon wajib diisi.',
    //         'email.email' => 'Format alamat email tidak valid.',
    //         'division.required' => 'Divisi wajib dipilih.',
    //         'position.required' => 'Posisi atau jabatan wajib dipilih.',
    //         'daily_fee.required' => 'Biaya harian wajib diisi.',
    //         'daily_fee.numeric' => 'Biaya harian harus berupa angka.',
    //         'daily_fee.min' => 'Biaya harian tidak boleh kurang dari nol.',
    //         'status.required' => 'Status anggota wajib dipilih.',
    //         'status.in' => 'Status anggota yang dipilih tidak valid.',
    //         'photo.image' => 'Foto anggota harus berupa gambar.',
    //         'photo.mimes' => 'Foto anggota harus berformat JPG, JPEG, PNG, atau WEBP.',
    //         'photo.max' => 'Ukuran foto anggota maksimal 2 MB.',
    //         'certification.image' => 'Sertifikasi harus berupa gambar.',
    //         'certification.mimes' => 'Sertifikasi harus berformat JPG, JPEG, PNG, atau WEBP.',
    //         'certification.max' => 'Ukuran sertifikasi maksimal 4 MB.',
    //     ]);

    //     unset($data['photo'], $data['certification']);

    //     if ($member) {
    //         $data['photo'] = $member->photo;
    //         $data['certification'] = $member->certification;
    //     }

    //     return $data;
    // }

    private function deleteStoredFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
