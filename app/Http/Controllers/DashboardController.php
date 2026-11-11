<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Services\ShipmentService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        // 1. KPI Telemetry & High Priority Incident
        $metrics = $this->shipmentService->getDashboardMetrics($user);

        // 2. Filtered Manifest Table
        $filters = [
            'search' => $request->input('search'),
            'mode' => $request->input('mode', 'all'),
            'filter' => $request->input('filter', 'all'),
            'carrier_id' => $request->input('carrier_id'),
        ];

        $manifests = $this->shipmentService
            ->getShipmentsQuery($user, $filters)
            ->paginate(10)
            ->withQueryString();

        $carriers = Carrier::where('is_active', true)->get();
        $locations = Location::all();

        // Lead shipment for Radar Display (either focused AT-2026-B8910 or first in transit)
        $focusShipmentNumber = $request->input('focus', 'AT-2026-B8910');
        $leadShipment = Shipment::with(['origin', 'destination', 'carrier', 'trackingEvents', 'items', 'exceptions'])
            ->where('shipment_number', $focusShipmentNumber)
            ->first() ?? $metrics['shipments']->first();

        return view('dashboard.index', compact(
            'metrics',
            'manifests',
            'leadShipment',
            'carriers',
            'locations',
            'filters'
        ));
    }
}
