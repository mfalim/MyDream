<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\ProvidesCrudResponses;
use App\Http\Controllers\Controller;
use App\Models\PublicEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicEventController extends Controller
{
    use ProvidesCrudResponses;

    public function index(Request $request)
    {
        return $this->paginated($request, PublicEvent::class, orderByKey: true);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $event = new PublicEvent();
        $event->fill($data);
        if (blank($event->slug)) {
            $event->slug = Str::slug($event->title);
        }
        $event->save();
        if (array_key_exists('vendor_ids', $data)) {
            $event->vendors()->sync($data['vendor_ids'] ?? []);
        }
        $event->refresh();

        return response()->json(['data' => $event], 201);
    }

    public function show(int $id)
    {
        return response()->json(['data' => $this->findRecord(PublicEvent::class, $id)]);
    }

    public function update(Request $request, int $id)
    {
        $event = PublicEvent::findOrFail($id);
        $data = $this->validated($request, $event, true);
        $vendorIds = $data['vendor_ids'] ?? null;
        unset($data['vendor_ids']);
        $event->fill($data)->save();
        if ($vendorIds !== null) {
            $event->vendors()->sync($vendorIds);
        }

        return response()->json(['data' => $event->refresh()]);
    }

    public function destroy(int $id)
    {
        return $this->deleteRecord(PublicEvent::class, $id);
    }

    private function validated(Request $request, ?PublicEvent $event = null, bool $partial = false): array
    {
        $data = $request->validate([
            'title' => [$partial ? 'sometimes' : 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', 'unique:public_events,slug' . ($event ? ',' . $event->getKey() : '')],
            'description' => ['nullable', 'string'],
            'starts_on' => [$partial ? 'sometimes' : 'required', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'location' => ['nullable', 'string', 'max:255'],
            'is_published' => ['sometimes', 'boolean'],
            'vendor_ids' => ['sometimes', 'array'],
            'vendor_ids.*' => ['integer', 'distinct', 'exists:vendors,id'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('public-events', 'public');
        }

        return $data;
    }
}
