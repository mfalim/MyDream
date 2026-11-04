<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Package;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function show(Request $request)
    {
        $cart = Cart::with(['package.vendors.category', 'package.vendors.photos', 'vendors.category', 'vendors.photos'])
            ->where('user_id', $request->user()->id)
            ->first();

        return response()->json(['data' => $this->cartData($cart)]);
    }

    public function addPackage(Request $request, int $packageId)
    {
        $package = Package::with(['vendors.category', 'vendors.photos'])->findOrFail($packageId);
        $cart = DB::transaction(function () use ($request, $package) {
            $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
            $cart->update(['type' => 'package', 'package_id' => $package->id]);
            $cart->vendors()->sync([]);

            return $cart->fresh(['package.vendors.category', 'package.vendors.photos', 'vendors.category', 'vendors.photos']);
        });

        return response()->json(['data' => $this->cartData($cart)]);
    }

    public function addVendor(Request $request, int $vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        $cart = DB::transaction(function () use ($request, $vendor) {
            $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
            $cart->refresh();
            if ($cart->type === 'package') {
                return null;
            }

            $cart->update(['type' => 'custom', 'package_id' => null]);
            $cart->vendors()->syncWithoutDetaching([$vendor->id]);

            return $cart->fresh(['package.vendors.category', 'package.vendors.photos', 'vendors.category', 'vendors.photos']);
        });

        if ($cart === null) {
            return response()->json([
                'message' => 'A package is already selected. Clear the package before adding custom vendors.',
            ], 409);
        }

        return response()->json(['data' => $this->cartData($cart)]);
    }

    public function removeVendor(Request $request, int $vendorId)
    {
        $cart = Cart::where('user_id', $request->user()->id)->first();
        if ($cart) {
            $cart->vendors()->detach($vendorId);
            if ($cart->type === 'custom' && ! $cart->vendors()->exists()) {
                $cart->update(['type' => null]);
            }
        }

        return $this->show($request);
    }

    public function clear(Request $request)
    {
        Cart::where('user_id', $request->user()->id)->delete();

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
