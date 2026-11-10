<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inspiration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Admin: tinjau cerita/inspirasi yang dikirim client sebelum tayang di /inspirasi. */
class InspirationController extends Controller
{
    private const STATUSES = ['pending' => 'Menunggu', 'approved' => 'Tayang', 'rejected' => 'Perlu Revisi'];

    public function index(Request $request): View
    {
        $status = array_key_exists($request->query('status'), self::STATUSES) ? $request->query('status') : 'pending';

        return view('admin.inspirations.index', [
            'items' => Inspiration::with('photos')->where('status', $status)->latest('submitted_at')->latest('id')->get(),
            'status' => $status,
            'statuses' => self::STATUSES,
            'counts' => Inspiration::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }

    public function edit(Inspiration $inspiration): View
    {
        $inspiration->load(['photos', 'package']);

        return view('admin.inspirations.edit', [
            'item' => $inspiration,
            'themes' => Inspiration::THEMES,
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(Request $request, Inspiration $inspiration): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:150',
            'theme' => ['required', Rule::in(Inspiration::THEMES)],
            'story' => 'required|string|min:20|max:5000',
            'tips' => 'nullable|string|max:2000',
            'testimonial' => 'nullable|string|max:1000',
            'show_couple_names' => 'required|in:0,1',
            'status' => ['required', Rule::in(array_keys(self::STATUSES))],
            'admin_note' => 'nullable|string|max:1000',
            'delete_photos' => 'nullable|array',
            'delete_photos.*' => 'integer',
        ], [
            'title.required' => 'Judul harus diisi',
            'story.required' => 'Cerita harus diisi',
            'status.in' => 'Status tidak valid',
        ]);

        $deleteIds = $inspiration->photos->whereIn('id', (array) $request->input('delete_photos', []))->pluck('id');
        $remaining = $inspiration->photos->count() - $deleteIds->count();

        if ($data['status'] === 'approved' && $remaining < 1) {
            return back()->withInput()->withErrors(['status' => 'Cerita yang tayang harus punya minimal 1 foto.']);
        }
        if ($data['status'] === 'rejected' && blank($data['admin_note'] ?? null)) {
            return back()->withInput()->withErrors(['admin_note' => 'Tulis catatan agar client tahu apa yang perlu direvisi.']);
        }

        $statusChanged = $data['status'] !== $inspiration->status;

        $inspiration->update([
            'title' => $data['title'],
            'theme' => $data['theme'],
            'story' => $data['story'],
            'tips' => $data['tips'] ?? null,
            'testimonial' => $data['testimonial'] ?? null,
            'show_couple_names' => $data['show_couple_names'] === '1',
            'status' => $data['status'],
            'admin_note' => $data['admin_note'] ?? null,
            'reviewed_at' => $statusChanged ? now() : $inspiration->reviewed_at,
        ]);

        foreach ($inspiration->photos->whereIn('id', $deleteIds) as $photo) {
            $this->deleteFile($photo->photo);
            $photo->delete();
        }

        return redirect()->route('admin.inspirations.index', ['status' => $data['status']])
            ->with('success', 'Cerita diperbarui: ' . self::STATUSES[$data['status']] . '.');
    }

    public function destroy(Inspiration $inspiration): RedirectResponse
    {
        foreach ($inspiration->photos as $photo) {
            $this->deleteFile($photo->photo);
        }
        $inspiration->delete();

        return redirect()->route('admin.inspirations.index')->with('success', 'Cerita berhasil dihapus.');
    }

    private function deleteFile(string $path): void
    {
        if (! Str::startsWith($path, ['http://', 'https://'])) {
            Storage::disk('public')->delete($path);
        }
    }
}
