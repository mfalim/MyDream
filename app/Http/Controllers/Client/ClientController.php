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
        $validated = $request->validate([
            'groom_name' => 'required|string|max:255',
            'bride_name' => 'required|string|max:255',
            'groom_phone' => 'required|string|regex:/^[0-9]{10,15}$/|max:20',
            'bride_phone' => 'required|string|regex:/^[0-9]{10,15}$/|max:20',
            'email' => 'required|email|max:255',
        ], [
            'groom_name.required' => 'Nama mempelai pria wajib diisi',
            'bride_name.required' => 'Nama mempelai wanita wajib diisi',
            'groom_phone.required' => 'Nomor telepon mempelai pria wajib diisi',
            'groom_phone.regex' => 'Nomor telepon mempelai pria harus berupa angka 10-15 digit',
            'bride_phone.required' => 'Nomor telepon mempelai wanita wajib diisi',
            'bride_phone.regex' => 'Nomor telepon mempelai wanita harus berupa angka 10-15 digit',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
        ]);

        Client::create([
            'users_id' => Auth::id(),
            'groom_name' => $validated['groom_name'],
            'bride_name' => $validated['bride_name'],
            'groom_phone' => $validated['groom_phone'],
            'bride_phone' => $validated['bride_phone'],
            'email' => $validated['email'],
        ]);

        return redirect('/user/dashboard')
            ->with('success', 'Informasi pribadi berhasil disimpan.');
    }
}