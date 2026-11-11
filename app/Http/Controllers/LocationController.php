<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    public function index(): View
    {
        $locations = Location::withCount(['shipmentsAsOrigin', 'shipmentsAsDestination'])->orderBy('name')->get();
        return view('locations.index', compact('locations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10', 'unique:locations,code'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:SEAPORT,AIRPORT,CANAL,INLAND_HUB'],
            'country' => ['required', 'string', 'max:100'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $loc = Location::create($validated);
        $this->auditService->log('LOCATION_CREATED', $loc, null, $loc->toArray(), $request->user());

        return redirect()->route('locations.index')
            ->with('success', "Location Node {$loc->name} ({$loc->code}) added.");
    }
}
