<?php

namespace App\Http\Controllers\User;

use App\Data\User\VendorConfirmationData;
use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class VendorConfirmationController extends Controller
{
    public function index()
    {
        $client = Client::where('users_id', Auth::id())->first();

        $bookings = $client
            ? $client->bookings()
                ->with([
                    'events.package',
                    'events.schedules.vendor.category',
                    'events.schedules.vendor.photos',
                ])
                ->latest('id')
                ->get()
            : collect();

        return view('user.vendor-confirmation.index', compact('bookings'));
    }
}
