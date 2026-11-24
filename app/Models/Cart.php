<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Cart extends Model
{
    protected $fillable = ['user_id', 'type', 'package_id'];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'cart_vendor');
    }

    /**
     * Ambil state keranjang user aktif dari database (atau session jika belum login),
     * dengan format array yang kompatibel penuh dengan view & controller web.
     */
    public static function forCurrentUser(): array
    {
        $empty = [
            'type' => null,
            'package_id' => null,
            'vendor_ids' => [],
        ];

        if (! Auth::check()) {
            return session()->get('cart', $empty);
        }

        $userId = (int) Auth::id();
        $dbCart = static::with('vendors:id')->where('user_id', $userId)->first();

        // Jika belum ada di DB tetapi session punya isi, migrasikan otomatis ke DB
        if (! $dbCart && session()->has('cart')) {
            $sessionCart = session()->get('cart', $empty);
            if (! empty($sessionCart['type'])) {
                $dbCart = static::create([
                    'user_id' => $userId,
                    'type' => $sessionCart['type'],
                    'package_id' => $sessionCart['type'] === 'package' ? ($sessionCart['package_id'] ?? null) : null,
                ]);
                if ($sessionCart['type'] === 'custom' && ! empty($sessionCart['vendor_ids'])) {
                    $dbCart->vendors()->sync($sessionCart['vendor_ids']);
                    $dbCart->load('vendors:id');
                }
            }
        }

        if (! $dbCart || empty($dbCart->type)) {
            session()->forget('cart');

            return $empty;
        }

        $state = [
            'type' => $dbCart->type,
            'package_id' => $dbCart->type === 'package' ? $dbCart->package_id : null,
            'vendor_ids' => $dbCart->type === 'custom'
                ? $dbCart->vendors->pluck('id')->map(fn ($id) => (int) $id)->values()->all()
                : [],
        ];

        session()->put('cart', $state);

        return $state;
    }

    public static function clearForCurrentUser(): void
    {
        if (Auth::check()) {
            static::where('user_id', Auth::id())->delete();
        }

        session()->forget('cart');
    }
}
