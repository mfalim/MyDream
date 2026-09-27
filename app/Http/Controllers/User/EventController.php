<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        $client = Client::where('users_id', Auth::id())->firstOrFail();

        $events = Event::with([
            'booking.package',
            'package',
            'schedules.vendor.category',
        ])
            ->whereHas('booking', function ($query) use ($client) {
                $query->where('client_id', $client->id);
            })
            ->latest('event_date')
            ->get();

        return view('user.event.index', [
            'events' => $events,
        ]);
    }
}
