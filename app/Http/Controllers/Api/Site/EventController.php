<?php

namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Controller;
use App\Models\PublicEvent;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = $request->query('status');
        $validStatuses = ['upcoming', 'ongoing', 'finished'];
        if (! in_array($statusFilter, $validStatuses, true)) {
            $statusFilter = null;
        }

        $order = ['ongoing' => 0, 'upcoming' => 1, 'finished' => 2];

        $events = PublicEvent::published()
            ->with(['vendors.category', 'vendors.photos'])
            ->when(
                $request->filled('q'),
                fn ($query) => $query->where('title', 'like', '%' . $request->query('q') . '%')
            )
            ->get()
            ->when($statusFilter, fn ($collection) => $collection->where('status', $statusFilter))
            ->sortBy(fn ($event) => [
                $order[$event->status] ?? 9,
                $event->status === 'finished' ? -$event->starts_on->timestamp : $event->starts_on->timestamp,
            ])
            ->values();

        return response()->json(['data' => $events]);
    }

    public function show(string $slug)
    {
        $event = PublicEvent::published()
            ->with(['vendors.category', 'vendors.photos'])
            ->where(function ($query) use ($slug) {
                $query->where('slug', $slug);
                if (is_numeric($slug)) {
                    $query->orWhere('id', (int) $slug);
                }
            })
            ->firstOrFail();

        return response()->json(['data' => $event]);
    }
}
