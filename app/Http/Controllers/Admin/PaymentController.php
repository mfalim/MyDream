<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\EventMember;
use App\Models\Payment;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Admin: Pusat Keuangan & Alur Kas Terpadu
 * (Verifikasi Inflow Klien, Input Bayar Manual, Piutang Booking, Settlement Vendor + Bukti, Neraca Acara, & Kwitansi/Invoice).
 */
class PaymentController extends Controller
{
    private const STATUSES = [
        'verified' => 'Terverifikasi',
        'initiated' => 'Diproses Midtrans',
        'pending' => 'Menunggu Verifikasi',
        'rejected' => 'Ditolak / Kedaluwarsa',
    ];

    public function index(Request $request): View
    {
        // Sinkronkan transaksi Midtrans aktif (maks. 1x per 60 detik & tidak saat hanya pindah tab AJAX)
        if (! $request->ajax() && \Illuminate\Support\Facades\Cache::add('admin_payments_midtrans_sync', 1, 60)) {
            $activeMidtrans = Payment::where('method', 'midtrans')
                ->whereIn('status', ['initiated', 'pending'])
                ->where('created_at', '>=', now()->subDay())
                ->latest('id')
                ->limit(5)
                ->get();
            foreach ($activeMidtrans as $mp) {
                if ($mp->syncWithMidtrans()) {
                    $mp->booking?->syncPaymentStatus();
                }
            }
        }

        $activeTab = $request->query('tab', 'bookings');
        if (! in_array($activeTab, ['bookings', 'vendor_payouts', 'report'], true)) {
            $activeTab = 'bookings';
        }

        $status = array_key_exists($request->query('status'), self::STATUSES)
            ? $request->query('status')
            : 'verified';

        $vendorStatusFilter = $request->query('vendor_status', '');
        $bookingFilter = $request->query('booking_id', '');

        // 1. Data Inflow Pembayaran Klien (Tabel payments)
        $payments = Payment::with(['booking.client', 'booking.package', 'events'])
            ->where('status', $status)
            ->latest('id')
            ->get();

        $counts = Payment::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // 2. Seluruh Booking dengan Relasi Keuangan Lengkap
        $bookings = Booking::with([
            'client',
            'package',
            'payments' => fn ($q) => $q->with('events')->latest('id'),
            'events.schedules.vendor.category',
            'events.eventMembers.member',
        ])->latest('id')->get();

        foreach ($bookings as $b) {
            if ((float) $b->total_price <= 0) {
                $b->syncTotalPrice();
            }
        }

        // 3. Data Pencairan Vendor (Tabel schedules yang memiliki vendor_id)
        $vendorSchedulesQuery = Schedule::with([
            'vendor.category',
            'event.booking.client',
            'event.package',
        ])->whereNotNull('vendor_id')->latest('id');

        if (in_array($vendorStatusFilter, ['unpaid', 'dp_paid', 'paid'], true)) {
            $vendorSchedulesQuery->where('payment_status', $vendorStatusFilter);
        }

        if (! empty($bookingFilter)) {
            $vendorSchedulesQuery->whereHas('event', fn ($q) => $q->where('booking_id', (int) $bookingFilter));
        }

        $vendorSchedules = $vendorSchedulesQuery->get();

        // Hitung Ringkasan Global KPI Keuangan
        $allVendorSchedules = Schedule::with('vendor')->whereNotNull('vendor_id')->get();
        $allCrewRecords = EventMember::whereNotNull('member_id')->get();

        $totalContractValue = (float) $bookings->where('status', '!=', 'rejected')->sum('total_price');
        $totalClientInflow = (float) $bookings->sum('paid_amount');
        $totalClientReceivables = max(0, $totalContractValue - $totalClientInflow);
        $pendingVerificationAmount = (float) Payment::where('status', 'pending')->sum('amount');

        $totalVendorObligation = (float) $allVendorSchedules->sum(fn (Schedule $s) => $s->effective_fee);
        $totalVendorDisbursed = (float) $allVendorSchedules->sum('paid_amount');
        $totalVendorRemaining = max(0, $totalVendorObligation - $totalVendorDisbursed);

        $totalCrewObligation = (float) $allCrewRecords->sum('total_fee');
        $totalCrewDisbursed = (float) $allCrewRecords->where('payment_status', 'paid')->sum('total_fee');
        $totalCrewRemaining = max(0, $totalCrewObligation - $totalCrewDisbursed);

        $netCashOnHand = $totalClientInflow - ($totalVendorDisbursed + $totalCrewDisbursed);

        // Hitung Ledger per Booking untuk Tab 4
        $projectLedgers = $bookings->map(function (Booking $booking) {
            $events = $booking->events;
            $schedules = $events->flatMap->schedules->whereNotNull('vendor_id');
            $crewMembers = $events->flatMap->eventMembers->whereNotNull('member_id');

            $contractTotal = (float) $booking->total_price;
            $clientPaid = (float) $booking->paid_amount;
            $clientRemaining = max(0, $contractTotal - $clientPaid);

            $catalogVendorTotal = (float) $schedules->sum(fn (Schedule $s) => (float) ($s->price ?? $s->vendor?->price ?? 0));
            $vendorTotalFee = (float) $schedules->sum(fn (Schedule $s) => $s->effective_fee);
            $vendorPaid = (float) $schedules->sum('paid_amount');
            $vendorRemaining = max(0, $vendorTotalFee - $vendorPaid);

            $crewTotalFee = (float) $crewMembers->sum('total_fee');
            $crewPaid = (float) $crewMembers->where('payment_status', 'paid')->sum('total_fee');
            $crewRemaining = max(0, $crewTotalFee - $crewPaid);

            $totalOutflowCommitted = $vendorTotalFee + $crewTotalFee;
            $totalOutflowPaid = $vendorPaid + $crewPaid;
            $cashBalanceNow = $clientPaid - $totalOutflowPaid;
            $projectedMargin = $contractTotal - $totalOutflowCommitted;

            return [
                'booking' => $booking,
                'events' => $events,
                'payment_summary' => $booking->paymentSummary(),
                'vendor_count' => $schedules->count(),
                'crew_count' => $crewMembers->count(),
                'contract_total' => $contractTotal,
                'catalog_vendor_total' => $catalogVendorTotal,
                'client_paid' => $clientPaid,
                'client_remaining' => $clientRemaining,
                'vendor_total' => $vendorTotalFee,
                'vendor_paid' => $vendorPaid,
                'vendor_remaining' => $vendorRemaining,
                'crew_total' => $crewTotalFee,
                'crew_paid' => $crewPaid,
                'crew_remaining' => $crewRemaining,
                'cash_balance_now' => $cashBalanceNow,
                'projected_margin' => $projectedMargin,
            ];
        });

        // Filter Laporan Organizer (Periode Tanggal Acara/Booking & Status Pembayaran)
        $reportFrom = (string) $request->query('report_from', '');
        $reportTo = (string) $request->query('report_to', '');
        $reportStatus = (string) $request->query('report_status', '');

        $reportLedgers = $projectLedgers->filter(function (array $row) use ($reportFrom, $reportTo, $reportStatus) {
            $b = $row['booking'];
            $refDate = $row['events']->pluck('event_date')->filter()->sort()->first() ?? $b->created_at;
            $dateStr = $refDate ? \Carbon\Carbon::parse($refDate)->toDateString() : null;

            if ($reportFrom !== '' && ($dateStr === null || $dateStr < $reportFrom)) {
                return false;
            }
            if ($reportTo !== '' && ($dateStr === null || $dateStr > $reportTo)) {
                return false;
            }
            if ($reportStatus !== '') {
                $pStatus = $b->payment_status ?: 'unpaid';
                if ($reportStatus === 'unpaid') {
                    if (! in_array($pStatus, ['unpaid', 'awaiting_verification'], true)) {
                        return false;
                    }
                } elseif ($pStatus !== $reportStatus) {
                    return false;
                }
            }

            return true;
        })->values();

        $rContract = (float) $reportLedgers->sum('contract_total');
        $rClientPaid = (float) $reportLedgers->sum('client_paid');
        $rClientRem = (float) $reportLedgers->sum('client_remaining');
        $rVendorTotal = (float) $reportLedgers->sum('vendor_total');
        $rVendorPaid = (float) $reportLedgers->sum('vendor_paid');
        $rCrewTotal = (float) $reportLedgers->sum('crew_total');
        $rCrewPaid = (float) $reportLedgers->sum('crew_paid');
        $rCashBalance = (float) $reportLedgers->sum('cash_balance_now');
        $rMargin = (float) $reportLedgers->sum('projected_margin');

        $reportSummary = [
            'count' => $reportLedgers->count(),
            'contract_total' => $rContract,
            'client_paid' => $rClientPaid,
            'client_remaining' => $rClientRem,
            'vendor_total' => $rVendorTotal,
            'vendor_paid' => $rVendorPaid,
            'crew_total' => $rCrewTotal,
            'crew_paid' => $rCrewPaid,
            'outflow_committed' => $rVendorTotal + $rCrewTotal,
            'outflow_disbursed' => $rVendorPaid + $rCrewPaid,
            'outflow_remaining' => max(0, ($rVendorTotal + $rCrewTotal) - ($rVendorPaid + $rCrewPaid)),
            'cash_balance' => $rCashBalance,
            'projected_margin' => $rMargin,
            'margin_pct' => $rContract > 0 ? round(($rMargin / $rContract) * 100, 1) : 0,
        ];

        return view('admin.payments.index', [
            'activeTab' => $activeTab,
            'payments' => $payments,
            'status' => $status,
            'statuses' => self::STATUSES,
            'counts' => $counts,
            'bookings' => $bookings,
            'vendorSchedules' => $vendorSchedules,
            'vendorStatusFilter' => $vendorStatusFilter,
            'bookingFilter' => $bookingFilter,
            'projectLedgers' => $projectLedgers,
            'reportLedgers' => $reportLedgers,
            'reportSummary' => $reportSummary,
            'reportFrom' => $reportFrom,
            'reportTo' => $reportTo,
            'reportStatus' => $reportStatus,
            'dpPercent' => max(1, min(100, (int) config('mydream.dp_percent', 10))),
            'summary' => [
                'total_contract' => $totalContractValue,
                'client_inflow' => $totalClientInflow,
                'client_receivables' => $totalClientReceivables,
                'pending_verification_amount' => $pendingVerificationAmount,
                'vendor_obligation' => $totalVendorObligation,
                'vendor_disbursed' => $totalVendorDisbursed,
                'vendor_remaining' => $totalVendorRemaining,
                'crew_obligation' => $totalCrewObligation,
                'crew_disbursed' => $totalCrewDisbursed,
                'crew_remaining' => $totalCrewRemaining,
                'net_cash_on_hand' => $netCashOnHand,
            ],
        ]);
    }

