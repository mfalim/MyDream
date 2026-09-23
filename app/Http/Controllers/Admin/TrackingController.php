<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Schedule;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with(['booking.client', 'schedules.vendor', 'schedules.member'])
            ->orderBy('event_date', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('booking.client', function($q) use ($search) {
                      $q->where('groom_name', 'like', "%{$search}%")
                        ->orWhere('bride_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $events = $query->get()->map(function($event) {
            $totalSchedules = $event->schedules->count();
            $approvedSchedules = $event->schedules->where('status', 'approved')->count();
            $event->progress = $totalSchedules > 0 ? ($approvedSchedules / $totalSchedules) * 100 : 0;
            return $event;
        });

        return view('admin.tracking.index', compact('events'));
    }

    public function show($id)
    {
        $event = Event::with([
            'booking.client',
            'package',
            'schedules.vendor.category',
            'schedules.member',
            'eventMembers.member'
        ])->findOrFail($id);

        $totalSchedules = $event->schedules->count();
        $approvedSchedules = $event->schedules->where('status', 'approved')->count();
        $rejectedSchedules = $event->schedules->where('status', 'rejected')->count();
        $pendingSchedules = $event->schedules->where('status', 'pending')->count();
        $progress = $totalSchedules > 0 ? ($approvedSchedules / $totalSchedules) * 100 : 0;

        return view('admin.tracking.show', compact('event', 'totalSchedules', 'approvedSchedules', 'rejectedSchedules', 'pendingSchedules', 'progress'));
    }

    public function updateScheduleStatus(Request $request, $scheduleId)
    {
        $schedule = Schedule::findOrFail($scheduleId);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $schedule->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }
    
    public function updateBookingStatus(Request $request, $bookingId)
    {
        $booking = \App\Models\Booking::findOrFail($bookingId);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $booking->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Status booking berhasil diupdate'
        ]);
    }
}
