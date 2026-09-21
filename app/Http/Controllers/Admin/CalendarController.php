<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\OrganizerEvent;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        return view('admin.event.calendar');
    }

    public function getEventsByMonth(Request $request)
    {
        $month = $request->input('month');
        
        if (!$month) {
            return response()->json(['error' => 'Month parameter required'], 400);
        }

        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = Carbon::parse($month . '-01')->endOfMonth();

        $clientEvents = Event::with(['booking.client', 'eventVendors.vendor', 'teamMembers.user'])
            ->whereBetween('event_date', [$startDate, $endDate])
            ->get()
            ->map(function ($event) {
                $booking = $event->booking;
                $client = $booking ? $booking->client : null;
                
                return [
                    'id' => $event->id,
                    'date' => $event->event_date->format('Y-m-d'),
                    'title' => $event->name,
                    'couple_name' => $client ? ($client->groom_name . ' & ' . $client->bride_name) : 'N/A',
                    'type' => 'client_event',
                    'event_type' => $event->event_type,
                    'venue' => $booking ? $booking->venue_name : 'N/A',
                    'time' => $event->start_time ? Carbon::parse($event->start_time)->format('H:i') : null,
                    'schedule' => $event->start_time && $event->end_time 
                        ? Carbon::parse($event->start_time)->format('H:i') . ' - ' . Carbon::parse($event->end_time)->format('H:i')
                        : null,
                    'status' => $event->status,
                    'vendor_count' => $event->eventVendors->count(),
                    'team_count' => $event->teamMembers->count()
                ];
            });

        $organizerEvents = OrganizerEvent::whereBetween('event_date', [$startDate, $endDate])
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'date' => $event->event_date->format('Y-m-d'),
                    'title' => $event->title,
                    'couple_name' => null,
                    'type' => 'organizer_event',
                    'event_type' => $event->event_type,
                    'venue' => $event->location,
                    'time' => $event->start_time ? Carbon::parse($event->start_time)->format('H:i') : null,
                    'schedule' => $event->start_time && $event->end_time 
                        ? Carbon::parse($event->start_time)->format('H:i') . ' - ' . Carbon::parse($event->end_time)->format('H:i')
                        : null,
                    'status' => $event->status,
                    'priority' => $event->priority
                ];
            });

        $allEvents = $clientEvents->concat($organizerEvents)->values();

        return response()->json([
            'events' => $allEvents,
            'stats' => [
                'total' => $allEvents->count(),
                'client_events' => $clientEvents->count(),
                'organizer_events' => $organizerEvents->count()
            ]
        ]);
    }

    public function getEventsByDate(Request $request)
    {
        $date = $request->input('date');
        
        if (!$date) {
            return response()->json(['error' => 'Date parameter required'], 400);
        }

        $clientEvents = Event::with(['booking.client', 'eventVendors.vendor', 'teamMembers.user', 'package'])
            ->whereDate('event_date', $date)
            ->get()
            ->map(function ($event) {
                $booking = $event->booking;
                $client = $booking ? $booking->client : null;
                $leadDirector = $event->teamMembers->where('role', 'lead_director')->first();
                
                return [
                    'id' => $event->id,
                    'title' => $event->name,
                    'couple_name' => $client ? ($client->groom_name . ' & ' . $client->bride_name) : 'N/A',
                    'type' => 'client_event',
                    'event_type' => $event->event_type ? ucfirst(str_replace('_', ' ', $event->event_type)) : 'N/A',
                    'venue' => $booking ? $booking->venue_name : 'N/A',
                    'venue_city' => $booking ? $booking->venue_city : null,
                    'schedule' => $event->start_time && $event->end_time 
                        ? Carbon::parse($event->start_time)->format('H:i') . ' - ' . Carbon::parse($event->end_time)->format('H:i')
                        : 'Belum ditentukan',
                    'status' => ucfirst($event->status),
                    'package' => $event->package ? $event->package->name : ($booking && $booking->package ? $booking->package->name : 'N/A'),
                    'vendor_count' => $event->eventVendors->count(),
                    'vendors' => $event->eventVendors->map(function($ev) {
                        return [
                            'name' => $ev->vendor->name,
                            'category' => $ev->vendor->category ? $ev->vendor->category->name : 'N/A',
                            'status' => $ev->status
                        ];
                    }),
                    'team_count' => $event->teamMembers->count(),
                    'lead_director' => $leadDirector ? $leadDirector->user->name : 'Belum ditentukan',
                    'notes' => $event->notes
                ];
            });

        $organizerEvents = OrganizerEvent::whereDate('event_date', $date)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'couple_name' => null,
                    'type' => 'organizer_event',
                    'event_type' => ucfirst(str_replace('_', ' ', $event->event_type)),
                    'venue' => $event->location ?? 'Internal',
                    'venue_city' => null,
                    'schedule' => $event->start_time && $event->end_time 
                        ? Carbon::parse($event->start_time)->format('H:i') . ' - ' . Carbon::parse($event->end_time)->format('H:i')
                        : 'Belum ditentukan',
                    'status' => ucfirst($event->status),
                    'priority' => ucfirst($event->priority),
                    'attendees' => $event->attendees,
                    'description' => $event->description,
                    'notes' => $event->notes
                ];
            });

        $allEvents = $clientEvents->concat($organizerEvents)->values();

        return response()->json([
            'events' => $allEvents,
            'date' => $date,
            'count' => [
                'total' => $allEvents->count(),
                'client_events' => $clientEvents->count(),
                'organizer_events' => $organizerEvents->count()
            ]
        ]);
    }
}
