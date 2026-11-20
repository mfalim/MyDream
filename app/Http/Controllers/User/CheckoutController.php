<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Event;
use App\Models\Package;
use App\Models\Vendor;
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
            return redirect()
                ->route('cart.index')
                ->with('error', 'Keranjang kosong. Pilih paket atau vendor terlebih dahulu.');
        }

        if ($cart['type'] === 'custom' && count($cart['vendor_ids']) < 2) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Minimal pilih 2 vendor untuk melanjutkan ke checkout.');
        }

        $package = null;
        $vendors = collect();
        $totalPrice = 0;
        $guestCount = null;
        $isCustomCatering = false;
        $maxGuests = null;

        if ($cart['type'] === 'package' && $cart['package_id']) {
            $package = Package::with('vendors.category')
                ->find($cart['package_id']);

            if ($package) {
                $vendors = $package->vendors;
                $totalPrice = $vendors->sum('price');
                $maxGuests = $package->guest_capacity ?: null;
            }
        }

        if ($cart['type'] === 'custom' && ! empty($cart['vendor_ids'])) {
            $vendors = Vendor::with(['category', 'photos'])
                ->whereIn('id', $cart['vendor_ids'])
                ->get();

            if ($vendors->count() < 2) {
                return redirect()
                    ->route('cart.index')
                    ->with('error', 'Minimal pilih 2 vendor untuk melanjutkan ke checkout.');
            }

            $cateringVendors = $vendors->filter(fn (Vendor $vendor) => $vendor->isCatering());
            $isCustomCatering = $cateringVendors->isNotEmpty();
            if ($isCustomCatering) {
                if ($cateringVendors->contains(fn (Vendor $vendor) => (int) $vendor->guest_capacity < 1)) {
                    return redirect()
                        ->route('cart.index')
                        ->with('error', 'Kapasitas vendor catering belum diatur. Hubungi admin sebelum checkout.');
                }

                $maxGuests = (int) $cateringVendors->min('guest_capacity');
            }

            $totalPrice = $vendors->sum(fn (Vendor $vendor) => $vendor->priceForGuests($guestCount));
        }

        $client = Client::where('users_id', Auth::id())->first();

        return view('site.checkout.index', [
            'cart' => $cart,
            'package' => $package,
            'vendors' => $vendors,
            'totalPrice' => $totalPrice,
            'client' => $client,
            'guestCount' => $guestCount,
            'maxGuests' => $maxGuests,
            'isCustomCatering' => $isCustomCatering,
        ]);
    }

    public function quote(Request $request)
    {
        $cart = session()->get('cart');
        if (($cart['type'] ?? null) !== 'custom' || empty($cart['vendor_ids'])) {
            return response()->json(['message' => 'Keranjang vendor custom tidak ditemukan.'], 422);
        }

        $vendors = Vendor::with('category')
            ->whereIn('id', $cart['vendor_ids'])
            ->get();

        if ($vendors->count() < 2) {
            return response()->json(['message' => 'Minimal pilih 2 vendor untuk melanjutkan.'], 422);
        }

        $cateringVendors = $vendors->filter(fn (Vendor $vendor) => $vendor->isCatering());
        if ($cateringVendors->isEmpty()) {
            return response()->json(['message' => 'Perhitungan jumlah tamu hanya tersedia untuk pesanan catering custom.'], 422);
        }

        if ($cateringVendors->contains(fn (Vendor $vendor) => (int) $vendor->guest_capacity < 1)) {
            return response()->json(['message' => 'Kapasitas vendor catering belum diatur. Hubungi admin sebelum checkout.'], 422);
        }

        $guestCapacity = (int) $cateringVendors->min('guest_capacity');
        $validated = $request->validate([
            'guest_count' => 'nullable|integer|min:1|max:' . $guestCapacity,
        ]);
        $guestCount = isset($validated['guest_count']) ? (int) $validated['guest_count'] : null;
        $totalPrice = $vendors->sum(fn (Vendor $vendor) => $vendor->priceForGuests($guestCount));
        $dpPercent = (int) config('mydream.dp_percent');

        return response()->json([
            'totalPrice' => $totalPrice,
            'dpAmount' => (int) ceil($totalPrice * $dpPercent / 100),
        ]);
    }

    public function store(Request $request)
    {
        $cart = session()->get('cart');
        if (empty($cart['type'])) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Keranjang kosong');
        }

        $package = null;
        $vendors = collect();
        $guestCapacity = 10000;

        if (($cart['type'] ?? null) === 'package' && ! empty($cart['package_id'])) {
            $package = Package::with('vendors.category')->find($cart['package_id']);
            if (! $package) {
                return redirect()
                    ->route('cart.index')
                    ->with('error', 'Paket tidak ditemukan.');
            }

            $vendors = $package->vendors;
            $guestCapacity = $package->guest_capacity ?: $guestCapacity;
        } elseif (($cart['type'] ?? null) === 'custom' && ! empty($cart['vendor_ids'])) {
            $vendors = Vendor::with('category')
                ->whereIn('id', $cart['vendor_ids'])
                ->get();

            if ($vendors->count() < 2) {
                return redirect()
                    ->route('cart.index')
                    ->with('error', 'Minimal pilih 2 vendor untuk melanjutkan.');
            }

            $cateringVendors = $vendors->filter(fn (Vendor $vendor) => $vendor->isCatering());
            if ($cateringVendors->isNotEmpty()) {
                if ($cateringVendors->contains(fn (Vendor $vendor) => (int) $vendor->guest_capacity < 1)) {
                    return redirect()
                        ->route('cart.index')
                        ->with('error', 'Kapasitas vendor catering belum diatur. Hubungi admin sebelum checkout.');
                }

                $guestCapacity = (int) $cateringVendors->min('guest_capacity');
            }
        } else {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Keranjang tidak valid.');
        }

        $validated = $request->validate([
            'venue_name' => 'required|string|max:255',
            'venue_address' => 'required|string|max:1000',
            'venue_city' => 'required|string|max:100',
            'venue_province' => 'required|string|max:100',
            'guest_count' => 'required|integer|min:1|max:' . $guestCapacity,
            'event_date' => 'required|date|after:today',
        ], [
            'venue_name.required' => 'Nama venue wajib diisi',
            'venue_address.required' => 'Alamat venue wajib diisi',
            'venue_city.required' => 'Kota venue wajib diisi',
            'venue_province.required' => 'Provinsi venue wajib diisi',
            'guest_count.required' => 'Jumlah tamu wajib diisi',
            'guest_count.min' => 'Jumlah tamu minimal 1 orang',
            'guest_count.max' => 'Jumlah tamu tidak boleh melebihi kapasitas yang dipilih (:max orang)',
            'event_date.required' => 'Tanggal acara wajib diisi',
            'event_date.after' => 'Tanggal acara harus setelah hari ini',
        ]);

        $packageId = null;
        $totalPrice = 0;

        if ($cart['type'] === 'package') {
            $packageId = $package->id;
            $totalPrice = $vendors->sum('price');
        }

        if ($cart['type'] === 'custom') {
            $guestCount = (int) $validated['guest_count'];
            $totalPrice = $vendors->sum(fn (Vendor $vendor) => $vendor->priceForGuests($guestCount));
        }

        $client = Client::where('users_id', Auth::id())->first();

        if (! $client) {
            return redirect()
                ->route('client.profile')
                ->with('error', 'Lengkapi data mempelai terlebih dahulu');
        }

        DB::beginTransaction();

        try {
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
            // Terbitkan invoice saat booking dibuat agar nomornya tersedia di riwayat.
            $booking->ensureInvoiceNumber();

            $event = Event::create([
                'booking_id' => $booking->id,
                'package_id' => $packageId,
                'name' => 'Pernikahan ' . $client->groom_name . ' & ' . $client->bride_name,
                'event_type' => 'wedding',
                'event_date' => $validated['event_date'],
                'guest_count' => $validated['guest_count'],
                'status' => 'scheduled',
            ]);

            if ($cart['type'] === 'package' && $packageId) {
                foreach ($vendors as $vendor) {
                    $event->schedules()->create([
                        'vendor_id' => $vendor->id,
                        'price' => $vendor->price,
                        'activity' => $vendor->name,
                        'start_time' => $validated['event_date'] . ' 09:00:00',
                        'end_time' => $validated['event_date'] . ' 17:00:00',
                        'status' => 'pending',
                        'agreed_fee' => $vendor->price,
                        'paid_amount' => 0,
                        'payment_status' => 'unpaid',
                    ]);
                }
            }

            if ($cart['type'] === 'custom' && ! empty($cart['vendor_ids'])) {
                foreach ($vendors as $vendor) {
                    $vendorPrice = $vendor->priceForGuests($guestCount);

                    $event->schedules()->create([
                        'vendor_id' => $vendor->id,
                        'price' => $vendorPrice,
                        'activity' => $vendor->name,
                        'start_time' => $validated['event_date'] . ' 09:00:00',
                        'end_time' => $validated['event_date'] . ' 17:00:00',
                        'status' => 'pending',
                        'agreed_fee' => $vendorPrice,
                        'paid_amount' => 0,
                        'payment_status' => 'unpaid',
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

            return redirect()
                ->route('payment.index', $booking->id)
                ->with('success', 'Booking berhasil dibuat. Silakan lakukan pembayaran.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


}
