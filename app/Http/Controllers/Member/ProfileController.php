<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberSpecialization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ProfileController extends Controller
{
    public function showCompleteForm()
    {
        $user = Auth::user();

        if ($user->role !== 'member') {
            abort(403, 'Akses ditolak.');
        }

        $member = Member::where('user_id', $user->id)->first();

        if (!$member) {
            abort(404, 'Data member belum terdaftar oleh admin.');
        }

        // Jika profil sudah dilengkapi, arahkan ke dashboard.
        if ($member->call_sign !== null && $member->call_sign !== '') {
            return redirect()->route('member.dashboard');
        }

        // Jika call_sign masih kosong, tampilkan form.
        return view('member.complete-profile', [
            'user' => $user,
            'member' => $member,
            'position' => $member->position,
            'division' => $member->division,
            'specialization' => $member->specialization,
        ]);
    }

    public function storeProfile(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'member') {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'call_sign' => 'required|string|max:100',
            'phone' => 'required|string|max:30',
            'domicile' => 'nullable|string|max:500',
            'ht_code' => 'nullable|string|max:50',
            'emergency_name' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:30',
        ]);

        $member = Member::where('user_id', $user->id)->firstOrFail();

        $member->update([
            'name' => $user->name,
            'call_sign' => $validated['call_sign'],
            'phone' => $validated['phone'],
            'email' => $user->email,
            'domicile' => $validated['domicile'],
            'ht_code' => $validated['ht_code'],
            'emergency_name' => $validated['emergency_name'],
            'emergency_phone' => $validated['emergency_phone'],
        ]);

        return redirect()->route('member.dashboard')
            ->with('success', 'Profile berhasil dilengkapi. Selamat datang!');
    }
}
