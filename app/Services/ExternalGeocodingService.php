<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalGeocodingService
{
    /**
     * Search coordinates for a port, hub, or city name using Nominatim OpenStreetMap API.
     * Includes timeout protection, rate-limit compliant User-Agent, and cached fallback.
     */
    public function geocode(string $query): ?array
    {
        $normalized = strtolower(trim($query));
        $cacheKey = 'geocode_' . md5($normalized);

        return Cache::remember($cacheKey, 86400, function () use ($normalized) {
            try {
                $response = Http::timeout(3)
                    ->withHeaders([
                        'User-Agent' => 'CargoTrace-Vector-Visibility/1.0 (logistics-platform)',
                    ])
                    ->get('https://nominatim.openstreetmap.org/search', [
                        'q' => $normalized,
                        'format' => 'json',
                        'limit' => 1,
                    ]);

                if ($response->successful() && !empty($response->json())) {
                    $item = $response->json()[0];

                    return [
                        'status' => 'success',
                        'source' => 'Nominatim OpenStreetMap',
                        'name' => $item['display_name'] ?? $normalized,
                        'latitude' => (float) $item['lat'],
                        'longitude' => (float) $item['lon'],
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning("Geocoding external API call failed for '{$normalized}': " . $e->getMessage());
            }

            // Deterministic hub fallbacks for standard maritime & air hubs
            $knownHubs = [
                'tanjung priok' => ['latitude' => -6.1017, 'longitude' => 106.8833, 'name' => 'Tanjung Priok, Jakarta, Indonesia'],
                'jakarta' => ['latitude' => -6.2088, 'longitude' => 106.8456, 'name' => 'Jakarta, Indonesia'],
                'singapore' => ['latitude' => 1.290270, 'longitude' => 103.851959, 'name' => 'Port of Singapore, Singapore'],
                'suez' => ['latitude' => 29.9737, 'longitude' => 32.5599, 'name' => 'Suez Canal Anchorage, Egypt'],
                'rotterdam' => ['latitude' => 51.9547, 'longitude' => 4.0531, 'name' => 'Port of Rotterdam, Netherlands'],
                'frankfurt' => ['latitude' => 50.0379, 'longitude' => 8.5622, 'name' => 'Frankfurt CargoCity, Germany'],
                'los angeles' => ['latitude' => 33.7423, 'longitude' => -118.2612, 'name' => 'Port of Los Angeles, USA'],
            ];

            foreach ($knownHubs as $key => $hub) {
                if (str_contains($normalized, $key)) {
                    return array_merge(['status' => 'fallback', 'source' => 'Deterministic Hub Directory'], $hub);
                }
            }

            return null;
        });
    }

    /**
     * Reverse geocode latitude and longitude to human-readable address.
     */
    public function reverseGeocode(float $lat, float $lon): array
    {
        $cacheKey = "rev_geocode_{$lat}_{$lon}";

        return Cache::remember($cacheKey, 86400, function () use ($lat, $lon) {
            try {
                $response = Http::timeout(3)
                    ->withHeaders([
                        'User-Agent' => 'CargoTrace-Vector-Visibility/1.0 (logistics-platform)',
                    ])
                    ->get('https://nominatim.openstreetmap.org/reverse', [
                        'lat' => $lat,
                        'lon' => $lon,
                        'format' => 'json',
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'status' => 'success',
                        'display_name' => $data['display_name'] ?? "Coordinates {$lat}, {$lon}",
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning("Reverse geocoding failed for [{$lat}, {$lon}]: " . $e->getMessage());
            }

            return [
                'status' => 'fallback',
                'display_name' => "Maritime Waypoint [{$lat}, {$lon}]",
            ];
        });
    }
}
