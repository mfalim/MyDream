<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',

            'dimensi_panggung' => 'nullable|string',
            'daya_listrik_rigging' => 'nullable|string',
            'alokasi_kru' => 'nullable|string',
            'durasi_loading_teardown' => 'nullable|string',

            'deskripsi' => 'nullable|string',

            'grand_entrance_gate' => 'nullable|boolean',
            'meja_akad' => 'nullable|boolean',
            'aisle_carpet' => 'nullable|boolean',
            'photo_booth' => 'nullable|boolean',

            'foto_utama' => 'nullable|image|max:5120',
            'gallery_1' => 'nullable|image|max:5120',
            'gallery_2' => 'nullable|image|max:5120',
            'gallery_3' => 'nullable|image|max:5120',
            'gallery_4' => 'nullable|image|max:5120',
        ]);

        $validated['grand_entrance_gate'] = $request->has('grand_entrance_gate');
        $validated['meja_akad'] = $request->has('meja_akad');
        $validated['aisle_carpet'] = $request->has('aisle_carpet');
        $validated['photo_booth'] = $request->has('photo_booth');

        $validated['status'] = 'draft';

        if ($request->hasFile('foto_utama')) {
            $validated['foto_utama'] =
                $request->file('foto_utama')->store('layanan', 'public');
        }

        for ($i = 1; $i <= 4; $i++) {
            if ($request->hasFile("gallery_$i")) {
                $validated["gallery_$i"] =
                    $request->file("gallery_$i")->store('layanan/gallery', 'public');
            }
        }

        Layanan::create($validated);

        return redirect()
            ->route('vendor.tambahlayanan')
            ->with('success', 'Layanan berhasil disimpan sebagai draft.');
    }
}