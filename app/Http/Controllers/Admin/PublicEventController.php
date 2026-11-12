<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\PublicEvent;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Admin: Kelola publikasi & promosi event ke halaman publik website (/event).
 * Alur: Memilih acara yang sudah ada di sistem (Event/Booking) untuk dipromosikan ke calon klien lain.
 */
class PublicEventController extends Controller
{
    private function rules(): array
    {
        return [
            'event_id' => 'nullable|exists:events,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'starts_on' => 'required|date',
            'ends_on' => 'nullable|date|after_or_equal:starts_on',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'vendor_ids' => 'nullable|array',
            'vendor_ids.*' => 'integer|exists:vendors,id',
        ];
    }

    private function messages(): array
    {
        return [
            'event_id.exists' => 'Acara yang dipilih tidak valid',
            'title.required' => 'Judul event promosi harus diisi',
            'title.max' => 'Judul event maksimal 255 karakter',
            'description.max' => 'Deskripsi promosi maksimal 5000 karakter',
            'photo.image' => 'File harus berupa gambar',
            'photo.mimes' => 'Format gambar harus jpg, jpeg, png, atau webp',
            'photo.max' => 'Ukuran gambar maksimal 2MB',
            'starts_on.required' => 'Tanggal mulai harus diisi',
            'starts_on.date' => 'Tanggal mulai tidak valid',
            'ends_on.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai',
            'start_time.date_format' => 'Jam mulai harus berformat JJ:MM',
            'end_time.date_format' => 'Jam selesai harus berformat JJ:MM',
            'location.max' => 'Lokasi maksimal 255 karakter',
            'vendor_ids.*.exists' => 'Vendor rekanan tidak valid',
        ];
    }

    public function index(): View
    {
        // 1. Event yang saat ini sedang dipromosikan / dipublikasikan di web
        $promotedEvents = PublicEvent::with([
            'originalEvent.booking.client',
            'originalEvent.package',
            'vendors.category'
        ])
        ->orderByDesc('starts_on')
        ->get();

        // 2. Seluruh event operasional yang ada di MyDream (dari Booking & Acara)
        $operationalEvents = Event::with([
            'booking.client',
            'package.vendors.category',
            'schedules.vendor.category',
            'publicEvent',
        ])
        ->whereHas('booking', function ($q) {
            $q->whereIn('status', ['approved', 'confirmed', 'completed']);
        })
        ->orWhereNull('booking_id')
        ->orderByDesc('event_date')
        ->get();

        return view('admin.public-events.index', [
            'events' => $promotedEvents,
            'operationalEvents' => $operationalEvents,
        ]);
    }

