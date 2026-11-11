<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Services\AuditService;
use App\Services\EtaService;
use App\Services\ShipmentService;
use App\Services\TrackingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService,
        protected TrackingService $trackingService,
        protected EtaService $etaService,
        protected AuditService $auditService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $filters = $request->only(['search', 'mode', 'status', 'carrier_id', 'filter']);

        $shipments = $this->shipmentService
            ->getShipmentsQuery($user, $filters)
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $shipments->items(),
            'meta' => [
                'current_page' => $shipments->currentPage(),
                'last_page' => $shipments->lastPage(),
                'per_page' => $shipments->perPage(),
                'total' => $shipments->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: Customers cannot create new consignments directly.',
            ], 403);
        }

        $validated = $request->validate([
            'shipment_number' => ['nullable', 'string', 'unique:shipments,shipment_number'],
            'customer_id' => ['required', 'exists:users,id'],
            'carrier_id' => ['required', 'exists:carriers,id'],
            'origin_id' => ['required', 'exists:locations,id', 'different:destination_id'],
            'destination_id' => ['required', 'exists:locations,id'],
            'transport_mode' => ['required', 'in:OCEAN,AIR,ROAD,MULTIMODAL'],
            'status' => ['nullable', 'in:CREATED,BOOKED,PICKED_UP,IN_TRANSIT,IN_FLIGHT'],
            'expected_departure' => ['required', 'date'],
            'expected_arrival' => ['required', 'date', 'after:expected_departure'],
            'estimated_arrival' => ['nullable', 'date'],
            'consignee_name' => ['required', 'string'],
            'vessel_or_flight' => ['nullable', 'string'],
            'berth_number' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'items' => ['nullable', 'array'],
        ]);

        $shipment = $this->shipmentService->createShipment($validated, $user);

        return response()->json([
            'success' => true,
            'message' => 'Shipment created successfully.',
            'data' => $shipment->load(['origin', 'destination', 'carrier', 'items', 'latestTrackingEvent']),
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $shipment = Shipment::with([
            'customer:id,name,email,phone',
            'carrier',
            'origin',
            'destination',
            'items',
            'trackingEvents.location',
            'trackingEvents.creator:id,name',
            'exceptions.reporter:id,name',
        ])->find($id);

        if (!$shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found.',
            ], 404);
        }

        if ($request->user()->isCustomer() && $shipment->customer_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: You do not have permission to view this consignment.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $shipment,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $shipment = Shipment::find($id);
        if (!$shipment) {
            return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
        }

        $user = $request->user();
        if ($user->isCustomer()) {
            return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
        }

        if ($shipment->status === Shipment::STATUS_DELIVERED && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment is marked DELIVERED and cannot be modified without administrative privileges.',
            ], 422);
        }

        $validated = $request->validate([
            'carrier_id' => ['sometimes', 'exists:carriers,id'],
            'destination_id' => ['sometimes', 'exists:locations,id'],
            'estimated_arrival' => ['nullable', 'date'],
            'consignee_name' => ['sometimes', 'string'],
            'vessel_or_flight' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $oldValues = $shipment->toArray();
        $shipment->fill($validated);

        if (!empty($validated['estimated_arrival'])) {
            $this->etaService->updateShipmentDelay($shipment, Carbon::parse($validated['estimated_arrival']));
        } else {
            $shipment->save();
        }

        $this->auditService->log('SHIPMENT_UPDATED_API', $shipment, $oldValues, $shipment->toArray(), $user);

        return response()->json([
            'success' => true,
            'message' => 'Shipment updated successfully.',
            'data' => $shipment,
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Forbidden: Admin privilege required.'], 403);
        }

        $shipment = Shipment::find($id);
        if (!$shipment) {
            return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
        }

        $shipment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shipment removed successfully.',
        ]);
    }

    public function tracking(Request $request, int $id): JsonResponse
    {
        $shipment = Shipment::find($id);
        if (!$shipment) {
            return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
        }

        if ($request->user()->isCustomer() && $shipment->customer_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
        }

        $events = $shipment->trackingEvents()->with('location')->get();

        return response()->json([
            'success' => true,
            'data' => $events,
        ]);
    }

    public function addTracking(Request $request, int $id): JsonResponse
    {
        $shipment = Shipment::find($id);
        if (!$shipment) {
            return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
        }

        $user = $request->user();
        if ($user->isCustomer()) {
            return response()->json(['success' => false, 'message' => 'Forbidden: Customers cannot record tracking events.'], 403);
        }

        $validated = $request->validate([
            'status' => ['required', 'string'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'event_time' => ['required', 'date'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'description' => ['required', 'string'],
            'current_stage' => ['nullable', 'integer'],
        ]);

        try {
            $event = $this->trackingService->addTrackingEvent($shipment, $validated, $user);

            return response()->json([
                'success' => true,
                'message' => 'Tracking event registered successfully.',
                'data' => $event->load('location'),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors(),
            ], 422);
        }
    }
}
