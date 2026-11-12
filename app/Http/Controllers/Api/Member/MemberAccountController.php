<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Controller;
use App\Models\EventMember;
use App\Models\Member;
use App\Models\Schedule;
use Illuminate\Http\Request;

class MemberAccountController extends Controller
{
    public function dashboard(Request $request)
    {
        $member = Member::where('user_id', $request->user()->id)->firstOrFail();
        $eventIds = EventMember::where('member_id', $member->id)->pluck('event_id');
        $schedules = Schedule::whereIn('event_id', $eventIds)->with(['event.booking.client', 'vendor'])
            ->orderBy('start_time')->get();

        return response()->json([
            'data' => [
                'member' => $member,
                'schedules' => $schedules,
                'stats' => [
                    'total_schedules' => $schedules->count(),
                    'pending' => $schedules->where('status', 'pending')->count(),
                    'approved' => $schedules->where('status', 'approved')->count(),
                    'rejected' => $schedules->where('status', 'rejected')->count(),
                    'total_events' => $eventIds->count(),
                ],
            ],
        ]);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'call_sign' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'domicile' => ['nullable', 'string', 'max:500'],
            'ht_code' => ['nullable', 'string', 'max:50'],
            'emergency_name' => ['nullable', 'string', 'max:255'],
            'emergency_phone' => ['nullable', 'string', 'max:30'],
        ]);
        $member = Member::where('user_id', $request->user()->id)->firstOrFail();
        $member->update($data + ['name' => $request->user()->name, 'email' => $request->user()->email]);

        return response()->json(['data' => $member->fresh()]);
    }

    public function updateScheduleStatus(Request $request, int $id)
    {
        $data = $request->validate(['status' => ['required', 'in:pending,approved,rejected']]);
        $member = Member::where('user_id', $request->user()->id)->firstOrFail();
        $schedule = Schedule::findOrFail($id);
        $assigned = $schedule->member_id === $member->id || ($schedule->member_id === null
            && EventMember::where('event_id', $schedule->event_id)->where('member_id', $member->id)->exists());
        abort_unless($assigned, 403);
        $schedule->update($data);

        return response()->json(['data' => $schedule->fresh()]);
    }
}
