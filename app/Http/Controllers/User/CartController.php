<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Package;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = Cart::forCurrentUser();

        $package = null;
        $vendors = collect();
        $totalPrice = 0;
        $hasCatering = false;
        $maxGuestCount = null;
        $missingCateringCapacity = false;

        if ($cart['type'] === 'package' && $cart['package_id']) {
            $package = Package::with('vendors.category')
                ->find($cart['package_id']);

            if ($package) {
                $vendors = $package->vendors;
                $totalPrice = $vendors->sum('price');
            }
        }

        if ($cart['type'] === 'custom' && ! empty($cart['vendor_ids'])) {
            $vendors = Vendor::with(['category', 'photos'])
                ->whereIn('id', $cart['vendor_ids'])
                ->get();

            $cateringVendors = $vendors->filter(fn (Vendor $vendor) => $vendor->isCatering());
            $hasCatering = $cateringVendors->isNotEmpty();
            $missingCateringCapacity = $cateringVendors->contains(
                fn (Vendor $vendor) => (int) $vendor->guest_capacity < 1
            );
            if ($hasCatering && ! $missingCateringCapacity) {
                $maxGuestCount = (int) $cateringVendors->min('guest_capacity');
            }

            $totalPrice = $vendors->sum('price');
        }

        return view('site.cart.index', [
            'cart' => $cart,
            'package' => $package,
            'vendors' => $vendors,
            'totalPrice' => $totalPrice,
            'hasCatering' => $hasCatering,
            'maxGuestCount' => $maxGuestCount,
            'missingCateringCapacity' => $missingCateringCapacity,
        ]);
    }

    public function addPackage(Request $request, $packageId)
    {
        $package = Package::availableForBooking()->find($packageId);
        if (! $package) {
            return redirect()->back()->with('error', 'Paket tidak tersedia karena ada vendor yang sedang menutup sesi.');
        }

        DB::transaction(function () use ($package) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
            $cart->update([
                'type' => 'package',
                'package_id' => $package->id,
            ]);
            $cart->vendors()->sync([]);
        });

        Cart::forCurrentUser();

        if ($request->input('redirect') === 'checkout') {
            return redirect()
                ->route('user.checkout')
                ->with('success', 'Paket siap untuk checkout.');
        }

        return redirect()
            ->route('user.cart')
            ->with('success', 'Paket berhasil ditambahkan ke keranjang');
    }

    public function addVendor($vendorId)
    {
        $vendor = Vendor::availableForBooking()->find($vendorId);
        if (! $vendor) {
            return redirect()->back()->with('error', 'Vendor sedang menutup sesi dan belum bisa dipesan.');
        }

        $current = Cart::forCurrentUser();

        if ($current['type'] === 'package') {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Anda sudah memilih paket. Hapus paket terlebih dahulu untuk menambah vendor custom.'
                );
        }

        DB::transaction(function () use ($vendor) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
            $cart->update([
                'type' => 'custom',
                'package_id' => null,
            ]);
            $cart->vendors()->syncWithoutDetaching([$vendor->id]);
        });

        Cart::forCurrentUser();

        return redirect()
            ->route('user.cart')
            ->with('success', 'Vendor berhasil ditambahkan ke keranjang');
    }

    public function remove($vendorId)
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        if ($cart) {
            $cart->vendors()->detach($vendorId);
            if ($cart->type === 'custom' && ! $cart->vendors()->exists()) {
                $cart->update(['type' => null]);
            }
        }

        Cart::forCurrentUser();

        return redirect()
            ->route('user.cart')
            ->with('success', 'Vendor dihapus dari keranjang');
    }

    public function clear()
    {
        Cart::clearForCurrentUser();

        return redirect()
            ->route('user.cart')
            ->with('success', 'Keranjang dikosongkan');
    }
}
