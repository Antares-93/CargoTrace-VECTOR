<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use App\Services\ExceptionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExceptionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $operator;
    protected Shipment $shipment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operator = User::factory()->create(['role' => 'operator']);
        $carrier = Carrier::create(['code' => 'MSK', 'name' => 'Maersk Line']);
        $origin = Location::create(['code' => 'IDTPP', 'name' => 'Jakarta', 'country' => 'ID', 'latitude' => -6.1, 'longitude' => 106.8]);
        $dest = Location::create(['code' => 'NLRTM', 'name' => 'Rotterdam', 'country' => 'NL', 'latitude' => 51.9, 'longitude' => 4.0]);

        $this->shipment = Shipment::create([
            'shipment_number' => 'AT-TEST-EXC',
            'customer_id' => User::factory()->create()->id,
            'carrier_id' => $carrier->id,
            'origin_id' => $origin->id,
            'destination_id' => $dest->id,
            'status' => Shipment::STATUS_IN_TRANSIT,
            'transport_mode' => 'OCEAN',
            'expected_departure' => Carbon::now()->subDays(5),
            'expected_arrival' => Carbon::now()->addDays(5),
            'estimated_arrival' => Carbon::now()->addDays(5),
            'consignee_name' => 'Test Exception Consignee',
        ]);
    }

    public function test_operator_can_report_exception_and_impact_delay(): void
    {
        $exceptionService = app(ExceptionService::class);

        $exception = $exceptionService->reportException($this->shipment, [
            'exception_type' => ShipmentException::TYPE_PORT_CONGESTION,
            'severity' => ShipmentException::SEVERITY_HIGH,
            'delay_impact_hours' => 28,
            'detected_at' => now(),
        ], $this->operator);

        $this->assertDatabaseHas('shipment_exceptions', [
            'id' => $exception->id,
            'exception_type' => ShipmentException::TYPE_PORT_CONGESTION,
            'severity' => ShipmentException::SEVERITY_HIGH,
            'status' => ShipmentException::STATUS_OPEN,
        ]);

        // Check that shipment ETA was adjusted by +28 hours and delay_status is HIGH
        $this->shipment->refresh();
        $this->assertEquals(28, $this->shipment->delay_hours);
        $this->assertEquals('HIGH', $this->shipment->delay_status);
    }

    public function test_exception_resolution_lifecycle(): void
    {
        $exceptionService = app(ExceptionService::class);

        $exception = $exceptionService->reportException($this->shipment, [
            'exception_type' => ShipmentException::TYPE_CUSTOMS_HOLD,
            'severity' => ShipmentException::SEVERITY_MEDIUM,
            'detected_at' => now()->subHours(6),
        ], $this->operator);

        // Resolve the exception
        $resolved = $exceptionService->resolveException($exception, [
            'status' => ShipmentException::STATUS_RESOLVED,
            'resolution_notes' => 'Customs declaration amendment inspected and cleared by Rotterdam Port Authority.',
        ], $this->operator);

        $this->assertEquals(ShipmentException::STATUS_RESOLVED, $resolved->status);
        $this->assertNotNull($resolved->resolved_at);
        $this->assertStringContainsString('amendment inspected and cleared', $resolved->resolution_notes);
    }
}
