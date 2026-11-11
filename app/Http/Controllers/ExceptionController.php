<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Services\ExceptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExceptionController extends Controller
{
    public function __construct(
        protected ExceptionService $exceptionService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $query = ShipmentException::with(['shipment.carrier', 'shipment.origin', 'shipment.destination', 'reporter'])
            ->latest('detected_at');

        if ($user->isCustomer()) {
            $query->whereHas('shipment', fn($s) => $s->where('customer_id', $user->id));
        }

        if ($status = $request->input('status')) {
            $query->where('status', strtoupper($status));
        }

        if ($severity = $request->input('severity')) {
            $query->where('severity', strtoupper($severity));
        }

        if ($type = $request->input('type')) {
            $query->where('exception_type', strtoupper($type));
        }

        $exceptions = $query->paginate(12)->withQueryString();
        $shipments = Shipment::orderBy('shipment_number')->get();

        return view('exceptions.index', compact('exceptions', 'shipments'));
    }

    public function store(Request $request, Shipment $shipment): RedirectResponse
    {
        $validated = $request->validate([
            'exception_type' => ['required', 'string', 'in:CUSTOMS_HOLD,WEATHER_DELAY,PORT_CONGESTION,DAMAGED,MISROUTED,DOCUMENTATION_ISSUE'],
            'severity' => ['required', 'string', 'in:LOW,MEDIUM,HIGH'],
            'detected_at' => ['required', 'date'],
            'delay_impact_hours' => ['nullable', 'integer', 'min:0', 'max:720'],
        ]);

        $this->exceptionService->reportException($shipment, $validated, $request->user());

        return redirect()->back()
            ->with('success', "Operational exception logged on consignment {$shipment->shipment_number}.");
    }

    public function resolve(Request $request, ShipmentException $exception): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:OPEN,INVESTIGATING,RESOLVED'],
            'resolution_notes' => ['required', 'string', 'max:1000'],
        ]);

        $this->exceptionService->resolveException($exception, $validated, $request->user());

        return redirect()->back()
            ->with('success', "Incident #{$exception->id} status updated to {$validated['status']}.");
    }
}
