<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * VendorPortalController
 *
 * Setiap method mencoba mengambil data lewat Eloquent Model (App\Models\...).
 * Kalau model/tabel belum ada (atau query gagal), otomatis pakai data contoh
 * (fallback) supaya tampilan tetap bisa didemokan sambil model disiapkan.
 */
class VendorPortalController extends Controller
{
    // ID vendor yang sedang login. Nanti ganti dengan auth()->user()->vendor_id
    private int $vendorId = 1;

    /* ===================== DASHBOARD ===================== */

    public function dashboard()
    {
        $vendor = $this->getVendorProfile();

        $acaraHariIni = $this->tryEloquent(function () {
            // return \App\Models\BookingVendor::with(['booking.eventDays'])
            //     ->where('vendor_id', $this->vendorId)
            //     ->whereHas('booking.eventDays', fn($q) => $q->whereDate('event_date', today()))
            //     ->first();
            throw new \Exception('belum diimplementasi');
        }, [
            "event_name" => "The Royal Emerald",
            "venue" => "Grand Ballroom Hotel Mulia",
            "progress_percent" => 85,
        ]);

        $ringkasan = $this->tryEloquent(function () {
            throw new \Exception('belum diimplementasi');
        }, ["total" => 8, "selesai" => 3, "hari_ini" => 1, "mendatang" => 4]);

        $persenEscrow = 100;

        $timeline = $this->tryEloquent(function () {
            throw new \Exception('belum diimplementasi');
        }, [
            ["time_slot" => "06:00", "title" => "Loading In & Gate Pass Ballroom", "status" => "selesai", "progress_percent" => 100],
            ["time_slot" => "09:30", "title" => "Rangka Truss & Rigging LED Screen Pelaminan", "status" => "selesai", "progress_percent" => 100],
            ["time_slot" => "12:30", "title" => "Instalasi Bunga Segar & Flooring Pelaminan", "status" => "berjalan", "progress_percent" => 85, "note" => "Area pelaminan tengah dan backdrop gazebo mawar putih sedang dirangkai oleh tim floris 2."],
            ["time_slot" => "15:30", "title" => "Final Lighting Cue & Handover Bersama Klien", "status" => "standby", "progress_percent" => 0],
            ["time_slot" => "22:30", "title" => "Teardown / Loading Out Ballroom", "status" => "menunggu", "progress_percent" => 0],
        ]);

        $vendorOnSite = $this->tryEloquent(function () {
            throw new \Exception('belum diimplementasi');
        }, [
            ["name" => "The Leonardi Photography", "category" => "Visual & Drone", "pic_name" => "King Leonardi", "status_note" => "Sedang Test Lighting Pelaminan"],
            ["name" => "Sound & Light Dynamics", "category" => "Tata Cahaya", "pic_name" => "Rian Hidayat", "status_note" => "Sinkronisasi Beam ke Pelaminan"],
            ["name" => "Puspa Catering VIP", "category" => "Katering & Food Stall", "pic_name" => "Ibu Retno", "status_note" => "Setup Banquet Table samping dekor"],
            ["name" => "Le Novelle Cake", "category" => "Wedding Castle Cake", "pic_name" => "Miyama", "status_note" => "Koordinasi spot meja cake utama"],
        ]);

        $pipeline = $this->getPipelineAcara();

        return view('vendor.dashboard', compact(
            'vendor', 'acaraHariIni', 'ringkasan', 'persenEscrow', 'timeline', 'vendorOnSite', 'pipeline'
        ));
    }

    /* ===================== KALENDER ===================== */

