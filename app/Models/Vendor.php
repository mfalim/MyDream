<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'price',
        'phone',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'address',
        'city',
        'instagram',
        'description',
    ];

    public function getFormattedBankAccountAttribute(): ?string
    {
        if (! $this->bank_name && ! $this->bank_account_number) {
            return null;
        }

        return trim(($this->bank_name ?: 'Bank') . ' ' . ($this->bank_account_number ?: '-') . ($this->bank_account_holder ? ' a.n. ' . $this->bank_account_holder : ''));
    }

    protected static function booted(): void
    {
        static::creating(function (self $vendor) {
            if (empty($vendor->slug)) {
                $baseSlug = \Illuminate\Support\Str::slug($vendor->name);
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $vendor->slug = $slug;
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function photos()
    {
        return $this->hasMany(VendorPhoto::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_vendor')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
