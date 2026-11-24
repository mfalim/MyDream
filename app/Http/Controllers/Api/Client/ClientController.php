<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Article;
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
            'groom_phone' => ['nullable', 'string', 'regex:/^[0-9]{10,15}$/'],
            'bride_phone' => ['nullable', 'string', 'regex:/^[0-9]{10,15}$/'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);
        $data['email'] = $data['email'] ?? $request->user()->email;
        $client = Client::updateOrCreate(['users_id' => $request->user()->id], $data);

        return response()->json(['data' => $client], 200);
    }

    public function bookings(Request $request)
    {
        $client = Client::where('users_id', $request->user()->id)->first();
        if (! $client) {
            return response()->json(['data' => []]);
        }

        $paginator = $client->bookings()
            ->with([
                'package',
                'events.package',
                'events.schedules.vendor.category',
                'events.schedules.member',
                'events.eventMembers.member',
                'payments',
            ])
            ->latest()
            ->paginate(15);

        $paginator->getCollection()->transform(function (Booking $b) {
            $b->syncPaymentStatus();
            $summary = $b->paymentSummary();
            $summary['deadline'] = $summary['deadline']?->toIso8601String();
            $summary['settlement_deadline'] = $summary['settlement_deadline']?->toIso8601String();
            $b->setAttribute('payment_summary', $summary);

            $schedules = $b->events->flatMap->schedules;
            $b->setAttribute('schedule_progress', [
                'approved' => $schedules->where('status', 'approved')->count(),
                'pending' => $schedules->where('status', 'pending')->count(),
                'rejected' => $schedules->where('status', 'rejected')->count(),
                'total' => $schedules->count(),
            ]);

            return $b;
        });

        return response()->json($paginator);
    }

    public function bookingPayments(Request $request, int $bookingId)
    {
        $booking = $this->ownedBooking($request, $bookingId);
        $booking->syncPaymentStatus();

        $latestMidtrans = $booking->payments()
            ->where('method', 'midtrans')
            ->whereIn('status', ['initiated', 'pending'])
            ->latest('id')
            ->first();

        if ($latestMidtrans) {
            $latestMidtrans->syncWithMidtrans();
            if ($latestMidtrans->status !== 'verified') {
                $latestMidtrans->update([
                    'status' => 'verified',
                    'verified_at' => now(),
                    'transaction_status' => 'settlement',
                ]);
            }
            $booking->syncPaymentStatus();
        }

        $booking->load([
            'package',
            'payments',
            'events.package',
            'events.schedules.vendor.category',
            'events.schedules.member',
            'events.eventMembers.member',
        ]);
        $summary = $booking->paymentSummary();

        return response()->json([
            'data' => $booking,
            'summary' => [
                'total' => $summary['total'],
                'paid' => $summary['paid'],
                'pending' => $summary['pending'],
                'remaining' => $summary['remaining'],
                'dp_percent' => $summary['dp_percent'],
                'dp_amount' => $summary['dp_amount'],
                'percent_paid' => $summary['percent_paid'],
                'payment_status' => $summary['status'],
                'deadline' => $summary['deadline']?->toIso8601String(),
                'deadline_type' => $summary['deadline_type'],
                'days_to_deadline' => $summary['days_to_deadline'],
                'is_overdue' => $summary['is_overdue'],
            ],
        ]);
    }

    public function submitPayment(Request $request, int $bookingId)
    {
        $booking = $this->ownedBooking($request, $bookingId);
        $booking->syncPaymentStatus();
        $total = (float) $booking->total_price;
        $paid = (float) $booking->paid_amount;
        $remaining = max(0, $total - $paid);
        $percent = max(1, min(100, (int) config('mydream.dp_percent', 10)));
        $dpAmount = (int) ceil($total * $percent / 100);

        $options = [
            'dp' => $dpAmount,
            'full' => (int) round($total),
            'remaining' => (int) round($remaining > 0 ? $remaining : ($total - $dpAmount)),
        ];

        $data = $request->validate([
            'scheme' => ['required', 'in:dp,full,remaining'],
            'proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        $proof = $request->file('proof')?->store('payment-proofs', 'public');
        $payment = DB::transaction(function () use ($booking, $data, $options, $proof) {
            $booking->ensureInvoiceNumber();

            // Gunakan transaksi Midtrans yang sudah diinisiasi untuk booking ini bila ada
            $existingMidtrans = Payment::query()
                ->where('booking_id', $booking->id)
                ->where('method', 'midtrans')
                ->whereIn('status', ['initiated', 'pending', 'verified'])
                ->latest('id')
                ->first();

            if ($existingMidtrans && ($existingMidtrans->proof_path === null || $existingMidtrans->type === $data['scheme'])) {
                $previousStatus = $existingMidtrans->status;
                $previousTx = $existingMidtrans->transaction_status;

                $existingMidtrans->syncWithMidtrans();

                $updates = [
                    'bank_info' => 'Midtrans Payment Gateway',
                    'status' => 'verified',
                    'verified_at' => $existingMidtrans->verified_at ?? now(),
                    'transaction_status' => 'settlement',
                ];
                if ($proof) {
                    $updates['proof_path'] = $proof;
                }

                $existingMidtrans->update($updates);
                $existingMidtrans->recordLifecycleEvent(
                    'midtrans_proof_upload',
                    $previousStatus,
                    $previousTx,
                    'Pembayaran Midtrans dikonfirmasi dan diverifikasi otomatis.'
                );
                $booking->syncPaymentStatus();

                return $existingMidtrans->fresh();
            }

            $payment = Payment::create([
                'booking_id' => $booking->id,
                'type' => $data['scheme'],
                'amount' => $options[$data['scheme']],
                'method' => 'midtrans',
                'bank_info' => 'Midtrans Payment Gateway',
                'status' => 'verified',
                'verified_at' => now(),
                'proof_path' => $proof,
                'transaction_status' => 'settlement',
            ]);
            $payment->recordLifecycleEvent('midtrans_proof_upload', null, null, 'Pembayaran Midtrans dikonfirmasi dan diverifikasi otomatis.');
            $booking->syncPaymentStatus();

            return $payment;
        });

        $freshBooking = $booking->fresh();

        return response()->json([
            'data' => [
                'payment' => $payment,
                'booking' => $freshBooking,
                'id' => $payment->id,
                'booking_id' => $payment->booking_id,
                'type' => $payment->type,
                'amount' => $payment->amount,
                'method' => $payment->method,
                'bank_info' => $payment->bank_info ?: 'Midtrans Payment Gateway',
                'status' => $payment->status,
                'transaction_status' => $payment->transaction_status,
                'proof_path' => $payment->proof_path,
                'created_at' => $payment->created_at,
            ],
            'booking' => $freshBooking,
        ], 201);
    }

    public function events(Request $request)
    {
        $client = Client::where('users_id', $request->user()->id)->first();
        if (! $client) {
            return response()->json(['data' => []]);
        }

        $events = Event::with([
            'booking.package',
            'package',
            'schedules' => fn ($q) => $q->orderBy('start_time')->orderBy('id'),
            'schedules.vendor.category',
            'schedules.member',
            'eventMembers.member',
        ])
            ->whereHas('booking', fn ($query) => $query->where('client_id', $client->id))
            ->when($request->filled('month'), function ($query) use ($request) {
                $month = $request->validate(['month' => ['date_format:Y-m']])['month'];
                $query->whereYear('event_date', substr($month, 0, 4))->whereMonth('event_date', substr($month, 5, 2));
            })
            ->orderByDesc('event_date')
            ->get();

        $reviewedMap = Article::whereIn('event_id', $events->pluck('id'))
            ->get()
            ->keyBy('event_id');

        $events->transform(function (Event $event) use ($reviewedMap) {
            $review = $reviewedMap->get($event->id);
            $event->setAttribute('has_review', $review !== null);
            $event->setAttribute('review', $review);

            return $event;
        });

        return response()->json(['data' => $events]);
    }

    public function reviews(Request $request)
    {
        $client = Client::where('users_id', $request->user()->id)->first();
        if (! $client) {
            return response()->json([
                'data' => [
                    'completed_events' => [],
                    'reviews' => [],
                ],
            ]);
        }

        $completedEvents = Event::query()
            ->where('status', 'completed')
            ->whereHas('booking', fn ($query) => $query
                ->where('client_id', $client->id)
                ->where('status', 'approved'))
            ->with(['booking.package', 'package'])
            ->orderByDesc('event_date')
            ->get();

        $reviews = Article::query()
            ->with('event')
            ->where('client_id', $client->id)
            ->whereNotNull('event_id')
            ->orderByDesc('created_at')
            ->get();

        $reviewedEventIds = $reviews->pluck('event_id')->flip();
        $completedEvents->transform(function (Event $event) use ($reviewedEventIds) {
            $event->setAttribute('has_review', $reviewedEventIds->has($event->id));

            return $event;
        });

        return response()->json([
            'data' => [
                'completed_events' => $completedEvents,
                'reviews' => $reviews,
            ],
        ]);
    }

    public function storeReview(Request $request)
    {
        $client = Client::where('users_id', $request->user()->id)->firstOrFail();
        $data = $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'excerpt' => ['nullable', 'string', 'max:255'],
            'comment' => ['nullable', 'string', 'max:3000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $event = Event::query()
            ->with('booking')
            ->whereKey($data['event_id'])
            ->where('status', 'completed')
            ->whereHas('booking', fn ($query) => $query
                ->where('client_id', $client->id)
                ->where('status', 'approved'))
            ->first();

        if (! $event) {
            return response()->json(['message' => 'Acara belum berstatus selesai atau tidak tersedia untuk diulas.'], 422);
        }

        if (Article::where('event_id', $event->id)->exists()) {
            return response()->json(['message' => 'Acara ini sudah memiliki ulasan.'], 422);
        }

        $photoPath = $request->file('photo')?->store('articles', 'public');
        $comment = trim((string) ($data['comment'] ?? ''));
        $content = $comment !== ''
            ? $comment
            : 'Acara ini mendapatkan rating ' . $data['rating'] . ' dari 5 bintang.';

        $article = Article::create([
            'title' => $event->name,
            'category' => 'Ulasan Client',
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $content,
            'photo' => $photoPath,
            'author' => $client->groom_name . ' & ' . $client->bride_name,
            'published_at' => today(),
            'package_id' => $event->package_id ?: $event->booking->package_id,
            'is_published' => true,
            'event_id' => $event->id,
            'client_id' => $client->id,
            'rating' => $data['rating'],
        ]);

        return response()->json(['data' => $article->load('event')], 201);
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
        $client = Client::where('users_id', $request->user()->id)->first();
        if (! $client) {
            $client = Client::create([
                'users_id' => $request->user()->id,
                'groom_name' => $request->input('groom_name', $request->user()->name),
                'bride_name' => $request->input('bride_name', 'Pasangan'),
                'email' => $request->user()->email,
            ]);
        }
        $data = $request->validate([
            'package_id' => ['nullable', 'exists:packages,id'],
            'vendor_ids' => ['required_without:package_id', 'array', 'min:1'],
            'vendor_ids.*' => ['integer', 'distinct', 'exists:vendors,id'],
            'venue_name' => ['required', 'string', 'max:255'],
            'venue_address' => ['required', 'string', 'max:1000'],
            'venue_city' => ['required', 'string', 'max:100'],
            'venue_province' => ['required', 'string', 'max:100'],
            'venue_maps' => ['nullable', 'string', 'max:1000'],
            'guest_count' => ['required', 'integer', 'min:1', 'max:10000'],
            'event_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);
        if (isset($data['package_id'])) {
            $package = Package::availableForBooking()->findOrFail($data['package_id']);
            $capacity = $package->guest_capacity;
            abort_if($capacity && $data['guest_count'] > $capacity, 422, 'Guest count exceeds package capacity.');
        } else {
            $availableVendors = Vendor::availableForBooking()
                ->whereIn('id', $data['vendor_ids'] ?? [])
                ->count();
            abort_if($availableVendors !== count($data['vendor_ids'] ?? []), 409, 'One or more vendors are not currently available.');
        }

        $booking = DB::transaction(function () use ($data, $client) {
            $isPackage = isset($data['package_id']);
            $package = $isPackage ? Package::availableForBooking()->with('vendors')->findOrFail($data['package_id']) : null;
            $vendors = $package
                ? $package->vendors
                : Vendor::availableForBooking()->with('category')->whereIn('id', $data['vendor_ids'] ?? [])->get();
            abort_if(! $isPackage && $vendors->count() !== count($data['vendor_ids'] ?? []), 409, 'One or more vendors are not currently available.');

            $guestCount = (int) $data['guest_count'];
            if (! $isPackage) {
                $cateringCapacities = $vendors
                    ->filter(fn (Vendor $v) => $v->isCatering() && (int) $v->guest_capacity > 0)
                    ->pluck('guest_capacity');
                if ($cateringCapacities->isNotEmpty()) {
                    $maxGuests = (int) $cateringCapacities->min();
                    abort_if($guestCount > $maxGuests, 422, 'Jumlah tamu melebihi kapasitas vendor catering.');
                }
            }

            $totalPrice = $isPackage
                ? (float) $vendors->sum('price')
                : (float) $vendors->sum(fn (Vendor $v) => $v->priceForGuests($guestCount));

            $booking = Booking::create([
                'client_id' => $client->id,
                'package_id' => $package?->id,
                'venue_name' => $data['venue_name'],
                'venue_address' => $data['venue_address'],
                'venue_city' => $data['venue_city'],
                'venue_province' => $data['venue_province'],
                'venue_maps' => $data['venue_maps'] ?? null,
                'guest_count' => $guestCount,
                'total_price' => $totalPrice,
                'notes' => $data['notes'] ?? null,
                'status' => 'pending',
            ]);
            $booking->ensureInvoiceNumber();

            $event = Event::create([
                'booking_id' => $booking->id,
                'package_id' => $package?->id,
                'name' => 'Pernikahan ' . $client->groom_name . ' & ' . $client->bride_name,
                'event_type' => 'wedding',
                'event_date' => $data['event_date'],
                'guest_count' => $guestCount,
                'status' => 'upcoming',
            ]);
            foreach ($vendors as $vendor) {
                $vendorPrice = $isPackage
                    ? (float) $vendor->price
                    : $vendor->priceForGuests($guestCount);

                $event->schedules()->create([
                    'vendor_id' => $vendor->id,
                    'price' => $vendorPrice,
                    'paid_amount' => 0,
                    'payment_status' => 'unpaid',
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
