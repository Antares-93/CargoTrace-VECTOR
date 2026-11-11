<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShipmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $operator;
    protected User $customer1;
    protected User $customer2;
    protected Carrier $carrier;
    protected Location $origin;
    protected Location $destination;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->operator = User::factory()->create(['role' => 'operator']);
        $this->customer1 = User::factory()->create(['role' => 'customer']);
        $this->customer2 = User::factory()->create(['role' => 'customer']);

        $this->carrier = Carrier::create([
            'code' => 'MSK',
            'name' => 'Maersk Line',
            'is_active' => true,
        ]);

        $this->origin = Location::create([
            'code' => 'IDTPP',
            'name' => 'Tanjung Priok',
            'type' => 'SEAPORT',
            'country' => 'Indonesia',
            'latitude' => -6.1017,
            'longitude' => 106.8833,
        ]);

        $this->destination = Location::create([
            'code' => 'NLRTM',
            'name' => 'Port of Rotterdam',
            'type' => 'SEAPORT',
            'country' => 'Netherlands',
            'latitude' => 51.9547,
            'longitude' => 4.0531,
        ]);
    }

    public function test_customer_can_only_see_their_own_shipments(): void
    {
        // Shipment for customer 1
        $s1 = Shipment::create([
            'shipment_number' => 'AT-TEST-001',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'status' => 'IN_TRANSIT',
            'transport_mode' => 'OCEAN',
            'expected_departure' => now()->subDays(2),
            'expected_arrival' => now()->addDays(5),
            'consignee_name' => 'Customer 1 Company',
        ]);

        // Shipment for customer 2
        $s2 = Shipment::create([
            'shipment_number' => 'AT-TEST-002',
            'customer_id' => $this->customer2->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'status' => 'BOOKED',
            'transport_mode' => 'OCEAN',
            'expected_departure' => now(),
            'expected_arrival' => now()->addDays(6),
            'consignee_name' => 'Customer 2 Company',
        ]);

        $response = $this->actingAs($this->customer1)->get('/shipments');
        $response->assertStatus(200);
        $response->assertSee('AT-TEST-001');
        $response->assertDontSee('AT-TEST-002');
    }

    public function test_customer_cannot_view_another_customer_shipment(): void
    {
        $s2 = Shipment::create([
            'shipment_number' => 'AT-TEST-002',
            'customer_id' => $this->customer2->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'status' => 'BOOKED',
            'expected_departure' => now(),
            'expected_arrival' => now()->addDays(6),
        ]);

        // Customer 1 tries to access Customer 2's consignment detail
        $response = $this->actingAs($this->customer1)->get("/shipments/{$s2->id}");
        $response->assertStatus(403);
    }

    public function test_operator_can_create_shipment(): void
    {
        $response = $this->actingAs($this->operator)->post('/shipments', [
            'shipment_number' => 'AT-2026-TEST9',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'transport_mode' => 'OCEAN',
            'status' => 'BOOKED',
            'expected_departure' => now()->addDay()->format('Y-m-d H:i:s'),
            'expected_arrival' => now()->addDays(10)->format('Y-m-d H:i:s'),
            'consignee_name' => 'Global Logistics Test',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('shipments', [
            'shipment_number' => 'AT-2026-TEST9',
            'consignee_name' => 'Global Logistics Test',
        ]);
    }

    public function test_origin_and_destination_cannot_be_identical(): void
    {
        $response = $this->actingAs($this->operator)->post('/shipments', [
            'shipment_number' => 'AT-2026-ERR',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->origin->id, // identical
            'transport_mode' => 'OCEAN',
            'status' => 'BOOKED',
            'expected_departure' => now()->addDay()->format('Y-m-d H:i:s'),
            'expected_arrival' => now()->addDays(10)->format('Y-m-d H:i:s'),
            'consignee_name' => 'Same Origin Destination Test',
        ]);

        $response->assertSessionHasErrors('origin_id');
    }

    public function test_customer_cannot_create_shipment_via_api(): void
    {
        $response = $this->actingAs($this->customer1, 'sanctum')->postJson('/api/shipments', [
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'transport_mode' => 'OCEAN',
            'expected_departure' => now()->format('Y-m-d H:i:s'),
            'expected_arrival' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'consignee_name' => 'Hacker Consignee',
        ]);

        $response->assertStatus(403);
    }
}
