<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use App\Services\AuditService;
use App\Services\EtaService;
use App\Services\ExternalWeatherService;
use App\Services\ShipmentService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShipmentController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService,
        protected EtaService $etaService,
        protected ExternalWeatherService $weatherService,
        protected AuditService $auditService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $filters = $request->only(['search', 'mode', 'status', 'carrier_id', 'filter']);

        $shipments = $this->shipmentService
            ->getShipmentsQuery($user, $filters)
            ->paginate(12)
            ->withQueryString();

        $carriers = Carrier::where('is_active', true)->get();
        $locations = Location::all();

        return view('shipments.index', compact('shipments', 'carriers', 'locations', 'filters'));
    }

    public function create(): View
    {
        $carriers = Carrier::where('is_active', true)->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $customers = User::where('role', 'customer')->orderBy('name')->get();

        return view('shipments.create', compact('carriers', 'locations', 'customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'shipment_number' => ['nullable', 'string', 'unique:shipments,shipment_number'],
            'customer_id' => ['required', 'exists:users,id'],
            'carrier_id' => ['required', 'exists:carriers,id'],
            'origin_id' => ['required', 'exists:locations,id', 'different:destination_id'],
            'destination_id' => ['required', 'exists:locations,id'],
            'transport_mode' => ['required', 'in:OCEAN,AIR,ROAD,MULTIMODAL'],
            'status' => ['required', 'in:CREATED,BOOKED,PICKED_UP,IN_TRANSIT,IN_FLIGHT'],
            'expected_departure' => ['required', 'date'],
            'expected_arrival' => ['required', 'date', 'after:expected_departure'],
            'estimated_arrival' => ['nullable', 'date'],
            'consignee_name' => ['required', 'string', 'max:255'],
            'vessel_or_flight' => ['nullable', 'string', 'max:255'],
            'berth_number' => ['nullable', 'string', 'max:100'],
            'temperature_celsius' => ['nullable', 'numeric'],
            'humidity_pct' => ['nullable', 'numeric'],
            'air_speed_kts' => ['nullable', 'integer'],
            'air_altitude_ft' => ['nullable', 'integer'],
            'notes' => ['nullable', 'string'],
            'items' => ['nullable', 'array'],
            'items.*.item_name' => ['required_with:items', 'string'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.weight_kg' => ['required_with:items', 'numeric', 'min:0.1'],
            'items.*.volume_cbm' => ['nullable', 'numeric', 'min:0'],
        ]);

        $shipment = $this->shipmentService->createShipment($validated, $request->user());

        return redirect()->route('shipments.show', $shipment)
            ->with('success', "Consignment {$shipment->shipment_number} successfully registered!");
    }

    public function show(Shipment $shipment, Request $request): View
    {
        $user = $request->user();

        // Customer authorization: can only view own shipment
        if ($user->isCustomer() && $shipment->customer_id !== $user->id) {
            abort(403, 'Unauthorized access: You may only view consignments assigned to your account.');
        }

        $shipment->load([
            'customer',
            'carrier',
            'origin',
            'destination',
            'items',
            'trackingEvents.location',
            'trackingEvents.creator',
            'exceptions.reporter',
        ]);

        // External Weather API integration for Origin and Destination
        $originWeather = $this->weatherService->getWeather(
            $shipment->origin->latitude,
            $shipment->origin->longitude
        );

        $destWeather = $this->weatherService->getWeather(
            $shipment->destination->latitude,
            $shipment->destination->longitude
        );

        $locations = Location::orderBy('name')->get();

        return view('shipments.show', compact('shipment', 'originWeather', 'destWeather', 'locations'));
    }

    public function edit(Shipment $shipment): View
    {
        $shipment->load(['customer', 'carrier', 'origin', 'destination', 'items']);
        $carriers = Carrier::where('is_active', true)->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $customers = User::where('role', 'customer')->orderBy('name')->get();

        return view('shipments.edit', compact('shipment', 'carriers', 'locations', 'customers'));
    }

    public function update(Request $request, Shipment $shipment): RedirectResponse
    {
        // Business Rule: Terminal state check
        if ($shipment->status === Shipment::STATUS_DELIVERED && !$request->user()->isAdmin()) {
            return back()->with('error', 'Shipment is marked DELIVERED and cannot be modified without administrative privileges.');
        }

        $validated = $request->validate([
            'carrier_id' => ['required', 'exists:carriers,id'],
            'origin_id' => ['required', 'exists:locations,id', 'different:destination_id'],
            'destination_id' => ['required', 'exists:locations,id'],
            'transport_mode' => ['required', 'in:OCEAN,AIR,ROAD,MULTIMODAL'],
            'expected_departure' => ['required', 'date'],
            'expected_arrival' => ['required', 'date'],
            'estimated_arrival' => ['nullable', 'date'],
            'consignee_name' => ['required', 'string', 'max:255'],
            'vessel_or_flight' => ['nullable', 'string', 'max:255'],
            'berth_number' => ['nullable', 'string', 'max:100'],
            'temperature_celsius' => ['nullable', 'numeric'],
            'humidity_pct' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'string'],
        ]);

        $oldValues = $shipment->toArray();

        $shipment->fill($validated);

        if (!empty($validated['estimated_arrival'])) {
            $this->etaService->updateShipmentDelay($shipment, Carbon::parse($validated['estimated_arrival']));
        } else {
            $shipment->save();
        }

        $this->auditService->log('SHIPMENT_UPDATED', $shipment, $oldValues, $shipment->toArray(), $request->user());

        return redirect()->route('shipments.show', $shipment)
            ->with('success', 'Shipment details updated successfully.');
    }

    public function destroy(Shipment $shipment, Request $request): RedirectResponse
    {
        if (!$request->user()->isAdmin()) {
            abort(403, 'Only administrators can delete shipment manifests.');
        }

        $number = $shipment->shipment_number;
        $this->auditService->log('SHIPMENT_DELETED', $shipment, $shipment->toArray(), null, $request->user());
        $shipment->delete();

        return redirect()->route('shipments.index')
            ->with('success', "Shipment {$number} has been deleted.");
    }
}
