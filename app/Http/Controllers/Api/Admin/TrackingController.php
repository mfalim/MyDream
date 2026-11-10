<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Schedule;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['sometimes', 'string', 'max:100'],
            'status' => ['sometimes', 'in:approved,pending,rejected'],
        ]);

        $bookings = Booking::with(['client', 'package', 'events.package', 'events.schedules'])
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filters['q'] ?? null, function ($q, $search) {
                $q->where(fn ($query) => $query->where('venue_name', 'like', "%{$search}%")
                    ->orWhereHas('client', fn ($client) => $client->where('groom_name', 'like', "%{$search}%")->orWhere('bride_name', 'like', "%{$search}%")));
            })
            ->latest()->paginate($request->integer('per_page', 20));

        $bookings->getCollection()->transform(function (Booking $booking) {
            $booking->syncTotalPrice();
            $booking->setAttribute('all_packages', $booking->all_packages);
            $booking->setAttribute('full_venue_address', $booking->full_venue_address);
            $schedules = $booking->events->flatMap->schedules;
            $booking->setAttribute('schedule_progress', [
                'approved' => $schedules->where('status', 'approved')->count(),
                'total' => $schedules->count(),
                'percent' => $schedules->isEmpty() ? 0 : round($schedules->where('status', 'approved')->count() / $schedules->count() * 100, 2),
            ]);

            return $booking;
        });

        return response()->json($bookings);
    }

    public function showEvent(int $id)
    {
        $event = Event::with(['booking.client', 'package', 'schedules.vendor.category', 'schedules.member', 'eventMembers.member'])->findOrFail($id);
        $counts = $event->schedules->countBy('status');

        return response()->json([
            'data' => $event,
            'progress' => [
                'total' => $event->schedules->count(),
                'approved' => $counts->get('approved', 0),
                'pending' => $counts->get('pending', 0),
                'rejected' => $counts->get('rejected', 0),
            ],
        ]);
    }

    public function updateScheduleStatus(Request $request, int $scheduleId)
    {
        $data = $request->validate(['status' => ['required', 'in:pending,approved,rejected']]);
        $schedule = Schedule::findOrFail($scheduleId);
        $schedule->update($data);

        return response()->json(['data' => $schedule->fresh()]);
    }

    public function updateScheduleTime(Request $request, int $scheduleId)
    {
        $schedule = Schedule::whereNotNull('vendor_id')->findOrFail($scheduleId);
        abort_unless($schedule->status === 'approved', 422, 'Only approved vendor schedules can be timed.');
        $data = $request->validate([
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
        ]);
        $schedule->update($data);

        return response()->json(['data' => $schedule->fresh()]);
    }

    public function updateBookingStatus(Request $request, int $bookingId)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
            'coordinator_id' => ['nullable', 'exists:members,id'],
            'coordinator_role' => ['nullable', 'in:Lead Coordinator,Coordinator'],
        ]);
        $booking = Booking::findOrFail($bookingId);
        $booking->update(['status' => $data['status']]);
        if ($data['status'] === 'approved' && ! empty($data['coordinator_id'])) {
            $event = Event::where('booking_id', $booking->id)->first();
            if ($event) {
                EventMember::updateOrCreate(
                    ['event_id' => $event->id, 'member_id' => $data['coordinator_id']],
                    ['role' => $data['coordinator_role'] ?? 'Coordinator', 'status' => 'assigned']
                );
            }
        }

        return response()->json(['data' => $booking->fresh()]);
    }
}
