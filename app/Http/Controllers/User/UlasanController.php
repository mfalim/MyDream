<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Client;
use App\Models\Event;
use App\Models\Review;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class UlasanController extends Controller
{
    public function index(Request $request)
    {
        $client = Client::where('users_id', auth()->id())->firstOrFail();

        $events = Event::query()
            ->where('status', 'completed')
            ->whereHas('booking', fn ($query) => $query
                ->where('client_id', $client->id)
                ->where('status', 'approved'))
            ->with('reviews')
            ->orderByDesc('event_date')
            ->get();

        $selectedEvent = null;
        if ($request->filled('event')) {
            $selectedEvent = $events->firstWhere('id', $request->integer('event'));
            abort_unless($selectedEvent !== null, 404);
        } elseif ($events->count() === 1) {
            $selectedEvent = $events->first();
        }

        $alreadyReviewed = $selectedEvent && $selectedEvent->reviews->isNotEmpty();

        $reviews = Review::with('event')
            ->where('client_id', $client->id)
            ->latest()
            ->paginate(8)
            ->withQueryString();

        return view('user.ulasan.index', compact('events', 'selectedEvent', 'alreadyReviewed', 'reviews'));
    }

    public function store(StoreReviewRequest $request)
    {
        $client = Client::where('users_id', auth()->id())->firstOrFail();
        $data = $request->validated();

        $event = Event::query()
            ->whereKey($data['event_id'])
            ->where('status', 'completed')
            ->whereHas('booking', fn ($query) => $query
                ->where('client_id', $client->id)
                ->where('status', 'approved'))
            ->first();

        if (! $event) {
            return back()->withInput()->withErrors(['event_id' => 'Acara selesai tidak tersedia untuk diulas.']);
        }

        if (Review::where('event_id', $event->id)->exists()) {
            return back()->withInput()->withErrors(['event_id' => 'Acara ini sudah pernah diulas.']);
        }

        try {
            Review::create([
                'event_id'   => $event->id,
                'client_id'  => $client->id,
                'rating'     => $data['rating'],
                'comment'    => $data['comment'] ?? null,
            ]);
        } catch (QueryException $exception) {
            if (! in_array($exception->getCode(), ['23000', '23505'], true)) {
                throw $exception;
            }

            return back()->withInput()->withErrors(['event_id' => 'Acara ini sudah pernah diulas.']);
        }

        return redirect()->route('user.reviews.index', ['event' => $event->id])
            ->with('success', 'Ulasan Anda berhasil dikirim. Terima kasih!');
    }
}