    public function kalender(Request $request)
    {
        $vendor = $this->getVendorProfile();

        $bulan = (int) $request->query('bulan', 10);
        $tahun = (int) $request->query('tahun', 2025);
        if ($bulan < 1) { $bulan = 12; $tahun--; }
        if ($bulan > 12) { $bulan = 1; $tahun++; }

        $tanggalTerpilih = $request->query('tanggal', sprintf('%04d-%02d-25', $tahun, $bulan));

        $namaBulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $namaHari  = ['SEN','SEL','RAB','KAM','JUM','SAB','MIN'];

        $grid = $this->generateCalendarGrid($tahun, $bulan);
        $rangeStart = $grid[0][0]['date'];
        $rangeEnd   = end($grid)[6]['date'];

        $events = $this->tryEloquent(function () use ($rangeStart, $rangeEnd) {
            // return \App\Models\EventDay::whereBetween('event_date', [$rangeStart, $rangeEnd])
            //     ->whereHas('booking.vendors', fn($q) => $q->where('vendor_id', $this->vendorId))
            //     ->get()->groupBy(fn($e) => $e->event_date->format('Y-m-d'))->toArray();
            throw new \Exception('belum diimplementasi');
        }, [
            "2025-10-01" => [["pasangan" => null, "venue" => "Plotting Tim Workshop", "event_type" => "internal", "status" => ""]],
            "2025-10-03" => [["pasangan" => null, "venue" => "Loading In 22:00", "event_type" => "loading", "status" => ""]],
            "2025-10-04" => [["booking_id" => 101, "pasangan" => "Kevin & Michelle", "venue" => "The Dharmawangsa", "event_type" => "hari_h", "status" => "Selesai"]],
            "2025-10-05" => [["pasangan" => null, "venue" => "Maintenance Alat", "event_type" => "internal", "status" => ""]],
            "2025-10-10" => [["pasangan" => null, "venue" => "Loading Plataran", "event_type" => "loading", "status" => ""]],
            "2025-10-11" => [["booking_id" => 102, "pasangan" => "Arya & Anindita", "venue" => "Plataran Cilandak", "event_type" => "hari_h", "status" => "Selesai"]],
            "2025-10-15" => [["pasangan" => null, "venue" => "Technical Meeting", "event_type" => "internal", "status" => ""]],
            "2025-10-17" => [["pasangan" => null, "venue" => "Loading Ritz 21:00", "event_type" => "loading", "status" => ""]],
            "2025-10-18" => [["booking_id" => 103, "pasangan" => "Clarissa & Danis", "venue" => "Ritz-Carlton Mega K", "event_type" => "hari_h", "status" => "Selesai"]],
            "2025-10-23" => [["pasangan" => null, "venue" => "Bunga Segar Tiba", "event_type" => "internal", "status" => ""]],
            "2025-10-24" => [["booking_id" => 104, "pasangan" => null, "venue" => "Loading In 23:00 · Ballroom Mulia", "event_type" => "loading", "status" => "", "stage_badge" => "STAGE 1"]],
            "2025-10-25" => [["booking_id" => 104, "pasangan" => "Aditya & Sarah", "venue" => "Grand Ballroom Mulia", "event_type" => "hari_h", "status" => "HARI UTAMA"]],
            "2025-10-26" => [["booking_id" => 104, "pasangan" => "Daniel & Fiora", "venue" => "Standby & Bongkaran", "event_type" => "loading", "status" => ""]],
            "2025-10-31" => [["pasangan" => null, "venue" => "Loading Bidakara", "event_type" => "loading", "status" => ""]],
            "2025-11-01" => [["booking_id" => 105, "pasangan" => "Fauzan & Gina", "venue" => "Bidakara Grand Hall", "event_type" => "hari_h", "status" => ""]],
        ]);

        $detailAcara = $this->getDetailAcaraByTanggal($tanggalTerpilih);
        $vendorRekanan = $this->getVendorRekananByTanggal($detailAcara['id'] ?? null);

        $tanggalObj = new \DateTime($tanggalTerpilih);
        $hariIndo = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        $labelTanggalTerpilih = $hariIndo[(int)$tanggalObj->format('w')] . ', ' . $tanggalObj->format('d') . ' ' . $namaBulan[(int)$tanggalObj->format('n')] . ' ' . $tanggalObj->format('Y');

        return view('vendor.kalender', compact(
            'vendor', 'bulan', 'tahun', 'namaBulan', 'namaHari', 'grid',
            'events', 'detailAcara', 'vendorRekanan', 'labelTanggalTerpilih'
        ));
    }

    /* ===================== OVERVIEW DETAIL ACARA ===================== */

