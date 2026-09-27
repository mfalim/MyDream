<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Vendor;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = session()->get('cart', [
            'type' => null,
            'package_id' => null,
            'vendor_ids' => [],
        ]);

        $package = null;
        $vendors = collect();
        $totalPrice = 0;

        if ($cart['type'] === 'package' && $cart['package_id']) {
            $package = Package::with('vendors.category')
                ->find($cart['package_id']);

            if ($package) {
                $vendors = $package->vendors;
                $totalPrice = $vendors->sum('price');
            }
        }

        if ($cart['type'] === 'custom' && !empty($cart['vendor_ids'])) {
            $vendors = Vendor::with('category')
                ->whereIn('id', $cart['vendor_ids'])
                ->get();

            $totalPrice = $vendors->sum('price');
        }

        return view('pages.cart.index', [
            'cart' => $cart,
            'package' => $package,
            'vendors' => $vendors,
            'totalPrice' => $totalPrice,
        ]);
    }

    public function addPackage(Request $request, $packageId)
    {
        $package = Package::findOrFail($packageId);

        session()->put('cart', [
            'type' => 'package',
            'package_id' => $package->id,
            'vendor_ids' => [],
        ]);

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
        $vendor = Vendor::findOrFail($vendorId);

        $cart = session()->get('cart', [
            'type' => 'custom',
            'package_id' => null,
            'vendor_ids' => [],
        ]);

        if ($cart['type'] === 'package') {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Anda sudah memilih paket. Hapus paket terlebih dahulu untuk menambah vendor custom.'
                );
        }

        $cart['type'] = 'custom';

        if (!in_array($vendorId, $cart['vendor_ids'])) {
            $cart['vendor_ids'][] = $vendorId;
        }

        session()->put('cart', $cart);

        return redirect()
            ->route('user.cart')
            ->with('success', 'Vendor berhasil ditambahkan ke keranjang');
    }

    public function remove($vendorId)
    {
        $cart = session()->get('cart', [
            'type' => 'custom',
            'package_id' => null,
            'vendor_ids' => [],
        ]);

        $cart['vendor_ids'] = array_values(
            array_filter(
                $cart['vendor_ids'],
                fn ($id) => $id != $vendorId
            )
        );

        if (empty($cart['vendor_ids'])) {
            $cart['type'] = null;
        }

        session()->put('cart', $cart);

        return redirect()
            ->route('user.cart')
            ->with('success', 'Vendor dihapus dari keranjang');
    }

    public function clear()
    {
        session()->forget('cart');

        return redirect()
            ->route('user.cart')
            ->with('success', 'Keranjang dikosongkan');
    }
}
