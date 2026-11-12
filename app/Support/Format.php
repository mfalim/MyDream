<?php

namespace App\Support;

final class Format
{
    public static function rupiah(int|float|string|null $amount): string
    {
        return 'Rp ' . number_format((float) $amount, 0, ',', '.');
    }

    /** Link WhatsApp dari nomor lokal (08xx) atau internasional (62xx). Kosong -> nomor admin. */
    public static function waLink(?string $phone, string $text = ''): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if ($digits === '') {
            $digits = preg_replace('/\D+/', '', (string) config('mydream.whatsapp'));
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        return 'https://wa.me/' . $digits . ($text !== '' ? '?text=' . rawurlencode($text) : '');
    }
}