    public function overview($id)
    {
        $vendor = $this->getVendorProfile();

        $overview = $this->tryEloquent(function () use ($id) {
            throw new \Exception('belum diimplementasi');
        }, [
            "id" => $id,
            "package_name" => "PAKET ROYAL EMERALD",
            "pasangan" => "Aditya Wardhana & Sarah Nadia",
            "tanggal" => "Sabtu, 25 Oktober 2025",
            "venue" => "Grand Ballroom Hotel Mulia Senayan (Lt. 2 Jakarta)",
            "akad_label" => "AKAD NIKAH & PANGGIH",
            "akad_time" => "08:00 - 10:30 WIB",
            "akad_desc" => "Ballroom A & B (Kapasitas 400 Kursi)",
            "resepsi_label" => "RESEPSI ADAT & MODERN",
            "resepsi_time" => "19:00 - 22:00 WIB",
            "resepsi_desc" => "Full Grand Ballroom (Target 1.200 Tamu)",
            "nilai_kontrak" => 45000000,
            "pic_name" => "Dimas Prasetyo, S.I.Kom",
            "pic_role" => "Show Director WO · HT CH 01 Mulia",
            "sync_confirmed" => 4,
            "sync_total" => 5,
        ]);

        $spesifikasi = $this->tryEloquent(function () {
            throw new \Exception('belum diimplementasi');
        }, [
            ["title" => "1. Pelaminan Tradisional Modern", "location_tag" => "Lebar 18m", "description" => "Bunga segar warna champagne & emerald sage mix, backdrop ukir gebyok putih mutiara, sofa pelaminan Jepara modern."],
            ["title" => "2. Gate Entrance & Gazebo Kirab", "location_tag" => "Grand Foyer", "description" => "Lengkung gerbang melati gantung, 4 standing pillars jalan kirab pengantin, serta karpet kelopak mawar 20 meter."],
            ["title" => "3. Photo Booth 360 Panoramic", "location_tag" => "Area Foyer Lt.2", "description" => "Dimensi 4×3 meter bertema 'Emerald Garden', include lighting warm spotlight & papan neon custom nama pengantin."],
        ]);

        $bongkarNote = "Bongkar Muat Dimulai: Jumat, 24 Okt pukul 22:00 WIB di Loading Bay B2.";

        $kesiapanMitra = $this->tryEloquent(function () {
            throw new \Exception('belum diimplementasi');
        }, [
            ["name" => "The Leonardi Photography", "category" => "Dokumentasi & Live Feed", "readiness_status" => "siap"],
            ["name" => "Sound & Light Dynamics", "category" => "Tata Suara 15.000W & Rigging", "readiness_status" => "siap"],
            ["name" => "Puspa Catering VIP", "category" => "Buffet & Fine Dining 1.200 Pax", "readiness_status" => "siap"],
            ["name" => "Bennu Sorumba MUA", "category" => "Jadwal Final Fitting Terbuka", "readiness_status" => "menunggu"],
            ["name" => "Dwiki Jazz Bigband", "category" => "12 Pieces Orchestra", "readiness_status" => "siap"],
        ]);

        $logistik = [
            ["icon" => "🚚", "title" => "Akses Loading Dock Basement 2", "desc" => "Ketinggian clearance truk maks. 3.2m. Wajib gunakan elevator barang No. 4 & 5 ke Lt. 2."],
            ["icon" => "↕️", "title" => "Batas Ketinggian Panggung & Rigging", "desc" => "Ketinggian langit-langit ballroom 7.5m. Beban gantung dekorasi max 250kg per rigging point."],
            ["icon" => "🛡️", "title" => "Kontak Security & Safety Officer", "desc" => "Bapak Hendra (Duty Eng): 0812-9988-2121. Kru dekorasi wajib memakai safety vest & ID badge."],
            ["icon" => "🕐", "title" => "Batas Waktu Bongkaran (Teardown)", "desc" => "Mulai Sabtu 25 Okt pukul 23:00 WIB sampai Minggu 26 Okt maks. 05:00 WIB (Clean Ballroom)."],
        ];

        $rundown = $this->tryEloquent(function () {
            throw new \Exception('belum diimplementasi');
        }, [
            ["tanggal" => "Jumat, 24 Okt", "waktu" => "22:00 - 05:00 WIB", "fase_label" => "FASE 1: SETUP PANGGUNG", "title" => "Bongkar Muat & Instalasi Rangka Pelaminan 18m", "description" => "Tim logistik memasukkan konstruksi backdrop gebyok melalui elevator barang B2. Pemasangan rangka truss sound & lighting diselaraskan bersamaan dengan tim Sound Dynamics.", "status" => "upcoming"],
            ["tanggal" => "Sabtu, 25 Okt", "waktu" => "05:00 - 07:30 WIB", "fase_label" => "FASE 2: FLORIST DELIVERY", "title" => "Perangkaian Fresh Flowers & Gazebo Kirab", "description" => "Rangkaian bunga segar diinstalasi pada pelaminan, standing flowers karpet kirab, serta dekorasi meja akad nikah.", "status" => "current"],
            ["tanggal" => "Sabtu, 25 Okt", "waktu" => "07:30 - 10:30 WIB", "fase_label" => "FASE 3: AKAD NIKAH", "title" => "Final Walkthrough Show Director & Acara Akad Berlangsung", "description" => "Dimas Prasetyo (Show Director) menandatangani lembar serah terima kesiapan dekorasi. 2 kru standby di venue.", "status" => "current"],
            ["tanggal" => "Sabtu, 25 Okt", "waktu" => "18:30 - 22:00 WIB", "fase_label" => "FASE 4: RESEPSI AKBAR", "title" => "Resepsi Malam & Operasional Photo Booth 360", "description" => "Kru operator photo booth melayani tamu VIP. Tim florist memastikan bunga meja keluarga tetap rapi.", "status" => "current"],
            ["tanggal" => "Sabtu, 25 Okt", "waktu" => "22:30 - 04:30 WIB", "fase_label" => "FASE 5: BONGKARAN", "title" => "Bongkaran (Teardown) & Serah Terima Kebersihan Area", "description" => "Pelepasan ornamen backdrop, pemilahan sisa bunga, dan inspeksi bebas kerusakan ballroom bersama Duty Manager Hotel Mulia.", "status" => "pending"],
        ]);

        $syncConfirmed = (int) ($overview['sync_confirmed'] ?? 0);
        $syncTotal     = max(1, (int) ($overview['sync_total'] ?? 1));
        $syncPercent   = round($syncConfirmed / $syncTotal * 100);

        return view('vendor.overview', compact(
            'vendor', 'overview', 'spesifikasi', 'bongkarNote', 'kesiapanMitra',
            'logistik', 'rundown', 'syncConfirmed', 'syncTotal', 'syncPercent'
        ));
    }

