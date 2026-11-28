<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Member;
use App\Services\NotificationService;
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
     * Update cepat status pembayaran per individu (ACC atau Cairkan + Upload Bukti Transfer).
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:pending,approved,paid',
            'notes' => 'nullable|string|max:1000',
            'payout_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'bank_name' => 'nullable|string|max:80',
            'bank_account_number' => 'nullable|string|max:60',
            'bank_account_holder' => 'nullable|string|max:120',
        ]);

        $eventMember = EventMember::with(['member.user', 'event'])->findOrFail($id);
        $previousStatus = $eventMember->payment_status;
        $eventMember->payment_status = $validated['payment_status'];

        if ($validated['payment_status'] === 'paid') {
            $eventMember->paid_at = now();
        }

        if ($request->hasFile('payout_proof')) {
            $eventMember->payout_proof_path = $request->file('payout_proof')->store('payroll-proofs', 'public');
        }

        if ($eventMember->member && ($request->filled('bank_name') || $request->filled('bank_account_number'))) {
            $eventMember->member->update([
                'bank_name' => $validated['bank_name'] ?: $eventMember->member->bank_name,
                'bank_account_number' => $validated['bank_account_number'] ?: $eventMember->member->bank_account_number,
                'bank_account_holder' => $validated['bank_account_holder'] ?: $eventMember->member->bank_account_holder,
            ]);
        }

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

        if ($previousStatus !== $eventMember->payment_status && in_array($eventMember->payment_status, ['approved', 'paid'], true)) {
            $feeText = 'Rp ' . number_format((float) $eventMember->total_fee, 0, ',', '.');
            $eventTitle = $eventMember->event?->title ?? 'Event';
            $isPaid = $eventMember->payment_status === 'paid';
            app(NotificationService::class)->notifyMember(
                $eventMember->member,
                'payment',
                $isPaid ? 'Honorarium Telah Dicairkan' : 'Honorarium Disetujui (ACC)',
                $isPaid
                    ? "Honorarium {$feeText} untuk {$eventTitle} telah ditransfer oleh Admin."
                    : "Honorarium {$feeText} untuk {$eventTitle} telah di-ACC Admin dan siap dicairkan.",
                '/member/dashboard'
            );
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status pembayaran berhasil diperbarui menjadi ' . ucfirst($validated['payment_status']),
                'payment_status' => $eventMember->payment_status,
                'total_fee' => 'Rp ' . number_format((float) $eventMember->total_fee, 0, ',', '.'),
                'payout_proof_url' => $eventMember->payout_proof_url,
            ]);
        }

        return redirect()->back()->with('success', 'Status & bukti pencairan honor kru berhasil diperbarui.');
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

        $members = EventMember::with(['member.user', 'event'])->whereIn('id', $validated['ids'])->get();
        $notifier = app(NotificationService::class);

        foreach ($members as $em) {
            $prevStatus = $em->payment_status;
            $em->payment_status = $targetStatus;
            if ($targetStatus === 'paid') {
                $em->paid_at = now();
            }
            if (!empty($validated['notes'])) {
                $noteEntry = "[{$timestamp} - Batch {$statusLabel}]: " . trim($validated['notes']);
                $em->notes = $em->notes ? $em->notes . "\n" . $noteEntry : $noteEntry;
            }
            $em->save();

            if ($prevStatus !== $targetStatus) {
                $feeText = 'Rp ' . number_format((float) $em->total_fee, 0, ',', '.');
                $eventTitle = $em->event?->title ?? 'Event';
                $isPaid = $targetStatus === 'paid';
                $notifier->notifyMember(
                    $em->member,
                    'payment',
                    $isPaid ? 'Honorarium Telah Dicairkan' : 'Honorarium Disetujui (ACC)',
                    $isPaid
                        ? "Honorarium {$feeText} untuk {$eventTitle} telah ditransfer oleh Admin."
                        : "Honorarium {$feeText} untuk {$eventTitle} telah di-ACC Admin dan siap dicairkan.",
                    '/member/dashboard'
                );
            }
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

