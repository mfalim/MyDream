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
    public function index(Request $request)
    {
        $user = Auth::user();
        $member = Member::where('user_id', $user->id)->first();

        if ($user->role !== 'member') {
            abort(403, 'Akses ditolak. Anda bukan member organizer.');
        }

        if (!$member) {
            return redirect()->route('member.profile.complete');
        }

        // Jika call_sign belum diisi, arahkan ke form pelengkapan profil.
        if (empty($member->call_sign)) {
            return redirect()->route('member.profile.complete');
        }

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

        $myEventMember = EventMember::where('event_id', $schedule->event_id)
            ->where('member_id', $member->id)
            ->first();

        if (!$myEventMember) {
            return response()->json(['success' => false, 'message' => 'Anda tidak terdaftar pada event ini.'], 403);
        }

        $isCoordinator = $myEventMember->isCoordinator();
        $allowed = $isCoordinator
            || $schedule->member_id === null
            || (int) $schedule->member_id === (int) $member->id;

        if (!$allowed) {
            return response()->json([
                'success' => false,
                'message' => 'Tugas ini ditugaskan ke anggota tim lain. Hanya PIC terkait atau Coordinator Utama yang dapat mengubahnya.',
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $updateData = ['status' => $validated['status']];

        // Jika belum ada PIC pada schedule tersebut dan member mencentang selesai, catat member sebagai PIC pelaksana
        if ($schedule->member_id === null && $validated['status'] === 'approved' && !$isCoordinator) {
            $updateData['member_id'] = $member->id;
        }

        $schedule->update($updateData);

        return response()->json(['success' => true, 'message' => 'Status tugas berhasil diperbarui.']);
    }

    public function assignScheduleMember(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        $user = Auth::user();
        $member = Member::where('user_id', $user->id)->first();

        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

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
        ]);
    }

    public function updateTeamAttendance(Request $request, $eventId, $memberId)
    {
        $user = Auth::user();
        $member = Member::where('user_id', $user->id)->first();

        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

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
            'message' => 'Laporan kehadiran & jam kerja berhasil disimpan (menunggu ACC bayaran Admin).',
            'total_fee' => 'Rp ' . number_format((float) $targetEventMember->total_fee, 0, ',', '.'),
        ]);
    }
}
