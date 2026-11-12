<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Vendor;
use App\Models\Booking;
use App\Models\Event;
use App\Models\OrganizerEvent;
use App\Models\Client;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. STATISTIK UTAMA (METRICS)
        $totalPackages = Package::count();
        $totalVendors = Vendor::count();
        $totalBookings = Booking::count();
        $totalEvents = Event::count();
        $totalOrganizerEvents = OrganizerEvent::count();
        $totalClients = Client::count();
        $totalMembers = Member::whereIn('status', ['active', 'aktif'])->count();

        // Status Booking
        $pendingBookings = Booking::where('status', 'pending')->count();
        $approvedBookings = Booking::where('status', 'approved')->count();
        $rejectedBookings = Booking::where('status', 'rejected')->count();

        // Status Event
        $upcomingEventsCount = Event::whereIn('status', ['upcoming', 'scheduled'])->count();
        $ongoingEventsCount = Event::where('status', 'ongoing')->count();
        $completedEventsCount = Event::where('status', 'completed')->count();

        // Revenue
        $totalRevenue = Booking::where('status', 'approved')->sum('total_price');
        $monthlyRevenue = Booking::where('status', 'approved')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_price');

        // 2. DATA CHART TREN 6 BULAN TERAKHIR (PENDAPATAN & BOOKING)
        $months = [];
        $revenueData = [];
        $bookingCountData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->translatedFormat('M Y');
            $months[] = $monthName;

            $rev = Booking::where('status', 'approved')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('total_price');
            // Dikonversi ke juta rupiah agar tampilan chart bersih
            $revenueData[] = round($rev / 1000000, 1);

            $count = Booking::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            $bookingCountData[] = $count;
        }

        // 3. DATA CHART DISTRIBUSI STATUS ACARA & BOOKING
        $statusDonutLabels = ['Approved Bookings', 'Pending Bookings', 'Upcoming Events', 'Completed Events'];
        $statusDonutSeries = [
            $approvedBookings,
            $pendingBookings,
            $upcomingEventsCount,
            $completedEventsCount
        ];

        // 4. ACARA MENDATANG (WEDDING KLIEN & INTERNAL WO)
        $today = Carbon::today();

        // Event Hari H Pernikahan Terdekat
        $upcomingClientEvents = Event::with(['booking.client', 'package'])
            ->where('event_date', '>=', $today)
            ->orderBy('event_date', 'asc')
            ->limit(4)
            ->get();

        // Jika event di masa depan sedikit, ambil event terbaru untuk preview
        if ($upcomingClientEvents->isEmpty()) {
            $upcomingClientEvents = Event::with(['booking.client', 'package'])
                ->orderBy('event_date', 'desc')
                ->limit(4)
                ->get();
        }

        // Agenda Internal WO Terdekat
        $upcomingOrganizerEvents = OrganizerEvent::where('event_date', '>=', $today)
            ->orderBy('event_date', 'asc')
            ->limit(4)
            ->get();

        if ($upcomingOrganizerEvents->isEmpty()) {
            $upcomingOrganizerEvents = OrganizerEvent::orderBy('event_date', 'desc')
                ->limit(4)
                ->get();
        }

        // 5. BOOKING TERBARU (RECENT TRANSACTIONS)
        $recentBookings = Booking::with(['client', 'package'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        return view('admin.dashboard', compact(
            'totalPackages',
            'totalVendors',
            'totalBookings',
            'totalEvents',
            'totalOrganizerEvents',
            'totalClients',
            'totalMembers',
            'pendingBookings',
            'approvedBookings',
            'rejectedBookings',
            'upcomingEventsCount',
            'ongoingEventsCount',
            'completedEventsCount',
            'totalRevenue',
            'monthlyRevenue',
            'months',
            'revenueData',
            'bookingCountData',
            'statusDonutLabels',
            'statusDonutSeries',
            'upcomingClientEvents',
            'upcomingOrganizerEvents',
            'recentBookings'
        ));
    }
}
