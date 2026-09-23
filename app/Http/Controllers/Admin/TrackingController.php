<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Schedule;
use App\Models\Booking;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['client', 'package'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('venue_name', 'like', "%{$search}%")
                  ->orWhereHas('client', function($q) use ($search) {
                      $q->where('groom_name', 'like', "%{$search}%")
                        ->orWhere('bride_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->get();
        
        $eventsByBooking = Event::with(['schedules'])
            ->get()
            ->groupBy('booking_id')
            ->map(function($events) {
                $allSchedules = $events->flatMap->schedules;
                $totalSchedules = $allSchedules->count();
                $approvedSchedules = $allSchedules->where('status', 'approved')->count();
                $progress = $totalSchedules > 0 ? ($approvedSchedules / $totalSchedules) * 100 : 0;
                
                return [
                    'events' => $events,
                    'progress' => $progress,
                    'approved_count' => $approvedSchedules,
                    'total_count' => $totalSchedules
                ];
            });
        
        $members = \App\Models\Member::where('status', 'active')->get();

        return view('admin.tracking.index', compact('bookings', 'eventsByBooking', 'members'));
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
        $booking = Booking::findOrFail($bookingId);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'coordinator_id' => 'nullable|exists:members,id'
        ]);

        $booking->update(['status' => $validated['status']]);
        
        if ($validated['status'] === 'approved' && isset($validated['coordinator_id'])) {
            $event = Event::where('booking_id', $bookingId)->first();
            
            if ($event) {
                $existingMember = \App\Models\EventMember::where('event_id', $event->id)
                    ->where('member_id', $validated['coordinator_id'])
                    ->first();
                
                if (!$existingMember) {
                    \App\Models\EventMember::create([
                        'event_id' => $event->id,
                        'member_id' => $validated['coordinator_id'],
                        'role' => 'Lead Coordinator',
                        'status' => 'assigned'
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status booking berhasil diupdate'
        ]);
    }
}
