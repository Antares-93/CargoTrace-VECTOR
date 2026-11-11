<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number')->unique(); // e.g. AT-2026-B8910, AWB-772-91024
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('carrier_id')->constrained('carriers')->restrictOnDelete();
            $table->foreignId('origin_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('destination_id')->constrained('locations')->restrictOnDelete();
            
            // Status lifecycle: CREATED -> BOOKED -> PICKED_UP -> IN_TRANSIT -> DELIVERED (plus operational: BERTHED, IN_FLIGHT)
            $table->string('status')->default('CREATED')->index();
            $table->string('transport_mode')->default('OCEAN'); // OCEAN, AIR, ROAD, MULTIMODAL
            
            $table->dateTime('expected_departure');
            $table->dateTime('actual_departure')->nullable();
            $table->dateTime('expected_arrival');
            $table->dateTime('estimated_arrival')->nullable();
            $table->dateTime('actual_arrival')->nullable();
            
            // Delay calculations (ON_TIME, LOW, MEDIUM, HIGH)
            $table->string('delay_status')->default('ON_TIME')->index();
            $table->integer('delay_hours')->default(0);
            
            // Operational & Telemetry display fields (for GIS radar & dashboard)
            $table->string('consignee_name')->nullable();
            $table->string('vessel_or_flight')->nullable(); // e.g. "Maersk Mc-Kinney Moller" or "Boeing 777-200F / QR-803F"
            $table->string('berth_number')->nullable(); // e.g. "Berth 402"
            $table->unsignedTinyInteger('current_stage')->default(1);
            $table->unsignedTinyInteger('total_stages')->default(4);
            $table->decimal('temperature_celsius', 4, 1)->nullable(); // e.g. 4.2 for Reefer
            $table->decimal('humidity_pct', 4, 1)->nullable(); // e.g. 62.0
            $table->integer('air_speed_kts')->nullable(); // e.g. 485
            $table->integer('air_altitude_ft')->nullable(); // e.g. 36000
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
