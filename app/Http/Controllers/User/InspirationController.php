<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Inspiration;
use App\Models\InspirationPhoto;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Dashboard client: menulis cerita/inspirasi pernikahan.
 * Aturan: hanya untuk pesanan yang disetujui admin DAN tanggal acaranya sudah lewat,
 * supaya tidak ada cerita palsu. Data acara disalin dari pesanan, bukan diketik client.
 */
class InspirationController extends Controller
{
    private const MAX_PHOTOS = 8;

    public function index(): View|RedirectResponse
    {
        $client = $this->client();
        if (! $client) {
            return redirect()->route('client.profile')->with('error', 'Lengkapi data mempelai terlebih dahulu');
        }

        $bookings = Booking::with(['package', 'events'])
            ->where('client_id', $client->id)
            ->where('status', 'approved')
            ->latest('id')
            ->get();

        $stories = Inspiration::whereIn('booking_id', $bookings->pluck('id'))->get()->keyBy('booking_id');

        $items = $bookings
            ->map(function (Booking $booking) use ($stories) {
                $date = $this->weddingDate($booking);

                return [
                    'booking' => $booking,
                    'date' => $date,
                    'open' => $date && $date->lt(today()),
                    'story' => $stories->get($booking->id),
                ];
            })
            ->filter(fn ($item) => $item['date'])
            ->values();

        return view('user.inspiration.index', ['items' => $items]);
    }

    public function form(string $bookingId): View|RedirectResponse
    {
        $booking = $this->openBooking($bookingId, $error);
        if (! $booking) {
            return redirect()->route('user.inspiration')->with('error', $error);
        }

        $story = Inspiration::with('photos')->where('booking_id', $booking->id)->first();
        if ($story && $story->status === 'approved') {
            return redirect()->route('user.inspiration')->with('success', 'Cerita Anda sudah tayang di halaman Inspirasi.');
        }

        return view('user.inspiration.form', [
            'booking' => $booking,
            'date' => $this->weddingDate($booking),
            'story' => $story,
            'themes' => Inspiration::THEMES,
            'maxPhotos' => self::MAX_PHOTOS,
        ]);
    }

