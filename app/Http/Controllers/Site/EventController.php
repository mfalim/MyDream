<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\PublicEvent;
use Illuminate\View\View;

/** Halaman event publik. Datanya dikelola admin (tabel public_events), bukan tabel events milik client. */
class EventController extends Controller
{
    public function index(): View
    {
        $order = ['ongoing' => 0, 'upcoming' => 1, 'finished' => 2];

        $events = PublicEvent::published()->get()
            ->sortBy(fn ($event) => [
                $order[$event->status],
                $event->status === 'finished' ? -$event->starts_on->timestamp : $event->starts_on->timestamp,
            ])
            ->values();

        return view('site.event.index', ['events' => $events]);
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
