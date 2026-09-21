<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberPosition;
use App\Models\MemberSpecialization;
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
        'resiko',
        'selesai',
    ];

    public function index(Request $request)
    {
        $search = trim((string) $request->input('q'));
        $division = $request->input('division');
        $status = $request->input('status');

        $members = Member::query()
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
        $data = $this->validatedData($request);
        $data['member_code'] = 'TEMP';
        $data['photo'] = $request->hasFile('photo')
            ? $request->file('photo')->store('members', 'public')
            : null;
        $data['certification'] = $request->hasFile('certification')
            ? $request->file('certification')->store('member-certifications', 'public')
            : null;

        $member = Member::create($data);
        $member->update([
            'member_code' => 'WO-KRU-' . str_pad($member->id, 3, '0', STR_PAD_LEFT),
        ]);

        return redirect()
            ->route('admin.members.index')
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function edit(Member $member)
    {
        return view('admin.members.edit', [
            'member' => $member,
            'positions' => MemberPosition::orderBy('name')->get(),
            'specializations' => MemberSpecialization::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Member $member)
    {
        $data = $this->validatedData($request, $member);

        if ($request->hasFile('photo')) {
            $this->deleteStoredFile($member->photo);
            $data['photo'] = $request->file('photo')->store('members', 'public');
        }

        if ($request->hasFile('certification')) {
            $this->deleteStoredFile($member->certification);
            $data['certification'] = $request->file('certification')->store('member-certifications', 'public');
        }

        $member->update($data);

        return redirect()
            ->route('admin.members.show', $member)
            ->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy(Member $member)
    {
        $this->deleteStoredFile($member->photo);
        $this->deleteStoredFile($member->certification);
        $member->delete();

        return redirect()
            ->route('admin.members.index')
            ->with('success', 'Anggota berhasil dihapus.');
    }

    private function validatedData(Request $request, ?Member $member = null): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'call_sign' => 'required|string|max:100',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:255',
            'domicile' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'division' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'specialization' => 'nullable|string|max:255',
            'certification' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'ht_code' => 'nullable|string|max:50',
            'emergency_name' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:30',
            'daily_fee' => 'required|numeric|min:0',
            'status' => 'required|string|in:standby,aktif,resiko,selesai',
        ]);

        unset($data['photo'], $data['certification']);

        if ($member) {
            $data['photo'] = $member->photo;
            $data['certification'] = $member->certification;
        }

        return $data;
    }

    private function deleteStoredFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
