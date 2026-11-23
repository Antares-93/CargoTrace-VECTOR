<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Shipment;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    protected AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Display the user's profile view.
     */
    public function show(Request $request)
    {
        $user = $request->user();

        // Gather relevant stats based on role
        $stats = [
            'shipments_count' => $user->isCustomer() 
                ? $user->shipments()->count() 
                : Shipment::count(),
            'active_shipments_count' => $user->isCustomer() 
                ? $user->shipments()->whereNotIn('status', ['delivered', 'cancelled'])->count()
                : Shipment::whereNotIn('status', ['delivered', 'cancelled'])->count(),
            'tracking_events_count' => ($user->isOperator() || $user->isAdmin())
                ? $user->trackingEvents()->count()
                : 0,
            'exceptions_reported' => $user->reportedExceptions()->count(),
            'audit_logs_count' => $user->isAdmin() 
                ? AuditLog::count() 
                : $user->auditLogs()->count(),
        ];

        // Recent activity depending on user role
        $recentShipments = $user->isCustomer()
            ? $user->shipments()->with(['origin', 'destination', 'carrier'])->latest()->take(5)->get()
            : collect();

        $recentTracking = ($user->isOperator() || $user->isAdmin())
            ? $user->trackingEvents()->with('shipment')->latest()->take(5)->get()
            : collect();

        $recentAuditLogs = $user->isAdmin()
            ? AuditLog::with('user')->latest()->take(5)->get()
            : $user->auditLogs()->latest()->take(5)->get();

        return view('profile.show', compact('user', 'stats', 'recentShipments', 'recentTracking', 'recentAuditLogs'));
    }

    /**
     * Update the user's personal profile information.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
        ];

        $user->fill($validated);
        $user->save();

        $this->auditService->log(
            'PROFILE_UPDATED',
            $user,
            $oldValues,
            [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            $user
        );

        return redirect()->route('profile.show')->with('success', 'Profil Anda berhasil diperbarui!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', Password::defaults()->min(8), 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->auditService->log(
            'PASSWORD_CHANGED',
            $user,
            ['action' => 'password_update_attempt'],
            ['action' => 'password_updated_successfully'],
            $user
        );

        return redirect()->route('profile.show')->with('success', 'Kata sandi berhasil diubah!');
    }
}
