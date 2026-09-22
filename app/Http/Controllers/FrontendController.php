<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FrontendController extends Controller
{
    public function home(): View
    {
        return view('pages.home');
    }

    public function catalog(): View
    {
        return view('pages.catalog.index');
    }

    public function package(string $slug): View
    {
        return view('pages.catalog.show', compact('slug'));
    }

    public function vendors(): View
    {
        return view('pages.vendor.index');
    }

    public function vendor(string $slug): View
    {
        return view('pages.vendor.show', compact('slug'));
    }

    public function blog(): View
    {
        return view('pages.blog.index');
    }

    public function article(string $slug): View
    {
        return view('pages.blog.show', compact('slug'));
    }

    public function inspiration(): View
    {
        return view('pages.inspiration.index');
    }

    public function cart(): View
    {
        return view('pages.cart.index');
    }

    public function checkout(): View
    {
        return view('pages.checkout.index');
    }

    public function placeOrder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'groom_name' => ['required', 'string', 'max:255'],
            'bride_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255'],
            'event_date' => ['required', 'date'],
            'guest_count' => ['required', 'integer', 'min:1'],
            'location' => ['required', 'string', 'max:1000'],
        ]);

        session(['checkout' => $validated]);

        return redirect()->route('payment.index');
    }

    public function addToCart(Request $request, string $slug): RedirectResponse
    {
        $request->validate([
            'event_date' => ['nullable', 'date'],
            'guest_count' => ['nullable', 'integer', 'min:1'],
        ]);

        session()->put('cart.last_added', [
            'slug' => $slug,
            'event_date' => $request->input('event_date'),
            'guest_count' => $request->integer('guest_count', 1),
        ]);

        return redirect()->route('cart.index')->with('success', 'Paket berhasil ditambahkan ke keranjang.');
    }

    public function payment(): View
    {
        return view('pages.payment.index');
    }

    public function paymentSuccess(): View
    {
        return view('pages.payment.success');
    }

    public function event(): View
    {
        return view('pages.event.index');
    }
}
