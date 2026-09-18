<?php


namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function profile()
    {
        return view('login.login_profile');
    }

    public function storeProfile(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
        ]);

        Client::create([
            'users_id' => Auth::id(),
            'full_name' => $request->full_name,
            'address' => $request->address,
            'phone' => $request->phone,
        ]);

        return redirect('/client/dashboard')
            ->with('success', 'Informasi pribadi berhasil disimpan.');
    }
}