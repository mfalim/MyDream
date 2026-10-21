<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'user_id',
        'member_code',
        'name',
        'call_sign',
        'phone',
        'email',
        'domicile',
        'division',
        'position',
        'specialization',
        'certification',
        'ht_code',
        'emergency_name',
        'emergency_phone',
        'daily_fee',
        'status',
    ];

    protected $casts = [
        'daily_fee' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $member) {
            if (empty($member->member_code)) {
                $member->member_code = self::generateMemberCode();
            }
        });
    }

    public static function generateMemberCode(): string
    {
        $lastMember = self::query()
            ->whereNotNull('member_code')
            ->orderByDesc('id')
            ->first();

        $sequence = 1;

        if ($lastMember && $lastMember->member_code) {
            $digits = preg_replace('/\D+/', '', (string) $lastMember->member_code);

            if ($digits !== '') {
                $sequence = (int) $digits + 1;
            }
        }

        $candidate = 'MBR' . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
        $counter = 1;

        while (self::query()->where('member_code', $candidate)->exists() && $counter <= 1000) {
            $sequence++;
            $candidate = 'MBR' . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
            $counter++;
        }

        return $candidate;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_members')
            ->withPivot('role', 'notes', 'status')
            ->withTimestamps();
    }
}
