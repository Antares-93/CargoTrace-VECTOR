<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TrackingService
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Add a historical tracking event and update the shipment status and location.
     * Validates transition rules and terminal state immutability.
     */
    public function addTrackingEvent(
        Shipment $shipment,
        array $data,
        User $user
    ): TrackingEvent {
        $targetStatus = $data['status'] ?? $shipment->status;
        $eventTime = isset($data['event_time']) ? Carbon::parse($data['event_time']) : now();

        // 1. Business Rule Validation: Status Transition
        if (!$shipment->canTransitionTo($targetStatus)) {
            throw ValidationException::withMessages([
                'status' => [
                    "Invalid status transition from '{$shipment->status}' to '{$targetStatus}'. Terminal statuses like DELIVERED cannot be altered without administrative override."
                ]
            ]);
        }

        // 2. Validate chronological order against latest event
        $latestEvent = $shipment->trackingEvents()->latest('event_time')->first();
        if ($latestEvent && $eventTime->lessThan($latestEvent->event_time->subMinutes(5))) {
            throw ValidationException::withMessages([
                'event_time' => [
                    "Event time ({$eventTime->toDateTimeString()}) cannot precede previous tracking event ({$latestEvent->event_time->toDateTimeString()})."
                ]
            ]);
        }

        return DB::transaction(function () use ($shipment, $data, $targetStatus, $eventTime, $user) {
            $oldShipmentData = $shipment->only(['status', 'actual_departure', 'actual_arrival', 'current_stage']);

            // Create immutable tracking event record
            $event = TrackingEvent::create([
                'shipment_id' => $shipment->id,
                'location_id' => $data['location_id'] ?? null,
                'created_by' => $user->id,
                'status' => $targetStatus,
                'event_time' => $eventTime,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'description' => $data['description'] ?? "Status updated to {$targetStatus}",
            ]);

            // Update shipment status and timestamps
            $shipment->status = $targetStatus;

            if (in_array($targetStatus, [Shipment::STATUS_PICKED_UP, Shipment::STATUS_IN_TRANSIT, Shipment::STATUS_IN_FLIGHT]) && !$shipment->actual_departure) {
                $shipment->actual_departure = $eventTime;
            }

            if ($targetStatus === Shipment::STATUS_DELIVERED && !$shipment->actual_arrival) {
                $shipment->actual_arrival = $eventTime;
                $shipment->current_stage = $shipment->total_stages;
            } elseif (isset($data['current_stage'])) {
                $shipment->current_stage = (int) $data['current_stage'];
            }

            $shipment->save();

            // Create notification for the shipment customer
            Notification::create([
                'user_id' => $shipment->customer_id,
                'shipment_id' => $shipment->id,
                'title' => "Shipment {$shipment->shipment_number} Updated",
                'message' => "New tracking update: {$event->description} (Status: {$targetStatus})",
            ]);

            // Record audit log
            $this->auditService->log(
                action: 'TRACKING_EVENT_ADDED',
                entity: $shipment,
                oldValues: $oldShipmentData,
                newValues: $shipment->only(['status', 'actual_departure', 'actual_arrival', 'current_stage']),
                user: $user
            );

            return $event;
        });
    }
}
