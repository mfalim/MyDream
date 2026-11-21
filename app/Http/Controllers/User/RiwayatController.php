<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Vendor;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    private function approvedBookings(Client $client)
    {
        return Booking::query()
            ->where('client_id', $client->id)
            ->where('status', 'approved');
    }

    public function index(Request $request)
    {
        $client = Client::where('users_id', auth()->id())->firstOrFail();
        $query = $this->approvedBookings($client)->with([
            'package',
            'payments' => fn ($q) => $q->orderByDesc('created_at')->orderByDesc('id'),
            'events' => fn ($q) => $q->orderBy('event_date'),
            'events.schedules' => fn ($q) => $q->whereNotNull('vendor_id')->where('status', 'approved'),
            'events.schedules.vendor.category',
        ]);

        if ($request->filled('search')) {
            $query->whereHas('events', fn ($q) => $q->where('name', 'like', '%' . $request->string('search') . '%'));
        }
        if ($request->filled('from')) {
            $query->whereHas('events', fn ($q) => $q->whereDate('event_date', '>=', $request->input('from')));
        }
        if ($request->filled('to')) {
            $query->whereHas('events', fn ($q) => $q->whereDate('event_date', '<=', $request->input('to')));
        }
        if ($request->filled('vendor')) {
            $vendorId = $request->integer('vendor');
            $query->whereHas('events.schedules', fn ($q) => $q
                ->where('vendor_id', $vendorId)
                ->whereNotNull('vendor_id')
                ->where('status', 'approved'));
        }

        $bookings = $query->orderByDesc('created_at')->paginate(8)->withQueryString();
        $bookings->getCollection()->each->syncPaymentStatus();
        $bookings->load(['payments' => fn ($q) => $q->orderByDesc('created_at')->orderByDesc('id')]);
        $vendors = Vendor::whereHas('schedules', fn ($q) => $q
            ->whereNotNull('event_id')
            ->where('status', 'approved')
            ->whereHas('event.booking', fn ($booking) => $booking
                ->where('client_id', $client->id)
                ->where('status', 'approved')))
            ->orderBy('name')->get(['id', 'name']);

        return view('user.riwayat.index', compact('bookings', 'vendors'));
    }

    public function show(int $booking)
    {
        $client = Client::where('users_id', auth()->id())->firstOrFail();
        $booking = Booking::query()
            ->where('client_id', $client->id)
            ->with([
                'package',
                'payments' => fn ($q) => $q->orderByDesc('created_at')->orderByDesc('id'),
                'events' => fn ($q) => $q->orderBy('event_date'),
                'events.schedules' => fn ($q) => $q->whereNotNull('vendor_id')->where('status', 'approved'),
                'events.schedules.vendor.category',
                'payments.events',
            ])
            ->findOrFail($booking);

        $booking->syncPaymentStatus();
        $booking->load([
            'payments' => fn ($q) => $q->orderByDesc('created_at')->orderByDesc('id'),
            'payments.events',
        ]);

        $paymentSummary = $booking->paymentSummary();
        $options = $booking->status === 'rejected' ? [] : $this->paymentOptions($booking);
        $snapClientKey = config('services.midtrans.client_key');
        $snapScriptUrl = config('services.midtrans.is_production')
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';

        return view('user.riwayat.show', compact(
            'booking',
            'paymentSummary',
            'options',
            'snapClientKey',
            'snapScriptUrl'
        ));
    }

    private function paymentOptions(Booking $booking): array
    {
        $total = (float) $booking->total_price;
        $paid = (float) $booking->paid_amount;
        $remaining = max(0, $total - $paid);
        $percent = max(1, min(100, (int) config('mydream.dp_percent', 10)));

        if ($total <= 0 || $remaining <= 0) {
            return [];
        }

        if ($paid <= 0) {
            $dpAmount = min((int) ceil($total * $percent / 100), (int) round($remaining));

            $options = [
                'dp' => [
                    'label' => "DP {$percent}%",
                    'amount' => $dpAmount,
                    'note' => 'Kunci tanggal sekarang, sisanya dilunasi sebelum hari acara.',
                ],
            ];

            if ($dpAmount < (int) round($remaining)) {
                $options['full'] = [
                    'label' => 'Bayar Lunas',
                    'amount' => (int) round($remaining),
                    'note' => 'Langsung lunasi seluruh tagihan dalam satu transaksi.',
                ];
            }

            return $options;
        }

        return [
            'remaining' => [
                'label' => 'Pelunasan Sisa',
                'amount' => (int) round($remaining),
                'note' => 'Lunasi sisa tagihan agar seluruh jadwal vendor terkunci penuh.',
            ],
        ];
    }
}
