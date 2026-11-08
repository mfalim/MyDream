<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Client;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $client = Client::query()
            ->where('users_id', $user->id)
            ->with([
                'bookings.package',
                'bookings.events' => fn ($query) => $query->orderBy('event_date')->orderBy('start_time'),
                'bookings.events.schedules.vendor.category',
            ])
            ->firstOrFail();

        $bookings = $client->bookings;
        $activeBookings = $bookings->whereIn('status', ['approved', 'pending'])->values();

        $eventEntries = $activeBookings->flatMap(fn ($booking) =>
            $booking->events->map(fn ($event) => [
                'event' => $event,
                'booking' => $booking,
            ])
        );

        $upcomingEntries = $eventEntries
            ->filter(fn ($entry) => $entry['event']->event_date?->gte(today()))
            ->sortBy(fn ($entry) => $entry['event']->event_date?->timestamp ?? PHP_INT_MAX)
            ->values();

        $selectedEntry = $upcomingEntries->first()
            ?? $eventEntries->sortByDesc(fn ($entry) => $entry['event']->event_date?->timestamp ?? 0)->first();

        $event = $selectedEntry['event'] ?? null;
        $booking = $selectedEntry['booking'] ?? null;
        $bookingEvents = $booking?->events ?? collect();
        $vendorSchedules = $event
            ? $event->schedules->filter(fn ($schedule) => $schedule->vendor_id !== null)->values()
            : collect();

        $vendorCount = $vendorSchedules->count();
        $confirmedCount = $vendorSchedules->where('status', 'approved')->count();
        $waitingCount = $vendorSchedules->where('status', 'pending')->count();
        $rejectedCount = $vendorSchedules->where('status', 'rejected')->count();
        $eventDate = $event?->event_date;

        return view('user.dashboard.index', [
            'userName' => $user->name,
            'client' => $client,
            'booking' => $booking,
            'event' => $event,
            'bookingEvents' => $bookingEvents,
            'upcomingEvents' => $upcomingEntries->take(4)->pluck('event'),
            'vendorSchedules' => $vendorSchedules->take(5),
            'vendorCount' => $vendorCount,
            'bookingCount' => $activeBookings->count(),
            'vendorReadiness' => [
                'total' => $vendorCount,
                'confirmed' => $confirmedCount,
                'waiting' => $waitingCount,
                'review' => $rejectedCount,
                'percent' => $vendorCount > 0 ? (int) round(($confirmedCount / $vendorCount) * 100) : 0,
            ],
            'daysToEvent' => $eventDate && $eventDate->gte(today())
                ? max(0, (int) today()->diffInDays($eventDate, false))
                : null,
            'eventDateLabel' => $eventDate?->format('d/m/Y'),
        ]);
    }
}
