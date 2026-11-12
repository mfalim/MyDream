<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Event;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function dashboard(Request $request)
    {
        $client = Client::where('users_id', $request->user()->id)->first();
        $bookings = $client
            ? $client->bookings()->with(['events.schedules', 'payments'])->latest()->limit(5)->get()
            : collect();

        return response()->json([
            'data' => [
                'profile' => $client,
                'booking_count' => $client?->bookings()->count() ?? 0,
                'upcoming_events' => $client
                    ? Event::with('schedules.vendor')->whereHas('booking', fn ($query) => $query->where('client_id', $client->id))
                        ->whereDate('event_date', '>=', today())->orderBy('event_date')->limit(5)->get()
                    : collect(),
                'recent_bookings' => $bookings,
            ],
        ]);
    }

    public function showProfile(Request $request)
    {
        return response()->json(['data' => Client::where('users_id', $request->user()->id)->first()]);
    }

    public function saveProfile(Request $request)
    {
        $data = $request->validate([
            'groom_name' => ['required', 'string', 'max:255'],
            'bride_name' => ['required', 'string', 'max:255'],
            'groom_phone' => ['required', 'regex:/^[0-9]{10,15}$/'],
            'bride_phone' => ['required', 'regex:/^[0-9]{10,15}$/'],
            'email' => ['required', 'email', 'max:255'],
        ]);
        $client = Client::updateOrCreate(['users_id' => $request->user()->id], $data);

        return response()->json(['data' => $client], 200);
    }

    public function bookings(Request $request)
    {
        $client = Client::where('users_id', $request->user()->id)->firstOrFail();

        return response()->json($client->bookings()->with(['package', 'events.schedules.vendor', 'payments'])->latest()->paginate(15));
    }

    public function bookingPayments(Request $request, int $bookingId)
    {
        $booking = $this->ownedBooking($request, $bookingId);
        $booking->syncPaymentStatus();
        $booking->load('payments');
        $total = (float) $booking->total_price;
        $paid = (float) $booking->paid_amount;

        return response()->json([
            'data' => $booking,
            'summary' => [
                'total' => $total,
                'paid' => $paid,
                'pending' => (float) $booking->payments->where('status', 'pending')->sum('amount'),
                'remaining' => max(0, $total - $paid),
                'payment_status' => $booking->payment_status,
            ],
        ]);
    }

    public function submitPayment(Request $request, int $bookingId)
    {
        $booking = $this->ownedBooking($request, $bookingId);
        $booking->syncPaymentStatus();
        $total = (float) $booking->total_price;
        $paid = (float) $booking->paid_amount;
        $percent = max(1, min(100, (int) config('mydream.dp_percent')));
        $options = match ($booking->payment_status) {
            'unpaid' => ['dp' => (int) ceil($total * $percent / 100), 'full' => (int) round($total)],
            'dp_paid' => ['remaining' => (int) round(max(0, $total - $paid))],
            default => [],
        };
        abort_if($total <= 0 || ! $options, 422, 'No payment is due.');
        $bank = config('mydream.bank');
        abort_unless(filled($bank['name'] ?? null) && filled($bank['number'] ?? null), 422, 'Bank transfer details are not configured.');
        $data = $request->validate([
            'scheme' => ['required', 'in:' . implode(',', array_keys($options))],
            'proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);
        $proof = $request->file('proof')?->store('payment-proofs', 'public');
        $payment = DB::transaction(function () use ($booking, $data, $options, $bank, $proof) {
            $booking->ensureInvoiceNumber();
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'type' => $data['scheme'],
                'amount' => $options[$data['scheme']],
                'method' => 'bank_transfer',
                'bank_info' => trim(($bank['name'] ?? '') . ' ' . ($bank['number'] ?? '') . ' ' . ($bank['holder'] ?? '')),
                'status' => 'pending',
                'proof_path' => $proof,
            ]);
            $booking->syncPaymentStatus();

            return $payment;
        });

        return response()->json(['data' => $payment, 'booking' => $booking->fresh()], 201);
    }

    public function events(Request $request)
    {
        $client = Client::where('users_id', $request->user()->id)->firstOrFail();
        $events = Event::with(['booking.package', 'package', 'schedules.vendor.category'])
            ->whereHas('booking', fn ($query) => $query->where('client_id', $client->id))
            ->when($request->filled('month'), function ($query) use ($request) {
                $month = $request->validate(['month' => ['date_format:Y-m']])['month'];
                $query->whereYear('event_date', substr($month, 0, 4))->whereMonth('event_date', substr($month, 5, 2));
            })
            ->orderByDesc('event_date')->get();

        return response()->json(['data' => $events]);
    }

    public function createEvent(Request $request)
    {
        $client = Client::where('users_id', $request->user()->id)->firstOrFail();
        $data = $request->validate([
            'booking_id' => ['required', 'exists:bookings,id'],
            'name' => ['required', 'string', 'max:255'],
            'event_type' => ['required', 'string', 'max:100'],
            'event_date' => ['required', 'date'],
            'guest_count' => ['required', 'integer', 'min:1', 'max:10000'],
            'package_id' => ['nullable', 'exists:packages,id'],
            'notes' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'vendors' => ['sometimes', 'array'],
            'vendors.*.vendor_id' => ['required', 'exists:vendors,id'],
            'vendors.*.activity' => ['required', 'string', 'max:255'],
            'vendors.*.location' => ['nullable', 'string', 'max:255'],
            'vendors.*.start_time' => ['required', 'date_format:H:i'],
            'vendors.*.end_time' => ['required', 'date_format:H:i', 'after:vendors.*.start_time'],
            'vendors.*.notes' => ['nullable', 'string'],
            'members' => ['sometimes', 'array'],
            'members.*.member_id' => ['required', 'exists:members,id'],
            'members.*.role' => ['required', 'string', 'max:100'],
            'members.*.notes' => ['nullable', 'string'],
        ]);
        $booking = Booking::where('id', $data['booking_id'])->where('client_id', $client->id)->firstOrFail();
        $photo = $request->file('photo')?->store('events', 'public');
        $event = DB::transaction(function () use ($data, $booking, $photo) {
            $event = Event::create([
                'booking_id' => $booking->id,
                'name' => $data['name'],
                'photo' => $photo,
                'event_type' => $data['event_type'],
                'event_date' => $data['event_date'],
                'guest_count' => $data['guest_count'],
                'package_id' => $data['package_id'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => 'pending',
            ]);
            foreach ($data['vendors'] ?? [] as $vendor) {
                $event->schedules()->create([
                    'vendor_id' => $vendor['vendor_id'],
                    'activity' => $vendor['activity'],
                    'location' => $vendor['location'] ?? null,
                    'start_time' => $data['event_date'] . ' ' . $vendor['start_time'],
                    'end_time' => $data['event_date'] . ' ' . $vendor['end_time'],
                    'notes' => $vendor['notes'] ?? null,
                    'status' => 'pending',
                ]);
            }
            foreach ($data['members'] ?? [] as $member) {
                $event->eventMembers()->create([
                    'member_id' => $member['member_id'],
                    'role' => $member['role'],
                    'notes' => $member['notes'] ?? null,
                    'status' => 'assigned',
                ]);
            }

            return $event;
        });

        return response()->json(['data' => $event->load(['schedules.vendor', 'eventMembers.member'])], 201);
    }

    public function createBooking(Request $request)
    {
        $client = Client::where('users_id', $request->user()->id)->firstOrFail();
        $data = $request->validate([
            'package_id' => ['nullable', 'exists:packages,id'],
            'vendor_ids' => ['required_without:package_id', 'array', 'min:3'],
            'vendor_ids.*' => ['integer', 'distinct', 'exists:vendors,id'],
            'venue_name' => ['required', 'string', 'max:255'],
            'venue_address' => ['required', 'string', 'max:1000'],
            'venue_city' => ['required', 'string', 'max:100'],
            'venue_province' => ['required', 'string', 'max:100'],
            'venue_maps' => ['nullable', 'string', 'max:1000'],
            'guest_count' => ['required', 'integer', 'min:1', 'max:10000'],
            'event_date' => ['required', 'date', 'after:today'],
            'notes' => ['nullable', 'string'],
        ]);
        if (isset($data['package_id'])) {
            $capacity = Package::findOrFail($data['package_id'])->guest_capacity;
            abort_if($capacity && $data['guest_count'] > $capacity, 422, 'Guest count exceeds package capacity.');
        }
        $booking = DB::transaction(function () use ($data, $client) {
            $package = isset($data['package_id']) ? Package::with('vendors')->findOrFail($data['package_id']) : null;
            $vendors = $package ? $package->vendors : Vendor::whereIn('id', $data['vendor_ids'] ?? [])->get();
            $booking = Booking::create([
                'client_id' => $client->id,
                'package_id' => $package?->id,
                'venue_name' => $data['venue_name'],
                'venue_address' => $data['venue_address'],
                'venue_city' => $data['venue_city'],
                'venue_province' => $data['venue_province'],
                'venue_maps' => $data['venue_maps'] ?? null,
                'guest_count' => $data['guest_count'],
                'total_price' => $vendors->sum('price'),
                'notes' => $data['notes'] ?? null,
                'status' => 'pending',
            ]);
            $event = Event::create([
                'booking_id' => $booking->id,
                'package_id' => $package?->id,
                'name' => 'Pernikahan ' . $client->groom_name . ' & ' . $client->bride_name,
                'event_type' => 'wedding',
                'event_date' => $data['event_date'],
                'guest_count' => $data['guest_count'],
                'status' => 'upcoming',
            ]);
            foreach ($vendors as $vendor) {
                $event->schedules()->create([
                    'vendor_id' => $vendor->id,
                    'activity' => $vendor->name,
                    'start_time' => $data['event_date'] . ' 09:00:00',
                    'end_time' => $data['event_date'] . ' 17:00:00',
                    'status' => 'pending',
                ]);
            }

            return $booking;
        });

        return response()->json(['data' => $booking->load(['package', 'events.schedules.vendor'])], 201);
    }

    private function ownedBooking(Request $request, int $bookingId): Booking
    {
        $client = Client::where('users_id', $request->user()->id)->firstOrFail();

        return Booking::where('client_id', $client->id)->with('payments')->findOrFail($bookingId);
    }
}
