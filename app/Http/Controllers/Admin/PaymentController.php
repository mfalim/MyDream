<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/** Admin: verifikasi pembayaran transfer dari client. */
class PaymentController extends Controller
{
    private const STATUSES = ['pending' => 'Menunggu', 'verified' => 'Terverifikasi', 'rejected' => 'Ditolak'];

    public function index(Request $request): View
    {
        $status = array_key_exists($request->query('status'), self::STATUSES) ? $request->query('status') : 'pending';

        return view('admin.payments.index', [
            'payments' => Payment::with(['booking.client', 'booking.package'])
                ->where('status', $status)
                ->latest('id')
                ->get(),
            'status' => $status,
            'statuses' => self::STATUSES,
            'counts' => Payment::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }

    public function verify(Payment $payment): RedirectResponse
    {
        return $this->decide($payment, 'verified', null);
    }

    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        $request->validate(['admin_note' => 'nullable|string|max:500'], ['admin_note.max' => 'Catatan maksimal 500 karakter']);

        return $this->decide($payment, 'rejected', $request->input('admin_note'));
    }

    private function decide(Payment $payment, string $status, ?string $note): RedirectResponse
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Pembayaran ini sudah diproses.');
        }

        DB::transaction(function () use ($payment, $status, $note) {
            $payment->update(['status' => $status, 'admin_note' => $note, 'verified_at' => now()]);
            $payment->booking->syncPaymentStatus();
        });

        return redirect()
            ->route('admin.payments.index', ['status' => 'pending'])
            ->with('success', $status === 'verified' ? 'Pembayaran diverifikasi.' : 'Pembayaran ditolak.');
    }
}
