<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Member;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    /**
     * Tampilan utama rekapitulasi & pencairan fee kru (Payroll Dashboard).
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $eventId = $request->query('event_id');
        $search = $request->query('search');

        $query = EventMember::with(['event.booking.client', 'event.package', 'member'])
            ->whereNotNull('member_id');

        if (!empty($status) && in_array($status, ['pending', 'approved', 'paid'], true)) {
            $query->where('payment_status', $status);
        }

        if (!empty($eventId)) {
            $query->where('event_id', (int) $eventId);
        }

        if (!empty($search)) {
            $query->whereHas('member', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('call_sign', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $eventMembers = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Hitung akumulasi statistik finansial
        $allRecords = EventMember::query()->get();
        $totalPendingFee = (float) $allRecords->where('payment_status', 'pending')->sum('total_fee');
        $totalApprovedFee = (float) $allRecords->where('payment_status', 'approved')->sum('total_fee');
        $totalPaidFee = (float) $allRecords->where('payment_status', 'paid')->sum('total_fee');
        $totalMembersAssigned = $allRecords->unique('member_id')->count();

        $events = Event::with('booking')
            ->orderBy('event_date', 'desc')
            ->get();

        return view('admin.payroll.index', compact(
            'eventMembers',
            'totalPendingFee',
            'totalApprovedFee',
            'totalPaidFee',
            'totalMembersAssigned',
            'events',
            'status',
            'eventId',
            'search'
        ));
    }

    /**
     * Update cepat status pembayaran per individu (ACC atau Cairkan).
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:pending,approved,paid',
            'notes' => 'nullable|string|max:1000',
        ]);

        $eventMember = EventMember::with('member')->findOrFail($id);
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

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status pembayaran berhasil diperbarui menjadi ' . ucfirst($validated['payment_status']),
                'payment_status' => $eventMember->payment_status,
                'total_fee' => 'Rp ' . number_format((float) $eventMember->total_fee, 0, ',', '.'),
            ]);
        }

        return redirect()->back()->with('success', 'Status honor kru berhasil diperbarui.');
    }

    /**
     * Batch update status pembayaran untuk multiple kru terpilih.
     */
    public function batchUpdate(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:event_members,id',
            'action' => 'required|in:approve,pay',
            'notes' => 'nullable|string|max:1000',
        ]);

        $targetStatus = $validated['action'] === 'approve' ? 'approved' : 'paid';
        $statusLabel = $validated['action'] === 'approve' ? 'ACC Disetujui' : 'Lunas Dicairkan';
        $timestamp = now()->format('d/m/Y H:i');

        $members = EventMember::whereIn('id', $validated['ids'])->get();

        foreach ($members as $em) {
            $em->payment_status = $targetStatus;
            if (!empty($validated['notes'])) {
                $noteEntry = "[{$timestamp} - Batch {$statusLabel}]: " . trim($validated['notes']);
                $em->notes = $em->notes ? $em->notes . "\n" . $noteEntry : $noteEntry;
            }
            $em->save();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => count($members) . ' kru berhasil di-' . $statusLabel . '.',
            ]);
        }

        return redirect()->back()->with('success', count($members) . ' data honor kru berhasil diperbarui.');
    }
}

