<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Article;
use App\Models\Client;
use App\Models\Event;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            ->with('booking')
            ->orderByDesc('event_date')
            ->get();

        $selectedEvent = null;
        if ($request->filled('event')) {
            $selectedEvent = $events->firstWhere('id', $request->integer('event'));
            abort_unless($selectedEvent !== null, 404);
        } elseif ($events->count() === 1) {
            $selectedEvent = $events->first();
        }

        $alreadyPublished = $selectedEvent
            ? Article::where('event_id', $selectedEvent->id)->exists()
            : false;

        $stories = Article::query()
            ->with('event')
            ->where('client_id', $client->id)
            ->whereNotNull('event_id')
            ->orderByDesc('created_at')
            ->paginate(8)
            ->withQueryString();

        return view('user.ulasan.index', compact('events', 'selectedEvent', 'alreadyPublished', 'stories'));
    }

    public function store(StoreReviewRequest $request)
    {
        $client = Client::where('users_id', auth()->id())->firstOrFail();
        $data = $request->validated();

        $event = Event::query()
            ->with('booking')
            ->whereKey($data['event_id'])
            ->where('status', 'completed')
            ->whereHas('booking', fn ($query) => $query
                ->where('client_id', $client->id)
                ->where('status', 'approved'))
            ->first();

        if (! $event) {
            return back()->withInput()->withErrors(['event_id' => 'Acara selesai tidak tersedia untuk diulas.']);
        }

        if (Article::where('event_id', $event->id)->exists()) {
            return back()->withInput()->withErrors(['event_id' => 'Acara ini sudah memiliki ulasan.']);
        }

        $photoPath = $request->file('photo')?->store('articles', 'public');
        $comment = trim((string) ($data['comment'] ?? ''));
        $content = $comment !== ''
            ? $comment
            : 'Acara ini mendapatkan rating ' . $data['rating'] . ' dari 5 bintang.';

        try {
            Article::create([
                'title' => $event->name,
                'category' => 'Ulasan Client',
                'excerpt' => $data['excerpt'] ?? null,
                'content' => $content,
                'photo' => $photoPath,
                'author' => 'Client MyDream',
                'published_at' => today(),
                'package_id' => $event->package_id ?: $event->booking->package_id,
                'is_published' => true,
                'event_id' => $event->id,
                'client_id' => $client->id,
                'rating' => $data['rating'],
            ]);
        } catch (QueryException $exception) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

            if (in_array($exception->getCode(), ['23000', '23505'], true)) {
                return back()->withInput()->withErrors(['event_id' => 'Acara ini sudah memiliki ulasan.']);
            }

            throw $exception;
        }

        return redirect()->route('user.reviews.index', ['event' => $event->id])
            ->with('success', 'Ulasan acara berhasil dipublikasikan di Blog.');
    }
}
