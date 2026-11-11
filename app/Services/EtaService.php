<?php

namespace App\Services;

use App\Models\Shipment;
use Carbon\Carbon;

class EtaService
{
    /**
     * Calculate and apply delay status & hours for a shipment based on deterministic rules.
     * Rule:
     * - If estimated_arrival > expected_arrival:
     *     diff <= 6 hours => LOW
     *     diff > 6 and <= 24 hours => MEDIUM
     *     diff > 24 hours => HIGH
     * - Else => ON_TIME (0 delay hours)
     */
    public function calculateDelay(Shipment $shipment, ?Carbon $newEstimatedArrival = null): array
    {
        $expectedArrival = $shipment->expected_arrival;
        $estimatedArrival = $newEstimatedArrival ?? ($shipment->estimated_arrival ?? $expectedArrival);

        if (!$expectedArrival || !$estimatedArrival) {
            return [
                'delay_hours' => 0,
                'delay_status' => 'ON_TIME',
            ];
        }

        if ($estimatedArrival->greaterThan($expectedArrival)) {
            $diffHours = (int) ceil($expectedArrival->diffInMinutes($estimatedArrival) / 60);

            if ($diffHours <= 6) {
                $status = 'LOW';
            } elseif ($diffHours <= 24) {
                $status = 'MEDIUM';
            } else {
                $status = 'HIGH';
            }

            return [
                'delay_hours' => $diffHours,
                'delay_status' => $status,
            ];
        }

        return [
            'delay_hours' => 0,
            'delay_status' => 'ON_TIME',
        ];
    }

    /**
     * Recompute and save delay parameters on a shipment.
     */
    public function updateShipmentDelay(Shipment $shipment, ?Carbon $newEstimatedArrival = null): Shipment
    {
        $calc = $this->calculateDelay($shipment, $newEstimatedArrival);

        if ($newEstimatedArrival) {
            $shipment->estimated_arrival = $newEstimatedArrival;
        }

        $shipment->delay_hours = $calc['delay_hours'];
        $shipment->delay_status = $calc['delay_status'];
        $shipment->save();

        return $shipment;
    }
}
