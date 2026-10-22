<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    /**
     * Menampilkan daftar layanan di katalog.
     */
    public function index()
    {
        $layanans = Layanan::latest()->get();

        return view('vendor.katalog', compact('layanans'));
    }

    /**
     * Menampilkan form tambah layanan.
     */
    public function create()
    {
        return view('vendor.tambahlayanan');
    }

    /**
     * Menyimpan layanan baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',

            'dimensi_panggung' => 'required|string|max:255',
            'daya_listrik_rigging' => 'required|string|max:255',
            'alokasi_kru' => 'required|string|max:255',
            'durasi_loading_teardown' => 'required|string|max:255',

            'deskripsi' => 'required|string',

            'status' => 'nullable|in:Draft Vendor,Aktif,Selesai,Nonaktif',

            'grand_entrance_gate' => 'nullable|string',
            'meja_akad' => 'nullable|string',
            'aisle_carpet' => 'nullable|string',
            'photo_booth' => 'nullable|string',
        ]);

        $layanan = new Layanan();

        $layanan->nama_layanan = $validated['nama_layanan'];
        $layanan->kategori = $validated['kategori'];
        $layanan->harga = $validated['harga'];

        $layanan->dimensi_panggung = $validated['dimensi_panggung'];
        $layanan->daya_listrik_rigging = $validated['daya_listrik_rigging'];
        $layanan->alokasi_kru = $validated['alokasi_kru'];
        $layanan->durasi_loading_teardown = $validated['durasi_loading_teardown'];

        $layanan->deskripsi = $validated['deskripsi'];

        // Checkbox dicentang: simpan nama fasilitas.
        // Checkbox tidak dicentang: simpan NULL.
        $layanan->grand_entrance_gate = $request->has('grand_entrance_gate')
            ? 'Grand Entrance Gate 4×3 Meter'
            : null;

        $layanan->meja_akad = $request->has('meja_akad')
            ? 'Meja Akad / Pemberkatan Khusus'
            : null;

        $layanan->aisle_carpet = $request->has('aisle_carpet')
            ? 'Aisle Carpet & 10 Standing Flowers'
            : null;

        $layanan->photo_booth = $request->has('photo_booth')
            ? 'Photo Booth & Galeri Foto Interaktif'
            : null;

        $layanan->status = $validated['status'] ?? 'Draft Vendor';

        $layanan->save();

        return redirect()
            ->route('vendor.katalog')
            ->with('success', 'Layanan berhasil ditambahkan.');
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
     * Memperbarui layanan.
     */
    public function update(Request $request, $id)
    {
        $layanan = Layanan::findOrFail($id);

        $validated = $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',

            'dimensi_panggung' => 'required|string|max:255',
            'daya_listrik_rigging' => 'required|string|max:255',
            'alokasi_kru' => 'required|string|max:255',
            'durasi_loading_teardown' => 'required|string|max:255',

            'deskripsi' => 'required|string',

            'status' => 'required|in:Draft Vendor,Aktif,Selesai,Nonaktif',

            'grand_entrance_gate' => 'nullable|string',
            'meja_akad' => 'nullable|string',
            'aisle_carpet' => 'nullable|string',
            'photo_booth' => 'nullable|string',
        ]);

        $layanan->nama_layanan = $validated['nama_layanan'];
        $layanan->kategori = $validated['kategori'];
        $layanan->harga = $validated['harga'];

        $layanan->dimensi_panggung = $validated['dimensi_panggung'];
        $layanan->daya_listrik_rigging = $validated['daya_listrik_rigging'];
        $layanan->alokasi_kru = $validated['alokasi_kru'];
        $layanan->durasi_loading_teardown = $validated['durasi_loading_teardown'];

        $layanan->deskripsi = $validated['deskripsi'];

        // Normalisasi checkbox agar database konsisten.
        $layanan->grand_entrance_gate = $request->has('grand_entrance_gate')
            ? 'Grand Entrance Gate 4×3 Meter'
            : null;

        $layanan->meja_akad = $request->has('meja_akad')
            ? 'Meja Akad / Pemberkatan Khusus'
            : null;

        $layanan->aisle_carpet = $request->has('aisle_carpet')
            ? 'Aisle Carpet & 10 Standing Flowers'
            : null;

        $layanan->photo_booth = $request->has('photo_booth')
            ? 'Photo Booth & Galeri Foto Interaktif'
            : null;

        $layanan->status = $validated['status'];

        $layanan->save();

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