    public function create(Request $request): View
    {
        $selectedEventId = $request->query('event_id');
        $selectedEvent = null;

        if ($selectedEventId) {
            $selectedEvent = Event::with([
                'booking.client',
                'booking.package.vendors.category',
                'package.vendors.category',
                'schedules.vendor.category',
            ])->find($selectedEventId);
        }

        $availableEvents = Event::with([
            'booking.client',
            'booking.package.vendors.category',
            'package.vendors.category',
            'schedules.vendor.category',
        ])
        ->orderByDesc('event_date')
        ->get();

        $vendors = Vendor::with('category')->orderBy('name')->get();

        return view('admin.public-events.create', [
            'selectedEvent' => $selectedEvent,
            'availableEvents' => $availableEvents,
            'vendors' => $vendors,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate($this->rules(), $this->messages());

        $data = $request->only([
            'event_id',
            'title',
            'description',
            'starts_on',
            'ends_on',
            'start_time',
            'end_time',
            'location'
        ]);
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('events', 'public');
        } elseif (!empty($request->event_id)) {
            // Gunakan foto bawaan event jika tidak ada unggahan foto promosi baru
            $originalEvent = Event::with(['package', 'booking.package'])->find($request->event_id);
            if ($originalEvent) {
                $data['photo'] = $originalEvent->photo ?: ($originalEvent->package?->photo ?: $originalEvent->booking?->package?->photo);
            }
        }

        $event = PublicEvent::create($data);

        // Jika ditautkan dengan acara yang ada, otomatis sinkronkan seluruh vendor yang terlibat di acara tersebut
        if (!empty($request->event_id)) {
            $originalEvent = Event::with([
                'booking.package.vendors',
                'package.vendors',
                'schedules.vendor'
            ])->find($request->event_id);

            $vendorIds = $originalEvent ? $originalEvent->all_vendors->pluck('id')->all() : [];
            $event->vendors()->sync($vendorIds);
        } else {
            $event->vendors()->sync($request->input('vendor_ids', []));
        }

        return redirect()->route('admin.public-events.index')
            ->with('success', 'Event "' . $event->title . '" berhasil dipromosikan ke halaman publik.');
    }

    /**
     * Aksi Cepat 1-Klik: Langsung promosikan event yang sudah ada dengan data bawaan.
     */
    public function quickPromote(Request $request, $eventId): RedirectResponse
    {
        $event = Event::with([
            'booking.client',
            'package.vendors',
            'schedules.vendor',
            'publicEvent',
        ])->findOrFail($eventId);

        if ($event->publicEvent) {
            return redirect()->route('admin.public-events.index')
                ->with('info', 'Acara ini sudah berada dalam daftar promosi publik.');
        }

        $title = $event->booking?->client
            ? 'Pernikahan ' . $event->booking->client->groom_name . ' & ' . $event->booking->client->bride_name . ($event->name ? ' (' . $event->name . ')' : '')
            : ($event->name ?? 'Acara Pernikahan');

        $location = $event->booking?->venue_name
            ? $event->booking->venue_name . ($event->booking->venue_city ? ', ' . $event->booking->venue_city : '')
            : ($event->notes ?: 'Venue Rekanan MyDream');

        // Kumpulkan vendor yang berpartisipasi
        $vendorIds = collect();
        if ($event->schedules) {
            $vendorIds = $vendorIds->merge($event->schedules->pluck('vendor_id')->filter());
        }
        if ($event->package && $event->package->vendors) {
            $vendorIds = $vendorIds->merge($event->package->vendors->pluck('id'));
        }

        $startTimeStr = null;
        if ($event->start_time) {
            $startTimeStr = is_string($event->start_time) ? substr($event->start_time, 0, 5) : $event->start_time->format('H:i');
        }

        $endTimeStr = null;
        if ($event->end_time) {
            $endTimeStr = is_string($event->end_time) ? substr($event->end_time, 0, 5) : $event->end_time->format('H:i');
        }

        $publicEvent = PublicEvent::create([
            'event_id' => $event->id,
            'title' => $title,
            'description' => 'Saksikan keindahan dan kemegahan ' . $title . ' yang ditangani oleh MyDream Wedding Organizer. Hadir sebagai inspirasi konsep dekorasi, katering, dan susunan acara terbaik untuk pernikahan impian Anda.',
            'starts_on' => $event->event_date ?? today(),
            'start_time' => $startTimeStr,
            'end_time' => $endTimeStr,
            'location' => $location,
            'photo' => $event->photo ?: $event->booking?->package?->photo,
            'is_published' => true,
        ]);

        if ($vendorIds->isNotEmpty()) {
            $publicEvent->vendors()->sync($vendorIds->unique()->values()->all());
        }

        return redirect()->route('admin.public-events.index')
            ->with('success', 'Acara "' . $title . '" berhasil dipromosikan ke website publik!');
    }

    public function togglePublish(PublicEvent $event): RedirectResponse
    {
        $event->is_published = ! $event->is_published;
        $event->save();

        $statusText = $event->is_published ? 'Aktif (Tampil di Website)' : 'Disembunyikan (Draft)';

        return redirect()->route('admin.public-events.index')
            ->with('success', 'Status promosi acara berhasil diubah menjadi: ' . $statusText);
    }

    public function edit(PublicEvent $event): View
    {
        $event->load([
            'vendors.category',
            'originalEvent.booking.client',
            'originalEvent.booking.package.vendors.category',
            'originalEvent.package.vendors.category',
            'originalEvent.schedules.vendor.category',
        ]);

        $availableEvents = Event::with([
            'booking.client',
            'booking.package.vendors.category',
            'package.vendors.category',
            'schedules.vendor.category',
        ])
        ->orderByDesc('event_date')
        ->get();

        return view('admin.public-events.edit', [
            'event' => $event,
            'selectedEvent' => $event->originalEvent,
            'availableEvents' => $availableEvents,
            'vendors' => Vendor::with('category')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, PublicEvent $event): RedirectResponse
    {
        $request->validate($this->rules(), $this->messages());

        $data = $request->only([
            'event_id',
            'title',
            'description',
            'starts_on',
            'ends_on',
            'start_time',
            'end_time',
            'location'
        ]);
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('photo')) {
            $this->deletePhoto($event);
            $data['photo'] = $request->file('photo')->store('events', 'public');
        } elseif ($request->boolean('remove_photo')) {
            $this->deletePhoto($event);
            $data['photo'] = null;
        }

        $event->update($data);

        // Jika ditautkan dengan acara yang ada, otomatis sinkronkan seluruh vendor yang terlibat di acara tersebut
        if (!empty($request->event_id)) {
            $originalEvent = Event::with([
                'booking.package.vendors',
                'package.vendors',
                'schedules.vendor'
            ])->find($request->event_id);

            $vendorIds = $originalEvent ? $originalEvent->all_vendors->pluck('id')->all() : [];
            $event->vendors()->sync($vendorIds);
        } else {
            $event->vendors()->sync($request->input('vendor_ids', []));
        }

        return redirect()->route('admin.public-events.index')
            ->with('success', 'Promosi event "' . $event->title . '" berhasil diperbarui.');
    }

    public function destroy(PublicEvent $event): RedirectResponse
    {
        $this->deletePhoto($event);
        $event->delete();

        return redirect()->route('admin.public-events.index')
            ->with('success', 'Event berhasil ditarik dari promosi website publik.');
    }

    private function deletePhoto(PublicEvent $event): void
    {
        if ($event->photo && ! Str::startsWith($event->photo, ['http://', 'https://'])) {
            Storage::disk('public')->delete($event->photo);
        }
    }
}
