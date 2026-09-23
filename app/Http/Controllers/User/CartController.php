<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = session()->get('cart', [
            'type' => null, // 'package' or 'custom'
            'package_id' => null,
            'vendor_ids' => [],
        ]);
        
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
        
        return view('user.cart.index', [
            'cart' => $cart,
            'package' => $package,
            'vendors' => $vendors,
            'totalPrice' => $totalPrice,
        ]);
    }
    
    public function addPackage(Request $request, $packageId)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('info', 'Silakan login terlebih dahulu untuk menambahkan ke keranjang');
        }
        
        $package = Package::findOrFail($packageId);
        
        session()->put('cart', [
            'type' => 'package',
            'package_id' => $package->id,
            'vendor_ids' => [],
        ]);
        
        return redirect()->route('user.cart')->with('success', 'Paket berhasil ditambahkan ke keranjang');
    }
    
    public function addVendor(Request $request, $vendorId)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('info', 'Silakan login terlebih dahulu untuk menambahkan ke keranjang');
        }
        
        $vendor = Vendor::findOrFail($vendorId);
        
        $cart = session()->get('cart', [
            'type' => 'custom',
            'package_id' => null,
            'vendor_ids' => [],
        ]);
        
        if ($cart['type'] === 'package') {
            return redirect()->back()->with('error', 'Anda sudah memilih paket. Hapus paket terlebih dahulu untuk menambah vendor custom.');
        }
        
        $cart['type'] = 'custom';
        if (!in_array($vendorId, $cart['vendor_ids'])) {
            $cart['vendor_ids'][] = $vendorId;
        }
        
        session()->put('cart', $cart);
        
        return redirect()->route('user.cart')->with('success', 'Vendor berhasil ditambahkan ke keranjang');
    }
    
    public function remove(Request $request, $vendorId)
    {
        $cart = session()->get('cart', [
            'type' => 'custom',
            'package_id' => null,
            'vendor_ids' => [],
        ]);
        
        $cart['vendor_ids'] = array_values(array_filter($cart['vendor_ids'], function($id) use ($vendorId) {
            return $id != $vendorId;
        }));
        
        if (empty($cart['vendor_ids'])) {
            $cart['type'] = null;
        }
        
        session()->put('cart', $cart);
        
        return redirect()->route('user.cart')->with('success', 'Vendor dihapus dari keranjang');
    }
    
    public function clear()
    {
        session()->forget('cart');
        
        return redirect()->route('user.cart')->with('success', 'Keranjang dikosongkan');
    }
}
