<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\EventMember;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $member = Member::where('user_id', $user->id)->first();

        if (!$member) {
            abort(403, 'Akses ditolak. Anda bukan member organizer.');
        }

        $eventIds = EventMember::where('member_id', $member->id)->pluck('event_id');

        $schedules = Schedule::whereIn('event_id', $eventIds)
            ->whereNotNull('vendor_id')
            ->with(['event.booking.client', 'vendor'])
            ->orderBy('start_time', 'asc')
            ->get();

        $eventMembers = EventMember::where('member_id', $member->id)
            ->with(['event.booking.client', 'event.schedules.vendor'])
            ->get();

        $stats = [
            'total_schedules' => $schedules->count(),
            'pending' => $schedules->where('status', 'pending')->count(),
            'approved' => $schedules->where('status', 'approved')->count(),
            'rejected' => $schedules->where('status', 'rejected')->count(),
            'total_events' => $eventMembers->count(),
        ];

        return view('member.dashboard', compact('member', 'schedules', 'eventMembers', 'stats'));
    }

    public function updateScheduleStatus(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        $user = Auth::user();
        $member = Member::where('user_id', $user->id)->first();

        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $allowed = false;

        if ($schedule->member_id !== null) {
            if ($schedule->member_id === $member->id) {
                $allowed = true;
            }
        } else {
            // If schedule has no explicit member assigned, allow update
            // for members who are part of the event (assigned via event_members).
            $isEventMember = EventMember::where('event_id', $schedule->event_id)
                ->where('member_id', $member->id)
                ->exists();

            if ($isEventMember) {
                $allowed = true;
            }
        }

        if (!$allowed) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $schedule->update(['status' => $validated['status']]);

        return response()->json(['success' => true, 'message' => 'Status berhasil diupdate']);
    }
}
