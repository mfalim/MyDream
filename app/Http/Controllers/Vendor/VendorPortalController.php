<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VendorPortalController extends Controller
{
    /**
     * Dashboard vendor berbasis jadwal yang ditugaskan ke akun aktif.
     */
    public function dashboard(Request $request)
    {
        $vendor = $request->user()->vendor()->firstOrFail();
        $today = now()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        $vendorSchedules = DB::table('schedules')
            ->where('vendor_id', $vendor->id);

        $totalTugas = (clone $vendorSchedules)->count();
        $disetujui = (clone $vendorSchedules)->where('status', 'approved')->count();
        $menunggu = (clone $vendorSchedules)->where('status', 'pending')->count();
        $ditolak = (clone $vendorSchedules)->where('status', 'rejected')->count();

        $acaraHariIni = DB::table('events')
            ->whereDate('event_date', $today)
            ->whereExists(function ($query) use ($vendor) {
                $query->selectRaw('1')
                    ->from('schedules')
                    ->whereColumn('schedules.event_id', 'events.id')
                    ->where('schedules.vendor_id', $vendor->id);
            })
            ->count();

        $acaraBulanIni = DB::table('events')
            ->whereBetween('event_date', [$startOfMonth, $endOfMonth])
            ->whereExists(function ($query) use ($vendor) {
                $query->selectRaw('1')
                    ->from('schedules')
                    ->whereColumn('schedules.event_id', 'events.id')
                    ->where('schedules.vendor_id', $vendor->id);
            })
            ->count();

        $jadwalMendatang = DB::table('schedules')
            ->join('events', 'schedules.event_id', '=', 'events.id')
            ->where('schedules.vendor_id', $vendor->id)
            ->whereDate('events.event_date', '>=', $today)
            ->orderBy('events.event_date')
            ->orderBy('schedules.start_time')
            ->limit(8)
            ->get([
                'schedules.activity',
                'schedules.location',
                'schedules.start_time',
                'schedules.end_time',
                'schedules.status',
                'events.name as event_name',
                'events.event_date',
            ]);

        return view('vendor.dashboard', compact(
            'vendor',
            'acaraHariIni',
            'acaraBulanIni',
            'totalTugas',
            'disetujui',
            'menunggu',
            'ditolak',
            'jadwalMendatang'
        ));
    }

    /**
     * HALAMAN KALENDER
     * Mengambil data acara dari tabel events.
     */
    public function kalender(Request $request)
    {
        $vendor = $request->user()->vendor()->firstOrFail();

        $bulan = (int) $request->query('bulan', now()->month);
        $tahun = (int) $request->query('tahun', now()->year);

        if ($bulan < 1 || $bulan > 12) {
            $bulan = now()->month;
        }

        if ($tahun < 2000 || $tahun > 2100) {
            $tahun = now()->year;
        }

        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $tanggalAwal = Carbon::create($tahun, $bulan, 1)
            ->startOfMonth();

        $tanggalAkhir = $tanggalAwal->copy()->endOfMonth();

        $events = DB::table('events')
            ->whereBetween('event_date', [
                $tanggalAwal->toDateString(),
                $tanggalAkhir->toDateString()
            ])
            ->whereExists(function ($query) use ($vendor) {
                $query->selectRaw('1')
                    ->from('schedules')
                    ->whereColumn('schedules.event_id', 'events.id')
                    ->where('schedules.vendor_id', $vendor->id);
            })
            ->orderBy('event_date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(function ($event) {
                return Carbon::parse($event->event_date)
                    ->format('Y-m-d');
            });

        $grid = $this->generateCalendarGrid($tahun, $bulan);

        return view('vendor.kalender', compact(
            'vendor',
            'bulan',
            'tahun',
            'namaBulan',
            'events',
            'grid'
        ));
    }

    /**
     * HALAMAN TRACKING TUGAS
     * Mengambil data dari tabel schedules.
     */
    public function tracking(Request $request)
    {
        $vendor = $request->user()->vendor()->firstOrFail();

        $schedules = DB::table('schedules')
            ->leftJoin('events', 'schedules.event_id', '=', 'events.id')
            ->where('schedules.vendor_id', $vendor->id)
            ->select(
                'schedules.*',
                'events.name as event_name',
                'events.event_date',
                'events.event_type'
            )
            ->orderBy('schedules.start_time')
            ->get();

        $totalTugas = $schedules->count();

        $disetujui = $schedules->where('status', 'approved')->count();
        $ditolak = $schedules->where('status', 'rejected')->count();
        $menunggu = $schedules->where('status', 'pending')->count();

        return view('vendor.tracking', compact(
            'vendor',
            'schedules',
            'totalTugas',
            'disetujui',
            'ditolak',
            'menunggu'
        ));
    }

    /**
     * MEMBUAT GRID KALENDER
     * Kalender dimulai dari hari Senin.
     */
    private function generateCalendarGrid($tahun, $bulan)
    {
        $awalBulan = Carbon::create($tahun, $bulan, 1);

        $offset = $awalBulan->dayOfWeekIso - 1;

        $tanggalMulai = $awalBulan->copy()->subDays($offset);

        $jumlahMinggu = (int) ceil(
            ($offset + $awalBulan->daysInMonth) / 7
        );

        $grid = [];

        for ($minggu = 0; $minggu < $jumlahMinggu; $minggu++) {

            $baris = [];

            for ($hari = 0; $hari < 7; $hari++) {

                $tanggal = $tanggalMulai->copy();

                $baris[] = [
                    'date' => $tanggal->toDateString(),
                    'day' => $tanggal->day,
                    'in_month' => $tanggal->month == $bulan,
                ];

                $tanggalMulai->addDay();
            }

            $grid[] = $baris;
        }

        return $grid;
    }
}
