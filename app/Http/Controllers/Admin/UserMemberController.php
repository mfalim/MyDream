<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MemberPosition;
use Illuminate\Http\Request;

class UserMemberController extends Controller
{
    private const DIVISIONS = [
        'show-management' => 'Show & Stage Management',
        'client-hospitality' => 'Client & VIP Hospitality',
        'av-lighting' => 'AV, Lighting & Multimedia',
        'logistik-protokoler' => 'Logistik & Protokoler',
    ];

    public function index()
    {
        $users = User::where('role', 'member')->latest()->get();
        return view('admin.user-members.index', compact('users'));
    }

    public function create()
    {
        $positions = MemberPosition::orderBy('name')->get();
        $divisions = self::DIVISIONS;
        return view('admin.user-members.create', compact('positions', 'divisions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'position' => 'required|string|max:100',
            'division' => 'required|string|in:' . implode(',', array_keys(self::DIVISIONS)),
        ]);

        $user = User::create([
            'email' => $validated['email'],
            'name' => explode('@', $validated['email'])[0],
            'role' => 'member',
            'status' => 'approved',
            'google_id' => null,
            'avatar' => null,
        ]);

        session(['member_setup_' . $user->id => [
            'position' => $validated['position'],
            'division' => $validated['division'],
        ]]);

        return redirect()->route('admin.user-members.index')
            ->with('success', 'Member user berhasil ditambahkan. Member dapat login dengan Google OAuth menggunakan email tersebut.');
    }

    public function destroy(User $user)
    {
        if ($user->role !== 'member') {
            return back()->with('error', 'Hanya user member yang bisa dihapus.');
        }

        $user->delete();

        return redirect()->route('admin.user-members.index')
            ->with('success', 'Member user berhasil dihapus.');
    }
}