    /**
     * Input pembayaran klien secara manual oleh Admin (Walk-in / Tunai / Transfer via WA).
     */
    public function storeManualPayment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'type' => 'required|in:dp,remaining,full',
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:bank_transfer,cash,edc',
            'bank_info' => 'nullable|string|max:255',
            'proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'admin_note' => 'nullable|string|max:500',
        ], [
            'booking_id.required' => 'Pilih booking klien terlebih dahulu.',
            'amount.required' => 'Nominal pembayaran wajib diisi.',
            'amount.min' => 'Nominal pembayaran harus lebih dari Rp 0.',
            'proof.mimes' => 'Bukti pembayaran harus berupa JPG, PNG, atau PDF.',
            'proof.max' => 'Ukuran file bukti maksimal 2 MB.',
        ]);

        $booking = Booking::findOrFail($validated['booking_id']);
        $proofPath = $request->file('proof')?->store('payment-proofs', 'public');

        $methodLabel = match ($validated['method']) {
            'cash' => 'Tunai Kantor WO',
            'edc' => 'Debit / QRIS Kantor WO',
            default => 'Transfer Bank',
        };

        $bankInfo = filled($validated['bank_info'] ?? null)
            ? $validated['bank_info']
            : $methodLabel . ' (Dicatat Admin)';

        DB::transaction(function () use ($booking, $validated, $proofPath, $bankInfo) {
            $booking->ensureInvoiceNumber();

            $payment = Payment::create([
                'booking_id' => $booking->id,
                'type' => $validated['type'],
                'amount' => (float) $validated['amount'],
                'method' => $validated['method'],
                'bank_info' => $bankInfo,
                'status' => 'verified',
                'proof_path' => $proofPath,
                'admin_note' => $validated['admin_note'] ?? 'Diverifikasi langsung oleh Admin.',
                'verified_at' => now(),
            ]);

            $payment->recordLifecycleEvent(
                'admin_manual_entry',
                null,
                null,
                'Pembayaran manual dicatat dan diverifikasi oleh Admin.'
            );

            $booking->syncPaymentStatus();
        });

        $refLabel = $booking->invoice_number ?: ('Booking #' . $booking->id);
        $formattedAmount = 'Rp ' . number_format((float) $validated['amount'], 0, ',', '.');
        app(\App\Services\NotificationService::class)->notifyBookingClient(
            $booking,
            'payment',
            'Pembayaran Dicatat & Diverifikasi',
            "Pembayaran {$formattedAmount} untuk {$refLabel} telah dicatat dan diverifikasi oleh Admin.",
            '/my-bookings'
        );

        return redirect()
            ->route('admin.payments.index', ['tab' => 'bookings'])
            ->with('success', "Pembayaran manual untuk Booking #{$booking->id} berhasil dicatat dan diverifikasi.");
    }

    /**
     * Tampilan Invoice & Kwitansi Resmi per Booking (siap cetak / PDF).
     */
    public function invoice(Booking $booking): View
    {
        $booking->ensureInvoiceNumber();
        $booking->load([
            'client',
            'package',
            'payments' => fn ($q) => $q->orderBy('id'),
            'events.schedules.vendor.category',
        ]);

        return view('admin.payments.invoice', [
            'booking' => $booking,
            'bank' => config('mydream.bank'),
        ]);
    }

    public function verify(Payment $payment): RedirectResponse
    {
        return $this->decide($payment, 'verified', null);
    }

    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        $request->validate(
            ['admin_note' => 'nullable|string|max:500'],
            ['admin_note.max' => 'Catatan maksimal 500 karakter']
        );

        return $this->decide($payment, 'rejected', $request->input('admin_note'));
    }

    /**
     * Pencairan dana / update settlement ke Vendor rekanan pada tabel schedules (+ Upload Bukti Transfer & Rekening Vendor).
     */
    public function updateVendorPayout(Request $request, Schedule $schedule): RedirectResponse
    {
        if (! $schedule->vendor_id) {
            return back()->with('error', 'Jadwal ini bukan merupakan penugasan vendor.');
        }

        $validated = $request->validate([
            'agreed_fee' => 'required|numeric|min:0',
            'payout_scheme' => 'required|in:unpaid,dp_50,paid_full,custom',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_notes' => 'nullable|string|max:1000',
            'payout_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'bank_name' => 'nullable|string|max:80',
            'bank_account_number' => 'nullable|string|max:60',
            'bank_account_holder' => 'nullable|string|max:120',
        ]);

        $agreedFee = (float) $validated['agreed_fee'];
        $scheme = $validated['payout_scheme'];

        if ($scheme === 'unpaid') {
            $paidAmount = 0;
            $paymentStatus = 'unpaid';
        } elseif ($scheme === 'dp_50') {
            $paidAmount = round($agreedFee * 0.5, 2);
            $paymentStatus = $paidAmount > 0 ? 'dp_paid' : 'unpaid';
        } elseif ($scheme === 'paid_full') {
            $paidAmount = $agreedFee;
            $paymentStatus = 'paid';
        } else {
            $paidAmount = min($agreedFee, max(0, (float) ($validated['paid_amount'] ?? 0)));
            $paymentStatus = match (true) {
                $agreedFee > 0 && $paidAmount >= $agreedFee => 'paid',
                $paidAmount > 0 => 'dp_paid',
                default => 'unpaid',
            };
        }

        $proofPath = $schedule->payout_proof_path;
        if ($request->hasFile('payout_proof')) {
            $proofPath = $request->file('payout_proof')->store('vendor-payout-proofs', 'public');
        }

        if ($schedule->vendor && ($request->filled('bank_name') || $request->filled('bank_account_number'))) {
            $schedule->vendor->update([
                'bank_name' => $validated['bank_name'] ?: $schedule->vendor->bank_name,
                'bank_account_number' => $validated['bank_account_number'] ?: $schedule->vendor->bank_account_number,
                'bank_account_holder' => $validated['bank_account_holder'] ?: $schedule->vendor->bank_account_holder,
            ]);
        }

        $schedule->update([
            'agreed_fee' => $agreedFee,
            'paid_amount' => $paidAmount,
            'payment_status' => $paymentStatus,
            'paid_at' => $paidAmount > 0 ? now() : null,
            'payment_notes' => $validated['payment_notes'] ?? $schedule->payment_notes,
            'payout_proof_path' => $proofPath,
        ]);

        $vendorName = $schedule->vendor?->name ?? $schedule->activity;

        return redirect()
            ->route('admin.payments.index', [
                'tab' => 'vendor_payouts',
                'vendor_status' => $request->input('redirect_vendor_status', ''),
                'booking_id' => $request->input('redirect_booking_id', ''),
            ])
            ->with('success', "Status pencairan & bukti transfer vendor {$vendorName} berhasil diperbarui.");
    }

    private function decide(Payment $payment, string $status, ?string $note): RedirectResponse
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Pembayaran ini sudah diproses.');
        }

        DB::transaction(function () use ($payment, $status, $note) {
            $previousStatus = $payment->status;
            $previousTransactionStatus = $payment->transaction_status;
            $payment->update([
                'status' => $status,
                'admin_note' => $note,
                'verified_at' => now(),
            ]);
            $payment->recordLifecycleEvent(
                'admin_manual_review',
                $previousStatus,
                $previousTransactionStatus,
                $status === 'verified' ? 'Pembayaran transfer diverifikasi admin.' : 'Pembayaran transfer ditolak admin.',
            );
            $payment->booking->syncPaymentStatus();
        });

        $booking = $payment->booking;
        $refLabel = $booking->invoice_number ?: ('Booking #' . $booking->id);
        $formattedAmount = 'Rp ' . number_format((float) $payment->amount, 0, ',', '.');
        $noteSuffix = filled($note) ? " Catatan: {$note}" : '';
        app(\App\Services\NotificationService::class)->notifyBookingClient(
            $booking,
            'payment',
            $status === 'verified' ? 'Pembayaran Diverifikasi Admin' : 'Pembayaran Ditolak',
            $status === 'verified'
                ? "Pembayaran {$formattedAmount} untuk {$refLabel} telah diverifikasi.{$noteSuffix}"
                : "Bukti pembayaran {$formattedAmount} untuk {$refLabel} ditolak Admin.{$noteSuffix}",
            '/my-bookings'
        );

        return redirect()
            ->route('admin.payments.index', ['tab' => 'verification', 'status' => 'pending'])
            ->with('success', $status === 'verified' ? 'Pembayaran klien berhasil diverifikasi dan saldo pesanan diperbarui.' : 'Pembayaran klien ditolak.');
    }
}
