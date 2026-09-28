<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Client;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RundownController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $client = Client::where('users_id', $user->id)->first();
        
        if (!$client) {
            return redirect()->route('client.profile')->with('error', 'Lengkapi data mempelai terlebih dahulu');
        }
        
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));
        
        $firstWeekday = (int) date('N', mktime(0, 0, 0, $month, 1, $year));
        $daysInMonth = (int) date('t', mktime(0, 0, 0, $month, 1, $year));
        
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, $daysInMonth)->endOfMonth();
        
        $events = Event::with(['booking.client', 'schedules.vendor', 'eventMembers.member'])
            ->whereHas('booking', function($query) use ($client) {
                $query->where('client_id', $client->id);
            })
            ->whereBetween('event_date', [$startDate, $endDate])
            ->get()
            ->groupBy(function($event) {
                return $event->event_date->format('Y-m-d');
            });
        
        $eventsByDay = [];
        foreach ($events as $date => $dayEvents) {
            $day = (int) Carbon::parse($date)->format('d');
            $eventsByDay[$day] = $dayEvents->map(function($event) {
                $type = 'full';
                if ($event->guest_count < 100) {
                    $type = 'intimate';
                } elseif (stripos($event->name, 'gladi') !== false || stripos($event->notes, 'gladi') !== false) {
                    $type = 'rehearsal';
                }
                
                return [
                    'type' => $type,
                    'label' => $event->name,
                    'event_id' => $event->id,
                ];
            })->toArray();
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
        $todayEvents = $eventsByDay[(int)date('d')] ?? [];
        $rehearsalCount = collect($eventsByDay)->flatten(1)->where('type', 'rehearsal')->count();
        
        $peakDay = collect($eventsByDay)->sortByDesc(function($events) {
            return count($events);
        })->first();
        
        $peakDate = $peakDay ? collect($cells)->firstWhere(function($cell) use ($peakDay) {
            return $cell && isset($cell['events']) && $cell['events'] === $peakDay;
        }) : null;
        
        $selectedDate = $request->get('selected', $peakDate['date'] ?? date('Y-m-d'));
        $selectedDay = (int) Carbon::parse($selectedDate)->format('d');
        $selectedEvents = Event::with(['booking.client', 'schedules.vendor', 'eventMembers.member'])
            ->whereHas('booking', function($query) use ($client) {
                $query->where('client_id', $client->id);
            })
            ->whereDate('event_date', $selectedDate)
            ->get();
        
        $crewTotal = $selectedEvents->sum(function($event) {
            return $event->eventMembers->count();
        });
        
        $monthLabel = Carbon::create($year, $month, 1)->isoFormat('MMMM YYYY');
        
        return view('user.rundown.calendar', [
            'monthLabel' => $monthLabel,
            'weeks' => $weeks,
            'tabCounts' => [
                'all' => $totalEvents,
                'today' => count($todayEvents),
                'rehearsal' => $rehearsalCount,
                'upcoming' => collect($eventsByDay)->filter(function($events, $day) {
                    return $day >= (int)date('d');
                })->flatten(1)->count(),
            ],
            'highlight' => [
                'title' => $peakDate ? Carbon::parse($peakDate['date'])->isoFormat('dddd, D MMMM YYYY') : 'Tidak ada acara',
                'desc' => $peakDay ? count($peakDay) . ' acara berlangsung serentak di berbagai venue.' : '',
                'crew_count' => $peakDay ? count($peakDay) : 0,
                'date' => $peakDate['date'] ?? date('Y-m-d'),
                'date_short' => $peakDate ? Carbon::parse($peakDate['date'])->format('d M') : date('d M'),
            ],
            'selected' => [
                'date' => $selectedDate,
                'date_short' => Carbon::parse($selectedDate)->format('d M'),
                'date_label' => Carbon::parse($selectedDate)->isoFormat('dddd, D MMMM YYYY'),
                'event_count' => $selectedEvents->count(),
                'crew_percent' => min(100, $crewTotal > 0 ? 100 : 0),
                'crew_ready' => $crewTotal,
                'crew_total' => $crewTotal,
                'leads' => $selectedEvents->count(),
                'mcs' => $selectedEvents->count(),
                'floor' => max(0, $crewTotal - ($selectedEvents->count() * 2)),
                'events' => $selectedEvents->map(function($event) {
                    $startTime = $event->start_time ? Carbon::parse($event->start_time)->format('H.i') : '-';
                    $endTime = $event->end_time ? Carbon::parse($event->end_time)->format('H.i') : '-';
                    
                    return [
                        'session' => (int)Carbon::parse($event->start_time)->format('H') < 16 ? 'Siang' : 'Malam',
                        'time' => $startTime . ' - ' . $endTime,
                        'couple' => $event->booking->client->groom_name . ' & ' . $event->booking->client->bride_name,
                        'venue' => $event->booking->venue_name ?? 'Venue',
                        'hall' => $event->booking->venue_address ?? '',
                        'lead' => $event->eventMembers->first()->member->name ?? 'TBA',
                        'crew' => $event->eventMembers->count() . ' Personil',
                        'progress_label' => 'Status: ' . ucfirst($event->status),
                    ];
                })->toArray(),
            ],
        ]);
    }
    
    public function show($date)
    {
        $client = Client::where('users_id', auth()->id())->first();

        if (!$client) {
            return redirect()->route('client.profile')->with('error', 'Lengkapi data mempelai terlebih dahulu');
        }

        $events = Event::with([
            'booking.client', 'booking.package', 'schedules.vendor.category',
            'schedules.member', 'eventMembers.member', 'package',
        ])
            ->whereDate('event_date', $date)
            ->whereHas('booking', fn ($query) => $query->where('client_id', $client->id))
            ->orderBy('start_time')
            ->get();

        abort_if($events->isEmpty(), 404);

        $now = Carbon::now();
        $events = $events->map(function ($event) use ($now) {
            $eventDate = Carbon::parse($event->event_date)->toDateString();
            $event->rundownPhases = $event->schedules->sortBy('start_time')->groupBy('activity')->map(function ($schedules, $activity) use ($now, $eventDate) {
                $items = $schedules->map(function ($schedule) use ($now, $eventDate) {
                    $start = Carbon::parse($eventDate . ' ' . $schedule->start_time);
                    $end = $schedule->end_time
                        ? Carbon::parse($eventDate . ' ' . $schedule->end_time)
                        : $start->copy();

                    return [
                        'time' => $start->format('H:i'),
                        'title' => $schedule->vendor?->name ?: $schedule->activity,
                        'note' => $schedule->notes,
                        'tag' => $schedule->vendor?->category?->name,
                        'done' => $now->gt($end),
                        'active' => $now->between($start, $end),
                        'start' => $start,
                        'end' => $end,
                    ];
                })->values();
                $first = $items->first();
                $last = $items->last();
                $isActive = $items->contains(fn ($item) => $item['active']);
                $isDone = $items->every(fn ($item) => $item['done']);

                return [
                    'title' => $activity ?: 'Rangkaian Acara',
                    'state' => $isActive ? 'active' : ($isDone ? 'done' : 'pending'),
                    'state_label' => $isActive ? 'Sedang Berlangsung' : ($isDone ? 'Selesai' : 'Menunggu'),
                    'time_range' => $first['start']->format('H:i') . ' - ' . $last['end']->format('H:i') . ' WIB',
                    'items' => $items,
                ];
            })->values();

            return $event;
        });

        return view('user.rundown.event-detail', [
            'dateLabel' => Carbon::parse($date)->isoFormat('dddd, D MMMM YYYY'),
            'events' => $events,
        ]);
    }

    public function create()
    {
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
        
        $event = Event::create([
            'booking_id' => $validated['booking_id'],
            'name' => $validated['name'],
            'event_type' => $validated['event_type'],
            'event_date' => $validated['event_date'],
            'guest_count' => $validated['guest_count'],
            'package_id' => $validated['package_id'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);
        
        if (!empty($validated['vendors'])) {
            foreach ($validated['vendors'] as $vendor) {
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
        }
        
        if (!empty($validated['members'])) {
            foreach ($validated['members'] as $member) {
                $event->eventMembers()->create([
                    'member_id' => $member['member_id'],
                    'role' => $member['role'],
                    'notes' => $member['notes'] ?? null,
                    'status' => 'approved',
                ]);
            }
        }
        
        $event->refresh();
        $schedules = $event->schedules;
        
        if ($schedules->isNotEmpty()) {
            $event->update([
                'start_time' => $schedules->min('start_time'),
                'end_time' => $schedules->max('end_time'),
            ]);
        }
        
        return redirect()->route('user.rundown')->with('success', 'Acara berhasil ditambahkan');
    }
}
