<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use App\Services\TrackingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class TrackingAndLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected User $operator;
    protected Shipment $shipment;
    protected Location $location;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operator = User::factory()->create(['role' => 'operator']);
        $carrier = Carrier::create(['code' => 'MSK', 'name' => 'Maersk Line']);
        $origin = Location::create(['code' => 'IDTPP', 'name' => 'Tanjung Priok', 'country' => 'ID', 'latitude' => -6.1, 'longitude' => 106.8]);
        $dest = Location::create(['code' => 'NLRTM', 'name' => 'Rotterdam', 'country' => 'NL', 'latitude' => 51.9, 'longitude' => 4.0]);
        $this->location = $origin;

        $this->shipment = Shipment::create([
            'shipment_number' => 'AT-TEST-LIFE',
            'customer_id' => User::factory()->create()->id,
            'carrier_id' => $carrier->id,
            'origin_id' => $origin->id,
            'destination_id' => $dest->id,
            'status' => Shipment::STATUS_CREATED,
            'transport_mode' => 'OCEAN',
            'expected_departure' => now(),
            'expected_arrival' => now()->addDays(10),
            'consignee_name' => 'Test Lifecycle Consignee',
        ]);
    }

    public function test_valid_status_transition_sequence(): void
    {
        $trackingService = app(TrackingService::class);

        // 1. CREATED -> BOOKED
        $event1 = $trackingService->addTrackingEvent($this->shipment, [
            'status' => Shipment::STATUS_BOOKED,
            'location_id' => $this->location->id,
            'description' => 'Booking confirmed by carrier',
            'event_time' => now(),
        ], $this->operator);

        $this->assertEquals(Shipment::STATUS_BOOKED, $this->shipment->fresh()->status);
        $this->assertDatabaseHas('tracking_events', ['status' => Shipment::STATUS_BOOKED]);

        // 2. BOOKED -> PICKED_UP
        $event2 = $trackingService->addTrackingEvent($this->shipment, [
            'status' => Shipment::STATUS_PICKED_UP,
            'location_id' => $this->location->id,
            'description' => 'Container picked up from warehouse',
            'event_time' => now()->addHour(),
        ], $this->operator);

        $this->assertEquals(Shipment::STATUS_PICKED_UP, $this->shipment->fresh()->status);

        // 3. PICKED_UP -> IN_TRANSIT
        $event3 = $trackingService->addTrackingEvent($this->shipment, [
            'status' => Shipment::STATUS_IN_TRANSIT,
            'location_id' => $this->location->id,
            'description' => 'Vessel departed maritime corridor',
            'event_time' => now()->addHours(2),
        ], $this->operator);

        $this->assertEquals(Shipment::STATUS_IN_TRANSIT, $this->shipment->fresh()->status);
        $this->assertNotNull($this->shipment->fresh()->actual_departure);

        // Verify all 3 historical events remain in database (never overwritten)
        $this->assertEquals(3, $this->shipment->trackingEvents()->count());
    }

    public function test_invalid_status_transition_from_terminal_delivered(): void
    {
        $this->shipment->status = Shipment::STATUS_DELIVERED;
        $this->shipment->save();

        $this->expectException(ValidationException::class);

        $trackingService = app(TrackingService::class);

        // Attempting to revert DELIVERED back to CREATED
        $trackingService->addTrackingEvent($this->shipment, [
            'status' => Shipment::STATUS_CREATED,
            'description' => 'Illegal reversion test',
            'event_time' => now(),
        ], $this->operator);
    }
}
