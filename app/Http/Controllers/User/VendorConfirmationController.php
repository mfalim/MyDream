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

        $approvedBookings = $client
            ? $client->bookings()
                ->where('status', 'approved')
                ->with([
                    'events' => fn ($query) => $query->orderBy('id'),
                    'events.package',
                    'events.schedules.vendor.category',
                    'events.schedules.vendor.photos',
                ])
                ->oldest('id')
                ->get()
            : collect();

        $firstBooking = $approvedBookings->first(function ($booking) {
            $firstEvent = $booking->events->sortBy('id')->first();

            return ! $firstEvent || $firstEvent->status !== 'completed';
        });

        $bookings = $firstBooking ? collect([$firstBooking]) : collect();

        return view('user.vendor-confirmation.index', compact('bookings'));
    }
}
