<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Mengubah nilai kolom foto (path upload / URL penuh) menjadi URL yang bisa dipakai <img>.
 * Kalau file tidak ditemukan, dipakai gambar placeholder agar halaman tidak rusak.
 */
final class Media
{
    public static function url(?string $path): string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return self::placeholder();
        }

        if (Str::startsWith($path, ['http://', 'https://', '//', 'data:'])) {
            return $path;
        }

        $relative = ltrim($path, '/');

        if (is_file(public_path('storage/' . $relative))) {
            return asset('storage/' . $relative);
        }

        if (is_file(public_path($relative))) {
            return asset($relative);
        }

        return self::placeholder();
    }

    /**
     * Foto beranda: pakai file asli di public/images/home/<nama>.(jpg|jpeg|png|webp) bila ada,
     * kalau belum ada pakai foto contoh ($fallback).
     */
    public static function home(string $name, string $fallback): string
    {
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            if (is_file(public_path("images/home/{$name}.{$ext}"))) {
                return asset("images/home/{$name}.{$ext}");
            }
        }

        return $fallback;
    }

    private static function placeholder(): string
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="800" height="600" viewBox="0 0 800 600">'
            . '<rect width="800" height="600" fill="#F3E6D8"/>'
            . '<text x="400" y="310" font-family="Georgia,serif" font-size="44" text-anchor="middle" fill="#B08D57">MyDream</text>'
            . '</svg>';

        return 'data:image/svg+xml;charset=utf-8,' . rawurlencode($svg);
    }
}