    /* ===================== TAMBAH VENDOR / KATALOG ===================== */

    public function tambahVendorForm()
    {
        $vendor = $this->getVendorProfile();
        $kategoriOptions = [
            "Dekorasi Pelaminan & Stage",
            "Dekorasi Gate & Entrance",
            "Florist & Bunga Segar",
            "Tata Cahaya & Sound",
            "Photo Booth & Fotografi",
            "Katering & Buffet",
        ];

        return view('vendor.tambahvendor', compact('vendor', 'kategoriOptions'));
    }

    public function simpanVendor(Request $request)
    {
        $validated = $request->validate([
            'nama_paket' => 'required|string|max:150',
            'kategori'   => 'required|string|max:100',
            'harga'      => 'required',
            'foto_utama' => 'nullable|image|max:5120',
            'galeri.*'   => 'nullable|image|max:5120',
        ]);

        $status = $request->input('aksi') === 'terbitkan' ? 'terbit' : 'draft';

        try {
            throw new \Exception('Model VendorKatalog belum diimplementasi');
        } catch (\Throwable $e) {
            return back()->with('form_message', [
                'success' => true,
                'text' => $status === 'terbit'
                    ? '(Mode demo — model belum aktif) Layanan berhasil "diterbitkan" dan diajukan ke tim kurator WO PROJECT.'
                    : '(Mode demo — model belum aktif) Draf layanan berhasil "disimpan".',
            ]);
        }
    }

    /* ===================== HELPER ===================== */

    /**
     * Jalankan closure (query Eloquent). Kalau class/tabel belum ada atau
     * query gagal (\Throwable apapun), kembalikan data contoh ($fallback).
     */
    private function tryEloquent(\Closure $fn, $fallback)
    {
        try {
            $result = $fn();
            return $result ?: $fallback;
        } catch (\Throwable $e) {
            return $fallback;
        }
    }

    private function getVendorProfile()
    {
        return $this->tryEloquent(function () {
            // return \App\Models\Vendor::findOrFail($this->vendorId)->toArray();
            throw new \Exception('belum diimplementasi');
        }, [
            "id" => 1,
            "name" => "Lotus Atelier",
            "category" => "Lotus Floral & Scenography",
            "phone" => "-",
            "address" => "-",
        ]);
    }

    private function getPipelineAcara()
    {
        return $this->tryEloquent(function () {
            throw new \Exception('belum diimplementasi');
        }, [
            ["pasangan" => "Aditya Wardhana & Sarah Nadia", "wo_code" => "WO-2024-8812", "event_date" => "26 Okt 2024", "loading_time" => "06:00 WIB", "venue" => "Grand Ballroom, Hotel Mulia Jakarta", "theme" => "Royal Emerald Javanese", "status_pembayaran" => "Loading 85%"],
            ["pasangan" => "Raden Daniswara & Clarissa", "wo_code" => "WO-2024-8840", "event_date" => "02 Nov 2024", "loading_time" => "23:00 WIB (H-1)", "venue" => "Glass House, Plataran Hutan Kota", "theme" => "Modern White Floral Arch", "status_pembayaran" => "SPK Terbit / Escrow 50%"],
            ["pasangan" => "Kevin Pratama & Michelle Tan", "wo_code" => "WO-2024-8891", "event_date" => "10 Nov 2024", "loading_time" => "02:00 WIB", "venue" => "Dian Ballroom, Raffles Hotel Jakarta", "theme" => "Minimalist Botanical Opulence", "status_pembayaran" => "Brief Desain Approved"],
            ["pasangan" => "Fahmi Ramadhan & Anindya L.", "wo_code" => "WO-2024-8920", "event_date" => "23 Nov 2024", "loading_time" => "04:00 WIB", "venue" => "The Ritz-Carlton Mega Kuningan", "theme" => "Celestial Garden Twilight", "status_pembayaran" => "Menunggu Approval Moodboard"],
        ]);
    }

