<?php

namespace App\Services;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\ShipmentItem;
use App\Models\TrackingEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ShipmentService
{
    public function __construct(
        protected EtaService $etaService,
        protected AuditService $auditService
    ) {}

    /**
     * Get filtered and paginated shipments based on user role and query filters.
     */
    public function getShipmentsQuery(?User $user = null, array $filters = []): Builder
    {
        $query = Shipment::with(['customer', 'carrier', 'origin', 'destination', 'items', 'latestTrackingEvent', 'exceptions']);

        // Scope by customer role: customer can ONLY see their own shipments
        if ($user && $user->isCustomer()) {
            $query->where('customer_id', $user->id);
        }

        // Search by shipment number, consignee, vessel/flight
        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $query->where(function ($q) use ($s) {
                $q->where('shipment_number', 'like', "%{$s}%")
                    ->orWhere('consignee_name', 'like', "%{$s}%")
                    ->orWhere('vessel_or_flight', 'like', "%{$s}%")
                    ->orWhereHas('carrier', fn($c) => $c->where('name', 'like', "%{$s}%")->orWhere('code', 'like', "%{$s}%"))
                    ->orWhereHas('origin', fn($o) => $o->where('name', 'like', "%{$s}%")->orWhere('code', 'like', "%{$s}%"))
                    ->orWhereHas('destination', fn($d) => $d->where('name', 'like', "%{$s}%")->orWhere('code', 'like', "%{$s}%"));
            });
        }

        // Filter by transport mode (OCEAN, AIR)
        if (!empty($filters['mode']) && $filters['mode'] !== 'all') {
            $query->where('transport_mode', strtoupper($filters['mode']));
        }

        // Filter by status (e.g. IN_TRANSIT, DELIVERED, BERTHED, etc.)
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', strtoupper($filters['status']));
        }

        // Filter by carrier
        if (!empty($filters['carrier_id'])) {
            $query->where('carrier_id', $filters['carrier_id']);
        }

        // Filter by delay status or exceptions
        if (!empty($filters['filter'])) {
            if ($filters['filter'] === 'ocean') {
                $query->where('transport_mode', 'OCEAN');
            } elseif ($filters['filter'] === 'air') {
                $query->where('transport_mode', 'AIR');
            } elseif ($filters['filter'] === 'exceptions' || $filters['filter'] === 'delay') {
                $query->where(function ($q) {
                    $q->where('delay_status', '!=', 'ON_TIME')
                      ->orWhereHas('exceptions', fn($e) => $e->where('status', '!=', 'RESOLVED'));
                });
            }
        }

        return $query->latest();
    }

    /**
     * Create a new shipment with cargo items, initial tracking event, and ETA calculation.
     */
    public function createShipment(array $data, User $creator): Shipment
    {
        return DB::transaction(function () use ($data, $creator) {
            // Generate shipment identifier if not provided
            $mode = strtoupper($data['transport_mode'] ?? 'OCEAN');
            $prefix = $mode === 'AIR' ? 'AWB' : 'AT';
            $number = $data['shipment_number'] ?? ($prefix . '-' . date('Y') . '-' . strtoupper(substr(uniqid(), -5)));

            // Validate origin != destination
            if (($data['origin_id'] ?? null) == ($data['destination_id'] ?? null)) {
                throw ValidationException::withMessages([
                    'destination_id' => ['Origin and Destination ports must be different nodes.'],
                ]);
            }

            $expectedDeparture = Carbon::parse($data['expected_departure']);
            $expectedArrival = Carbon::parse($data['expected_arrival']);
            $estimatedArrival = !empty($data['estimated_arrival'])
                ? Carbon::parse($data['estimated_arrival'])
                : (clone $expectedArrival);

            $status = $data['status'] ?? Shipment::STATUS_CREATED;

            // Initial delay calculation
            $delayCalc = $this->etaService->calculateDelay(
                new Shipment(['expected_arrival' => $expectedArrival]),
                $estimatedArrival
            );

            $shipment = Shipment::create([
                'shipment_number' => $number,
                'customer_id' => $data['customer_id'],
                'carrier_id' => $data['carrier_id'],
                'origin_id' => $data['origin_id'],
                'destination_id' => $data['destination_id'],
                'status' => $status,
                'transport_mode' => $mode,
                'expected_departure' => $expectedDeparture,
                'actual_departure' => in_array($status, [Shipment::STATUS_IN_TRANSIT, Shipment::STATUS_IN_FLIGHT]) ? $expectedDeparture : null,
                'expected_arrival' => $expectedArrival,
                'estimated_arrival' => $estimatedArrival,
                'actual_arrival' => null,
                'delay_status' => $delayCalc['delay_status'],
                'delay_hours' => $delayCalc['delay_hours'],
                'consignee_name' => $data['consignee_name'] ?? null,
                'vessel_or_flight' => $data['vessel_or_flight'] ?? null,
                'berth_number' => $data['berth_number'] ?? null,
                'current_stage' => 1,
                'total_stages' => $data['total_stages'] ?? 4,
                'temperature_celsius' => $data['temperature_celsius'] ?? null,
                'humidity_pct' => $data['humidity_pct'] ?? null,
                'air_speed_kts' => $data['air_speed_kts'] ?? null,
                'air_altitude_ft' => $data['air_altitude_ft'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            // Add Cargo Items
            if (!empty($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    ShipmentItem::create([
                        'shipment_id' => $shipment->id,
                        'item_name' => $item['item_name'] ?? 'General Cargo Container',
                        'quantity' => $item['quantity'] ?? 1,
                        'weight_kg' => $item['weight_kg'] ?? 1000.0,
                        'volume_cbm' => $item['volume_cbm'] ?? 5.0,
                    ]);
                }
            } else {
                // Default initial manifest item
                ShipmentItem::create([
                    'shipment_id' => $shipment->id,
                    'item_name' => $mode === 'AIR' ? 'High-Priority Cargo Consignment' : 'Standard 40ft High Cube Container',
                    'quantity' => 1,
                    'weight_kg' => $mode === 'AIR' ? 4500.00 : 26500.00,
                    'volume_cbm' => $mode === 'AIR' ? 25.00 : 67.50,
                ]);
            }

            // Create initial tracking event at origin
            $origin = Location::find($shipment->origin_id);
            TrackingEvent::create([
                'shipment_id' => $shipment->id,
                'location_id' => $shipment->origin_id,
                'created_by' => $creator->id,
                'status' => $status,
                'event_time' => now(),
                'latitude' => $origin?->latitude,
                'longitude' => $origin?->longitude,
                'description' => "Consignment registered at origin: {$origin?->name} ({$origin?->code})",
            ]);

            // Audit
            $this->auditService->log('SHIPMENT_CREATED', $shipment, null, $shipment->toArray(), $creator);

            return $shipment;
        });
    }

    /**
     * Compute comprehensive dashboard telemetry and KPI metrics.
     */
    public function getDashboardMetrics(?User $user = null): array
    {
        $baseQuery = Shipment::query();
        if ($user && $user->isCustomer()) {
            $baseQuery->where('customer_id', $user->id);
        }

        $totalConsignments = (clone $baseQuery)->count();
        $inTransitCount = (clone $baseQuery)->whereIn('status', [Shipment::STATUS_IN_TRANSIT, Shipment::STATUS_IN_FLIGHT, Shipment::STATUS_BERTHED])->count();
        $deliveredCount = (clone $baseQuery)->where('status', Shipment::STATUS_DELIVERED)->count();
        $delayedShipments = (clone $baseQuery)->where('delay_status', '!=', 'ON_TIME')->get();
        $delayedCount = $delayedShipments->count();
        $activeDelayHoursSum = $delayedShipments->sum('delay_hours');

        // Exceptions
        $openExceptionsQuery = ShipmentException::where('status', '!=', ShipmentException::STATUS_RESOLVED);
        if ($user && $user->isCustomer()) {
            $openExceptionsQuery->whereHas('shipment', fn($s) => $s->where('customer_id', $user->id));
        }
        $openExceptions = $openExceptionsQuery->with('shipment')->latest('detected_at')->get();
        $openExceptionsCount = $openExceptions->count();

        // High priority incident notice
        $activeIncident = $openExceptions->firstWhere('severity', ShipmentException::SEVERITY_HIGH) ?? $openExceptions->first();

        // Carriers & Hubs
        $carrierCount = Carrier::where('is_active', true)->count();
        $hubCount = Location::count();

        // On-time reliability KPI percentage
        $onTimeReliability = $totalConsignments > 0
            ? round((($totalConsignments - $delayedCount) / $totalConsignments) * 100, 1)
            : 100.0;

        // Next port berthing highlight
        $nextBerthing = (clone $baseQuery)
            ->whereNotNull('destination_id')
            ->whereNotNull('berth_number')
            ->with(['destination', 'carrier'])
            ->orderBy('estimated_arrival', 'asc')
            ->first();

        // Recent shipments with relationships for map & list
        $shipmentsForMap = (clone $baseQuery)
            ->with(['origin', 'destination', 'carrier', 'latestTrackingEvent', 'items', 'exceptions'])
            ->latest()
            ->limit(15)
            ->get();

        return [
            'total_consignments' => $totalConsignments,
            'in_transit_count' => $inTransitCount,
            'delivered_count' => $deliveredCount,
            'delayed_count' => $delayedCount,
            'active_delay_hours_sum' => $activeDelayHoursSum,
            'open_exceptions_count' => $openExceptionsCount,
            'active_incident' => $activeIncident,
            'carrier_count' => $carrierCount,
            'hub_count' => $hubCount,
            'on_time_reliability' => $onTimeReliability,
            'next_berthing' => $nextBerthing,
            'shipments' => $shipmentsForMap,
        ];
    }
}
