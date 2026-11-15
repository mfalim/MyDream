<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Schedule;
use App\Models\EventMember;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['client', 'package'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('venue_name', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($q) use ($search) {
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
            ->map(function ($events) {
                $allSchedules = $events->flatMap->schedules;
                $totalSchedules = $allSchedules->count();
                $approvedSchedules = $allSchedules->where('status', 'approved')->count();
                $progress = $totalSchedules > 0 ? ($approvedSchedules / $totalSchedules) * 100 : 0;

                return [
                    'events' => $events,
                    'progress' => $progress,
                    'approved_count' => $approvedSchedules,
                    'total_count' => $totalSchedules,
                ];
            });

        $members = \App\Models\Member::whereIn('status', ['active', 'aktif', 'standby'])
            ->orderBy('name')
            ->get();

        return view('admin.tracking.index', compact('bookings', 'eventsByBooking', 'members'));
    }

    public function show($id)
    {
        $event = Event::with([
            'booking.client',
            'package',
            'schedules.vendor.category',
            'schedules.member',
            'eventMembers.member',
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
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $schedule->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
        ]);
    }

    public function updateScheduleTime(Request $request, $scheduleId)
    {
        $schedule = Schedule::whereNotNull('vendor_id')->findOrFail($scheduleId);

        if ($schedule->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Jam hanya dapat diatur untuk vendor yang sudah approved',
            ], 422);
        }

        $validated = $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
        ]);

        $schedule->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Jam vendor berhasil disimpan',
        ]);
    }

    public function updateBookingStatus(Request $request, $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'coordinator_id' => 'nullable|exists:members,id',
            'coordinator_role' => 'nullable|in:Lead Coordinator,Coordinator',
            'coordinator_notes' => 'nullable|string|max:2000',
            'team_member_id' => 'nullable|array',
            'team_member_id.*' => 'nullable|exists:members,id',
            'team_member_role' => 'nullable|array',
            'team_member_role.*' => 'nullable|string|max:100',
            'team_member_notes' => 'nullable|array',
            'team_member_notes.*' => 'nullable|string|max:2000',
        ]);

        $selectedMemberIds = [];

        if (! empty($validated['coordinator_id'])) {
            $selectedMemberIds[] = (int) $validated['coordinator_id'];
        }

        if (! empty($validated['team_member_id']) && is_array($validated['team_member_id'])) {
            foreach ($validated['team_member_id'] as $memberId) {
                $memberId = (int) $memberId;
                if ($memberId > 0 && in_array($memberId, $selectedMemberIds, true)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Member tidak boleh dipilih lebih dari satu kali dalam satu event.',
                    ], 422);
                }

                $selectedMemberIds[] = $memberId;
            }
        }

        $booking->update(['status' => $validated['status']]);

        if ($validated['status'] === 'approved') {
            $event = Event::where('booking_id', $bookingId)->first();

            if ($event) {
                $selectedMemberIds = [];

                if (! empty($validated['coordinator_id'])) {
                    $coordinatorId = (int) $validated['coordinator_id'];
                    $selectedMemberIds[] = $coordinatorId;

                    $role = $validated['coordinator_role'] ?? 'Lead Coordinator';

                    \App\Models\EventMember::updateOrCreate(
                        [
                            'event_id' => $event->id,
                            'member_id' => $coordinatorId,
                        ],
                        [
                            'role' => $role,
                            'status' => 'assigned',
                            'notes' => $validated['coordinator_notes'] ?? null,
                            'attendance_status' => 'present',
                            'actual_hours' => 0,
                            'allowance' => 0,
                            'payment_status' => 'pending',
                        ]
                    );
                }

                if (! empty($validated['team_member_id']) && is_array($validated['team_member_id'])) {
                    foreach ($validated['team_member_id'] as $index => $memberId) {
                        $memberId = (int) $memberId;

                        if ($memberId <= 0 || in_array($memberId, $selectedMemberIds, true)) {
                            continue;
                        }

                        $selectedMemberIds[] = $memberId;
                        $role = $validated['team_member_role'][$index] ?? 'Team Member';
                        $notes = $validated['team_member_notes'][$index] ?? null;

                        \App\Models\EventMember::updateOrCreate(
                            [
                                'event_id' => $event->id,
                                'member_id' => $memberId,
                            ],
                            [
                                'role' => $role,
                                'status' => 'assigned',
                                'notes' => $notes,
                                'attendance_status' => 'present',
                                'actual_hours' => 0,
                                'allowance' => 0,
                                'payment_status' => 'pending',
                            ]
                        );
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status booking berhasil diupdate',
        ]);
    }

        public function updateEventMemberAttendance(Request $request, $eventId, $memberId)
    {
        $eventMember = EventMember::where('event_id', $eventId)
            ->where('member_id', $memberId)
            ->firstOrFail();

        $validated = $request->validate([
            'attendance_status' => 'required|in:present,late,absent',
            'actual_hours' => 'required|numeric|min:0|max:24',
            'allowance' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:2000',
        ]);

        $eventMember->attendance_status = $validated['attendance_status'];
        $eventMember->actual_hours = $validated['actual_hours'];
        $eventMember->allowance = $validated['allowance'] ?? 0;
        $eventMember->notes = $validated['notes'] ?? $eventMember->notes;

        $eventMember->save();

        return response()->json([
            'success' => true,
            'message' => 'Tracking member berhasil disimpan.',
            'total_fee' => 'Rp ' . number_format(
                (float) $eventMember->total_fee,
                0,
                ',',
                '.'
            ),
        ]);
    }
}
