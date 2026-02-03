<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GeocodingService
{
    public static function reverse(float $latitude, float $longitude): ?array
    {
        $cacheKey = 'geo_' . md5($latitude . '_' . $longitude);

        return Cache::remember($cacheKey, now()->addDays(7), function () use ($latitude, $longitude) {

            $response = Http::withHeaders([
                'User-Agent' => 'LaravelPropertyApp/1.0'
            ])->get('https://nominatim.openstreetmap.org/reverse', [
                'lat' => $latitude,
                'lon' => $longitude,
                'format' => 'json',
                'addressdetails' => 1,
            ]);

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();

            return [
                'country'  => $data['address']['country'] ?? null,
                'city'     => $data['address']['city']
                                ?? $data['address']['town']
                                ?? $data['address']['village']
                                ?? null,
                'district' => $data['address']['suburb']
                                ?? $data['address']['neighbourhood']
                                ?? null,
                'address'  => $data['display_name'] ?? null,
            ];
        });
    }
}
