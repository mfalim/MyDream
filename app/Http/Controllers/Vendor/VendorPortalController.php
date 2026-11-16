<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VendorPortalController extends Controller
{
    /**
     * HALAMAN KALENDER
     * Mengambil data acara dari tabel events.
     */
    public function kalender(Request $request)
    {
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
            ->orderBy('event_date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(function ($event) {
                return Carbon::parse($event->event_date)
                    ->format('Y-m-d');
            });

        $grid = $this->generateCalendarGrid($tahun, $bulan);

        $tanggalTerpilih = $request->query(
            'tanggal',
            now()->format('Y-m-d')
        );

        $tanggalObj = Carbon::parse($tanggalTerpilih);

        $detailAcara = DB::table('events')
            ->whereDate('event_date', $tanggalObj->toDateString())
            ->orderBy('start_time')
            ->get();

        return view('vendor.kalender', compact(
            'bulan',
            'tahun',
            'namaBulan',
            'events',
            'grid',
            'tanggalTerpilih',
            'tanggalObj',
            'detailAcara'
        ));
    }

    /**
     * HALAMAN TRACKING TUGAS
     * Mengambil data dari tabel schedules.
     */
    public function tracking()
    {
        $schedules = DB::table('schedules')
            ->leftJoin('events', 'schedules.event_id', '=', 'events.id')
            ->select(
                'schedules.*',
                'events.name as event_name',
                'events.event_date',
                'events.event_type'
            )
            ->orderBy('schedules.start_time')
            ->get();

        $totalTugas = $schedules->count();

        $selesai = $schedules->where('status', 'selesai')->count();

        $berjalan = $schedules->where('status', 'berjalan')->count();

        $menunggu = $totalTugas - $selesai - $berjalan;

        return view('vendor.tracking', compact(
            'schedules',
            'totalTugas',
            'selesai',
            'berjalan',
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
