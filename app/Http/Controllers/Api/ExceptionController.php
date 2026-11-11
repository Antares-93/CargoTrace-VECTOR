<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Services\ExceptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExceptionController extends Controller
{
    public function __construct(
        protected ExceptionService $exceptionService
    ) {}

    public function index(Request $request, int $id): JsonResponse
    {
        $shipment = Shipment::find($id);
        if (!$shipment) {
            return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
        }

        if ($request->user()->isCustomer() && $shipment->customer_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
        }

        $exceptions = $shipment->exceptions()->with('reporter:id,name')->get();

        return response()->json([
            'success' => true,
            'data' => $exceptions,
        ]);
    }

    public function store(Request $request, int $id): JsonResponse
    {
        $shipment = Shipment::find($id);
        if (!$shipment) {
            return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
        }

        $user = $request->user();
        if ($user->isCustomer()) {
            return response()->json(['success' => false, 'message' => 'Forbidden: Customers cannot report operational exceptions.'], 403);
        }

        $validated = $request->validate([
            'exception_type' => ['required', 'string'],
            'severity' => ['required', 'in:LOW,MEDIUM,HIGH'],
            'detected_at' => ['required', 'date'],
            'delay_impact_hours' => ['nullable', 'integer'],
        ]);

        $exception = $this->exceptionService->reportException($shipment, $validated, $user);

        return response()->json([
            'success' => true,
            'message' => 'Exception logged successfully.',
            'data' => $exception,
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $exception = ShipmentException::with('shipment')->find($id);
        if (!$exception) {
            return response()->json(['success' => false, 'message' => 'Exception record not found.'], 404);
        }

        $user = $request->user();
        if ($user->isCustomer()) {
            return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:OPEN,INVESTIGATING,RESOLVED'],
            'resolution_notes' => ['required', 'string'],
        ]);

        $updated = $this->exceptionService->resolveException($exception, $validated, $user);

        return response()->json([
            'success' => true,
            'message' => 'Exception status updated successfully.',
            'data' => $updated,
        ]);
    }
}
