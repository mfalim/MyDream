<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Package;
use App\Models\Vendor;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', [
            'type' => null,
            'package_id' => null,
            'vendor_ids' => [],
        ]);
        
        if (empty($cart['type'])) {
            return redirect()->route('user.cart')->with('error', 'Keranjang kosong. Pilih paket atau vendor terlebih dahulu.');
        }
        
        // Validasi minimal 3 vendor jika custom
        if ($cart['type'] === 'custom' && count($cart['vendor_ids']) < 3) {
            return redirect()->route('user.cart')->with('error', 'Minimal pilih 3 vendor untuk melanjutkan ke checkout.');
        }
        
        $package = null;
        $vendors = collect();
        $totalPrice = 0;
        
        if ($cart['type'] === 'package' && $cart['package_id']) {
            $package = Package::with('vendors.category')->find($cart['package_id']);
            if ($package) {
                $vendors = $package->vendors;
                $totalPrice = $package->price;
            }
        } elseif ($cart['type'] === 'custom' && !empty($cart['vendor_ids'])) {
            $vendors = Vendor::with('category')->whereIn('id', $cart['vendor_ids'])->get();
            $totalPrice = $vendors->sum('price');
        }
        
        $user = Auth::user();
        $client = $user ? Client::where('users_id', $user->id)->first() : null;
        
        return view('user.checkout.index', [
            'cart' => $cart,
            'package' => $package,
            'vendors' => $vendors,
            'totalPrice' => $totalPrice,
            'client' => $client,
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'venue_name' => 'required|string|max:255',
            'venue_address' => 'required|string|max:1000',
            'venue_city' => 'required|string|max:100',
            'venue_province' => 'required|string|max:100',
            'guest_count' => 'required|integer|min:10|max:10000',
            'event_date' => 'required|date|after:today',
        ], [
            'venue_name.required' => 'Nama venue wajib diisi',
            'venue_address.required' => 'Alamat venue wajib diisi',
            'venue_city.required' => 'Kota venue wajib diisi',
            'venue_province.required' => 'Provinsi venue wajib diisi',
            'guest_count.required' => 'Jumlah tamu wajib diisi',
            'guest_count.min' => 'Jumlah tamu minimal 10 orang',
            'guest_count.max' => 'Jumlah tamu maksimal 10.000 orang',
            'event_date.required' => 'Tanggal acara wajib diisi',
            'event_date.after' => 'Tanggal acara harus setelah hari ini',
        ]);
        
        $cart = session()->get('cart');
        
        if (empty($cart['type'])) {
            return redirect()->route('user.cart')->with('error', 'Keranjang kosong');
        }
        
        $packageId = null;
        $totalPrice = 0;
        
        if ($cart['type'] === 'package' && $cart['package_id']) {
            $package = Package::find($cart['package_id']);
            if ($package) {
                $packageId = $package->id;
                $totalPrice = $package->price;
            }
        } elseif ($cart['type'] === 'custom' && !empty($cart['vendor_ids'])) {
            $vendors = Vendor::whereIn('id', $cart['vendor_ids'])->get();
            $totalPrice = $vendors->sum('price');
        }
        
        DB::beginTransaction();
        
        try {
            $user = Auth::user();
            
            $client = Client::where('users_id', $user->id)->first();
            
            if (!$client) {
                return redirect()->route('client.profile')->with('error', 'Lengkapi data mempelai terlebih dahulu');
            }
            
            $booking = Booking::create([
                'client_id' => $client->id,
                'package_id' => $packageId,
                'venue_name' => $validated['venue_name'],
                'venue_address' => $validated['venue_address'],
                'venue_city' => $validated['venue_city'],
                'venue_province' => $validated['venue_province'],
                'guest_count' => $validated['guest_count'],
                'total_price' => $totalPrice,
                'status' => 'pending',
            ]);
            
            $event = Event::create([
                'booking_id' => $booking->id,
                'package_id' => $packageId,
                'name' => 'Pernikahan ' . $client->groom_name . ' & ' . $client->bride_name,
                'event_type' => 'wedding',
                'event_date' => $validated['event_date'],
                'guest_count' => $validated['guest_count'],
                'status' => 'pending',
            ]);
            
            if ($cart['type'] === 'package' && $packageId) {
                $package = Package::with('vendors')->find($packageId);
                foreach ($package->vendors as $vendor) {
                    $event->schedules()->create([
                        'vendor_id' => $vendor->id,
                        'activity' => $vendor->name,
                        'start_time' => $validated['event_date'] . ' 09:00:00',
                        'end_time' => $validated['event_date'] . ' 17:00:00',
                        'status' => 'pending',
                    ]);
                }
            } elseif ($cart['type'] === 'custom' && !empty($cart['vendor_ids'])) {
                $vendors = Vendor::whereIn('id', $cart['vendor_ids'])->get();
                foreach ($vendors as $vendor) {
                    $event->schedules()->create([
                        'vendor_id' => $vendor->id,
                        'activity' => $vendor->name,
                        'start_time' => $validated['event_date'] . ' 09:00:00',
                        'end_time' => $validated['event_date'] . ' 17:00:00',
                        'status' => 'pending',
                    ]);
                }
            }
            
            $event->refresh();
            $schedules = $event->schedules;
            
            if ($schedules->isNotEmpty()) {
                $event->update([
                    'start_time' => $schedules->min('start_time'),
                    'end_time' => $schedules->max('end_time'),
                ]);
            }
            
            session()->put('booking_id', $booking->id);
            session()->forget('cart');
            
            DB::commit();
            
            return redirect()->route('user.payment', $booking->id)->with('success', 'Booking berhasil dibuat. Silakan lakukan pembayaran.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function payment($bookingId)
    {
        $booking = Booking::with(['client', 'package'])->findOrFail($bookingId);
        
        $dp = (int) round($booking->total_price * 0.3);
        $lunas = (int) round($booking->total_price * 0.97);
        $savings = $booking->total_price - $lunas;
        $cicilan = (int) round($booking->total_price / 6);
        
        return view('user.checkout.payment', [
            'booking' => $booking,
            'dp' => $dp,
            'lunas' => $lunas,
            'savings' => $savings,
            'cicilan' => $cicilan,
        ]);
    }
}
