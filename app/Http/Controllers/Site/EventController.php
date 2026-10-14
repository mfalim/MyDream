<?php

namespace App\Http\Controllers\Site;

use App\Data\Site\EventData;
use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\View\View;

/** Event publik MyDream (bukan acara pernikahan klien; itu ada di tabel events). */
class EventController extends Controller
{
    public function index(): View
    {
        $order = ['ongoing' => 0, 'upcoming' => 1, 'finished' => 2];

        return view('site.event.index', [
            'events' => collect(EventData::all())
                ->sortBy(fn ($event) => [$order[$event['status']], $event['starts_on']->timestamp])
                ->values(),
        ]);
    }

    public function show(string $slug): View
    {
        $event = EventData::find($slug);
        abort_if(! $event, 404);

        return view('site.event.show', [
            'event' => $event,
            'vendors' => Vendor::with(['category', 'photos'])->whereIn('slug', $event['vendor_slugs'])->get(),
        ]);
    }
}
