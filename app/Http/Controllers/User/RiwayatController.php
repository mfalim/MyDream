<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Vendor;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    private function approvedBookings(Client $client)
    {
        return Booking::query()
            ->where('client_id', $client->id)
            ->where('status', 'approved');
    }

    public function index(Request $request)
    {
        $client = Client::where('users_id', auth()->id())->firstOrFail();
        $query = $this->approvedBookings($client)->with([
            'package',
            'events' => fn ($q) => $q->orderBy('event_date'),
            'events.schedules' => fn ($q) => $q->whereNotNull('vendor_id')->where('status', 'approved'),
            'events.schedules.vendor.category',
            'events.reviews',
        ]);

        if ($request->filled('search')) {
            $query->whereHas('events', fn ($q) => $q->where('name', 'like', '%' . $request->string('search') . '%'));
        }
        if ($request->filled('from')) {
            $query->whereHas('events', fn ($q) => $q->whereDate('event_date', '>=', $request->input('from')));
        }
        if ($request->filled('to')) {
            $query->whereHas('events', fn ($q) => $q->whereDate('event_date', '<=', $request->input('to')));
        }
        if ($request->filled('vendor')) {
            $vendorId = $request->integer('vendor');
            $query->whereHas('events.schedules', fn ($q) => $q
                ->where('vendor_id', $vendorId)
                ->whereNotNull('vendor_id')
                ->where('status', 'approved'));
        }

        $bookings = $query->orderByDesc('created_at')->paginate(8)->withQueryString();
        $vendors = Vendor::whereHas('schedules', fn ($q) => $q
            ->whereNotNull('event_id')
            ->where('status', 'approved')
            ->whereHas('event.booking', fn ($booking) => $booking
                ->where('client_id', $client->id)
                ->where('status', 'approved')))
            ->orderBy('name')->get(['id', 'name']);

        return view('user.riwayat.index', compact('bookings', 'vendors'));
    }

    public function show(int $booking)
    {
        $client = Client::where('users_id', auth()->id())->firstOrFail();
        $booking = $this->approvedBookings($client)
            ->with([
                'package',
                'events' => fn ($q) => $q->orderBy('event_date'),
                'events.schedules' => fn ($q) => $q->whereNotNull('vendor_id')->where('status', 'approved'),
                'events.schedules.vendor.category',
                'events.reviews',
            ])
            ->findOrFail($booking);

        return view('user.riwayat.show', compact('booking'));
    }
}
