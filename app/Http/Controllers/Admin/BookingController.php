<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Client;
use App\Models\Package;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Schedule;
use App\Models\EventMember;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index()
    {
        $events = Event::with(['booking.client', 'schedules.vendor', 'eventMembers.member'])
            ->orderBy('event_date', 'desc')
            ->get();

        return view('admin.event.event_days', compact('events'));
    }

    public function create() {
        $vendors = Vendor::all();
        $packages = Package::with('vendors')->get();
        $bookings = Booking::with(['client', 'package'])->where('status', 'approved')->get();
        $members = \App\Models\Member::where('status', 'active')->get();
        return view('admin.event.event_form', compact('vendors', 'packages', 'bookings', 'members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'event_name' => 'required|string|max:255',
            'event_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'event_type' => 'nullable|string',
            'event_date' => 'required|date|after_or_equal:today',
            'guest_count' => 'nullable|integer|min:1|max:10000',
            'event_notes' => 'nullable|string|max:2000',
            'event_status' => 'nullable|string|in:scheduled,ongoing,completed,cancelled',
            'package_id' => 'nullable|exists:packages,id',
            'vendor_id' => 'nullable|array',
            'vendor_id.*' => 'nullable|exists:vendors,id',
            'vendor_status' => 'nullable|array',
            'vendor_status.*' => 'nullable|in:pending,approved,rejected',
            'vendor_start_time' => 'nullable|array',
            'vendor_start_time.*' => 'nullable|date_format:H:i',
            'vendor_end_time' => 'nullable|array',
            'vendor_end_time.*' => 'nullable|date_format:H:i',
            'vendor_member_id' => 'nullable|array',
            'vendor_member_id.*' => 'nullable|exists:members,id',
            'package_vendor_id' => 'nullable|array',
            'package_vendor_start_time' => 'nullable|array',
            'package_vendor_start_time.*' => 'nullable|date_format:H:i',
            'package_vendor_end_time' => 'nullable|array',
            'package_vendor_end_time.*' => 'nullable|date_format:H:i',
            'team_member_id' => 'nullable|array',
            'team_member_id.*' => 'nullable|exists:members,id',
            'team_member_role' => 'nullable|array',
            'team_member_role.*' => 'nullable|string|max:100',
        ], [
            'booking_id.required' => 'Booking harus dipilih',
            'booking_id.exists' => 'Booking tidak valid',
            'event_name.required' => 'Nama event harus diisi',
            'event_name.max' => 'Nama event maksimal 255 karakter',
            'event_photo.image' => 'File harus berupa gambar',
            'event_photo.mimes' => 'Format gambar harus jpg, jpeg, png, atau webp',
            'event_photo.max' => 'Ukuran gambar maksimal 2MB',
            'event_date.required' => 'Tanggal event harus diisi',
            'event_date.after_or_equal' => 'Tanggal event tidak boleh kurang dari hari ini',
            'guest_count.min' => 'Jumlah tamu minimal 1',
            'guest_count.max' => 'Jumlah tamu maksimal 10000',
            'event_notes.max' => 'Catatan maksimal 2000 karakter',
            'vendor_start_time.*.date_format' => 'Format waktu harus HH:MM',
            'vendor_end_time.*.date_format' => 'Format waktu harus HH:MM',
        ]);

        $photoPath = null;
        if ($request->hasFile('event_photo')) {
            $photoPath = $request->file('event_photo')->store('events', 'public');
        }

        $event = Event::create([
            'booking_id' => $request->booking_id,
            'name' => $request->event_name,
            'photo' => $photoPath,
            'event_type' => $request->event_type,
            'event_date' => $request->event_date,
            'guest_count' => $request->guest_count,
            'start_time' => null,
            'end_time' => null,
            'package_id' => $request->package_id,
            'notes' => $request->event_notes,
            'status' => $request->event_status ?? 'scheduled',
        ]);

        $vendorTimes = [];

        if ($request->package_id) {
            $selectedPackage = Package::with('vendors')->find($request->package_id);
            $packageVendorStartTimes = $request->package_vendor_start_time ?? [];
            $packageVendorEndTimes = $request->package_vendor_end_time ?? [];
            
            foreach ($selectedPackage->vendors as $index => $vendor) {
                $startTime = $packageVendorStartTimes[$index] ?? null;
                $endTime = $packageVendorEndTimes[$index] ?? null;
                
                if ($startTime) $vendorTimes[] = $startTime;
                if ($endTime) $vendorTimes[] = $endTime;
                
                Schedule::create([
                    'event_id' => $event->id,
                    'vendor_id' => $vendor->id,
                    'member_id' => null,
                    'activity' => $vendor->name,
                    'location' => null,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'status' => 'pending',
                ]);
            }
        } else {
            if ($request->has('vendor_id') && is_array($request->vendor_id)) {
                foreach ($request->vendor_id as $index => $vendorId) {
                    if ($vendorId) {
                        $startTime = $request->vendor_start_time[$index] ?? null;
                        $endTime = $request->vendor_end_time[$index] ?? null;
                        $memberId = $request->vendor_member_id[$index] ?? null;
                        
                        if ($startTime) $vendorTimes[] = $startTime;
                        if ($endTime) $vendorTimes[] = $endTime;
                        
                        $vendor = Vendor::find($vendorId);
                        Schedule::create([
                            'event_id' => $event->id,
                            'vendor_id' => $vendorId,
                            'member_id' => $memberId,
                            'activity' => $vendor ? $vendor->name : 'Vendor',
                            'location' => null,
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                            'status' => $request->vendor_status[$index] ?? 'pending',
                        ]);
                    }
                }
            }
        }

        if (!empty($vendorTimes)) {
            sort($vendorTimes);
            $event->start_time = reset($vendorTimes);
            $event->end_time = end($vendorTimes);
            $event->save();
        }

        if ($request->has('team_member_id') && is_array($request->team_member_id)) {
            foreach ($request->team_member_id as $index => $memberId) {
                if ($memberId) {
                    EventMember::create([
                        'event_id' => $event->id,
                        'member_id' => $memberId,
                        'role' => $request->team_member_role[$index] ?? null,
                        'status' => 'assigned',
                    ]);
                }
            }
        }

        return redirect()->route('admin.event_day.index')->with('success', 'Event created successfully!');
    }

    public function edit($id)
    {
        $event = Event::with(['booking', 'schedules.vendor', 'eventMembers.member'])->findOrFail($id);
        $vendors = Vendor::all();
        $packages = Package::with('vendors')->get();
        $members = \App\Models\Member::where('status', 'active')->get();
        $bookings = Booking::with(['client', 'package'])->where('status', 'approved')->get();
        
        return view('admin.event.event_edit', compact('event', 'vendors', 'packages', 'members', 'bookings'));
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        
        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'event_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'event_type' => 'nullable|string',
            'event_date' => 'required|date|after_or_equal:today',
            'guest_count' => 'nullable|integer|min:1|max:10000',
            'event_notes' => 'nullable|string|max:2000',
            'event_status' => 'nullable|string|in:scheduled,ongoing,completed,cancelled',
            'package_id' => 'nullable|exists:packages,id',
            'vendor_id' => 'nullable|array',
            'vendor_id.*' => 'nullable|exists:vendors,id',
            'vendor_status' => 'nullable|array',
            'vendor_status.*' => 'nullable|in:pending,approved,rejected',
            'vendor_start_time' => 'nullable|array',
            'vendor_start_time.*' => 'nullable|date_format:H:i',
            'vendor_end_time' => 'nullable|array',
            'vendor_end_time.*' => 'nullable|date_format:H:i',
            'vendor_member_id' => 'nullable|array',
            'vendor_member_id.*' => 'nullable|exists:members,id',
            'team_member_id' => 'nullable|array',
            'team_member_id.*' => 'nullable|exists:members,id',
            'team_member_role' => 'nullable|array',
            'team_member_role.*' => 'nullable|string|max:100',
        ], [
            'event_name.required' => 'Nama event harus diisi',
            'event_name.max' => 'Nama event maksimal 255 karakter',
            'event_photo.image' => 'File harus berupa gambar',
            'event_photo.mimes' => 'Format gambar harus jpg, jpeg, png, atau webp',
            'event_photo.max' => 'Ukuran gambar maksimal 2MB',
            'event_date.required' => 'Tanggal event harus diisi',
            'event_date.after_or_equal' => 'Tanggal event tidak boleh kurang dari hari ini',
            'guest_count.min' => 'Jumlah tamu minimal 1',
            'guest_count.max' => 'Jumlah tamu maksimal 10000',
            'event_notes.max' => 'Catatan maksimal 2000 karakter',
            'vendor_start_time.*.date_format' => 'Format waktu harus HH:MM',
            'vendor_end_time.*.date_format' => 'Format waktu harus HH:MM',
        ]);

        $photoPath = $event->photo;
        if ($request->hasFile('event_photo')) {
            if ($event->photo && \Storage::disk('public')->exists($event->photo)) {
                \Storage::disk('public')->delete($event->photo);
            }
            $photoPath = $request->file('event_photo')->store('events', 'public');
        }

        $event->update([
            'name' => $request->event_name,
            'photo' => $photoPath,
            'event_type' => $request->event_type,
            'event_date' => $request->event_date,
            'guest_count' => $request->guest_count,
            'package_id' => $request->package_id,
            'notes' => $request->event_notes,
            'status' => $request->event_status ?? $event->status,
        ]);

        $event->schedules()->delete();
        $vendorTimes = [];

        if ($request->package_id) {
            $selectedPackage = Package::with('vendors')->find($request->package_id);
            $packageVendorStartTimes = $request->package_vendor_start_time ?? [];
            $packageVendorEndTimes = $request->package_vendor_end_time ?? [];
            
            foreach ($selectedPackage->vendors as $index => $vendor) {
                $startTime = $packageVendorStartTimes[$index] ?? null;
                $endTime = $packageVendorEndTimes[$index] ?? null;
                
                if ($startTime) $vendorTimes[] = $startTime;
                if ($endTime) $vendorTimes[] = $endTime;
                
                Schedule::create([
                    'event_id' => $event->id,
                    'vendor_id' => $vendor->id,
                    'member_id' => null,
                    'activity' => $vendor->name,
                    'location' => null,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'status' => 'pending',
                ]);
            }
        } else {
            if ($request->has('vendor_id') && is_array($request->vendor_id)) {
                foreach ($request->vendor_id as $index => $vendorId) {
                    if ($vendorId) {
                        $startTime = $request->vendor_start_time[$index] ?? null;
                        $endTime = $request->vendor_end_time[$index] ?? null;
                        $memberId = $request->vendor_member_id[$index] ?? null;
                        
                        if ($startTime) $vendorTimes[] = $startTime;
                        if ($endTime) $vendorTimes[] = $endTime;
                        
                        $vendor = Vendor::find($vendorId);
                        Schedule::create([
                            'event_id' => $event->id,
                            'vendor_id' => $vendorId,
                            'member_id' => $memberId,
                            'activity' => $vendor ? $vendor->name : 'Vendor',
                            'location' => null,
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                            'status' => $request->vendor_status[$index] ?? 'pending',
                        ]);
                    }
                }
            }
        }

        if (!empty($vendorTimes)) {
            sort($vendorTimes);
            $event->start_time = reset($vendorTimes);
            $event->end_time = end($vendorTimes);
            $event->save();
        }

        $event->eventMembers()->delete();
        if ($request->has('team_member_id') && is_array($request->team_member_id)) {
            foreach ($request->team_member_id as $index => $memberId) {
                if ($memberId) {
                    EventMember::create([
                        'event_id' => $event->id,
                        'member_id' => $memberId,
                        'role' => $request->team_member_role[$index] ?? null,
                        'status' => 'assigned',
                    ]);
                }
            }
        }

        // Get referrer from session or default to index
        $referrer = session('event_detail_referrer');
        session()->forget('event_detail_referrer');
        
        if ($referrer && str_contains($referrer, 'calendar')) {
            return redirect()->route('admin.calendar')->with('success', 'Event updated successfully!');
        }
        
        return redirect()->route('admin.event_day.index')->with('success', 'Event updated successfully!');
    }

    public function show($id)
    {
        $event = Event::with([
            'booking.client',
            'booking.package',
            'package',
            'schedules.vendor.category',
            'eventMembers.member'
        ])->findOrFail($id);

        // Store the referrer in session for back navigation
        if (request()->headers->get('referer')) {
            session(['event_detail_referrer' => request()->headers->get('referer')]);
        }

        return view('admin.event.event_detail', compact('event'));
    }

    public function getEventDays($bookingId)
    {
        $events = Event::where('booking_id', $bookingId)
            ->select('id', 'name', 'event_date', 'start_time', 'end_time')
            ->orderBy('event_date')
            ->get();
        
        return response()->json($events);
    }
}
