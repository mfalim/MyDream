<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberSpecialization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function showCompleteForm()
    {
        $user = Auth::user();
        
        if ($user->role !== 'member') {
            abort(403, 'Akses ditolak.');
        }

        $member = Member::where('user_id', $user->id)->first();
        if ($member) {
            return redirect()->route('member.dashboard');
        }

        $memberSetup = session('member_setup_' . $user->id);
        if (!$memberSetup) {
            return redirect()->route('login')->with('error', 'Data setup member tidak ditemukan. Hubungi admin.');
        }

        return view('member.complete-profile', [
            'user' => $user,
            'position' => $memberSetup['position'],
            'division' => $memberSetup['division'],
            'specializations' => MemberSpecialization::orderBy('name')->get(),
        ]);
    }

    public function storeProfile(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'member') {
            abort(403, 'Akses ditolak.');
        }

        $memberSetup = session('member_setup_' . $user->id);
        if (!$memberSetup) {
            return redirect()->route('login')->with('error', 'Data setup member tidak ditemukan. Hubungi admin.');
        }

        $validated = $request->validate([
            'call_sign' => 'required|string|max:100',
            'phone' => 'required|string|max:30',
            'domicile' => 'nullable|string|max:500',
            'specialization' => 'nullable|string|max:255',
            'ht_code' => 'nullable|string|max:50',
            'emergency_name' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:30',
        ]);

        $member = Member::create([
            'user_id' => $user->id,
            'member_code' => 'TEMP',
            'name' => $user->name,
            'call_sign' => $validated['call_sign'],
            'phone' => $validated['phone'],
            'email' => $user->email,
            'domicile' => $validated['domicile'],
            'division' => $memberSetup['division'],
            'position' => $memberSetup['position'],
            'specialization' => $validated['specialization'],
            'ht_code' => $validated['ht_code'],
            'emergency_name' => $validated['emergency_name'],
            'emergency_phone' => $validated['emergency_phone'],
            'daily_fee' => 0,
            'status' => 'active',
        ]);

        $member->update([
            'member_code' => 'WO-KRU-' . str_pad($member->id, 3, '0', STR_PAD_LEFT),
        ]);

        session()->forget('member_setup_' . $user->id);

        return redirect()->route('member.dashboard')
            ->with('success', 'Profile berhasil dilengkapi. Selamat datang!');
    }
}
