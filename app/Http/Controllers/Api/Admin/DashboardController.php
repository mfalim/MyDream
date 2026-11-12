<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Member;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => [
                'bookings' => [
                    'total' => Booking::count(),
                    'pending' => Booking::where('status', 'pending')->count(),
                    'approved' => Booking::where('status', 'approved')->count(),
                    'rejected' => Booking::where('status', 'rejected')->count(),
                ],
                'upcoming_events' => Event::whereDate('event_date', '>=', today())->count(),
                'members' => Member::count(),
                'recent_bookings' => Booking::with(['client', 'package'])->latest()->limit(5)->get(),
            ],
        ]);
    }
}
