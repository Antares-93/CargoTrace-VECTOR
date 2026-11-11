<?php

namespace Tests\Feature;

use App\Models\Shipment;
use App\Services\EtaService;
use Carbon\Carbon;
use Tests\TestCase;

class EtaDelayRuleTest extends TestCase
{
    public function test_on_time_when_estimated_arrival_matches_or_earlier(): void
    {
        $etaService = new EtaService();
        $expected = Carbon::now()->addDays(5)->setTime(12, 0);

        $shipment = new Shipment([
            'expected_arrival' => $expected,
            'estimated_arrival' => $expected,
        ]);

        $res = $etaService->calculateDelay($shipment);

        $this->assertEquals(0, $res['delay_hours']);
        $this->assertEquals('ON_TIME', $res['delay_status']);
    }

    public function test_low_severity_delay_under_6_hours(): void
    {
        $etaService = new EtaService();
        $expected = Carbon::now()->addDays(5)->setTime(12, 0);
        $estimated = (clone $expected)->addHours(4); // 4 hours difference

        $shipment = new Shipment([
            'expected_arrival' => $expected,
            'estimated_arrival' => $estimated,
        ]);

        $res = $etaService->calculateDelay($shipment);

        $this->assertEquals(4, $res['delay_hours']);
        $this->assertEquals('LOW', $res['delay_status']);
    }

    public function test_medium_severity_delay_between_6_and_24_hours(): void
    {
        $etaService = new EtaService();
        $expected = Carbon::now()->addDays(5)->setTime(12, 0);
        $estimated = (clone $expected)->addHours(18); // 18 hours difference

        $shipment = new Shipment([
            'expected_arrival' => $expected,
            'estimated_arrival' => $estimated,
        ]);

        $res = $etaService->calculateDelay($shipment);

        $this->assertEquals(18, $res['delay_hours']);
        $this->assertEquals('MEDIUM', $res['delay_status']);
    }

    public function test_high_severity_delay_greater_than_24_hours(): void
    {
        $etaService = new EtaService();
        $expected = Carbon::now()->addDays(5)->setTime(12, 0);
        $estimated = (clone $expected)->addHours(28); // 28 hours (exact scenario from Suez Convoy Delay screenshot!)

        $shipment = new Shipment([
            'expected_arrival' => $expected,
            'estimated_arrival' => $estimated,
        ]);

        $res = $etaService->calculateDelay($shipment);

        $this->assertEquals(28, $res['delay_hours']);
        $this->assertEquals('HIGH', $res['delay_status']);
    }
}
