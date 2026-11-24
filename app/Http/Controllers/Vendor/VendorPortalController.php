<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $totalKasMasuk = (float) (clone $vendorSchedules)->sum('paid_amount');
        $totalKontrak = (float) (clone $vendorSchedules)
            ->selectRaw('COALESCE(SUM(CASE WHEN COALESCE(agreed_fee, 0) > 0 THEN agreed_fee ELSE COALESCE(price, 0) END), 0) as total')
            ->value('total');
        $sisaPiutang = max(0, $totalKontrak - $totalKasMasuk);

        return view('vendor.dashboard', compact(
            'vendor',
            'acaraHariIni',
            'acaraBulanIni',
            'totalTugas',
            'disetujui',
            'menunggu',
            'ditolak',
            'jadwalMendatang',
            'totalKasMasuk',
            'totalKontrak',
            'sisaPiutang'
        ));
    }

    public function updateAvailability(Request $request)
    {
        $validated = $request->validate([
            'is_open' => ['required', 'boolean'],
        ]);

        $vendor = $request->user()->vendor()->firstOrFail();
        $vendor->update(['is_open' => $validated['is_open']]);

        return redirect()
            ->route('vendor.dashboard')
            ->with('success', $vendor->is_open
                ? 'Sesi dibuka. Vendor dan paket yang memuat vendor Anda kini dapat dipesan.'
                : 'Sesi ditutup. Vendor dan paket yang memuat vendor Anda tidak dapat dipesan.');
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
     * HALAMAN KAS & PEMBAYARAN MASUK VENDOR
     * Menggunakan data pencairan yang sudah ada di tabel schedules.
     */
    public function kas(Request $request)
    {
        $vendor = $request->user()->vendor()->firstOrFail();
        $statusFilter = $request->query('status', 'all');

        $allPayouts = DB::table('schedules')
            ->leftJoin('events', 'schedules.event_id', '=', 'events.id')
            ->leftJoin('bookings', 'events.booking_id', '=', 'bookings.id')
            ->where('schedules.vendor_id', $vendor->id)
            ->select(
                'schedules.id',
                'schedules.activity',
                'schedules.price',
                'schedules.agreed_fee',
                'schedules.paid_amount',
                'schedules.payment_status',
                'schedules.paid_at',
                'schedules.payment_notes',
                'schedules.payout_proof_path',
                'schedules.status as schedule_status',
                'events.name as event_name',
                'events.event_date',
                'bookings.invoice_number'
            )
            ->orderByDesc('events.event_date')
            ->orderByDesc('schedules.id')
            ->get()
            ->map(function ($row) {
                $fee = (float) ($row->agreed_fee > 0 ? $row->agreed_fee : ($row->price ?? 0));
                $paid = (float) ($row->paid_amount ?? 0);
                $row->effective_fee = $fee;
                $row->paid_value = $paid;
                $row->remaining_value = max(0, $fee - $paid);
                $row->effective_payment_status = $row->payment_status ?: ($fee > 0 && $paid >= $fee ? 'paid' : ($paid > 0 ? 'dp_paid' : 'unpaid'));

                return $row;
            });

        $totalKontrak = (float) $allPayouts->sum('effective_fee');
        $totalKasMasuk = (float) $allPayouts->sum('paid_value');
        $sisaPiutang = max(0, $totalKontrak - $totalKasMasuk);
        $countPaid = $allPayouts->where('effective_payment_status', 'paid')->count();
        $countDp = $allPayouts->where('effective_payment_status', 'dp_paid')->count();
        $countUnpaid = $allPayouts->where('effective_payment_status', 'unpaid')->count();

        $payouts = in_array($statusFilter, ['paid', 'dp_paid', 'unpaid'], true)
            ? $allPayouts->where('effective_payment_status', $statusFilter)->values()
            : $allPayouts;

        return view('vendor.kas', compact(
            'vendor',
            'payouts',
            'statusFilter',
            'totalKontrak',
            'totalKasMasuk',
            'sisaPiutang',
            'countPaid',
            'countDp',
            'countUnpaid'
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
