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
            'position' => 'required|string|max:100',
            'specialization' => 'nullable|string|max:255',
            'division' => 'required|string|in:' . implode(',', self::DIVISIONS),
        ], [
            'email.required' => 'Email member wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email tersebut sudah terdaftar.',
            'position.required' => 'Posisi atau jabatan wajib dipilih.',
            'specialization.string' => 'Spesialisasi tidak valid.',
            'division.required' => 'Divisi wajib dipilih.',
            'division.in' => 'Divisi yang dipilih tidak valid.',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'email' => $validated['email'],
                'name' => explode('@', $validated['email'])[0],
                'role' => 'member',
                'status' => 'approved',
                'google_id' => null,
                'avatar' => null,
            ]);

            Member::create([
                'user_id' => $user->id,
                'position' => $validated['position'],
                'specialization' => $validated['specialization'] ?? null,
                'division' => $validated['division'],
            ]);
        });

        // Penugasan member akan disimpan ke tabel members
        // setelah struktur tabel dan relasinya dipastikan.

        return redirect()
            ->route('admin.members.index')
            ->with(
                'success',
                'Akun member berhasil didaftarkan. Member dapat login melalui Google OAuth menggunakan email tersebut.'
            );
    }

    public function edit(Member $member)
    {
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
            'position' => 'required|string|max:100',
            'specialization' => 'nullable|string|max:255',
            'division' => 'required|string|in:' . implode(',', self::DIVISIONS),
        ], [
            'email.required' => 'Email member wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email tersebut sudah digunakan.',
            'position.required' => 'Posisi atau jabatan wajib dipilih.',
            'division.required' => 'Divisi wajib dipilih.',
            'division.in' => 'Divisi yang dipilih tidak valid.',
        ]);

        $member->update([
            'position' => $validated['position'],
            'specialization' => $validated['specialization'] ?? null,
            'division' => $validated['division'],
        ]);

        if ($member->user) {
            $member->user->update([
                'email' => $validated['email'],
            ]);
        }

        return redirect()
            ->route('admin.members.index')
            ->with('success', 'Data awal member berhasil diperbarui.');
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