    public function save(Request $request, string $bookingId): RedirectResponse
    {
        $booking = $this->openBooking($bookingId, $error);
        if (! $booking) {
            return redirect()->route('user.inspiration')->with('error', $error);
        }

        $existing = Inspiration::with('photos')->where('booking_id', $booking->id)->first();
        if ($existing && $existing->status === 'approved') {
            return redirect()->route('user.inspiration')->with('success', 'Cerita Anda sudah tayang di halaman Inspirasi.');
        }

        $request->validate([
            'title' => 'required|string|max:150',
            'theme' => ['required', Rule::in(Inspiration::THEMES)],
            'story' => 'required|string|min:50|max:5000',
            'tips' => 'nullable|string|max:2000',
            'rating' => 'required|integer|between:1,5',
            'testimonial' => 'nullable|string|max:1000',
            'show_couple_names' => 'required|in:0,1',
            'consent' => 'accepted',
            'photos' => 'nullable|array|max:' . self::MAX_PHOTOS,
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'delete_photos' => 'nullable|array',
            'delete_photos.*' => 'integer',
        ], [
            'title.required' => 'Judul cerita harus diisi',
            'theme.required' => 'Pilih tema acara',
            'theme.in' => 'Tema acara tidak valid',
            'story.required' => 'Ceritakan bagaimana acara Anda berlangsung',
            'story.min' => 'Cerita minimal 50 karakter',
            'rating.required' => 'Beri penilaian untuk MyDream',
            'consent.accepted' => 'Anda harus menyetujui publikasi cerita dan foto',
            'photos.max' => 'Maksimal ' . self::MAX_PHOTOS . ' foto',
            'photos.*.image' => 'File harus berupa gambar',
            'photos.*.mimes' => 'Format gambar harus jpg, jpeg, png, atau webp',
            'photos.*.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        // Hanya foto milik cerita ini yang boleh dihapus.
        $deleteIds = $existing
            ? $existing->photos->whereIn('id', (array) $request->input('delete_photos', []))->pluck('id')
            : collect();
        $remaining = ($existing?->photos->count() ?? 0) - $deleteIds->count();
        $total = $remaining + count($request->file('photos', []));

        if ($total < 1) {
            return back()->withInput()->withErrors(['photos' => 'Unggah minimal 1 foto acara Anda.']);
        }
        if ($total > self::MAX_PHOTOS) {
            return back()->withInput()->withErrors(['photos' => 'Total foto maksimal ' . self::MAX_PHOTOS . '.']);
        }

        $client = $booking->client;

        DB::transaction(function () use ($request, $booking, $client, $existing, $deleteIds) {
            $story = $existing ?? new Inspiration();

            $story->fill([
                'booking_id' => $booking->id,
                'package_id' => $booking->package_id,
                'title' => $request->input('title'),
                'theme' => $request->input('theme'),
                // Disalin dari pesanan, bukan dari isian client:
                'couple_names' => trim($client->groom_name . ' & ' . $client->bride_name),
                'venue_name' => $booking->venue_name,
                'city' => $booking->venue_city,
                'event_date' => $this->weddingDate($booking)->toDateString(),
                'guest_count' => $booking->guest_count,
                'show_couple_names' => $request->input('show_couple_names') === '1',
                'story' => $request->input('story'),
                'tips' => $request->input('tips'),
                'testimonial' => $request->input('testimonial'),
                'rating' => (int) $request->input('rating'),
                'status' => 'pending',
                'admin_note' => null,
                'consented_at' => now(),
                'submitted_at' => now(),
                'reviewed_at' => null,
            ])->save();

            if ($existing) {
                foreach ($existing->photos->whereIn('id', $deleteIds) as $photo) {
                    $this->deleteFile($photo->photo);
                    $photo->delete();
                }
            }

            $order = (int) ($story->photos()->max('sort_order') ?? -1);
            foreach ($request->file('photos', []) as $file) {
                InspirationPhoto::create([
                    'inspiration_id' => $story->id,
                    'photo' => $file->store('inspirations', 'public'),
                    'sort_order' => ++$order,
                ]);
            }
        });

        return redirect()->route('user.inspiration')
            ->with('success', 'Terima kasih! Cerita Anda terkirim dan akan ditinjau tim MyDream sebelum tayang.');
    }

    private function client(): ?Client
    {
        return Client::where('users_id', Auth::id())->first();
    }

    /** Tanggal acara terakhir pada pesanan ini (null jika belum ada acara). */
    private function weddingDate(Booking $booking): ?Carbon
    {
        return $booking->events->max('event_date');
    }

    /** Pesanan milik client yang disetujui dan acaranya sudah berlalu; jika tidak, $error berisi alasannya. */
    private function openBooking(string $bookingId, ?string &$error = null): ?Booking
    {
        $client = $this->client();
        if (! $client) {
            $error = 'Lengkapi data mempelai terlebih dahulu.';

            return null;
        }

        $booking = Booking::with(['client', 'package', 'events'])
            ->where('client_id', $client->id)
            ->where('status', 'approved')
            ->find($bookingId);

        if (! $booking) {
            $error = 'Pernikahan tidak ditemukan.';

            return null;
        }

        $date = $this->weddingDate($booking);
        if (! $date || ! $date->lt(today())) {
            $error = 'Form cerita dibuka setelah tanggal pernikahan Anda berlalu.';

            return null;
        }

        return $booking;
    }

    private function deleteFile(string $path): void
    {
        if (! Str::startsWith($path, ['http://', 'https://'])) {
            Storage::disk('public')->delete($path);
        }
    }
}
