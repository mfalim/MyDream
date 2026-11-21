<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class VendorConfirmationController extends Controller
{
    public function index()
    {
        $client = Client::where('users_id', Auth::id())->first();

        $activeBookings = $client
            ? $client->bookings()
                ->whereIn('status', ['approved', 'pending'])
                ->with([
                    'events' => fn ($query) => $query->orderBy('id'),
                    'events.package',
                    'events.schedules.vendor.category',
                    'events.schedules.vendor.photos',
                ])
                ->orderByRaw("CASE WHEN status = 'approved' THEN 0 ELSE 1 END")
                ->latest('id')
                ->get()
            : collect();

        $firstBooking = $activeBookings->first(function ($booking) {
            $firstEvent = $booking->events->sortBy('id')->first();

            return ! $firstEvent || $firstEvent->status !== 'completed';
        });

        $bookings = $firstBooking ? collect([$firstBooking]) : collect();

        return view('user.vendor-confirmation.index', compact('bookings'));
    }
}
