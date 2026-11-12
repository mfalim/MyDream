<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with(['booking.client', 'package', 'schedules.vendor', 'eventMembers.member'])->latest('event_date');

        return response()->json($query->paginate($request->integer('per_page', 20)));
    }

    public function store(Request $request)
    {
        $data = $this->eventData($request);
        $event = DB::transaction(function () use ($data) {
            $schedules = $data['schedules'] ?? [];
            unset($data['schedules']);
            $event = Event::create($data);
            foreach ($schedules as $schedule) {
                $vendor = isset($schedule['vendor_id']) ? Vendor::findOrFail($schedule['vendor_id']) : null;
                $event->schedules()->create($schedule + ['activity' => $vendor?->name ?? 'Activity']);
            }

            return $event;
        });

        return response()->json(['data' => $event->load('schedules.vendor')], 201);
    }

    public function show(int $id)
    {
        return response()->json(['data' => Event::with(['booking.client', 'package', 'schedules.vendor', 'schedules.member', 'eventMembers.member'])->findOrFail($id)]);
    }

    public function update(Request $request, int $id)
    {
        $event = Event::findOrFail($id);
        $data = $this->eventData($request, true);
        $schedules = $data['schedules'] ?? null;
        unset($data['schedules']);
        DB::transaction(function () use ($event, $data, $schedules) {
            $event->update($data);
            if ($schedules !== null) {
                $event->schedules()->delete();
                foreach ($schedules as $schedule) {
                    $vendor = isset($schedule['vendor_id']) ? Vendor::findOrFail($schedule['vendor_id']) : null;
                    $event->schedules()->create($schedule + ['activity' => $vendor?->name ?? 'Activity']);
                }
            }
        });

        return response()->json(['data' => $event->fresh()->load('schedules.vendor')]);
    }

    public function destroy(int $id)
    {
        Event::findOrFail($id)->delete();

        return response()->json(['message' => 'Event deleted.']);
    }

    private function eventData(Request $request, bool $partial = false): array
    {
        return $request->validate([
            'booking_id' => [$partial ? 'sometimes' : 'required', 'exists:bookings,id'],
            'name' => [$partial ? 'sometimes' : 'required', 'string', 'max:255'],
            'event_type' => ['nullable', 'string', 'max:100'],
            'event_date' => [$partial ? 'sometimes' : 'required', 'date'],
            'guest_count' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'package_id' => ['nullable', 'exists:packages,id'],
            'notes' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:draft,scheduled,upcoming,ongoing,completed,cancelled'],
            'schedules' => ['sometimes', 'array'],
            'schedules.*.vendor_id' => ['nullable', 'exists:vendors,id'],
            'schedules.*.member_id' => ['nullable', 'exists:members,id'],
            'schedules.*.activity' => ['nullable', 'string', 'max:255'],
            'schedules.*.location' => ['nullable', 'string', 'max:255'],
            'schedules.*.start_time' => ['nullable', 'date_format:H:i'],
            'schedules.*.end_time' => ['nullable', 'date_format:H:i'],
            'schedules.*.status' => ['sometimes', 'in:pending,approved,rejected'],
            'schedules.*.notes' => ['nullable', 'string'],
        ]);
    }
}
