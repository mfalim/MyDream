<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Cart extends Model
{
    public const RELATIONS = [
        'package.vendors.category',
        'package.vendors.photos',
        'vendors.category',
        'vendors.photos',
    ];

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
     * Set keranjang user ke mode Paket (menggantikan paket/vendor custom sebelumnya).
     */
    public static function setPackageForUser(int $userId, int $packageId): self
    {
        return DB::transaction(function () use ($userId, $packageId) {
            $cart = static::firstOrCreate(['user_id' => $userId]);
            $cart->update([
                'type' => 'package',
                'package_id' => $packageId,
            ]);
            $cart->vendors()->sync([]);

            return $cart;
        });
    }

    /**
     * Tambahkan satu vendor ke Custom Order user. Mengembalikan null bila keranjang sedang berisi Paket.
     */
    public static function addVendorForUser(int $userId, int $vendorId): ?self
    {
        return DB::transaction(function () use ($userId, $vendorId) {
            $cart = static::firstOrCreate(['user_id' => $userId]);
            $cart->refresh();

            if ($cart->type === 'package') {
                return null;
            }

            $cart->update([
                'type' => 'custom',
                'package_id' => null,
            ]);
            $cart->vendors()->syncWithoutDetaching([$vendorId]);

            return $cart;
        });
    }

    /**
     * Hapus satu vendor dari Custom Order user; bersihkan baris cart bila sudah kosong.
     */
    public static function removeVendorForUser(int $userId, int $vendorId): void
    {
        $cart = static::where('user_id', $userId)->first();
        if (! $cart) {
            return;
        }

        $cart->vendors()->detach($vendorId);
        if ($cart->type === 'custom' && ! $cart->vendors()->exists()) {
            $cart->delete();
        }
    }

    public static function clearForUser(int $userId): void
    {
        static::where('user_id', $userId)->delete();
    }

    /**
     * Ambil state keranjang user aktif langsung dari tabel `carts` & `cart_vendor` (tanpa session).
     */
    public static function forCurrentUser(): array
    {
        $empty = [
            'type' => null,
            'package_id' => null,
            'vendor_ids' => [],
        ];

        if (! Auth::check()) {
            return $empty;
        }

        $dbCart = static::with('vendors:id')
            ->where('user_id', (int) Auth::id())
            ->first();

        if (! $dbCart || empty($dbCart->type)) {
            return $empty;
        }

        return [
            'type' => $dbCart->type,
            'package_id' => $dbCart->type === 'package' ? $dbCart->package_id : null,
            'vendor_ids' => $dbCart->type === 'custom'
                ? $dbCart->vendors->pluck('id')->map(fn ($id) => (int) $id)->values()->all()
                : [],
        ];
    }

    public static function clearForCurrentUser(): void
    {
        if (Auth::check()) {
            static::clearForUser((int) Auth::id());
        }
    }
}
