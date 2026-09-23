<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Vendor;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Client;
use App\Models\Member;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik utama
        $totalPackages = Package::count();
        $totalVendors = Vendor::count();
        $totalBookings = Booking::count();
        $totalEvents = Event::count();
        $totalClients = Client::count();
        $totalMembers = Member::where('status', 'active')->count();
        
        // Statistik booking berdasarkan status
        $pendingBookings = Booking::where('status', 'pending')->count();
        $approvedBookings = Booking::where('status', 'approved')->count();
        $rejectedBookings = Booking::where('status', 'rejected')->count();
        
        // Statistik event berdasarkan status
        $scheduledEvents = Event::where('status', 'scheduled')->count();
        $ongoingEvents = Event::where('status', 'ongoing')->count();
        $completedEvents = Event::where('status', 'completed')->count();
        
        // Event mendatang (7 hari ke depan)
        $upcomingEvents = Event::with(['booking.client', 'schedules'])
            ->whereBetween('event_date', [Carbon::today(), Carbon::today()->addDays(7)])
            ->orderBy('event_date', 'asc')
            ->limit(5)
            ->get();
        
        // Booking terbaru
        $recentBookings = Booking::with(['client', 'package'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Total pendapatan (dari booking yang approved)
        $totalRevenue = Booking::where('status', 'approved')->sum('total_price');
        
        // Pendapatan bulan ini
        $monthlyRevenue = Booking::where('status', 'approved')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_price');
        
        return view('admin.dashboard', compact(
            'totalPackages',
            'totalVendors',
            'totalBookings',
            'totalEvents',
            'totalClients',
            'totalMembers',
            'pendingBookings',
            'approvedBookings',
            'rejectedBookings',
            'scheduledEvents',
            'ongoingEvents',
            'completedEvents',
            'upcomingEvents',
            'recentBookings',
            'totalRevenue',
            'monthlyRevenue'
        ));
    }
}
