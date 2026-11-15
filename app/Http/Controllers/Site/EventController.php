<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\PublicEvent;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Halaman event publik. Datanya dikelola admin (tabel public_events), bukan tabel events milik client. */
class EventController extends Controller
{
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status');
        $validStatuses = ['upcoming', 'ongoing', 'finished'];
        if (! in_array($statusFilter, $validStatuses, true)) {
            $statusFilter = null;
        }

        $allPublished = PublicEvent::published()->with('vendors')->get();

        $counts = [
            'all' => $allPublished->count(),
            'upcoming' => $allPublished->where('status', 'upcoming')->count(),
            'ongoing' => $allPublished->where('status', 'ongoing')->count(),
            'finished' => $allPublished->where('status', 'finished')->count(),
        ];

        $order = ['ongoing' => 0, 'upcoming' => 1, 'finished' => 2];

        $events = $allPublished
            ->when($statusFilter, fn ($collection) => $collection->where('status', $statusFilter))
            ->sortBy(fn ($event) => [
                $order[$event->status],
                $event->status === 'finished' ? -$event->starts_on->timestamp : $event->starts_on->timestamp,
            ])
            ->values();

        return view('site.event.index', [
            'events' => $events,
            'activeStatus' => $statusFilter,
            'counts' => $counts,
        ]);
    }

    public function show(string $slug): View
    {
        $event = PublicEvent::published()
            ->with(['vendors.category', 'vendors.photos'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('site.event.show', ['event' => $event, 'vendors' => $event->vendors]);
    }
}
