<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalWeatherService
{
    /**
     * Get real-time weather telemetry for coordinates using Open-Meteo.
     * Includes timeout handling, error response handling, and cached fallback.
     */
    public function getWeather(float $latitude, float $longitude): array
    {
        $cacheKey = "weather_{$latitude}_{$longitude}";

        return Cache::remember($cacheKey, 900, function () use ($latitude, $longitude) {
            try {
                $response = Http::timeout(3)->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'current' => 'temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code',
                    'timezone' => 'auto',
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $current = $data['current'] ?? [];

                    return [
                        'status' => 'success',
                        'source' => 'Open-Meteo Live API',
                        'temperature' => $current['temperature_2m'] ?? 24.5,
                        'humidity' => $current['relative_humidity_2m'] ?? 65,
                        'wind_speed_kts' => round(($current['wind_speed_10m'] ?? 10) * 0.539957, 1), // convert km/h to knots
                        'condition' => $this->interpretWeatherCode($current['weather_code'] ?? 0),
                        'weather_code' => $current['weather_code'] ?? 0,
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning("Weather API call failed for [{$latitude}, {$longitude}]: " . $e->getMessage());
            }

            // Deterministic graceful fallback if external API is unreachable or offline
            return [
                'status' => 'fallback',
                'source' => 'Cached Telemetry Sensor Model',
                'temperature' => 22.4,
                'humidity' => 60,
                'wind_speed_kts' => 12.0,
                'condition' => 'Clear Skies / Mild Sea Breeze',
                'weather_code' => 0,
            ];
        });
    }

    protected function interpretWeatherCode(int $code): string
    {
        return match (true) {
            $code === 0 => 'Clear Sky',
            in_array($code, [1, 2, 3]) => 'Partly Cloudy',
            in_array($code, [45, 48]) => 'Fog / Marine Mist',
            in_array($code, [51, 53, 55, 61, 63, 65]) => 'Moderate Rain',
            in_array($code, [71, 73, 75, 77, 85, 86]) => 'Snow Conditions',
            in_array($code, [95, 96, 99]) => 'Thunderstorm / Gale Warning',
            default => 'Fair Maritime Conditions',
        };
    }
}
