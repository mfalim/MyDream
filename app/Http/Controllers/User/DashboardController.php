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
                'bookings' => fn ($query) => $query
                    ->orderBy('created_at'),
                'bookings.package',
                'bookings.events' => fn ($query) => $query->orderBy('id'),
                'bookings.events.schedules.vendor.category',
            ])
            ->firstOrFail();

        // Ringkasan mengambil booking pertama yang event terawalnya belum completed.
        // Jika event dengan ID terkecil sudah completed, booking tersebut dilewati.
        $bookings = $client->bookings->values();
        $bookingSummary = $bookings->first(function ($candidateBooking) {
            $firstEvent = $candidateBooking->events->sortBy('id')->first();

            return ! $firstEvent || $firstEvent->status !== 'completed';
        });

        // Fitur acara hanya boleh memakai booking yang sudah disetujui.
        $approvedBookings = $bookings->where('status', 'approved')->values();

        $eventEntries = $approvedBookings->flatMap(fn ($booking) =>
            $booking->events->map(fn ($event) => [
                'event' => $event,
                'booking' => $booking,
            ])
        );

        // Event completed tidak lagi ditampilkan sebagai event utama dashboard.
        // Event dipilih berdasarkan urutan dibuat (ID terendah), bukan tanggal acara.
        // Jika event terawal sudah completed, entry berikutnya akan dipilih.
        $activeEventEntries = $eventEntries
            ->reject(fn ($entry) => $entry['event']->status === 'completed')
            ->sortBy(fn ($entry) => $entry['event']->id)
            ->values();

        $selectedEntry = $activeEventEntries->first();

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
        // Jadwal Anda menampilkan seluruh event aktif dari booking approved user,
        // diurutkan dari tanggal acara yang paling dekat.
        $upcomingEntries = $activeEventEntries
            ->sortBy(fn ($entry) => [
                $entry['event']->event_date?->timestamp ?? PHP_INT_MAX,
                $entry['event']->start_time ?? '',
            ])
            ->values();

        return view('user.dashboard.index', [
            'userName' => $user->name,
            'client' => $client,
            'booking' => $booking,
            'bookingSummary' => $bookingSummary,
            'event' => $event,
            'bookingEvents' => $bookingEvents,
            'upcomingEvents' => $upcomingEntries->pluck('event'),
            'vendorSchedules' => $vendorSchedules->take(5),
            'vendorCount' => $vendorCount,
            'bookingCount' => $approvedBookings->count(),
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
