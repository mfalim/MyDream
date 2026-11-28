<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Package;
use App\Models\Vendor;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show(Request $request)
    {
        $cart = Cart::with(Cart::RELATIONS)
            ->where('user_id', $request->user()->id)
            ->first();

        return response()->json(['data' => $this->cartData($cart)]);
    }

    public function addPackage(Request $request, int $packageId)
    {
        $package = Package::availableForBooking()->findOrFail($packageId);
        $cart = Cart::setPackageForUser((int) $request->user()->id, (int) $package->id)
            ->fresh(Cart::RELATIONS);

        return response()->json(['data' => $this->cartData($cart)]);
    }

    public function addVendor(Request $request, int $vendorId)
    {
        $vendor = Vendor::availableForBooking()->findOrFail($vendorId);
        $cart = Cart::addVendorForUser((int) $request->user()->id, (int) $vendor->id);

        if ($cart === null) {
            return response()->json([
                'message' => 'Anda sudah memilih paket. Hapus paket terlebih dahulu untuk menambah vendor custom.',
            ], 409);
        }

        return response()->json(['data' => $this->cartData($cart->fresh(Cart::RELATIONS))]);
    }

    public function removeVendor(Request $request, int $vendorId)
    {
        Cart::removeVendorForUser((int) $request->user()->id, $vendorId);

        return $this->show($request);
    }

    public function clear(Request $request)
    {
        Cart::clearForUser((int) $request->user()->id);

        return $this->show($request);
    }

    private function cartData(?Cart $cart): array
    {
        $package = $cart?->type === 'package' ? $cart->package : null;
        $vendors = $package ? $package->vendors : ($cart?->vendors ?? collect());

        return [
            'cart' => [
                'type' => $cart?->type,
                'package_id' => $cart?->package_id,
                'vendor_ids' => $cart?->type === 'custom' ? $vendors->pluck('id')->values() : [],
            ],
            'package' => $package,
            'vendors' => $vendors,
            'total_price' => (float) $vendors->sum('price'),
        ];
    }
}
