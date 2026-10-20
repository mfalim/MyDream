<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PublicEvent;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

/** Admin: kelola event publik yang tampil di halaman /event. */
class PublicEventController extends Controller
{
    private function rules(): array
    {
        return [
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
            'title.required' => 'Judul event harus diisi',
            'title.max' => 'Judul event maksimal 255 karakter',
            'description.max' => 'Deskripsi maksimal 5000 karakter',
            'photo.image' => 'File harus berupa gambar',
            'photo.mimes' => 'Format gambar harus jpg, jpeg, png, atau webp',
            'photo.max' => 'Ukuran gambar maksimal 2MB',
            'starts_on.required' => 'Tanggal mulai harus diisi',
            'starts_on.date' => 'Tanggal mulai tidak valid',
            'ends_on.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai',
            'start_time.date_format' => 'Jam mulai harus berformat JJ:MM',
            'end_time.date_format' => 'Jam selesai harus berformat JJ:MM',
            'location.max' => 'Lokasi maksimal 255 karakter',
            'vendor_ids.*.exists' => 'Vendor tidak valid',
        ];
    }

    public function index(): View
    {
        return view('admin.public-events.index', [
            'events' => PublicEvent::withCount('vendors')->orderByDesc('starts_on')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.public-events.create', [
            'vendors' => Vendor::with('category')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate($this->rules(), $this->messages());

        $data = $request->only(['title', 'description', 'starts_on', 'ends_on', 'start_time', 'end_time', 'location']);
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('events', 'public');
        }

        $event = PublicEvent::create($data);
        $event->vendors()->sync($request->input('vendor_ids', []));

        return redirect()->route('admin.public-events.index')->with('success', 'Event berhasil ditambahkan.');
    }

    public function edit(PublicEvent $event): View
    {
        $event->load('vendors');

        return view('admin.public-events.edit', [
            'event' => $event,
            'vendors' => Vendor::with('category')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, PublicEvent $event): RedirectResponse
    {
        $request->validate($this->rules(), $this->messages());

        $data = $request->only(['title', 'description', 'starts_on', 'ends_on', 'start_time', 'end_time', 'location']);
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('photo')) {
            $this->deletePhoto($event);
            $data['photo'] = $request->file('photo')->store('events', 'public');
        } elseif ($request->boolean('remove_photo')) {
            $this->deletePhoto($event);
            $data['photo'] = null;
        }

        $event->update($data);
        $event->vendors()->sync($request->input('vendor_ids', []));

        return redirect()->route('admin.public-events.index')->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(PublicEvent $event): RedirectResponse
    {
        $this->deletePhoto($event);
        $event->delete();

        return redirect()->route('admin.public-events.index')->with('success', 'Event berhasil dihapus.');
    }

    /** Hapus file foto hanya jika berupa file unggahan (bukan URL luar). */
    private function deletePhoto(PublicEvent $event): void
    {
        if ($event->photo && ! Str::startsWith($event->photo, ['http://', 'https://'])) {
            Storage::disk('public')->delete($event->photo);
        }
    }
}
