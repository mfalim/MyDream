<?php

namespace App\MyDream;

class Format
{
    /** 139000000 => "Rp 139.000.000" */
    public static function rupiah(int|float $n): string
    {
        return 'Rp ' . number_format($n, 0, ',', '.');
    }

    /** 139000000 => "139 Jt" */
    public static function juta(int|float $n): string
    {
        $v = $n / 1_000_000;
        return rtrim(rtrim(number_format($v, 1, ',', '.'), '0'), ',') . ' Jt';
    }

    public static function stars(float $rating): string
    {
        return number_format($rating, 1, '.', '');
    }
}
