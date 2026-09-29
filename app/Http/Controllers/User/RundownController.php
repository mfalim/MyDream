<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RundownController extends Controller
{
    public function index(Request $request)
    {
        $client = Client::where('users_id', Auth::id())->first();

        if (!$client) {
            return redirect()
                ->route('client.profile')
                ->with('error', 'Lengkapi data mempelai terlebih dahulu');
        }

        $year = (int) $request->get('year', date('Y'));
        $month = (int) $request->get('month', date('m'));

        $firstWeekday = (int) date('N', mktime(0, 0, 0, $month, 1, $year));

        $daysInMonth = (int) date('t', mktime(0, 0, 0, $month, 1, $year));

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, $daysInMonth)->endOfMonth();

        $events = Event::with(['booking.client', 'schedules.vendor', 'eventMembers.member'])
            ->whereHas('booking', function ($query) use ($client) {
                $query->where('client_id', $client->id);
            })
            ->whereBetween('event_date', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($event) {
                return $event->event_date->format('Y-m-d');
            });

        $eventsByDay = [];

        foreach ($events as $date => $dayEvents) {
            $day = (int) Carbon::parse($date)->format('d');

            $eventsByDay[$day] = $dayEvents
                ->map(function ($event) {
                    $type = 'full';

                    if ($event->guest_count < 100) {
                        $type = 'intimate';
                    } elseif (
                        stripos($event->name, 'gladi') !== false ||
                        stripos($event->notes ?? '', 'gladi') !== false
                    ) {
                        $type = 'rehearsal';
                    }

                    return [
                        'type' => $type,
                        'label' => $event->name,
                        'event_id' => $event->id,
                    ];
                })
                ->toArray();
        }

        $cells = [];

        for ($i = 1; $i < $firstWeekday; $i++) {
            $cells[] = null;
        }

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $d);

            $cells[] = [
                'num' => $d,
                'date' => $date,
                'is_peak' => isset($eventsByDay[$d]) && count($eventsByDay[$d]) >= 3,
                'events' => $eventsByDay[$d] ?? [],
            ];
        }

        while (count($cells) % 7 !== 0) {
            $cells[] = null;
        }

        $weeks = array_chunk($cells, 7);

        $totalEvents = collect($eventsByDay)->flatten(1)->count();

        $todayEvents = $eventsByDay[(int) date('d')] ?? [];

        $rehearsalCount = collect($eventsByDay)->flatten(1)->where('type', 'rehearsal')->count();

        $peakDay = collect($eventsByDay)->sortByDesc(fn($events) => count($events))->first();

        $peakDate = null;

        if ($peakDay) {
            foreach ($cells as $cell) {
                if ($cell && isset($cell['events']) && $cell['events'] === $peakDay) {
                    $peakDate = $cell;
                    break;
                }
            }
        }

        $selectedDate = $request->get('selected', $peakDate['date'] ?? date('Y-m-d'));

        $selectedEvents = Event::with(['booking.client', 'schedules.vendor', 'eventMembers.member'])
            ->whereHas('booking', function ($query) use ($client) {
                $query->where('client_id', $client->id);
            })
            ->whereDate('event_date', $selectedDate)
            ->get();

        $crewTotal = $selectedEvents->sum(fn($event) => $event->eventMembers->count());

        $monthLabel = Carbon::create($year, $month, 1)->isoFormat('MMMM YYYY');

        return view('user.rundown.calendar', [
            'monthLabel' => $monthLabel,
            'weeks' => $weeks,

            'tabCounts' => [
                'all' => $totalEvents,
                'today' => count($todayEvents),
                'rehearsal' => $rehearsalCount,
                'upcoming' => collect($eventsByDay)
                    ->filter(function ($events, $day) {
                        return $day >= (int) date('d');
                    })
                    ->flatten(1)
                    ->count(),
            ],

            'highlight' => [
                'title' => $peakDate
                    ? Carbon::parse($peakDate['date'])->isoFormat('dddd, D MMMM YYYY')
                    : 'Tidak ada acara',

                'desc' => $peakDay
                    ? count($peakDay) . ' acara berlangsung serentak di berbagai venue.'
                    : '',

                'crew_count' => $peakDay ? count($peakDay) : 0,

                'date' => $peakDate['date'] ?? date('Y-m-d'),

                'date_short' => $peakDate
                    ? Carbon::parse($peakDate['date'])->format('d M')
                    : date('d M'),
            ],

            'selected' => [
                'date' => $selectedDate,

                'date_short' => Carbon::parse($selectedDate)->format('d M'),

                'date_label' => Carbon::parse($selectedDate)->isoFormat('dddd, D MMMM YYYY'),

                'event_count' => $selectedEvents->count(),

                'crew_percent' => $crewTotal > 0 ? 100 : 0,

                'crew_ready' => $crewTotal,
                'crew_total' => $crewTotal,

                'leads' => $selectedEvents->count(),
                'mcs' => $selectedEvents->count(),

                'floor' => max(0, $crewTotal - $selectedEvents->count() * 2),

                'events' => $selectedEvents
                    ->map(function ($event) {
                        $startTime = $event->start_time
                            ? Carbon::parse($event->start_time)->format('H.i')
                            : '-';

                        $endTime = $event->end_time
                            ? Carbon::parse($event->end_time)->format('H.i')
                            : '-';

                        $firstMember = $event->eventMembers->first();

                        return [
                            'session' =>
                                $event->start_time && Carbon::parse($event->start_time)->hour < 16
                                    ? 'Siang'
                                    : 'Malam',

                            'time' => $startTime . ' - ' . $endTime,

                            'couple' =>
                                ($event->booking->client->groom_name ?? '-') .
                                ' & ' .
                                ($event->booking->client->bride_name ?? '-'),

                            'venue' => $event->booking->venue_name ?? 'Venue',

                            'hall' => $event->booking->venue_address ?? '',

                            'lead' => $firstMember?->member?->name ?? 'TBA',

                            'crew' => $event->eventMembers->count() . ' Personil',

                            'progress_label' => 'Status: ' . ucfirst($event->status),
                        ];
                    })
                    ->toArray(),
            ],
        ]);
    }

        public function show($date)
        {
            $user = auth()->user();

            $client = Client::where('users_id', $user->id)->firstOrFail();

            $event = Event::with([
                'booking.client',
                'schedules.vendor.category',
                'eventMembers.member',
                'package',
            ])
                ->whereDate('event_date', $date)
                ->whereHas('booking', function ($query) use ($client) {
                    $query->where('client_id', $client->id);
                })
                ->firstOrFail();

        $schedules = $event->schedules->sortBy('start_time');

        $phases = [];
        $currentPhase = null;

        foreach ($schedules as $schedule) {
            $scheduleStart = Carbon::parse($schedule->start_time);

            $scheduleEnd = Carbon::parse($schedule->end_time);

            $now = Carbon::now();

            $state = 'pending';

            if ($now->gt($scheduleEnd)) {
                $state = 'done';
            } elseif ($now->between($scheduleStart, $scheduleEnd)) {
                $state = 'active';
            }

            if (!$currentPhase || $currentPhase['title'] !== $schedule->activity) {
                if ($currentPhase) {
                    $phases[] = $currentPhase;
                }

                $currentPhase = [
                    'state' => $state,

                    'state_label' =>
                        $state === 'done'
                            ? 'Selesai'
                            : ($state === 'active'
                                ? 'Sedang Berlangsung'
                                : 'Menunggu'),

                    'title' => $schedule->activity,

                    'time_range' =>
                        $scheduleStart->format('H:i') .
                        ' - ' .
                        $scheduleEnd->format('H:i') .
                        ' WIB',

                    'items' => [],
                ];
            }

            $currentPhase['items'][] = [
                'time' => $scheduleStart->format('H:i'),

                'title' => $schedule->vendor?->name ?? $schedule->activity,

                'note' => $schedule->notes,

                'tag' => $schedule->vendor?->category?->name ?? '',

                'done' => $now->gt($scheduleEnd),

                'active' => $now->between($scheduleStart, $scheduleEnd),
            ];
        }

        if ($currentPhase) {
            $phases[] = $currentPhase;
        }

        $totalPrice = $event->booking->total_price ?? 0;

        return view('user.rundown.event-detail', [
            'event' => [
                'date_label' => Carbon::parse($date)->isoFormat('dddd, D MMMM YYYY'),

                'title' => $event->name,

                'hero_image' => $event->photo
                    ? asset('storage/' . $event->photo)
                    : 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=1600&q=85',

                'live' => Carbon::parse($event->event_date)->isToday(),

                'session_time' => $event->start_time
                    ? Carbon::parse($event->start_time)->format('H:i') .
                        ' - ' .
                        Carbon::parse($event->end_time)->format('H:i') .
                        ' WIB'
                    : '-',

                'venue' =>
                    ($event->booking->venue_name ?? 'Venue') .
                    ', ' .
                    ($event->booking->venue_city ?? ''),

                'couple' =>
                    ($event->booking->client->groom_name ?? '-') .
                    ' & ' .
                    ($event->booking->client->bride_name ?? '-'),

                'package' => $event->package->name ?? 'Standard Package',

                'guests' => [
                    'confirmed' => $event->guest_count,
                    'total' => $event->guest_count,
                    'percent' => 100,
                ],

                'lead_director' => $event->eventMembers->first()?->member?->name ?? 'TBA',

                'progress' => [
                    'current_time' => Carbon::now()->format('H:i') . ' WIB',

                    'phase_label' => 'Fase berlangsung',

                    'phase_percent' => 50,

                    'crew_count' => $event->eventMembers->count(),

                    'crew_note' => $event->eventMembers->count() . ' personil on-site',
                ],
            ],

            'phases' => $phases,

            'finance' => [
                'total' => $totalPrice,
                'status' => 'Lunas',

                'terms' => [
                    [
                        'label' => 'DP 30%',
                        'amount' => $totalPrice * 0.3,
                        'status' => 'Paid',
                    ],
                    [
                        'label' => 'Progress 40%',
                        'amount' => $totalPrice * 0.4,
                        'status' => 'Paid',
                    ],
                    [
                        'label' => 'Pelunasan 30%',
                        'amount' => $totalPrice * 0.3,
                        'status' => 'Paid',
                    ],
                ],

                'disbursement_status' => 'Escrow Secured',
            ],

            'vendorsOnSite' => [
                'ready' => $event->schedules->count(),
                'total' => $event->schedules->count(),

                'list' => $event->schedules
                    ->map(function ($schedule) {
                        if (!$schedule->vendor) {
                            return null;
                        }

                        return [
                            'name' => $schedule->vendor->name,

                            'status' => ucfirst($schedule->status),

                            'icon' => 'shop',

                            'desc' => $schedule->activity,

                            'pic' => $schedule->member?->name ?? 'TBA',

                            'note' => $schedule->notes ?? '-',
                        ];
                    })
                    ->filter()
                    ->values()
                    ->toArray(),
            ],

            'crew' => [
                'total' => $event->eventMembers->count(),

                'list' => $event->eventMembers
                    ->map(function ($eventMember) {
                        $name = $eventMember->member?->name ?? 'TBA';

                        $initials = collect(explode(' ', $name))
                            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
                            ->take(2)
                            ->join('');

                        return [
                            'initials' => $initials,
                            'name' => $name,
                            'role' => $eventMember->role ?? 'Staff',
                        ];
                    })
                    ->toArray(),
            ],
        ]);
    }

    public function create()
    {
        $client = Client::where('users_id', Auth::id())->first();

        if (!$client) {
            return redirect()
                ->route('client.profile')
                ->with('error', 'Lengkapi data mempelai terlebih dahulu');
        }

        return view('user.rundown.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'name' => 'required|string|max:255',
            'event_type' => 'required|string',
            'event_date' => 'required|date',
            'guest_count' => 'required|integer|min:1',
            'package_id' => 'required|exists:packages,id',
            'notes' => 'nullable|string',

            'vendors' => 'nullable|array',
            'vendors.*.vendor_id' => 'required|exists:vendors,id',
            'vendors.*.activity' => 'required|string',
            'vendors.*.location' => 'nullable|string',
            'vendors.*.start_time' => 'required|date_format:H:i',
            'vendors.*.end_time' => 'required|date_format:H:i',
            'vendors.*.notes' => 'nullable|string',

            'members' => 'nullable|array',
            'members.*.member_id' => 'required|exists:members,id',
            'members.*.role' => 'required|string',
            'members.*.notes' => 'nullable|string',
        ]);

        $client = Client::where('users_id', Auth::id())->first();

        if (!$client) {
            return redirect()
                ->route('client.profile')
                ->with('error', 'Lengkapi data mempelai terlebih dahulu');
        }

        $booking = Booking::where('id', $validated['booking_id'])
            ->where('client_id', $client->id)
            ->first();

        if (!$booking) {
            abort(403, 'Booking tidak dapat diakses.');
        }

        $event = DB::transaction(function () use ($validated, $booking) {
            $event = Event::create([
                'booking_id' => $booking->id,
                'name' => $validated['name'],
                'event_type' => $validated['event_type'],
                'event_date' => $validated['event_date'],
                'guest_count' => $validated['guest_count'],
                'package_id' => $validated['package_id'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
            ]);

            foreach ($validated['vendors'] ?? [] as $vendor) {
                $event->schedules()->create([
                    'vendor_id' => $vendor['vendor_id'],
                    'activity' => $vendor['activity'],
                    'location' => $vendor['location'] ?? null,
                    'start_time' => $validated['event_date'] . ' ' . $vendor['start_time'],
                    'end_time' => $validated['event_date'] . ' ' . $vendor['end_time'],
                    'notes' => $vendor['notes'] ?? null,
                    'status' => 'pending',
                ]);
            }

            foreach ($validated['members'] ?? [] as $member) {
                $event->eventMembers()->create([
                    'member_id' => $member['member_id'],
                    'role' => $member['role'],
                    'notes' => $member['notes'] ?? null,
                    'status' => 'approved',
                ]);
            }

            $event->refresh();

            $schedules = $event->schedules;

            if ($schedules->isNotEmpty()) {
                $event->update([
                    'start_time' => $schedules->min('start_time'),
                    'end_time' => $schedules->max('end_time'),
                ]);
            }

            return $event;
        });

        return redirect()->route('user.rundown')->with('success', 'Acara berhasil ditambahkan');
    }
}
