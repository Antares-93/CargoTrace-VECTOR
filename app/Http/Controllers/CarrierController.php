<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarrierController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    public function index(): View
    {
        $carriers = Carrier::withCount('shipments')->orderBy('name')->get();
        return view('carriers.index', compact('carriers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10', 'unique:carriers,code'],
            'name' => ['required', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
        ]);

        $carrier = Carrier::create($validated);
        $this->auditService->log('CARRIER_CREATED', $carrier, null, $carrier->toArray(), $request->user());

        return redirect()->route('carriers.index')
            ->with('success', "Carrier {$carrier->name} ({$carrier->code}) added.");
    }

    public function toggleStatus(Carrier $carrier, Request $request): RedirectResponse
    {
        $carrier->is_active = !$carrier->is_active;
        $carrier->save();

        $this->auditService->log('CARRIER_STATUS_TOGGLED', $carrier, null, ['is_active' => $carrier->is_active], $request->user());

        return redirect()->route('carriers.index')
            ->with('success', "Carrier {$carrier->name} is now " . ($carrier->is_active ? 'Active' : 'Inactive') . '.');
    }
}
