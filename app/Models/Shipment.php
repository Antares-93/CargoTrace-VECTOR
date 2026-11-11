<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use HasFactory;

    // Standard shipment status lifecycle
    public const STATUS_CREATED = 'CREATED';
    public const STATUS_BOOKED = 'BOOKED';
    public const STATUS_PICKED_UP = 'PICKED_UP';
    public const STATUS_IN_TRANSIT = 'IN_TRANSIT';
    public const STATUS_BERTHED = 'BERTHED';
    public const STATUS_IN_FLIGHT = 'IN_FLIGHT';
    public const STATUS_DELIVERED = 'DELIVERED';
    public const STATUS_CANCELLED = 'CANCELLED';

    // Allowed status transitions mapping
    public const ALLOWED_TRANSITIONS = [
        self::STATUS_CREATED => [self::STATUS_BOOKED, self::STATUS_CANCELLED],
        self::STATUS_BOOKED => [self::STATUS_PICKED_UP, self::STATUS_CANCELLED],
        self::STATUS_PICKED_UP => [self::STATUS_IN_TRANSIT, self::STATUS_IN_FLIGHT, self::STATUS_CANCELLED],
        self::STATUS_IN_TRANSIT => [self::STATUS_BERTHED, self::STATUS_DELIVERED, self::STATUS_IN_TRANSIT],
        self::STATUS_IN_FLIGHT => [self::STATUS_DELIVERED, self::STATUS_IN_TRANSIT],
        self::STATUS_BERTHED => [self::STATUS_IN_TRANSIT, self::STATUS_DELIVERED],
        self::STATUS_DELIVERED => [], // Terminal status
        self::STATUS_CANCELLED => [], // Terminal status
    ];

    protected $fillable = [
        'shipment_number',
        'customer_id',
        'carrier_id',
        'origin_id',
        'destination_id',
        'status',
        'transport_mode',
        'expected_departure',
        'actual_departure',
        'expected_arrival',
        'estimated_arrival',
        'actual_arrival',
        'delay_status',
        'delay_hours',
        'consignee_name',
        'vessel_or_flight',
        'berth_number',
        'current_stage',
        'total_stages',
        'temperature_celsius',
        'humidity_pct',
        'air_speed_kts',
        'air_altitude_ft',
        'notes',
    ];

    protected $casts = [
        'expected_departure' => 'datetime',
        'actual_departure' => 'datetime',
        'expected_arrival' => 'datetime',
        'estimated_arrival' => 'datetime',
        'actual_arrival' => 'datetime',
        'delay_hours' => 'integer',
        'current_stage' => 'integer',
        'total_stages' => 'integer',
        'temperature_celsius' => 'float',
        'humidity_pct' => 'float',
        'air_speed_kts' => 'integer',
        'air_altitude_ft' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    public function origin(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'origin_id');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(TrackingEvent::class)->orderBy('event_time', 'asc');
    }

    public function latestTrackingEvent()
    {
        return $this->hasOne(TrackingEvent::class)->latestOfMany('event_time');
    }

    public function exceptions(): HasMany
    {
        return $this->hasMany(ShipmentException::class)->latest('detected_at');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function getTotalWeightAttribute(): float
    {
        return (float) $this->items->sum('weight_kg');
    }

    public function getTotalVolumeAttribute(): float
    {
        return (float) $this->items->sum('volume_cbm');
    }

    public function getIsDelayedAttribute(): bool
    {
        return $this->delay_status !== 'ON_TIME' && $this->delay_hours > 0;
    }

    public function canTransitionTo(string $targetStatus): bool
    {
        if ($this->status === $targetStatus) {
            return true;
        }

        $allowed = self::ALLOWED_TRANSITIONS[$this->status] ?? [];
        return in_array($targetStatus, $allowed, true);
    }
}
