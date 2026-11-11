<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExceptionService
{
    public function __construct(
        protected EtaService $etaService,
        protected AuditService $auditService
    ) {}

    /**
     * Report an operational exception on a shipment.
     */
    public function reportException(
        Shipment $shipment,
        array $data,
        User $user
    ): ShipmentException {
        return DB::transaction(function () use ($shipment, $data, $user) {
            $detectedAt = isset($data['detected_at']) ? Carbon::parse($data['detected_at']) : now();

            $exception = ShipmentException::create([
                'shipment_id' => $shipment->id,
                'reported_by' => $user->id,
                'exception_type' => $data['exception_type'],
                'severity' => $data['severity'] ?? ShipmentException::SEVERITY_MEDIUM,
                'status' => ShipmentException::STATUS_OPEN,
                'detected_at' => $detectedAt,
                'resolution_notes' => null,
            ]);

            // If an estimated delay in hours is provided, adjust shipment ETA
            if (!empty($data['delay_impact_hours'])) {
                $hours = (int) $data['delay_impact_hours'];
                $baseEta = $shipment->estimated_arrival ?? $shipment->expected_arrival;
                $newEta = (clone $baseEta)->addHours($hours);
                $this->etaService->updateShipmentDelay($shipment, $newEta);
            }

            // Notification for customer
            Notification::create([
                'user_id' => $shipment->customer_id,
                'shipment_id' => $shipment->id,
                'title' => "Incident Alert: {$exception->exception_type} on {$shipment->shipment_number}",
                'message' => "Severity: {$exception->severity}. Incident logged. Our operations desk is investigating.",
            ]);

            // Audit log
            $this->auditService->log(
                action: 'EXCEPTION_REPORTED',
                entity: $exception,
                oldValues: null,
                newValues: $exception->toArray(),
                user: $user
            );

            return $exception;
        });
    }

    /**
     * Resolve or update status of an existing exception.
     */
    public function resolveException(
        ShipmentException $exception,
        array $data,
        User $user
    ): ShipmentException {
        return DB::transaction(function () use ($exception, $data, $user) {
            $oldValues = $exception->only(['status', 'resolved_at', 'resolution_notes']);

            $targetStatus = $data['status'] ?? ShipmentException::STATUS_RESOLVED;
            $exception->status = $targetStatus;

            if ($targetStatus === ShipmentException::STATUS_RESOLVED) {
                $exception->resolved_at = now();
            }

            if (!empty($data['resolution_notes'])) {
                $exception->resolution_notes = $data['resolution_notes'];
            }

            $exception->save();

            // Notification for customer
            Notification::create([
                'user_id' => $exception->shipment->customer_id,
                'shipment_id' => $exception->shipment_id,
                'title' => "Incident Resolved: {$exception->shipment->shipment_number}",
                'message' => "The {$exception->exception_type} exception has been marked as {$targetStatus}. Notes: {$exception->resolution_notes}",
            ]);

            // Audit log
            $this->auditService->log(
                action: 'EXCEPTION_RESOLVED',
                entity: $exception,
                oldValues: $oldValues,
                newValues: $exception->only(['status', 'resolved_at', 'resolution_notes']),
                user: $user
            );

            return $exception;
        });
    }
}
