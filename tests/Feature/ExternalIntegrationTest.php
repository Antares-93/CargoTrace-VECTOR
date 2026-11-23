<?php

namespace Tests\Feature;

use App\Services\ExternalGeocodingService;
use App\Services\ExternalWeatherService;
use Tests\TestCase;

class ExternalIntegrationTest extends TestCase
{
    public function test_external_weather_service_returns_data_or_fallback(): void
    {
        $weatherService = new ExternalWeatherService();
        $data = $weatherService->getWeather(-6.1017, 106.8833);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('temperature', $data);
        $this->assertArrayHasKey('humidity', $data);
        $this->assertArrayHasKey('wind_speed_kts', $data);
        $this->assertArrayHasKey('source', $data);
    }

    public function test_external_geocoding_service_resolves_known_port(): void
    {
        $geocodingService = new ExternalGeocodingService();
        $data = $geocodingService->geocode('Tanjung Priok');

        $this->assertNotNull($data);
        $this->assertArrayHasKey('latitude', $data);
        $this->assertArrayHasKey('longitude', $data);
        $this->assertEqualsWithDelta(-6.10, $data['latitude'], 0.1);
    }

    public function test_api_weather_endpoint_returns_json(): void
    {
        $response = $this->getJson('/api/weather?lat=-6.1017&lng=106.8833');
        $response->assertStatus(200)
                 ->assertJsonStructure(['temperature', 'humidity', 'wind_speed_kts', 'source']);
    }

    public function test_api_geocode_endpoint_returns_json(): void
    {
        $response = $this->getJson('/api/geocode?q=Rotterdam');
        $response->assertStatus(200)
                 ->assertJsonStructure(['status', 'latitude', 'longitude']);
    }
}
