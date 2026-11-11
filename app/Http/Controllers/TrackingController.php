<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Services\TrackingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function __construct(
        protected TrackingService $trackingService
    ) {}

    public function store(Request $request, Shipment $shipment): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'event_time' => ['required', 'date'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'description' => ['required', 'string', 'max:500'],
            'current_stage' => ['nullable', 'integer', 'min:1', 'max:4'],
        ]);

        try {
            $this->trackingService->addTrackingEvent($shipment, $validated, $request->user());

            return redirect()->route('shipments.show', $shipment)
                ->with('success', 'New tracking event registered and timeline updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }
}
