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
        $query = Booking::with(['client', 'package', 'events.package', 'vendors'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('venue_name', 'like', "%{$search}%")
                    ->orWhere('venue_address', 'like', "%{$search}%")
                    ->orWhere('venue_city', 'like', "%{$search}%")
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
        foreach ($bookings as $b) {
            $b->syncTotalPrice();
        }

        $eventsByBooking = Event::with(['schedules', 'eventMembers.member', 'package'])
            ->orderBy('event_date', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->groupBy('booking_id')
            ->map(function ($events) {
                $allSchedules = $events->flatMap->schedules;
                $totalSchedules = $allSchedules->count();
                $approvedSchedules = $allSchedules->where('status', 'approved')->count();
                $progress = $totalSchedules > 0 ? ($approvedSchedules / $totalSchedules) * 100 : 0;

                return [
                    'events' => $events->values(),
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
            'booking.package',
            'package',
            'schedules.vendor.category',
            'schedules.member',
            'eventMembers.member',
        ])->findOrFail($id);

        $event->booking?->syncTotalPrice();

        $bookingEvents = Event::with(['schedules', 'eventMembers.member', 'package'])
            ->where('booking_id', $event->booking_id)
            ->orderBy('event_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $totalSchedules = $event->schedules->count();
        $approvedSchedules = $event->schedules->where('status', 'approved')->count();
        $rejectedSchedules = $event->schedules->where('status', 'rejected')->count();
        $pendingSchedules = $event->schedules->where('status', 'pending')->count();
        $progress = $totalSchedules > 0 ? ($approvedSchedules / $totalSchedules) * 100 : 0;

        return view('admin.tracking.show', compact(
            'event',
            'bookingEvents',
            'totalSchedules',
            'approvedSchedules',
            'rejectedSchedules',
            'pendingSchedules',
            'progress'
        ));
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

    public function updateScheduleMember(Request $request, $scheduleId)
    {
        $schedule = Schedule::findOrFail($scheduleId);

        $validated = $request->validate([
            'member_id' => 'nullable|exists:members,id',
        ]);

        if (! empty($validated['member_id'])) {
            $isEventMember = EventMember::where('event_id', $schedule->event_id)
                ->where('member_id', $validated['member_id'])
                ->exists();

            if (! $isEventMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member tersebut belum terdaftar di tim event ini.',
                ], 422);
            }
        }

        $schedule->update([
            'member_id' => $validated['member_id'] ?: null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'PIC tugas berhasil diperbarui.',
        ]);
    }

    public function updateBookingStatus(Request $request, $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'events_team' => 'nullable|array',
            'target_event_id' => 'nullable',
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

        $booking->update(['status' => $validated['status']]);

        if ($validated['status'] === 'approved') {
            // Mode Baru: Per-Event Team Payload (events_team) dari Modal Multi-Event
            if (! empty($validated['events_team']) && is_array($validated['events_team'])) {
                foreach ($validated['events_team'] as $eventId => $teamData) {
                    $event = Event::where('booking_id', $bookingId)
                        ->where('id', (int) $eventId)
                        ->first();

                    if (! $event || ! is_array($teamData)) {
                        continue;
                    }

                    $this->syncSingleEventTeam($event, $teamData);
                }
            } elseif ($request->has('coordinator_id') || $request->has('team_member_id')) {
                // Mode Fallback: Single Event / Semua Event
                $targetEventId = $validated['target_event_id'] ?? null;

                if ($targetEventId && $targetEventId !== 'all') {
                    $targetEvents = Event::where('booking_id', $bookingId)
                        ->where('id', (int) $targetEventId)
                        ->get();
                } else {
                    $targetEvents = Event::where('booking_id', $bookingId)->get();
                }

                foreach ($targetEvents as $event) {
                    $this->syncSingleEventTeam($event, $validated);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status booking & susunan tim per event berhasil disimpan.',
        ]);
    }

    private function syncSingleEventTeam(Event $event, array $data): void
    {
        $eventSelectedMemberIds = [];

        if (! empty($data['coordinator_id'])) {
            $coordinatorId = (int) $data['coordinator_id'];
            $eventSelectedMemberIds[] = $coordinatorId;

            $role = $data['coordinator_role'] ?? 'Lead Coordinator';
            if (! in_array($role, ['Lead Coordinator', 'Coordinator'], true)) {
                $role = 'Lead Coordinator';
            }

            $existingCoord = EventMember::where('event_id', $event->id)
                ->where('member_id', $coordinatorId)
                ->first();

            if ($existingCoord) {
                $existingCoord->update([
                    'role' => $role,
                    'notes' => $data['coordinator_notes'] ?? $existingCoord->notes,
                ]);
            } else {
                EventMember::create([
                    'event_id' => $event->id,
                    'member_id' => $coordinatorId,
                    'role' => $role,
                    'status' => 'assigned',
                    'notes' => $data['coordinator_notes'] ?? null,
                    'attendance_status' => 'present',
                    'actual_hours' => 0,
                    'allowance' => 0,
                    'payment_status' => 'pending',
                ]);
            }
        }

        if (! empty($data['team_member_id']) && is_array($data['team_member_id'])) {
            foreach ($data['team_member_id'] as $index => $memberId) {
                $memberId = (int) $memberId;

                if ($memberId <= 0 || in_array($memberId, $eventSelectedMemberIds, true)) {
                    continue;
                }

                $eventSelectedMemberIds[] = $memberId;
                $role = trim((string) ($data['team_member_role'][$index] ?? '')) ?: 'Team Member';
                $notes = $data['team_member_notes'][$index] ?? null;

                $existingMember = EventMember::where('event_id', $event->id)
                    ->where('member_id', $memberId)
                    ->first();

                if ($existingMember) {
                    $existingMember->update([
                        'role' => $role,
                        'notes' => $notes ?? $existingMember->notes,
                    ]);
                } else {
                    EventMember::create([
                        'event_id' => $event->id,
                        'member_id' => $memberId,
                        'role' => $role,
                        'status' => 'assigned',
                        'notes' => $notes,
                        'attendance_status' => 'present',
                        'actual_hours' => 0,
                        'allowance' => 0,
                        'payment_status' => 'pending',
                    ]);
                }
            }
        }

        if (empty($eventSelectedMemberIds)) {
            EventMember::where('event_id', $event->id)->delete();
            Schedule::where('event_id', $event->id)->update(['member_id' => null]);
        } else {
            EventMember::where('event_id', $event->id)
                ->whereNotIn('member_id', $eventSelectedMemberIds)
                ->delete();
            Schedule::where('event_id', $event->id)
                ->whereNotNull('member_id')
                ->whereNotIn('member_id', $eventSelectedMemberIds)
                ->update(['member_id' => null]);
        }
    }

    public function updateEventMemberAttendance(Request $request, $eventId, $memberId)
    {
        $eventMember = EventMember::with('member')
            ->where('event_id', $eventId)
            ->where('member_id', $memberId)
            ->firstOrFail();

        $validated = $request->validate([
            'attendance_status' => 'required|in:present,late,absent',
            'actual_hours' => 'required|numeric|min:0|max:24',
            'allowance' => 'nullable|numeric|min:0',
            'payment_status' => 'nullable|in:pending,approved,paid',
            'notes' => 'nullable|string|max:2000',
        ]);

        $eventMember->attendance_status = $validated['attendance_status'];
        $eventMember->actual_hours = $validated['actual_hours'];
        $eventMember->allowance = $validated['allowance'] ?? 0;
        $eventMember->payment_status = $validated['payment_status'] ?? $eventMember->payment_status ?? 'pending';
        $eventMember->notes = $validated['notes'] ?? $eventMember->notes;
        $eventMember->status = $validated['attendance_status'] === 'absent' ? 'assigned' : 'completed';

        $eventMember->save();

        return response()->json([
            'success' => true,
            'message' => 'Verifikasi kehadiran & bayaran member berhasil disimpan.',
            'total_fee' => 'Rp ' . number_format(
                (float) $eventMember->total_fee,
                0,
                ',',
                '.'
            ),
            'payment_status' => $eventMember->payment_status,
        ]);
    }

    public function quickUpdateFeeStatus(Request $request, $eventId, $memberId)
    {
        $eventMember = EventMember::with('member')
            ->where('event_id', $eventId)
            ->where('member_id', $memberId)
            ->firstOrFail();

        $validated = $request->validate([
            'payment_status' => 'required|in:pending,approved,paid',
            'notes' => 'nullable|string|max:1000',
        ]);

        $eventMember->payment_status = $validated['payment_status'];
        if (!empty($validated['notes'])) {
            $timestamp = now()->format('d/m/Y H:i');
            $statusLabel = match ($validated['payment_status']) {
                'approved' => 'ACC Disetujui',
                'paid' => 'Lunas Dicairkan',
                default => 'Pending',
            };
            $noteEntry = "[{$timestamp} - {$statusLabel}]: " . trim($validated['notes']);
            $eventMember->notes = $eventMember->notes ? $eventMember->notes . "\n" . $noteEntry : $noteEntry;
        }

        $eventMember->save();

        return response()->json([
            'success' => true,
            'message' => 'Status pembayaran ' . ($eventMember->member?->name ?? 'kru') . ' berhasil diubah menjadi ' . ucfirst($validated['payment_status']),
            'payment_status' => $eventMember->payment_status,
            'total_fee' => 'Rp ' . number_format((float) $eventMember->total_fee, 0, ',', '.'),
        ]);
    }

    public function batchUpdateEventFee(Request $request, $eventId)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve_all,pay_all',
            'notes' => 'nullable|string|max:1000',
        ]);

        $targetStatus = $validated['action'] === 'approve_all' ? 'approved' : 'paid';
        $statusLabel = $validated['action'] === 'approve_all' ? 'ACC Disetujui' : 'Lunas Dicairkan';
        $timestamp = now()->format('d/m/Y H:i');

        $eventMembers = EventMember::where('event_id', $eventId)->get();

        foreach ($eventMembers as $em) {
            $em->payment_status = $targetStatus;
            if (!empty($validated['notes'])) {
                $noteEntry = "[{$timestamp} - {$statusLabel}]: " . trim($validated['notes']);
                $em->notes = $em->notes ? $em->notes . "\n" . $noteEntry : $noteEntry;
            }
            $em->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Semua kru pada acara ini berhasil di-' . $statusLabel . '.',
        ]);
    }
}
