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
        $user = $request->user();
        $member = Member::where('user_id', $user->id)->firstOrFail();

        $eventMembers = EventMember::where('member_id', $member->id)
            ->with([
                'event.booking.client',
                'event.schedules.vendor.category',
                'event.schedules.member',
                'event.eventMembers.member',
            ])
            ->get();

        $eventIds = $eventMembers->pluck('event_id');
        $coordinatorEventIds = $eventMembers->filter(fn ($em) => $em->isCoordinator())->pluck('event_id');
        $regularEventIds = $eventMembers->reject(fn ($em) => $em->isCoordinator())->pluck('event_id');

        // Koordinator melihat seluruh checklist tugas event;
        // Kru biasa hanya fokus pada tugas miliknya atau tugas umum tanpa PIC.
        $schedules = Schedule::whereIn('event_id', $eventIds)
            ->where(function ($query) use ($coordinatorEventIds, $regularEventIds, $member) {
                if ($coordinatorEventIds->isNotEmpty()) {
                    $query->whereIn('event_id', $coordinatorEventIds);
                }
                if ($regularEventIds->isNotEmpty()) {
                    $query->orWhere(function ($q) use ($regularEventIds, $member) {
                        $q->whereIn('event_id', $regularEventIds)
                            ->where(function ($sub) use ($member) {
                                $sub->where('member_id', $member->id)
                                    ->orWhereNull('member_id');
                            });
                    });
                }
            })
            ->with(['event.booking.client', 'vendor.category', 'member'])
            ->orderBy('start_time', 'asc')
            ->get();

        $stats = [
            'total_schedules' => $schedules->count(),
            'pending' => $schedules->where('status', 'pending')->count(),
            'approved' => $schedules->where('status', 'approved')->count(),
            'rejected' => $schedules->where('status', 'rejected')->count(),
            'total_events' => $eventMembers->count(),
            'coordinator_events' => $eventMembers->filter(fn ($em) => $em->isCoordinator())->count(),
            'approved_fee' => (float) $eventMembers->whereIn('payment_status', ['approved', 'paid'])->sum('total_fee'),
            'estimated_fee' => (float) $eventMembers->sum('total_fee'),
        ];

        return response()->json([
            'data' => [
                'member' => $member,
                'schedules' => $schedules,
                'event_members' => $eventMembers,
                'stats' => $stats,
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

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'data' => $member->fresh(),
        ]);
    }

    public function updateScheduleStatus(Request $request, int $id)
    {
        $data = $request->validate(['status' => ['required', 'in:pending,approved,rejected']]);
        $member = Member::where('user_id', $request->user()->id)->firstOrFail();
        $schedule = Schedule::findOrFail($id);

        $myEventMember = EventMember::where('event_id', $schedule->event_id)->where('member_id', $member->id)->first();
        $isCoordinator = $myEventMember && $myEventMember->isCoordinator();
        $assigned = $myEventMember && ($isCoordinator || (int) $schedule->member_id === (int) $member->id || $schedule->member_id === null);
        abort_unless($assigned, 403, 'Anda tidak memiliki hak akses untuk tugas ini.');

        if ($schedule->member_id === null && $data['status'] === 'approved' && ! $isCoordinator) {
            $data['member_id'] = $member->id;
        }

        $schedule->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Status tugas berhasil diperbarui.',
            'data' => $schedule->fresh(['event.booking.client', 'vendor.category', 'member']),
        ]);
    }

    public function assignScheduleMember(Request $request, int $id)
    {
        $schedule = Schedule::findOrFail($id);
        $member = Member::where('user_id', $request->user()->id)->firstOrFail();

        $myEventMember = EventMember::where('event_id', $schedule->event_id)
            ->where('member_id', $member->id)
            ->first();

        if (!$myEventMember || !$myEventMember->isCoordinator()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Coordinator Utama event yang dapat menugaskan PIC jadwal.',
            ], 403);
        }

        $validated = $request->validate([
            'member_id' => 'nullable|exists:members,id',
        ]);

        if (!empty($validated['member_id'])) {
            $isTeamMember = EventMember::where('event_id', $schedule->event_id)
                ->where('member_id', $validated['member_id'])
                ->exists();

            if (!$isTeamMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member yang dipilih bukan bagian dari tim event ini.',
                ], 422);
            }
        }

        $schedule->update([
            'member_id' => $validated['member_id'] ?: null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Penanggung jawab (PIC) tugas berhasil diperbarui.',
            'data' => $schedule->fresh(['event.booking.client', 'vendor.category', 'member']),
        ]);
    }

    public function updateTeamAttendance(Request $request, int $eventId, int $memberId)
    {
        $member = Member::where('user_id', $request->user()->id)->firstOrFail();

        $myEventMember = EventMember::where('event_id', $eventId)
            ->where('member_id', $member->id)
            ->first();

        if (!$myEventMember || !$myEventMember->isCoordinator()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Coordinator Utama yang dapat menginput kehadiran anggota tim.',
            ], 403);
        }

        $targetEventMember = EventMember::with('member')
            ->where('event_id', $eventId)
            ->where('member_id', $memberId)
            ->firstOrFail();

        if ($targetEventMember->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Bayaran member ini sudah dilunasi oleh Admin dan tidak dapat diubah.',
            ], 422);
        }

        $validated = $request->validate([
            'attendance_status' => 'required|in:present,late,absent',
            'actual_hours' => 'required|numeric|min:0|max:24',
        ]);

        $targetEventMember->attendance_status = $validated['attendance_status'];
        $targetEventMember->actual_hours = $validated['actual_hours'];
        $targetEventMember->status = $validated['attendance_status'] === 'absent' ? 'assigned' : 'completed';
        $targetEventMember->payment_status = 'pending';
        $targetEventMember->save();

        return response()->json([
            'success' => true,
            'message' => 'Laporan kehadiran & jam kerja berhasil disimpan.',
            'data' => [
                'event_member' => $targetEventMember,
                'formatted_fee' => 'Rp ' . number_format((float) $targetEventMember->total_fee, 0, ',', '.'),
            ],
        ]);
    }
}