    private function getDetailAcaraByTanggal($tanggal)
    {
        return $this->tryEloquent(function () use ($tanggal) {
            throw new \Exception('belum diimplementasi');
        }, [
            "id" => "WO-2025-081",
            "pasangan" => "Aditya Wardhana & Sarah Nadia",
            "package_name" => "The Royal Emerald Wedding Series",
            "venue" => "Grand Ballroom Hotel Mulia Senayan, Jakarta Pusat",
            "room" => "Lt. 2",
            "akad_time" => "08:00 - 10:30 WIB",
            "resepsi_time" => "19:00 - 22:00 WIB",
            "pic_name" => "Dimas Prasetyo",
            "pic_callsign" => "Alpha Lead · HT Channel 01 Mulia",
            "decor_scope" => "Paket Dekorasi Pelaminan Adat Modern Emerald 18 Meter, Gazebo Kirab Bunga Segar, Foyer Photo Gallery, & 360 Photo Booth Area",
            "nilai_kontrak" => 45000000,
            "status_escrow" => "locked",
        ]);
    }

    private function getVendorRekananByTanggal($bookingId)
    {
        return $this->tryEloquent(function () use ($bookingId) {
            throw new \Exception('belum diimplementasi');
        }, [
            ["name" => "The Leonardi Photography", "category" => "Dokumentasi", "pic_name" => "King Leonardi", "pic_phone" => "+62 811-923-881", "fokus_koordinasi" => "Sinkronisasi spot lighting panggung pelaminan & clearance area backdrop foto keluarga."],
            ["name" => "Bennu Sorumba MUA & Attire", "category" => "Rias & Busana", "pic_name" => "Mas Danar", "pic_phone" => "+62 812-7711-209", "fokus_koordinasi" => "Standby cermin rias & backdrop touch-up ruang tunggu pengantin ballroom."],
            ["name" => "Sound & Light Dynamics", "category" => "Tata Cahaya", "pic_name" => "Rian Hidayat", "pic_phone" => "+62 813-1029-443", "fokus_koordinasi" => "Setup warm spotlight pelaminan adat 18m, beam follow spot gazebo kirab, jalur kabel dekor."],
            ["name" => "Puspa Catering VIP", "category" => "Katering & Buffet", "pic_name" => "Ibu Retno Wardani", "pic_phone" => "+62 818-449-011", "fokus_koordinasi" => "Tata letak dessert island table, clearance line garland pelaminan, taplak meja matching warna emerald."],
            ["name" => "MC Choky Sitohang & Dwiki Jazz", "category" => "Show & Music", "pic_name" => "Tommy", "pic_phone" => "+62 878-990-213", "fokus_koordinasi" => "Sound monitor panggung dekor, cue music saat prosesi kirab melintasi gazebo utama."],
        ]);
    }

    /**
     * Susun grid kalender bulanan (mulai hari Senin), termasuk tanggal
     * "bocoran" dari bulan sebelum/sesudah supaya grid selalu penuh 7 kolom.
     */
    private function generateCalendarGrid($year, $month)
    {
        $firstOfMonth = new \DateTime("$year-$month-01");
        $startOffset = ((int) $firstOfMonth->format('N')) - 1;

        $gridStart = clone $firstOfMonth;
        $gridStart->modify("-$startOffset days");

        $weeks = [];
        $cursor = clone $gridStart;

        do {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $week[] = [
                    'date'     => $cursor->format('Y-m-d'),
                    'day'      => (int) $cursor->format('j'),
                    'in_month' => (int) $cursor->format('n') === (int) $month,
                ];
                $cursor->modify('+1 day');
            }
            $weeks[] = $week;
        } while ((int) $cursor->format('n') === (int) $month || count($weeks) < 5);

        return $weeks;
    }
}