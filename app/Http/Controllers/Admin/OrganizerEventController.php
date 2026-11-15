<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrganizerEvent;
use App\Models\User;
use Illuminate\Http\Request;

class OrganizerEventController extends Controller
{
    public function index(Request $request)
    {
        $query = OrganizerEvent::query();

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('event_type', $request->type);
        }

        if ($request->filled('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('attendees', 'like', "%{$search}%");
            });
        }

        $organizerEvents = $query->orderByDesc('event_date')->paginate(15)->withQueryString();

        $stats = [
            'total' => OrganizerEvent::count(),
            'scheduled' => OrganizerEvent::where('status', 'scheduled')->count(),
            'completed' => OrganizerEvent::where('status', 'completed')->count(),
            'high_priority' => OrganizerEvent::where('priority', 'high')->count(),
        ];

        return view('admin.organizer_event.index', compact('organizerEvents', 'stats'));
    }

    public function create()
    {
        $users = User::all();
        return view('admin.organizer_event.form', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'event_type' => 'required|string',
            'location' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'attendees' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['status'] = 'scheduled';

        OrganizerEvent::create($validated);

        return redirect()->route('admin.organizer-event.index')->with('success', 'Organizer event created successfully!');
    }

    public function edit($id)
    {
        $organizerEvent = OrganizerEvent::findOrFail($id);
        $users = User::all();
        
        // Store the referrer in session for back navigation
        if (request()->headers->get('referer')) {
            session(['organizer_event_referrer' => request()->headers->get('referer')]);
        }
        
        return view('admin.organizer_event.form', compact('organizerEvent', 'users'));
    }

    public function show($id)
    {
        $organizerEvent = OrganizerEvent::findOrFail($id);
        
        // Store the referrer in session for back navigation
        if (request()->headers->get('referer')) {
            session(['organizer_event_detail_referrer' => request()->headers->get('referer')]);
        }
        
        return view('admin.organizer_event.detail', compact('organizerEvent'));
    }

    public function update(Request $request, $id)
    {
        $organizerEvent = OrganizerEvent::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'event_type' => 'required|string',
            'location' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'attendees' => 'nullable|string',
            'status' => 'required|in:scheduled,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $organizerEvent->update($validated);

        // Get referrer from session or default to index
        $referrer = session('organizer_event_referrer');
        session()->forget('organizer_event_referrer');
        
        if ($referrer && str_contains($referrer, 'calendar')) {
            return redirect()->route('admin.calendar')->with('success', 'Organizer event updated successfully!');
        }

        return redirect()->route('admin.organizer-event.index')->with('success', 'Organizer event updated successfully!');
    }

    public function destroy($id)
    {
        $organizerEvent = OrganizerEvent::findOrFail($id);
        $organizerEvent->delete();

        return redirect()->route('admin.organizer-event.index')->with('success', 'Organizer event deleted successfully!');
    }
}
