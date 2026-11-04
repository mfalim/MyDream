<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\ProvidesCrudResponses;
use App\Http\Controllers\Controller;
use App\Models\OrganizerEvent;
use Illuminate\Http\Request;

class OrganizerEventController extends Controller
{
    use ProvidesCrudResponses;

    public function index(Request $request)
    {
        return $this->paginated($request, OrganizerEvent::class, orderByKey: true);
    }

    public function store(Request $request)
    {
        return response()->json(['data' => OrganizerEvent::create($this->validated($request))], 201);
    }

    public function show(int $id)
    {
        return response()->json(['data' => $this->findRecord(OrganizerEvent::class, $id)]);
    }

    public function update(Request $request, int $id)
    {
        $event = OrganizerEvent::findOrFail($id);
        $event->update($this->validated($request, true));

        return response()->json(['data' => $event->refresh()]);
    }

    public function destroy(int $id)
    {
        return $this->deleteRecord(OrganizerEvent::class, $id);
    }

    private function validated(Request $request, bool $partial = false): array
    {
        return $request->validate([
            'title' => [$partial ? 'sometimes' : 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_date' => [$partial ? 'sometimes' : 'required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'event_type' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'priority' => ['nullable', 'string', 'max:50'],
            'attendees' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
