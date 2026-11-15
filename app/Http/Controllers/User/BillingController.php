<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/** Dashboard client: daftar tagihan & status pembayaran semua pesanan. */
class BillingController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $client = Client::where('users_id', Auth::id())->first();
        if (! $client) {
            return redirect()->route('client.profile')->with('error', 'Lengkapi data mempelai terlebih dahulu');
        }

        $bookings = Booking::with(['package', 'events', 'payments'])
            ->where('client_id', $client->id)
            ->latest('id')
            ->get();

        // Pastikan status yang tampil selalu sama dengan isi tabel payments.
        $bookings->each->syncPaymentStatus();
        $bookings->load('payments');

        return view('user.billing.index', ['bookings' => $bookings]);
    }
}
