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
            $completedSchedules = $event->schedules->where('status', 'completed')->count();
            $event->progress = $totalSchedules > 0 ? ($completedSchedules / $totalSchedules) * 100 : 0;
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
        $completedSchedules = $event->schedules->where('status', 'completed')->count();
        $inProgressSchedules = $event->schedules->where('status', 'in_progress')->count();
        $pendingSchedules = $event->schedules->where('status', 'pending')->count();
        $progress = $totalSchedules > 0 ? ($completedSchedules / $totalSchedules) * 100 : 0;

        return view('admin.tracking.show', compact('event', 'totalSchedules', 'completedSchedules', 'inProgressSchedules', 'pendingSchedules', 'progress'));
    }

    public function updateScheduleStatus(Request $request, $scheduleId)
    {
        $schedule = Schedule::findOrFail($scheduleId);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed'
        ]);

        $schedule->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }
}
