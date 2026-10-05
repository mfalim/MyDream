<?php
namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    /**
     * Menampilkan daftar layanan.
     */
    public function index()
    {
        $layanans = Layanan::latest()->get();

        return view('vendor.katalog', compact('layanans'));
    }

    /**
     * Menyimpan layanan baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'harga' => 'required|numeric',

            'dimensi_panggung' => 'required|string|max:255',
            'daya_listrik_rigging' => 'required|string|max:255',
            'alokasi_kru' => 'required|string|max:255',
            'durasi_loading_teardown' => 'required|string|max:255',

            'deskripsi' => 'required|string|max:500',

            'grand_entrance_gate' => 'nullable|string|max:255',
            'meja_akad' => 'nullable|string|max:255',
            'aisle_carpet' => 'nullable|string|max:255',
            'photo_booth' => 'nullable|string|max:255',

            'foto_utama' => 'nullable|string|max:255',
            'gallery_1' => 'nullable|string|max:255',
            'gallery_2' => 'nullable|string|max:255',
            'gallery_3' => 'nullable|string|max:255',
            'gallery_4' => 'nullable|string|max:255',

            'status' => 'nullable|in:Draft Vendor,Aktif,Selesai,Nonaktif',
        ]);

        /*
         * Checkbox yang tidak dicentang tidak dikirim oleh browser.
         * Kita pastikan nilainya menjadi NULL.
         */
        $validated['grand_entrance_gate'] = $request->input('grand_entrance_gate') ?: null;
        $validated['meja_akad'] = $request->input('meja_akad') ?: null;
        $validated['aisle_carpet'] = $request->input('aisle_carpet') ?: null;
        $validated['photo_booth'] = $request->input('photo_booth') ?: null;

        $validated['status'] = $validated['status'] ?? 'Draft Vendor';

        Layanan::create($validated);

        return redirect()
            ->route('vendor.katalog')
            ->with('success', 'Layanan berhasil disimpan.');
    }

    /**
     * Menampilkan form edit layanan.
     */
    public function edit($id)
    {
        $layanan = Layanan::findOrFail($id);

        return view('vendor.editlayanan', compact('layanan'));
    }

    /**
     * Mengupdate layanan.
     */
    public function update(Request $request, $id)
    {
        $layanan = Layanan::findOrFail($id);

        $validated = $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'harga' => 'required|numeric',

            'dimensi_panggung' => 'nullable|string|max:255',
            'daya_listrik_rigging' => 'nullable|string|max:255',
            'alokasi_kru' => 'nullable|string|max:255',
            'durasi_loading_teardown' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',

            'grand_entrance_gate' => 'nullable|string|max:255',
            'meja_akad' => 'nullable|string|max:255',
            'aisle_carpet' => 'nullable|string|max:255',
            'photo_booth' => 'nullable|string|max:255',

            'foto_utama' => 'nullable|string|max:255',
            'gallery_1' => 'nullable|string|max:255',
            'gallery_2' => 'nullable|string|max:255',
            'gallery_3' => 'nullable|string|max:255',
            'gallery_4' => 'nullable|string|max:255',

            'status' => 'nullable|in:Draft Vendor,Aktif,Selesai,Nonaktif',
        ]);

        /*
         * Checkbox:
         * Dicentang     = simpan teks fasilitas
         * Tidak dicentang = simpan NULL
         */
        $validated['grand_entrance_gate'] = $request->input('grand_entrance_gate') ?: null;
        $validated['meja_akad'] = $request->input('meja_akad') ?: null;
        $validated['aisle_carpet'] = $request->input('aisle_carpet') ?: null;
        $validated['photo_booth'] = $request->input('photo_booth') ?: null;

        $validated['status'] = $validated['status'] ?? $layanan->status;

        $layanan->update($validated);

        return redirect()
            ->route('vendor.katalog')
            ->with('success', 'Layanan berhasil diperbarui.');
    }

    /**
     * Menghapus layanan.
     */
    public function destroy($id)
    {
        $layanan = Layanan::findOrFail($id);

        $layanan->delete();

        return redirect()
            ->route('vendor.katalog')
            ->with('success', 'Layanan berhasil dihapus.');
    }
}