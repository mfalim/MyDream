<?php

namespace App\Support;

/** Data provinsi dan kabupaten/kota Indonesia dari resources/data/wilayah.json. */
class Wilayah
{
    private static ?array $data = null;

    /** @return array<int, array{name: string, cities: array<int, string>}> */
    public static function all(): array
    {
        return self::$data ??= json_decode((string) @file_get_contents(resource_path('data/wilayah.json')), true) ?: [];
    }

    /** @return array<int, string> */
    public static function cities(?string $province): array
    {
        foreach (self::all() as $row) {
            if ($row['name'] === $province) {
                return $row['cities'];
            }
        }

        return [];
    }

    /** Apakah kota/kabupaten tersebut benar berada di provinsi itu. */
    public static function exists(?string $province, ?string $city): bool
    {
        return $city !== null && in_array($city, self::cities($province), true);
    }
}
